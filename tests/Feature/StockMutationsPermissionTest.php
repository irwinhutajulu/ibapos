<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use App\Models\StockMutation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockMutationsPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionsSeeder::class);
    }

    public function test_user_without_confirm_permission_cannot_confirm()
    {
        $from = Location::factory()->create();
        $to = Location::factory()->create();
        $user = User::factory()->create();
        // user has no roles/permissions

        $p = Product::factory()->create();
        Stock::create(['product_id'=>$p->id,'location_id'=>$from->id,'qty'=>'5','avg_cost'=>'1000']);
        Stock::create(['product_id'=>$p->id,'location_id'=>$to->id,'qty'=>'0','avg_cost'=>'1000']);

        $mut = StockMutation::create([
            'product_id'=>$p->id,'from_location_id'=>$from->id,'to_location_id'=>$to->id,'qty'=>'2.000','date'=>now()->toDateString(),'status'=>'pending','requested_by'=>$user->id
        ]);

        $this->actingAs($user)
            ->postJson(route('stock-mutations.confirm', $mut))
            ->assertStatus(403);
    }

    public function test_user_without_reject_permission_cannot_reject()
    {
        $from = Location::factory()->create();
        $to = Location::factory()->create();
        $user = User::factory()->create();

        $p = Product::factory()->create();
        $mut = StockMutation::create([
            'product_id'=>$p->id,'from_location_id'=>$from->id,'to_location_id'=>$to->id,'qty'=>'1.000','date'=>now()->toDateString(),'status'=>'pending','requested_by'=>$user->id
        ]);

        $this->actingAs($user)
            ->postJson(route('stock-mutations.reject', $mut))
            ->assertStatus(403);
    }

    public function test_user_with_permissions_can_confirm_and_reject()
    {
        $from = Location::factory()->create();
        $to = Location::factory()->create();
        $user = User::factory()->create();
        $user->assignRole('warehouse');

        $p = Product::factory()->create();
        Stock::create(['product_id'=>$p->id,'location_id'=>$from->id,'qty'=>'5','avg_cost'=>'1000']);
        Stock::create(['product_id'=>$p->id,'location_id'=>$to->id,'qty'=>'0','avg_cost'=>'1000']);

        $mut = StockMutation::create([
            'product_id'=>$p->id,'from_location_id'=>$from->id,'to_location_id'=>$to->id,'qty'=>'2.000','date'=>now()->toDateString(),'status'=>'pending','requested_by'=>$user->id
        ]);

        $this->actingAs($user)
            ->postJson(route('stock-mutations.confirm', $mut))
            ->assertOk();

        // create another pending mutation to reject
        $mut2 = StockMutation::create([
            'product_id'=>$p->id,'from_location_id'=>$from->id,'to_location_id'=>$to->id,'qty'=>'1.000','date'=>now()->toDateString(),'status'=>'pending','requested_by'=>$user->id
        ]);

        $this->actingAs($user)
            ->postJson(route('stock-mutations.reject', $mut2))
            ->assertOk();
    }
}
