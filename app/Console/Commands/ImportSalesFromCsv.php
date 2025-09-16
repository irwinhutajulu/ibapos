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
            'invoice_no' => 'invoice_no',
            'date' => 'date',
            'status' => 'status',
            'location_id' => 'location_id',
            'customer_id' => 'customer_id',
            'customer_email' => 'customer_email',
            'customer_name' => 'customer_name',
            'total' => 'total',
            'items' => 'items', // JSON array: [{"product_sku":"SKU","qty":1,"price":100,"discount":0},...]
            'payments' => 'payments', // JSON array: [{"type":"cash","amount":100,"reference":""},...]
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

            // Basic validation
            if (empty($salePayload['invoice_no'])) {
                $this->warn("Row {$rowCount}: missing invoice_no — skipping");
                $skipped++;
                continue;
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

            // Prepare final sale values
            $saleData = [
                'invoice_no' => $salePayload['invoice_no'],
                'sale_date' => $saleDate,
                'status' => $salePayload['status'] ?? 'draft',
                'location_id' => $salePayload['location_id'] ?? null,
                'customer_id' => null,
                'payment' => 0,
                'change' => 0,
                'total' => isset($salePayload['total']) ? floatval($salePayload['total']) : 0,
            ];

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
                        }
                    }
                    DB::rollBack();
                    $imported++;
                    continue;
                }

                // Create sale
                $sale = Sale::create(array_merge($saleData, [
                    'created_at' => $saleDate,
                    'updated_at' => $saleDate,
                ]));

                $totalPayments = 0;
                $totalItemsSubtotal = 0;

                // Items
                foreach ($items as $it) {
                    // Expected item keys: product_sku OR product_id, qty, price, discount, subtotal
                    $productId = $it['product_id'] ?? null;
                    if (empty($productId) && !empty($it['product_sku'])) {
                        $product = Product::where('sku', $it['product_sku'])->first();
                        if ($product) $productId = $product->id;
                    }

                    $qty = isset($it['qty']) ? floatval($it['qty']) : 1;
                    $price = isset($it['price']) ? floatval($it['price']) : 0;
                    $discount = isset($it['discount']) ? floatval($it['discount']) : 0;
                    $subtotal = isset($it['subtotal']) ? floatval($it['subtotal']) : ($qty * $price - $discount);

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $productId,
                        'qty' => $qty,
                        'price' => $price,
                        'discount' => $discount,
                        'subtotal' => $subtotal,
                        'source_location_id' => $it['source_location_id'] ?? null,
                    ]);

                    $totalItemsSubtotal += $subtotal;
                }

                // Payments
                foreach ($payments as $p) {
                    $amount = isset($p['amount']) ? floatval($p['amount']) : 0;
                    SalesPayment::create([
                        'sale_id' => $sale->id,
                        'type' => $p['type'] ?? 'unknown',
                        'amount' => $amount,
                        'reference' => $p['reference'] ?? null,
                        'note' => $p['note'] ?? null,
                        'paid_at' => isset($p['paid_at']) ? Carbon::parse($p['paid_at'])->toDateTimeString() : Carbon::now()->toDateTimeString(),
                    ]);
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
