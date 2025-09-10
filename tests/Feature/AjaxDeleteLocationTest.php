<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AjaxDeleteLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_ajax_delete_location_returns_json_and_deletes()
    {
        // Arrange: create an admin user and a location
        $user = User::factory()->create();
        // Ensure permission exists (test DB may not have seeder run)
        if (!\Spatie\Permission\Models\Permission::where('name', 'admin.locations')->exists()) {
            \Spatie\Permission\Models\Permission::create(['name' => 'admin.locations']);
        }
        $user->givePermissionTo('admin.locations');

        $location = Location::factory()->create();

        // Act: send DELETE with Accept: application/json
        $response = $this->actingAs($user)
            ->deleteJson(route('locations.destroy', $location));

        // Assert: JSON success and location deleted
        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseMissing('locations', ['id' => $location->id]);
    }
}
