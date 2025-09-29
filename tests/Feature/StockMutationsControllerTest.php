<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use App\Models\StockMutation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockMutationsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionsSeeder::class);
    }

    public function test_store_creates_mutation_and_redirects()
    {
        $from = Location::factory()->create();
        $to = Location::factory()->create();
        $user = User::factory()->create();
        $user->assignRole('super-admin');
        $p = Product::factory()->create();

        $this->actingAs($user)
            ->post(route('stock-mutations.store'), [
                'product_id' => $p->id,
                'from_location_id' => $from->id,
                'to_location_id' => $to->id,
                'qty' => '1.000',
            ])
            ->assertRedirect(route('stock-mutations.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('stock_mutations', ['product_id' => $p->id, 'from_location_id' => $from->id, 'to_location_id' => $to->id]);
    }

    public function test_reject_updates_status()
    {
        $from = Location::factory()->create();
        $to = Location::factory()->create();
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        $p = Product::factory()->create();
        $mut = StockMutation::create([
            'product_id'=>$p->id,'from_location_id'=>$from->id,'to_location_id'=>$to->id,'qty'=>'1.000','date'=>now()->toDateString(),'status'=>'pending','requested_by'=>$user->id
        ]);

        $this->actingAs($user)
            ->post(route('stock-mutations.reject', $mut))
            ->assertRedirect();

        $this->assertDatabaseHas('stock_mutations', ['id' => $mut->id, 'status' => 'rejected']);
    }

    public function test_confirm_ajax_returns_json()
    {
        $from = Location::factory()->create();
        $to = Location::factory()->create();
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        $p = Product::factory()->create();
        Stock::create(['product_id'=>$p->id,'location_id'=>$from->id,'qty'=>'5','avg_cost'=>'1000']);
        Stock::create(['product_id'=>$p->id,'location_id'=>$to->id,'qty'=>'1','avg_cost'=>'1200']);

        $mut = StockMutation::create([
            'product_id'=>$p->id,'from_location_id'=>$from->id,'to_location_id'=>$to->id,'qty'=>'2.000','date'=>now()->toDateString(),'status'=>'pending','requested_by'=>$user->id
        ]);

        $this->actingAs($user)
            ->postJson(route('stock-mutations.confirm', $mut))
            ->assertOk()->assertJson(['status' => 'ok']);

        $this->assertDatabaseHas('stock_ledger', ['ref_type'=>'mutation','ref_id'=>$mut->id,'location_id'=>$from->id,'qty_change'=>'-2.000']);
    }
}
