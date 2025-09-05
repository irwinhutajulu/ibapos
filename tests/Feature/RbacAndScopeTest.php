<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacAndScopeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionsSeeder::class);
    }

    public function test_sales_route_requires_permission(): void
    {
        $loc = Location::factory()->create();
        $user = User::factory()->create();
        $user->locations()->attach($loc->id);
        session(['active_location_id' => $loc->id]);

        $this->actingAs($user);
        $resp = $this->get('/sales');
        $resp->assertStatus(403);
    }
}
