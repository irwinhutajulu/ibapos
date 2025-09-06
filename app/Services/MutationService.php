<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockMutation;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class MutationService
{
    public function create(int $productId, int $fromLocationId, int $toLocationId, string $qty, int $userId, ?string $note = null): \App\Models\StockMutation
    {
        if ($fromLocationId === $toLocationId) {
            throw new BadRequestHttpException('from and to location must differ');
        }
        return \App\Models\StockMutation::create([
            'product_id' => $productId,
            'from_location_id' => $fromLocationId,
            'to_location_id' => $toLocationId,
            'qty' => $qty,
            'date' => now()->toDateString(),
            'note' => $note,
            'status' => 'pending',
            'requested_by' => $userId,
        ]);
    }
    public function confirm(StockMutation $mutation, int $userId): void
    {
        if ($mutation->status !== 'pending') {
            throw new BadRequestHttpException('Only pending mutation can be confirmed.');
        }

        DB::transaction(function () use ($mutation, $userId) {
            $qty = (float)$mutation->qty;

            // Deduct from origin at origin avg_cost
            $originStock = Stock::where('product_id', $mutation->product_id)
                ->where('location_id', $mutation->from_location_id)
                ->lockForUpdate()->first();
            if (!$originStock || (float)$originStock->qty < $qty) {
                throw new BadRequestHttpException('Insufficient origin stock');
            }
            $originCost = (float)$originStock->avg_cost;

            app(\App\Services\InventoryService::class)->adjustStockWithLedger(
                productId: $mutation->product_id,
                locationId: $mutation->from_location_id,
                qtyDelta: (string)(-1 * $qty),
                costPerUnit: (string)$originCost,
                refType: 'mutation',
                refId: $mutation->id,
                userId: $userId,
                note: 'Stock mutation out'
            );

            // Add to destination with weighted average
            $destStock = Stock::where('product_id', $mutation->product_id)
                ->where('location_id', $mutation->to_location_id)
                ->lockForUpdate()->first();

            if (!$destStock) {
                $destStock = Stock::create([
                    'product_id' => $mutation->product_id,
                    'location_id' => $mutation->to_location_id,
                    'qty' => 0,
                    'avg_cost' => $originCost,
                ]);
            }

            // Compute new avg cost for destination (do not change qty here; ledger will adjust)
            $currentQty = (float)$destStock->qty;
            $currentCost = (float)$destStock->avg_cost;
            $newQty = $currentQty + $qty;
            $newAvg = $newQty > 0 ? (($currentQty * $currentCost) + ($qty * $originCost)) / $newQty : $originCost;
            $destStock->avg_cost = (string)round($newAvg, 4);
            $destStock->save();

            app(\App\Services\InventoryService::class)->adjustStockWithLedger(
                productId: $mutation->product_id,
                locationId: $mutation->to_location_id,
                qtyDelta: (string)$qty,
                costPerUnit: (string)$originCost,
                refType: 'mutation',
                refId: $mutation->id,
                userId: $userId,
                note: 'Stock mutation in'
            );

            $mutation->update(['status' => 'confirmed', 'confirmed_at' => now(), 'confirmed_by' => $userId]);
        });
    }

    public function reject(StockMutation $mutation, int $userId, ?string $reason = null): void
    {
        if ($mutation->status !== 'pending') {
            throw new BadRequestHttpException('Only pending mutation can be rejected.');
        }
        $mutation->update(['status' => 'rejected', 'rejected_at' => now(), 'rejected_by' => $userId, 'note' => $reason]);
    }
}
