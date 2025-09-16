<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalesPayment;
use App\Models\Customer;
use App\Models\Product;

class ImportSalesFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Usage:
     *  php artisan import:sales "storage\imports\sales.csv" --dry-run
     *
     * Options:
     *  --delimiter=,   CSV delimiter
     *  --header=1      Whether file has a header row (1/0)
     *  --map=...       (Optional) comma-separated custom column mapping (not implemented parsing)
     */
    protected $signature = 'import:sales
                            {path : CSV file path}
                            {--dry-run : Validate only, do not persist}
                            {--delimiter=, : CSV delimiter}
                            {--header=1 : Whether CSV contains a header row (1 = yes)}';

    protected $description = 'Import sales from a CSV file. Supports items and payments as JSON columns.';

    public function handle()
    {
        $path = $this->argument('path');
        $delimiter = $this->option('delimiter') ?? ',';
        $hasHeader = (int) $this->option('header') === 1;
        $dryRun = $this->option('dry-run');

        if (!file_exists($path)) {
            $this->error("File not found: {$path}");
            return 1;
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            $this->error("Unable to open file: {$path}");
            return 1;
        }

        // Default mapping — edit this map to match your CSV header names if needed
        $map = [
            // basic identifiers
            'id' => 'id',
            'invoice_no' => 'invoice_no',
            'date' => 'date',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
            // relations
            'user_id' => 'user_id',
            'location_id' => 'location_id',
            'customer_id' => 'customer_id',
            'customer_email' => 'customer_email',
            'customer_name' => 'customer_name',
            // amounts and metadata
            'additional_fee' => 'additional_fee',
            'discount' => 'discount',
            'total' => 'total',
            'payment' => 'payment',
            'change' => 'change',
            'payment_type' => 'payment_type',
            'status' => 'status',
            'posted_at' => 'posted_at',
            'posted_by' => 'posted_by',
            'voided_at' => 'voided_at',
            'voided_by' => 'voided_by',
            // embedded data
            'items' => 'items', // JSON array: [{"id":...,"product_sku":"SKU","product_id":123,"qty":1,"price":100,"discount":0,"subtotal":100,...},...]
            'payments' => 'payments', // JSON array: [{"id":...,"type":"cash","amount":100,"reference":""},...]
        ];

        $headers = [];
        $rowCount = 0;
        $imported = 0;
        $skipped = 0;
        $errors = 0;

        if ($hasHeader) {
            $headers = fgetcsv($handle, 0, $delimiter);
            if ($headers === false) {
                $this->error('Unable to parse header row');
                return 1;
            }
            $headers = array_map('trim', $headers);
        }

        $this->info('Starting import (dry-run=' . ($dryRun ? 'true' : 'false') . ')');

        $bar = null;
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rowCount++;
            // Map row to associative array
            $data = [];
            if ($hasHeader) {
                foreach ($headers as $i => $h) {
                    $data[$h] = isset($row[$i]) ? $row[$i] : null;
                }
            } else {
                // If no header, use numeric indexes as keys
                foreach ($row as $i => $v) {
                    $data[$i] = $v;
                }
            }

            // Build sale payload using mapping
            $salePayload = [];
            foreach ($map as $key => $col) {
                if (isset($data[$col])) {
                    $salePayload[$key] = $data[$col];
                }
            }

            // Fallback: some CSVs use alternative column names for location (e.g. source_location_code)
            if (empty($salePayload['location_id'])) {
                $altCols = ['source_location_code', 'location_code', 'location'];
                foreach ($altCols as $c) {
                    if (isset($data[$c]) && $data[$c] !== '') {
                        // prefer numeric ids if provided, otherwise store raw value (importer can translate later if needed)
                        $salePayload['location_id'] = is_numeric($data[$c]) ? intval($data[$c]) : $data[$c];
                        break;
                    }
                }
            }

            // Basic: if invoice_no is empty, generate one. In dry-run we simulate generation without touching counters.
            $generatedInvoice = false;
            if (empty($salePayload['invoice_no'])) {
                $generatedInvoice = true;
                if ($dryRun) {
                    // simulate invoice format: IBA-location-YYYYMMDD-DRY-<row>
                    $loc = $salePayload['location_id'] ?? '0';
                    $salePayload['invoice_no'] = sprintf('IBA-%s-%s-DRY-%05d', $loc, date('Ymd'), $rowCount);
                    $this->info("Row {$rowCount}: invoice_no simulated as {$salePayload['invoice_no']} (dry-run)");
                } else {
                    // use InvoiceGenerator to create real invoice (safe, concurrency handled inside service)
                    try {
                        $locId = $salePayload['location_id'] ?? null;
                        $salePayload['invoice_no'] = \App\Services\InvoiceGenerator::next('sale', $locId);
                        $this->info("Row {$rowCount}: generated invoice_no {$salePayload['invoice_no']}");
                    } catch (\Exception $e) {
                        $this->warn("Row {$rowCount}: failed to generate invoice_no: " . $e->getMessage());
                        $skipped++;
                        continue;
                    }
                }
            }

            // parse date
            $saleDate = null;
            if (!empty($salePayload['date'])) {
                try {
                    $saleDate = Carbon::parse($salePayload['date'])->toDateTimeString();
                } catch (\Exception $e) {
                    $this->warn("Row {$rowCount}: invalid date '{$salePayload['date']}' — using now");
                    $saleDate = Carbon::now()->toDateTimeString();
                }
            } else {
                $saleDate = Carbon::now()->toDateTimeString();
            }

            // Normalize created_at / updated_at: accept explicit datetimes, but treat empty strings as missing
            $createdAt = $saleDate;
            if (isset($salePayload['created_at']) && trim($salePayload['created_at']) !== '') {
                try {
                    $createdAt = Carbon::parse($salePayload['created_at'])->toDateTimeString();
                } catch (\Exception $e) {
                    $this->warn("Row {$rowCount}: invalid created_at '{$salePayload['created_at']}' — using sale date");
                    $createdAt = $saleDate;
                }
            }

            $updatedAt = $saleDate;
            if (isset($salePayload['updated_at']) && trim($salePayload['updated_at']) !== '') {
                try {
                    $updatedAt = Carbon::parse($salePayload['updated_at'])->toDateTimeString();
                } catch (\Exception $e) {
                    $this->warn("Row {$rowCount}: invalid updated_at '{$salePayload['updated_at']}' — using sale date");
                    $updatedAt = $saleDate;
                }
            }

            // items and payments are expected to be JSON strings in CSV
            $items = [];
            if (!empty($salePayload['items'])) {
                $items = json_decode($salePayload['items'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->warn("Row {$rowCount}: invalid JSON in items column — skipping");
                    $skipped++;
                    continue;
                }
            }

            $payments = [];
            if (!empty($salePayload['payments'])) {
                $payments = json_decode($salePayload['payments'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->warn("Row {$rowCount}: invalid JSON in payments column — skipping");
                    $skipped++;
                    continue;
                }
            }

            if (empty($items) && empty($payments)) {
                $this->warn("Row {$rowCount}: no items AND no payments — skipping");
                $skipped++;
                continue;
            }

            // Normalize status and date fields; prepare final sale values (match migration fields)
            // Allowed status values come from DB schema: draft, posted, void
            $allowedStatuses = ['draft', 'posted', 'void'];
            $rawStatus = isset($salePayload['status']) ? trim(strtolower($salePayload['status'])) : '';
            $status = 'draft';
            if ($rawStatus !== '' && in_array($rawStatus, $allowedStatuses, true)) {
                $status = $rawStatus;
            }

            // Parse posted_at / voided_at into valid datetimes or null
            $postedAt = null;
            if (isset($salePayload['posted_at']) && trim($salePayload['posted_at']) !== '') {
                try {
                    $postedAt = Carbon::parse($salePayload['posted_at'])->toDateTimeString();
                } catch (\Exception $e) {
                    $this->warn("Row {$rowCount}: invalid posted_at '{$salePayload['posted_at']}' — ignoring");
                    $postedAt = null;
                }
            }

            $voidedAt = null;
            if (isset($salePayload['voided_at']) && trim($salePayload['voided_at']) !== '') {
                try {
                    $voidedAt = Carbon::parse($salePayload['voided_at'])->toDateTimeString();
                } catch (\Exception $e) {
                    $this->warn("Row {$rowCount}: invalid voided_at '{$salePayload['voided_at']}' — ignoring");
                    $voidedAt = null;
                }
            }

            $saleData = [
                'invoice_no' => $salePayload['invoice_no'],
                'date' => $saleDate,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
                'status' => $status,
                'location_id' => $salePayload['location_id'] ?? null,
                'user_id' => $salePayload['user_id'] ?? null,
                'customer_id' => null,
                'additional_fee' => isset($salePayload['additional_fee']) ? floatval($salePayload['additional_fee']) : 0,
                'discount' => isset($salePayload['discount']) ? floatval($salePayload['discount']) : 0,
                'total' => isset($salePayload['total']) ? floatval($salePayload['total']) : 0,
                'payment' => isset($salePayload['payment']) ? floatval($salePayload['payment']) : 0,
                'change' => isset($salePayload['change']) ? floatval($salePayload['change']) : 0,
                'payment_type' => $salePayload['payment_type'] ?? null,
                'posted_at' => $postedAt,
                'posted_by' => !empty($salePayload['posted_by']) ? intval($salePayload['posted_by']) : null,
                'voided_at' => $voidedAt,
                'voided_by' => !empty($salePayload['voided_by']) ? intval($salePayload['voided_by']) : null,
            ];

            // allow explicit id if provided in CSV
            $explicitId = null;
            if (!empty($salePayload['id'])) {
                $explicitId = intval($salePayload['id']);
            }

            // customer lookup: prefer customer_id, then email, then name (create if name provided)
            if (!empty($salePayload['customer_id'])) {
                $saleData['customer_id'] = $salePayload['customer_id'];
            } elseif (!empty($salePayload['customer_email'])) {
                $customer = Customer::where('email', $salePayload['customer_email'])->first();
                if ($customer) {
                    $saleData['customer_id'] = $customer->id;
                } elseif (!empty($salePayload['customer_name'])) {
                    // create customer
                    if (!$dryRun) {
                        $customer = Customer::create([
                            'name' => $salePayload['customer_name'],
                            'email' => $salePayload['customer_email'],
                        ]);
                        $saleData['customer_id'] = $customer->id;
                    }
                }
            } elseif (!empty($salePayload['customer_name'])) {
                // create by name only
                if (!$dryRun) {
                    $customer = Customer::create([
                        'name' => $salePayload['customer_name'],
                    ]);
                    $saleData['customer_id'] = $customer->id;
                }
            }

            // Insert/update within a transaction
            DB::beginTransaction();
            try {
                if ($dryRun) {
                    // Validate lookups (product existence) without persisting
                    foreach ($items as $it) {
                        if (!empty($it['product_sku'])) {
                            $product = Product::where('sku', $it['product_sku'])->first();
                            if (!$product) {
                                $this->warn("Row {$rowCount}: product sku {$it['product_sku']} not found (dry-run)");
                            }
                        } elseif (!empty($it['product_id'])) {
                            $product = Product::find($it['product_id']);
                            if (!$product) {
                                $this->warn("Row {$rowCount}: product id {$it['product_id']} not found (dry-run)");
                            }
                        }
                    }
                    DB::rollBack();
                    $imported++;
                    continue;
                }

                // Create sale — allow explicit id if provided by inserting via query builder
                if ($explicitId) {
                    $insert = array_merge($saleData, ['id' => $explicitId]);
                    DB::table('sales')->insert($insert);
                    $sale = Sale::find($explicitId);
                } else {
                    $sale = Sale::create(array_merge($saleData, [
                        'created_at' => $saleData['created_at'],
                        'updated_at' => $saleData['updated_at'],
                    ]));
                }

                $totalPayments = 0;
                $totalItemsSubtotal = 0;

                // Items
                foreach ($items as $it) {
                    // Expected item keys: id (optional), product_sku OR product_id, qty, price, discount, subtotal
                    $itemExplicitId = isset($it['id']) ? intval($it['id']) : null;
                    $productId = $it['product_id'] ?? null;
                    if (empty($productId) && !empty($it['product_sku'])) {
                        $product = Product::where('sku', $it['product_sku'])->first();
                        if ($product) $productId = $product->id;
                    }

                    $qty = isset($it['qty']) ? floatval($it['qty']) : 1;
                    $price = isset($it['price']) ? floatval($it['price']) : 0;
                    $discount = isset($it['discount']) ? floatval($it['discount']) : 0;
                    $subtotal = isset($it['subtotal']) ? floatval($it['subtotal']) : ($qty * $price - $discount);

                    if ($itemExplicitId) {
                        DB::table('sale_items')->insert([
                            'id' => $itemExplicitId,
                            'sale_id' => $sale->id,
                            'product_id' => $productId,
                            'qty' => $qty,
                            'price' => $price,
                            'discount' => $discount,
                            'subtotal' => $subtotal,
                            'source_location_id' => $it['source_location_id'] ?? null,
                            'created_at' => $saleData['created_at'],
                            'updated_at' => $saleData['updated_at'],
                        ]);
                    } else {
                        SaleItem::create([
                            'sale_id' => $sale->id,
                            'product_id' => $productId,
                            'qty' => $qty,
                            'price' => $price,
                            'discount' => $discount,
                            'subtotal' => $subtotal,
                            'source_location_id' => $it['source_location_id'] ?? null,
                        ]);
                    }

                    $totalItemsSubtotal += $subtotal;
                }

                // Payments
                foreach ($payments as $p) {
                    $paymentExplicitId = isset($p['id']) ? intval($p['id']) : null;
                    $amount = isset($p['amount']) ? floatval($p['amount']) : 0;
                    $paidAt = isset($p['paid_at']) ? Carbon::parse($p['paid_at'])->toDateTimeString() : Carbon::now()->toDateTimeString();
                    if ($paymentExplicitId) {
                        DB::table('sales_payments')->insert([
                            'id' => $paymentExplicitId,
                            'sale_id' => $sale->id,
                            'type' => $p['type'] ?? 'unknown',
                            'amount' => $amount,
                            'reference' => $p['reference'] ?? null,
                            'note' => $p['note'] ?? null,
                            'paid_at' => $paidAt,
                            'created_at' => $saleData['created_at'],
                            'updated_at' => $saleData['updated_at'],
                        ]);
                    } else {
                        SalesPayment::create([
                            'sale_id' => $sale->id,
                            'type' => $p['type'] ?? 'unknown',
                            'amount' => $amount,
                            'reference' => $p['reference'] ?? null,
                            'note' => $p['note'] ?? null,
                            'paid_at' => $paidAt,
                        ]);
                    }
                    $totalPayments += $amount;
                }

                // Update sale payment / change / total if necessary
                $sale->payment = $totalPayments;
                $sale->change = max(0, $totalPayments - $sale->total);
                // if total was zero but we calculated items, prefer items subtotal
                if (empty($sale->total) && $totalItemsSubtotal > 0) {
                    $sale->total = $totalItemsSubtotal;
                }
                $sale->save();

                DB::commit();
                $imported++;
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Row {$rowCount}: failed to import — " . $e->getMessage());
                Log::error('import:sales error', ['row' => $rowCount, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                $errors++;
                continue;
            }
        }

        fclose($handle);

        $this->info("Import finished. Rows: {$rowCount}, Imported: {$imported}, Skipped: {$skipped}, Errors: {$errors}");

        return 0;
    }
}
