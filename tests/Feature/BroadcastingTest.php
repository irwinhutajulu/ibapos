<?php

namespace Tests\Feature;

use App\Events\SalePosted;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class BroadcastingTest extends TestCase
{
    use RefreshDatabase;

    public function test_sale_posted_event_broadcasts_on_location_channel(): void
    {
    Config::set('broadcasting.default', 'log');
    $sale = new Sale(['location_id' => 123, 'status' => 'draft']);

        // Event channel formatting
        $event = new SalePosted($sale);
        $channels = $event->broadcastOn();
        $this->assertIsArray($channels);
        $this->assertInstanceOf(PrivateChannel::class, $channels[0]);
    $this->assertSame('private-location.123', $channels[0]->name);
        $this->assertSame('sale.posted', $event->broadcastAs());
    }
}
