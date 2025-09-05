<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationScopeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionsSeeder::class);
    }

    public function test_sales_index_returns_only_active_location_records(): void
    {
        $a = Location::factory()->create();
        $b = Location::factory()->create();
    $user = User::factory()->create();
    // Use a non-super-admin role so global location scope applies
    $user->assignRole('manager');
        $user->locations()->attach([$a->id, $b->id]);

        Sale::create(['invoice_no'=>'A1','date'=>now(),'user_id'=>$user->id,'location_id'=>$a->id,'status'=>'draft']);
        Sale::create(['invoice_no'=>'B1','date'=>now(),'user_id'=>$user->id,'location_id'=>$b->id,'status'=>'draft']);

        session(['active_location_id' => $a->id]);
        $this->actingAs($user);

        $res = $this->get('/sales');
        $res->assertOk();
        $data = $res->json('data');
        $this->assertNotEmpty($data);
        $this->assertCount(1, $data);
        $this->assertEquals('A1', $data[0]['invoice_no']);
    }
}
