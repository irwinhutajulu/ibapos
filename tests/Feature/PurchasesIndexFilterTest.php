<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchasesIndexFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionsSeeder::class);
    }

    public function test_purchase_index_filters_by_status_and_date(): void
    {
        $loc = Location::factory()->create();
        $user = User::factory()->create();
        $user->assignRole('super-admin');
        $user->locations()->attach($loc->id);
        session(['active_location_id' => $loc->id]);

        $supplier = Supplier::factory()->create();
    // make P1 clearly older than the filter 'from' (yesterday) to avoid timezone edge cases
    $p1 = Purchase::create(['invoice_no'=>'P1','date'=>now()->subDays(5),'user_id'=>$user->id,'location_id'=>$loc->id,'supplier_id'=>$supplier->id,'status'=>'draft']);
        $p2 = Purchase::create(['invoice_no'=>'P2','date'=>now(),'user_id'=>$user->id,'location_id'=>$loc->id,'supplier_id'=>$supplier->id,'status'=>'posted']);

        $response = $this->actingAs($user)->get('/purchases?status=posted&from='.now()->subDay()->format('Y-m-d'));
        $response->assertStatus(200);
        $response->assertSee('P2');
        $response->assertDontSee('P1');
    }
}
