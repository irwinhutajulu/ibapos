<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PurchasePostingService
{
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
            foreach ($purchase->items as $item) {
                $stock = Stock::where('product_id', $item->product_id)
                    ->where('location_id', $purchase->location_id)
                    ->lockForUpdate()
                    ->first();

                $qtyAdd = (float)$item->qty;
                $unitCost = (float)$item->price; // simple; freight allocation can be added later

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
        });
    }

    public function void(Purchase $purchase, int $userId): void
    {
        if ($purchase->status !== 'posted') {
            throw new BadRequestHttpException('Only posted purchase can be voided.');
        }
        // Policy: business-specific; keep avg_cost consistency in mind.
        $purchase->update(['status' => 'void','voided_at' => now(),'voided_by' => $userId]);
    }
}
