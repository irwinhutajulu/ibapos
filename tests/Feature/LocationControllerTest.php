<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Location;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class LocationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user with proper permissions
        $this->adminUser = User::factory()->create();
        
        // Create role and permission
        $adminRole = Role::create(['name' => 'admin']);
        $permission = Permission::create(['name' => 'admin.locations']);
        $adminRole->givePermissionTo($permission);
        $this->adminUser->assignRole($adminRole);
    }

    public function test_can_view_locations_index(): void
    {
        // Create some test locations
        Location::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser)
            ->get('/locations');

        $response->assertStatus(200);
        $response->assertSee('Locations');
        $response->assertSee('Add Location');
    }

    public function test_can_create_location(): void
    {
        $users = User::factory()->count(2)->create();

        $locationData = [
            'name' => 'Test Location',
            'address' => 'Test Address 123',
            'user_ids' => $users->pluck('id')->toArray()
        ];

        $response = $this->actingAs($this->adminUser)
            ->post('/locations', $locationData);

        $response->assertRedirect('/locations');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('locations', [
            'name' => 'Test Location',
            'address' => 'Test Address 123'
        ]);

        // Check user assignment
        $location = Location::where('name', 'Test Location')->first();
        $this->assertEquals(2, $location->users()->count());
    }

    public function test_can_update_location(): void
    {
        $location = Location::factory()->create([
            'name' => 'Old Name',
            'address' => 'Old Address'
        ]);

        $user = User::factory()->create();

        $updateData = [
            'name' => 'New Name',
            'address' => 'New Address',
            'user_ids' => [$user->id]
        ];

        $response = $this->actingAs($this->adminUser)
            ->put("/locations/{$location->id}", $updateData);

        $response->assertRedirect('/locations');
        $response->assertSessionHas('success');

        $location->refresh();
        $this->assertEquals('New Name', $location->name);
        $this->assertEquals('New Address', $location->address);
        $this->assertEquals(1, $location->users()->count());
    }

    public function test_can_delete_location(): void
    {
        $location = Location::factory()->create();

        $response = $this->actingAs($this->adminUser)
            ->delete("/locations/{$location->id}");

        $response->assertRedirect('/locations');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('locations', [
            'id' => $location->id
        ]);
    }

    public function test_cannot_delete_location_with_users(): void
    {
        $location = Location::factory()->create();
        $user = User::factory()->create();
        $location->users()->attach($user);

        $response = $this->actingAs($this->adminUser)
            ->delete("/locations/{$location->id}");

        $response->assertRedirect('/locations');
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('locations', [
            'id' => $location->id
        ]);
    }

    public function test_api_returns_locations(): void
    {
        Location::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser)
            ->get('/api/admin/locations');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['id', 'name', 'address']
            ]
        ]);
    }

    public function test_unauthorized_user_cannot_access(): void
    {
        $user = User::factory()->create(); // No permissions

        $response = $this->actingAs($user)
            ->get('/locations');

        $response->assertStatus(403);
    }

    public function test_location_name_must_be_unique(): void
    {
        Location::factory()->create(['name' => 'Existing Location']);

        $locationData = [
            'name' => 'Existing Location',
            'address' => 'Test Address'
        ];

        $response = $this->actingAs($this->adminUser)
            ->post('/locations', $locationData);

        $response->assertSessionHasErrors('name');
    }
}
