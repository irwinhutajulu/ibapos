<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockAdjustment;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AdjustmentService
{
    public function post(StockAdjustment $adj, int $userId): void
    {
        if ($adj->status !== 'draft') {
            throw new BadRequestHttpException('Only draft adjustment can be posted.');
        }

        DB::transaction(function () use ($adj, $userId) {
            foreach ($adj->items as $item) {
                $qtyDelta = (float)$item->qty_change; // positive or negative

                $stock = Stock::where('product_id', $item->product_id)
                    ->where('location_id', $adj->location_id)
                    ->lockForUpdate()->first();

                if (!$stock) {
                    $stock = Stock::create([
                        'product_id' => $item->product_id,
                        'location_id' => $adj->location_id,
                        'qty' => 0,
                        'avg_cost' => 0,
                    ]);
                }

                $costPerUnit = (float)$stock->avg_cost;
                if ($qtyDelta > 0 && $item->unit_cost !== null) {
                    // positive adjustment can affect avg cost (do not change qty here)
                    $unitCost = (float)$item->unit_cost;
                    $currentQty = (float)$stock->qty;
                    $newQty = $currentQty + $qtyDelta;
                    $newAvg = $newQty > 0 ? (($currentQty * (float)$stock->avg_cost) + ($qtyDelta * $unitCost)) / $newQty : $unitCost;
                    $stock->avg_cost = (string)round($newAvg, 4);
                    $stock->save();
                    $costPerUnit = $unitCost; // ledger snapshot at provided unit cost
                }

                app(\App\Services\InventoryService::class)->adjustStockWithLedger(
                    productId: $item->product_id,
                    locationId: $adj->location_id,
                    qtyDelta: (string)$qtyDelta,
                    costPerUnit: (string)$costPerUnit,
                    refType: 'adjustment',
                    refId: $adj->id,
                    userId: $userId,
                    note: $item->note ?? 'Stock adjustment'
                );
            }

            $adj->update(['status' => 'posted', 'posted_at' => now(), 'posted_by' => $userId]);
        });
    }

    public function void(StockAdjustment $adj, int $userId): void
    {
        if ($adj->status !== 'posted') {
            throw new BadRequestHttpException('Only posted adjustment can be voided.');
        }
        $adj->update(['status' => 'void', 'voided_at' => now(), 'voided_by' => $userId]);
    }
}
