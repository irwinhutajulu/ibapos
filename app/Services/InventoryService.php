<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockLedger;
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
        });
    }
}
