<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Clear permission cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // List of permissions with display names
        $permissions = [
            'View Permissions',
            'Create Permissions',
            'Edit Permissions',
            'Delete Permissions',
            'View Roles',
            'Create Roles',
            'Edit Roles',
            'Delete Roles',
            'View Articles',
            'Create Articles',
            'Edit Articles',
            'Delete Articles',
            'View Users',
            'Create Users',
            'Edit Users',
            'Delete Users',
        ];

        // Create permissions
        foreach ($permissions as $displayName) {
            $name = Str::slug($displayName, '-');
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'sanctum'],
                ['display_name' => $displayName]
            );
        }

        // Create roles
        $admin  = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'sanctum']);
        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'sanctum']);
        $user   = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'sanctum']);

        // Assign permissions to roles
        $admin->givePermissionTo(Permission::all());

        $editor->givePermissionTo([
            'view-articles',
            'create-articles',
            'edit-articles',
        ]);

        $user->givePermissionTo([
            'view-articles',
        ]);

        // Create users and assign roles
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => Hash::make('password')]
        );
        $adminUser->assignRole($admin);

        $editorUser = User::firstOrCreate(
            ['email' => 'editor@example.com'],
            ['name' => 'Editor', 'password' => Hash::make('password')]
        );
        $editorUser->assignRole($editor);

        $normalUser = User::firstOrCreate(
            ['email' => 'user@example.com'],
            ['name' => 'User', 'password' => Hash::make('password')]
        );
        $normalUser->assignRole($user);
    }
}
