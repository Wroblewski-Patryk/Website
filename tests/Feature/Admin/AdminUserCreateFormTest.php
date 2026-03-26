<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AdminUserCreateFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_create_user_form_with_admin_default_role(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.users.create'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Users/Edit')
            ->where('default_roles.0', 'admin')
        );
    }

    public function test_non_admin_with_manage_users_permission_cannot_view_create_user_form(): void
    {
        $editor = User::factory()->editor()->create();
        Permission::firstOrCreate(['name' => 'manage-users']);
        $editor->givePermissionTo('manage-users');

        $response = $this->actingAs($editor)->get(route('admin.users.create'));

        $response->assertForbidden();
    }
}
