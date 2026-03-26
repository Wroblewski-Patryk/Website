<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AdminUserCreationAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'New Admin User',
            'email' => 'new-admin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'roles' => ['admin'],
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'new-admin@example.com',
        ]);
    }

    public function test_non_admin_with_manage_users_permission_cannot_create_user(): void
    {
        $editor = User::factory()->editor()->create();
        Permission::firstOrCreate(['name' => 'manage-users']);
        $editor->givePermissionTo('manage-users');

        $response = $this->actingAs($editor)->post(route('admin.users.store'), [
            'name' => 'Blocked User',
            'email' => 'blocked-user@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'roles' => ['editor'],
        ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('users', [
            'email' => 'blocked-user@example.com',
        ]);
    }
}
