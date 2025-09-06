<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\Stock;
use App\Models\User;
use App\Services\AdjustmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockAdjustmentPostingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionsSeeder::class);
    }

    public function test_stock_adjustment_posting_updates_stock_and_writes_ledger(): void
    {
        $loc = Location::factory()->create();
        $user = User::factory()->create();
        $user->assignRole('super-admin');
        $user->locations()->attach($loc->id);
        session(['active_location_id' => $loc->id]);

        $p = Product::factory()->create();
        $stock = Stock::create(['product_id'=>$p->id,'location_id'=>$loc->id,'qty'=>'10','avg_cost'=>'1000']);

        $adjustment = StockAdjustment::create(['code'=>'ADJ1','date'=>now(),'location_id'=>$loc->id,'user_id'=>$user->id,'status'=>'draft']);
        StockAdjustmentItem::create(['stock_adjustment_id'=>$adjustment->id,'product_id'=>$p->id,'qty_change'=>'5','unit_cost'=>'1200','note'=>'Penyesuaian']);

        app(AdjustmentService::class)->post($adjustment, $user->id);

        $stock->refresh();
        $this->assertEquals(15.0, (float)$stock->qty);
        $this->assertDatabaseHas('stock_ledger', ['ref_type'=>'adjustment','ref_id'=>$adjustment->id,'product_id'=>$p->id]);
    }
}
