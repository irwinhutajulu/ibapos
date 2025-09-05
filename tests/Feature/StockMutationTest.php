<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMutation;
use App\Models\User;
use App\Services\MutationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockMutationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionsSeeder::class);
    }

    public function test_confirm_moves_stock_and_writes_ledger(): void
    {
        $from = Location::factory()->create();
        $to = Location::factory()->create();
        $user = User::factory()->create();
        $user->assignRole('super-admin');
        $user->locations()->attach([$from->id, $to->id]);
        session(['active_location_id' => $from->id]);

        $p = Product::factory()->create();
        Stock::create(['product_id'=>$p->id,'location_id'=>$from->id,'qty'=>'5','avg_cost'=>'1000']);
        Stock::create(['product_id'=>$p->id,'location_id'=>$to->id,'qty'=>'2','avg_cost'=>'1200']);

        $mut = StockMutation::create([
            'product_id' => $p->id,
            'from_location_id' => $from->id,
            'to_location_id' => $to->id,
            'qty' => '3',
            'date' => now()->toDateString(),
            'status' => 'pending',
            'requested_by' => $user->id,
        ]);

        app(MutationService::class)->confirm($mut, $user->id);

        $this->assertDatabaseHas('stock_ledger', ['ref_type'=>'mutation','ref_id'=>$mut->id,'location_id'=>$from->id,'qty_change'=>'-3']);
        $this->assertDatabaseHas('stock_ledger', ['ref_type'=>'mutation','ref_id'=>$mut->id,'location_id'=>$to->id,'qty_change'=>'3']);

        $this->assertEquals('2.000', number_format(Stock::where('product_id',$p->id)->where('location_id',$from->id)->value('qty'), 3));
        $this->assertEquals('5.000', number_format(Stock::where('product_id',$p->id)->where('location_id',$to->id)->value('qty'), 3));
    }
}
