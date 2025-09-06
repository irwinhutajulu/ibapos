<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $productId,
        public int $locationId,
        public float $qty,
        public ?float $avgCost = null,
        public ?string $refType = null,
        public ?int $refId = null,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('location.'.$this->locationId)];
    }

    public function broadcastWith(): array
    {
        return [
            'product_id' => $this->productId,
            'location_id' => $this->locationId,
            'qty' => $this->qty,
            'avg_cost' => $this->avgCost,
            'ref_type' => $this->refType,
            'ref_id' => $this->refId,
        ];
    }

    public function broadcastAs(): string
    {
        return 'stock.updated';
    }
}
