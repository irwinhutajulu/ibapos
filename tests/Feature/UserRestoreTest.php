<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserRestoreTest extends TestCase
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

    public function test_restore_endpoint_untrashes_user_and_returns_success()
    {
        // Create and soft-delete a user
        $target = User::factory()->create();
        $target->delete();

        // Ensure user is soft deleted
        $this->assertSoftDeleted('users', ['id' => $target->id]);

        // Act as admin and call restore endpoint
        $response = $this->actingAs($this->adminUser)
            ->post("/admin/users/{$target->id}/restore");

    // Controller may return JSON for AJAX or redirect back; in web flow we expect redirect to users index
    $response->assertRedirect('/admin/users');

        // The user should now be restored (not soft deleted)
        $this->assertDatabaseHas('users', ['id' => $target->id, 'deleted_at' => null]);
    }
}
