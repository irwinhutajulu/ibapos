<?php

namespace App\Events;

use App\Models\Sale;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SalePosted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Sale $sale)
    {
        // Minimize payload
        $this->sale->setHidden(['items', 'payments']);
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('location.'.$this->sale->location_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->sale->id,
            'invoice' => $this->sale->invoice_no ?? null,
            'total' => (float) $this->sale->total,
            'status' => $this->sale->status,
            'location_id' => $this->sale->location_id,
            'posted_at' => optional($this->sale->posted_at)->toISOString(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'sale.posted';
    }
}
