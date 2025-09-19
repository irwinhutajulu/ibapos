<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Stock;
use App\Models\User;
use App\Services\PurchasePostingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchasesPostingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionsSeeder::class);
    }

    public function test_purchase_posting_increases_stock_and_updates_avg_cost(): void
    {
        $loc = Location::factory()->create();
        $user = User::factory()->create();
        $user->assignRole('super-admin');
        $user->locations()->attach($loc->id);
        session(['active_location_id' => $loc->id]);

        $p = Product::factory()->create();
        $stock = Stock::create(['product_id'=>$p->id,'location_id'=>$loc->id,'qty'=>'10','avg_cost'=>'1000']);

    $supplier = \App\Models\Supplier::factory()->create();
    $purchase = Purchase::create(['invoice_no'=>'P1','date'=>now(),'user_id'=>$user->id,'location_id'=>$loc->id,'supplier_id'=>$supplier->id,'status'=>'received','freight_cost'=>0,'loading_cost'=>0,'unloading_cost'=>0]);
        PurchaseItem::create(['purchase_id'=>$purchase->id,'product_id'=>$p->id,'qty'=>'10','price'=>'2000','subtotal'=>'20000']);

        app(PurchasePostingService::class)->post($purchase, $user->id);

        $stock->refresh();
    $this->assertEquals(20.0, (float)$stock->qty);
    $this->assertEquals(1500.0000, (float)$stock->avg_cost);
        $this->assertDatabaseHas('stock_ledger', ['ref_type'=>'purchase','ref_id'=>$purchase->id,'product_id'=>$p->id]);
    }
}
