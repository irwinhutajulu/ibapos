<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchasesPostingJsonTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionsSeeder::class);
    }

    public function test_posting_actions_return_json_when_accept_header_is_json(): void
    {
        $loc = Location::factory()->create();
        $user = User::factory()->create();
        $user->assignRole('super-admin');
        $user->locations()->attach($loc->id);
        session(['active_location_id' => $loc->id]);

        $supplier = Supplier::factory()->create();
        $purchase = Purchase::create(['invoice_no'=>'P1','date'=>now(),'user_id'=>$user->id,'location_id'=>$loc->id,'supplier_id'=>$supplier->id,'status'=>'received']);

        $response = $this->actingAs($user)->post('/purchases/'.$purchase->id.'/post', [], ['Accept' => 'application/json']);
        $response->assertStatus(200);
        $response->assertJsonStructure(['status','purchase']);
    }
}
