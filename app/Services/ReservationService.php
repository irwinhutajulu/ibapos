<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockReservation;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ReservationService
{
    public function reserve(int $productId, int $locationId, string $qtyReserved, int $saleId, ?int $saleItemId, int $userId, ?string $expiresAt = null): StockReservation
    {
        return DB::transaction(function () use ($productId, $locationId, $qtyReserved, $saleId, $saleItemId, $userId, $expiresAt) {
            $stock = Stock::where('product_id', $productId)
                ->where('location_id', $locationId)
                ->lockForUpdate()
                ->first();

            $onHand = $stock ? (float)$stock->qty : 0.0;
            $activeReserved = (float) StockReservation::where('product_id', $productId)
                ->where('location_id', $locationId)
                ->where('status', 'active')
                ->sum('qty_reserved');
            $available = $onHand - $activeReserved;
            if ($available < (float)$qtyReserved) {
                throw new BadRequestHttpException('Insufficient stock to reserve');
            }

            return StockReservation::create([
                'product_id' => $productId,
                'location_id' => $locationId,
                'sale_id' => $saleId,
                'sale_item_id' => $saleItemId,
                'qty_reserved' => $qtyReserved,
                'status' => 'active',
                'expires_at' => $expiresAt,
                'created_by' => $userId,
            ]);
        });
    }

    public function release(StockReservation $reservation, int $userId): void
    {
        if ($reservation->status !== 'active') {
            return;
        }
        $reservation->update(['status' => 'released', 'released_at' => now(), 'released_by' => $userId]);
    }

    public function consume(StockReservation $reservation, int $userId): void
    {
        if ($reservation->status !== 'active') {
            throw new BadRequestHttpException('Only active reservation can be consumed');
        }
        $reservation->update(['status' => 'consumed', 'consumed_at' => now(), 'consumed_by' => $userId]);
    }
}
