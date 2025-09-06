<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use App\Events\SalePosted;
use App\Events\SaleVoided;
use App\Events\StockUpdated;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SalesPostingService
{
    public function validateForPosting(Sale $sale): void
    {
        if ($sale->status !== 'draft') {
            throw new BadRequestHttpException('Sale is not in draft.');
        }
        foreach ($sale->items as $item) {
            $sourceLocationId = $item->source_location_id ?? $sale->location_id;
            $onHand = (float) (\App\Models\Stock::where('product_id',$item->product_id)->where('location_id',$sourceLocationId)->value('qty') ?? 0);
            if ($onHand < (float)$item->qty) {
                throw new BadRequestHttpException('Insufficient stock for product ID '.$item->product_id);
            }
        }
    }

    public function calculateCOGS(Sale $sale): float
    {
        $total = 0.0;
        foreach ($sale->items as $item) {
            $loc = $item->source_location_id ?? $sale->location_id;
            $avg = (float) (\App\Models\Stock::where('product_id',$item->product_id)->where('location_id',$loc)->value('avg_cost') ?? 0);
            $total += $avg * (float)$item->qty;
        }
        return round($total, 2);
    }

    public function syncPayments(Sale $sale): void
    {
        $sum = (float) $sale->payments()->sum('amount');
        $change = max(0, $sum - (float)$sale->total);
        $sale->payment = (string) $sum;
        $sale->change = (string) $change;
        $sale->save();
    }
    public function post(Sale $sale, int $userId): void
    {
    $this->validateForPosting($sale);

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

                event(new StockUpdated(
                    productId: (int)$item->product_id,
                    locationId: (int)$sourceLocationId,
                    qty: (float)$qtyDelta,
                    avgCost: $stock?->avg_cost ? (float)$stock->avg_cost : null,
                    refType: 'sale',
                    refId: (int)$sale->id,
                ));
            }

            $sale->update([
                'status' => 'posted',
                'posted_at' => now(),
                'posted_by' => $userId,
            ]);
            $this->syncPayments($sale);

            event(new SalePosted($sale->fresh(['items', 'payments'])));
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

                event(new StockUpdated(
                    productId: (int)$item->product_id,
                    locationId: (int)$sourceLocationId,
                    qty: (float)$item->qty,
                    avgCost: $avgCost !== null ? (float)$avgCost : null,
                    refType: 'sale_void',
                    refId: (int)$sale->id,
                ));
            }

            $sale->update([
                'status' => 'void',
                'voided_at' => now(),
                'voided_by' => $userId,
            ]);

            event(new SaleVoided($sale));
        });
    }
}
