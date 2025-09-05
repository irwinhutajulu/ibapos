<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use App\Models\User;
use App\Services\SalesPostingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesPostingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionsSeeder::class);
    }

    public function test_sale_posting_reduces_stock_and_writes_ledger(): void
    {
        $loc = Location::factory()->create();
        $user = User::factory()->create();
        $user->assignRole('super-admin');
        $user->locations()->attach($loc->id);
        session(['active_location_id' => $loc->id]);

        $p = Product::factory()->create();
        $stock = Stock::create(['product_id'=>$p->id,'location_id'=>$loc->id,'qty'=>'10','avg_cost'=>'1000']);

        $sale = Sale::create(['invoice_no'=>'T1','date'=>now(),'user_id'=>$user->id,'location_id'=>$loc->id,'status'=>'draft']);
        $item = SaleItem::create(['sale_id'=>$sale->id,'product_id'=>$p->id,'qty'=>'2','price'=>'1500','discount'=>'0','subtotal'=>'3000']);

        app(SalesPostingService::class)->post($sale, $user->id);

        $stock->refresh();
        $this->assertEquals('8.000', number_format($stock->qty, 3));
        $this->assertDatabaseHas('stock_ledger', ['ref_type'=>'sale','ref_id'=>$sale->id,'product_id'=>$p->id]);
    }
}
