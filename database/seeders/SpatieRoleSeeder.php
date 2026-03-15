<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class SpatieRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view-admin',
            'manage-settings',
            'manage-users',
            'manage-content',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign created permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        $editorRole = Role::firstOrCreate(['name' => 'editor']);
        $editorRole->syncPermissions(['view-admin', 'manage-content']);

        // Migrate existing users
        User::all()->each(function ($user) {
            if ($user->role === 'admin') {
                $user->assignRole('admin');
            } elseif ($user->role === 'editor') {
                $user->assignRole('editor');
            }
        });
    }
}
