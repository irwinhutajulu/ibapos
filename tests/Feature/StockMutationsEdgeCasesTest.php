<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use App\Models\StockMutation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class StockMutationsEdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionsSeeder::class);
    }

    public function test_store_validates_from_to_different()
    {
        $loc = Location::factory()->create();
        $user = User::factory()->create();
        $user->assignRole('super-admin');
        $p = Product::factory()->create();

        $this->actingAs($user)
            ->post(route('stock-mutations.store'), [
                'product_id' => $p->id,
                'from_location_id' => $loc->id,
                'to_location_id' => $loc->id,
                'qty' => '1.000',
            ])
            ->assertSessionHasErrors('to_location_id');
    }

    public function test_confirm_fails_when_insufficient_origin_stock()
    {
        $from = Location::factory()->create();
        $to = Location::factory()->create();
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        $p = Product::factory()->create();
        // origin has 0 stock
        Stock::create(['product_id'=>$p->id,'location_id'=>$from->id,'qty'=>'0','avg_cost'=>'1000']);

        $mut = StockMutation::create([
            'product_id'=>$p->id,'from_location_id'=>$from->id,'to_location_id'=>$to->id,'qty'=>'2.000','date'=>now()->toDateString(),'status'=>'pending','requested_by'=>$user->id
        ]);

        $this->actingAs($user)
            ->postJson(route('stock-mutations.confirm', $mut))
            ->assertStatus(400)
            ->assertJsonFragment(['message' => 'Insufficient origin stock']);
    }

    public function test_confirm_reject_non_pending_fails()
    {
        $from = Location::factory()->create();
        $to = Location::factory()->create();
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        $p = Product::factory()->create();
        $mut = StockMutation::create([
            'product_id'=>$p->id,'from_location_id'=>$from->id,'to_location_id'=>$to->id,'qty'=>'1.000','date'=>now()->toDateString(),'status'=>'confirmed','requested_by'=>$user->id
        ]);

        $this->actingAs($user)
            ->postJson(route('stock-mutations.confirm', $mut))
            ->assertStatus(400)
            ->assertJsonFragment(['message' => 'Only pending mutation can be confirmed.']);
    }

    public function test_store_requires_authentication()
    {
        $from = Location::factory()->create();
        $to = Location::factory()->create();
        $p = Product::factory()->create();

        $this->post(route('stock-mutations.store'), [
            'product_id' => $p->id,
            'from_location_id' => $from->id,
            'to_location_id' => $to->id,
            'qty' => '1.000',
        ])->assertRedirect('/login');
    }
}
