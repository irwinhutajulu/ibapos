<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesAvailabilityValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_save_sale_when_stock_insufficient(): void
    {
    $this->withoutMiddleware();
        $this->seed();
        $user = User::factory()->create();
        $loc = Location::factory()->create();
        $user->locations()->sync([$loc->id]);
        $user->givePermissionTo(['sales.create']);
        $this->actingAs($user);
        session(['active_location_id' => $loc->id]);

        $product = Product::factory()->create();
        // no stock seeded for this product at this location

        $payload = [
            'invoice_no' => 'TST-001',
            'date' => now()->toDateString(),
            'items' => [
                [
                    'product_id' => $product->id,
                    'qty' => 2,
                    'price' => 1000,
                    'discount' => 0,
                    'subtotal' => 2000,
                ],
            ],
        ];

        $res = $this->postJson(route('sales.store'), $payload);
        $res->assertStatus(422);
        $res->assertJsonStructure(['message','available']);
    }
}
