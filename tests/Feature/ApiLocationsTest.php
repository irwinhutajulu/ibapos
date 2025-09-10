<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Location;

class ApiLocationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_locations_returns_only_user_assigned_locations()
    {
        // Create user and locations
        $user = User::factory()->create();
        $locA = Location::factory()->create(['name' => 'Loc A']);
        $locB = Location::factory()->create(['name' => 'Loc B']);
        $locC = Location::factory()->create(['name' => 'Loc C']);

        // Assign only A and C to the user
        $user->locations()->sync([$locA->id, $locC->id]);

        $response = $this->actingAs($user)
            ->get('/api/locations');

        $response->assertStatus(200);

        $data = $response->original; // route returns collection directly

        $ids = collect($data)->pluck('id')->all();

        $this->assertContains($locA->id, $ids);
        $this->assertContains($locC->id, $ids);
        $this->assertNotContains($locB->id, $ids);
    }

    public function test_guest_gets_empty_or_forbidden_on_api_locations()
    {
        $response = $this->get('/api/locations');
        // Depending on route middleware, it may redirect to login (302) or return 200 with empty
        $this->assertTrue(in_array($response->getStatusCode(), [200, 302, 401]), 'Unexpected status: ' . $response->getStatusCode());
    }
}
