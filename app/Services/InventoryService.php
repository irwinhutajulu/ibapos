<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockLedger;
use App\Events\StockUpdated;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function getAvailableStock(int $productId, int $locationId): string
    {
        $stock = Stock::where('product_id', $productId)->where('location_id', $locationId)->first();
        return $stock?->qty ?? '0';
    }

    public function adjustStockWithLedger(int $productId, int $locationId, string $qtyDelta, ?string $costPerUnit, string $refType, int $refId, ?int $userId = null, ?string $note = null): void
    {
        DB::transaction(function () use ($productId, $locationId, $qtyDelta, $costPerUnit, $refType, $refId, $userId, $note) {
            $stock = Stock::where('product_id', $productId)
                ->where('location_id', $locationId)
                ->lockForUpdate()
                ->first();

            if (!$stock) {
                $stock = Stock::create([
                    'product_id' => $productId,
                    'location_id' => $locationId,
                    'qty' => '0',
                    'avg_cost' => $costPerUnit ?? '0',
                ]);
                $stock->refresh();
            }

            $newQty = (string) ((float)$stock->qty + (float)$qtyDelta);
            $stock->qty = $newQty;
            $stock->save();

            StockLedger::create([
                'product_id' => $productId,
                'location_id' => $locationId,
                'ref_type' => $refType,
                'ref_id' => $refId,
                'qty_change' => $qtyDelta,
                'balance_after' => $newQty,
                'cost_per_unit_at_time' => $costPerUnit,
                'total_cost_effect' => $costPerUnit !== null ? (string)((float)$costPerUnit * (float)$qtyDelta) : null,
                'user_id' => $userId,
                'note' => $note,
                'created_at' => now(),
            ]);

            event(new StockUpdated(
                productId: $productId,
                locationId: $locationId,
                qty: (float)$qtyDelta,
                avgCost: $costPerUnit !== null ? (float)$costPerUnit : null,
                refType: $refType,
                refId: $refId,
            ));
        });
    }

    public function calculateAverageCost(float $currentQty, float $currentCost, float $addQty, float $addCost): float
    {
        $newQty = $currentQty + $addQty;
        if ($newQty <= 0) {
            return $addCost; // fallback
        }
        return (($currentQty * $currentCost) + ($addQty * $addCost)) / $newQty;
    }

    public function transferStock(int $productId, int $fromLocationId, int $toLocationId, string $qty, int $userId): void
    {
        DB::transaction(function () use ($productId, $fromLocationId, $toLocationId, $qty, $userId) {
            $qtyF = (float)$qty;

            $origin = Stock::where('product_id', $productId)->where('location_id', $fromLocationId)->lockForUpdate()->first();
            if (!$origin || (float)$origin->qty < $qtyF) {
                throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException('Insufficient stock for transfer');
            }
            $originCost = (float)$origin->avg_cost;

            // out from origin
            $this->adjustStockWithLedger($productId, $fromLocationId, (string)(-1*$qtyF), (string)$originCost, 'transfer', 0, $userId, 'Internal transfer out');

            // adjust avg cost at destination first
            $dest = Stock::where('product_id', $productId)->where('location_id', $toLocationId)->lockForUpdate()->first();
            if (!$dest) {
                $dest = Stock::create(['product_id'=>$productId,'location_id'=>$toLocationId,'qty'=>0,'avg_cost'=>$originCost]);
            }
            $newAvg = $this->calculateAverageCost((float)$dest->qty, (float)$dest->avg_cost, $qtyF, $originCost);
            $dest->avg_cost = (string)round($newAvg, 4);
            $dest->save();

            // in to destination
            $this->adjustStockWithLedger($productId, $toLocationId, (string)$qtyF, (string)$originCost, 'transfer', 0, $userId, 'Internal transfer in');
        });
    }
}
