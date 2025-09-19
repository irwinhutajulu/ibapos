<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PurchasePostingService
{
    public function calculateLandedCost(Purchase $purchase): array
    {
        // total extra cost includes freight + loading + unloading
        $totalExtra = (float)($purchase->freight_cost ?? 0) + (float)($purchase->loading_cost ?? 0) + (float)($purchase->unloading_cost ?? 0);
        if ($totalExtra <= 0) {
            return [];
        }

        // Prefer weight-based allocation: use item weight * qty
        // Each item may not have product relation loaded; try to read product weight if available on item or via relation
        $itemWeights = [];
        $totalWeight = 0.0;
        foreach ($purchase->items as $item) {
            // item may have a 'weight' attribute (copied from Product when saving purchase), otherwise try relation
            $w = (float)($item->weight ?? 0);
            if ($w <= 0 && isset($item->product) && isset($item->product->weight)) {
                $w = (float)$item->product->weight;
            }
            $itemWeightTotal = $w * (float)$item->qty;
            $itemWeights[$item->id] = $itemWeightTotal;
            $totalWeight += $itemWeightTotal;
        }

        $map = [];

        // We'll allocate using integer cents via Largest Remainder (Hamilton) method
        $totalExtraCents = (int) round($totalExtra * 100);
        if ($totalExtraCents === 0) {
            return [];
        }

        $itemsData = [];
        $index = 0;

        if ($totalWeight > 0) {
            // compute raw portion in cents for each item based on weight
            foreach ($purchase->items as $item) {
                $raw = ($itemWeights[$item->id] / $totalWeight) * $totalExtraCents; // in cents (float)
                $floor = (int) floor($raw + 1e-9);
                $fraction = $raw - $floor;
                $itemsData[$item->id] = ['floor' => $floor, 'fraction' => $fraction, 'index' => $index++];
            }
        } else {
            // fallback: allocate by subtotal
            $totalSubtotal = (float)$purchase->items->sum(fn($i) => (float)$i->subtotal);
            if ($totalSubtotal <= 0) {
                return [];
            }
            foreach ($purchase->items as $item) {
                $raw = ((float)$item->subtotal / $totalSubtotal) * $totalExtraCents; // in cents
                $floor = (int) floor($raw + 1e-9);
                $fraction = $raw - $floor;
                $itemsData[$item->id] = ['floor' => $floor, 'fraction' => $fraction, 'index' => $index++];
            }
        }

        // sum floor cents and compute residual cents to distribute
        $sumFloor = array_sum(array_map(fn($d) => $d['floor'], $itemsData));
        $residualCents = $totalExtraCents - $sumFloor; // >= 0

        if ($residualCents > 0) {
            // sort items by fractional remainder desc, tie-break by original index (stable)
            uasort($itemsData, function ($a, $b) {
                if ($a['fraction'] === $b['fraction']) return $a['index'] <=> $b['index'];
                return ($b['fraction'] <=> $a['fraction']);
            });

            // distribute one cent to top residualCents items
            $i = 0;
            foreach ($itemsData as $id => &$d) {
                if ($i < $residualCents) {
                    $d['floor'] += 1;
                }
                $i++;
            }
            unset($d);
        }

        // prepare map in decimal currency
        foreach ($itemsData as $id => $d) {
            $map[$id] = round($d['floor'] / 100, 2);
        }

        return $map;
    }

    public function allocateFreightCost(Purchase $purchase): void
    {
        $allocation = $this->calculateLandedCost($purchase);
        if (!$allocation) return;
        foreach ($purchase->items as $item) {
            $extra = $allocation[$item->id] ?? 0.0;
            if ($extra > 0) {
                $perUnit = $extra / max(1e-9, (float)$item->qty);
                // Adjust effective price used for avg_cost calculation by adding per-unit freight
                $item->price = (string) round(((float)$item->price) + $perUnit, 4);
            }
        }
    }
    public function markAsReceived(Purchase $purchase, int $userId): void
    {
        if ($purchase->status !== 'draft') {
            throw new BadRequestHttpException('Only draft purchase can be marked as received.');
        }
        $purchase->update(['status' => 'received','received_at' => now(), 'received_by' => $userId]);
    }

    public function post(Purchase $purchase, int $userId): void
    {
        if (!in_array($purchase->status, ['draft','received'])) {
            throw new BadRequestHttpException('Invalid state for posting.');
        }

        DB::transaction(function () use ($purchase, $userId) {
            // Adjust item prices for landed cost
            $this->allocateFreightCost($purchase);
            foreach ($purchase->items as $item) {
                $stock = Stock::where('product_id', $item->product_id)
                    ->where('location_id', $purchase->location_id)
                    ->lockForUpdate()
                    ->first();

                $qtyAdd = (float)$item->qty;
                $unitCost = (float)$item->price;

                if (!$stock) {
                    $stock = Stock::create([
                        'product_id' => $item->product_id,
                        'location_id' => $purchase->location_id,
                        'qty' => 0,
                        'avg_cost' => $unitCost,
                    ]);
                }

                // Weighted average cost (do NOT change qty here; ledger call will adjust qty)
                $currentQty = (float)$stock->qty;
                $currentCost = (float)$stock->avg_cost;
                $newQty = $currentQty + $qtyAdd;
                $newAvg = $newQty > 0 ? (($currentQty * $currentCost) + ($qtyAdd * $unitCost)) / $newQty : $unitCost;
                $stock->avg_cost = (string)round($newAvg, 4);
                $stock->save();

                app(\App\Services\InventoryService::class)->adjustStockWithLedger(
                    productId: $item->product_id,
                    locationId: $purchase->location_id,
                    qtyDelta: (string)$qtyAdd,
                    costPerUnit: (string)$unitCost,
                    refType: 'purchase',
                    refId: $purchase->id,
                    userId: $userId,
                    note: 'Purchase posting'
                );
            }

                $purchase->update(['status' => 'posted','posted_at' => now(),'posted_by' => $userId]);
                event(new \App\Events\PurchasePosted($purchase));
                // Kirim notifikasi ke admin
                $admin = \App\Models\User::role('super-admin')->first();
                if ($admin) {
                    app(\App\Services\NotificationService::class)->sendToUser(
                        $admin,
                        'purchase.posted',
                        [
                            'invoice_no' => $purchase->invoice_no,
                            'location_id' => $purchase->location_id,
                            'supplier_id' => $purchase->supplier_id,
                            'total' => $purchase->total,
                            'id' => $purchase->id,
                        ]
                    );
                }
        });
    }

    public function void(Purchase $purchase, int $userId): void
    {
        if ($purchase->status !== 'posted') {
            throw new BadRequestHttpException('Only posted purchase can be voided.');
        }
        // Policy: business-specific; keep avg_cost consistency in mind.
        $purchase->update(['status' => 'void','voided_at' => now(),'voided_by' => $userId]);
        event(new \App\Events\PurchaseVoided($purchase));
        // Kirim notifikasi ke admin
        $admin = \App\Models\User::role('super-admin')->first();
        if ($admin) {
            app(\App\Services\NotificationService::class)->sendToUser(
                $admin,
                'purchase.voided',
                [
                    'invoice_no' => $purchase->invoice_no,
                    'location_id' => $purchase->location_id,
                    'supplier_id' => $purchase->supplier_id,
                    'total' => $purchase->total,
                    'id' => $purchase->id,
                ]
            );
        }
    }
}
