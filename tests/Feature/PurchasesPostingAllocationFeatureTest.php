<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchasesPostingAllocationFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_posting_with_loading_and_unloading_adjusts_avg_cost()
    {
        $loc = Location::factory()->create();
    $user = User::factory()->create();
    $this->seed(\Database\Seeders\PermissionsSeeder::class);
    $user->assignRole('super-admin');
        $user->locations()->attach($loc->id);
        session(['active_location_id' => $loc->id]);

        $product = Product::factory()->create();
        // existing stock: 10 units at avg cost 1000
        $stock = Stock::create(['product_id' => $product->id, 'location_id' => $loc->id, 'qty' => '10', 'avg_cost' => '1000']);

        $supplier = \App\Models\Supplier::factory()->create();
        // Create purchase with one item: 10 units @ 2000 subtotal 20000
        $purchase = Purchase::create(['invoice_no' => 'PA1', 'date' => now(), 'user_id' => $user->id, 'location_id' => $loc->id, 'supplier_id' => $supplier->id, 'status' => 'received', 'freight_cost' => 10.00, 'loading_cost' => 5.00, 'unloading_cost' => 5.00]);
        PurchaseItem::create(['purchase_id' => $purchase->id, 'product_id' => $product->id, 'qty' => '10', 'price' => '2000', 'subtotal' => '20000']);

        // Post the purchase via service (controller would do same)
        app(\App\Services\PurchasePostingService::class)->post($purchase, $user->id);

        $stock->refresh();

        // New qty should be 20
        $this->assertEquals(20.0, (float)$stock->qty);

        // Calculate expected avg cost:
        // existing value = 10 * 1000 = 10000
        // incoming unit cost = 2000
        // total extra = 10+5+5 = 20 ; allocation per item = 20 / qty(10) = 2 per unit
        // effective unit cost = 2000 + 2 = 2002
        // new avg = ((10*1000) + (10*2002)) / 20 = (10000 + 20020) / 20 = 30020/20 = 1501.0

        $this->assertEqualsWithDelta(1501.0, (float)$stock->avg_cost, 0.1);
        $this->assertDatabaseHas('stock_ledger', ['ref_type' => 'purchase', 'ref_id' => $purchase->id, 'product_id' => $product->id]);
    }
}
