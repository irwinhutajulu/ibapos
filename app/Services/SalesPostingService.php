<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SalesPostingService
{
    public function post(Sale $sale, int $userId): void
    {
        if ($sale->status !== 'draft') {
            throw new BadRequestHttpException('Sale is not in draft.');
        }

        DB::transaction(function () use ($sale, $userId) {
            foreach ($sale->items as $item) {
                $sourceLocationId = $item->source_location_id ?? $sale->location_id;

                $stock = Stock::where('product_id', $item->product_id)
                    ->where('location_id', $sourceLocationId)
                    ->lockForUpdate()
                    ->first();

                $avgCost = $stock?->avg_cost ?? '0';
                $qtyDelta = (string)(-(float)$item->qty);

                app(InventoryService::class)->adjustStockWithLedger(
                    productId: $item->product_id,
                    locationId: $sourceLocationId,
                    qtyDelta: $qtyDelta,
                    costPerUnit: $avgCost,
                    refType: 'sale',
                    refId: $sale->id,
                    userId: $userId,
                    note: 'Sale posting'
                );
            }

            $sale->update([
                'status' => 'posted',
                'posted_at' => now(),
                'posted_by' => $userId,
            ]);
        });
    }

    public function void(Sale $sale, int $userId): void
    {
        if ($sale->status !== 'posted') {
            throw new BadRequestHttpException('Only posted sale can be voided.');
        }

        DB::transaction(function () use ($sale, $userId) {
            foreach ($sale->items as $item) {
                $sourceLocationId = $item->source_location_id ?? $sale->location_id;
                $avgCost = optional(
                    Stock::where('product_id', $item->product_id)->where('location_id', $sourceLocationId)->lockForUpdate()->first()
                )->avg_cost;

                app(InventoryService::class)->adjustStockWithLedger(
                    productId: $item->product_id,
                    locationId: $sourceLocationId,
                    qtyDelta: (string)$item->qty,
                    costPerUnit: $avgCost,
                    refType: 'sale_void',
                    refId: $sale->id,
                    userId: $userId,
                    note: 'Sale void reversal'
                );
            }

            $sale->update([
                'status' => 'void',
                'voided_at' => now(),
                'voided_by' => $userId,
            ]);
        });
    }
}
