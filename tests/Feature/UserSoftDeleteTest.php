<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Sale;
use App\Models\Location;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSoftDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create();
        $adminRole = Role::create(['name' => 'admin']);
        $permission = Permission::create(['name' => 'admin.users']);
        $adminRole->givePermissionTo($permission);
        $this->adminUser->assignRole($adminRole);
    }

    public function test_soft_delete_user_preserves_related_sales(): void
    {
        // Create a target user and a related sale
        $target = User::factory()->create();

        // Create a location for the sale
        $location = Location::factory()->create();

        // Create a sale pointing to this user via model create (no factory available)
        $sale = Sale::create([
            'invoice_no' => 'T-TEST',
            'date' => now(),
            'user_id' => $target->id,
            'location_id' => $location->id,
            'total' => 0,
            'status' => 'draft'
        ]);

        // Perform delete as admin
        $response = $this->actingAs($this->adminUser)
            ->delete("/admin/users/{$target->id}");

        // Expect redirect back to users index with success or error handled by controller
        $response->assertRedirect('/admin/users');

        // The user should be soft-deleted (deleted_at set)
        $this->assertSoftDeleted('users', ['id' => $target->id]);

        // The sale should still exist in the database
        $this->assertDatabaseHas('sales', ['id' => $sale->id, 'user_id' => $target->id]);
    }
}
