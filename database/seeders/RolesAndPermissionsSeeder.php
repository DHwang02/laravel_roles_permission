<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        // Create permissions
        $permissions = [
            'View_Permissions',
            'Create_Permissions',
            'Edit_Permissions',
            'Delete_Permissions',
            'View_Roles',
            'Create_Roles',
            'Edit_Roles',
            'Delete_Roles',
            'View_Articles',
            'Create_Articles',
            'Edit_Articles',
            'Delete_Articles',
            'View_Users',
            'Create_Users',
            'Edit_Users',
            'Delete_Users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        
        // Create roles
        $admin = Role::firstOrCreate(['name' => 'admin','guard_name' => 'sanctum']);
        $editor = Role::firstOrCreate(['name' => 'editor','guard_name' => 'sanctum']);
        $user = Role::firstOrCreate(['name' => 'user','guard_name' => 'sanctum']);

        // Assign permissions to roles  
        $admin->givePermissionTo(Permission::all()); // Admin has all permissions
        $editor->givePermissionTo(['View_Articles', 'Create_Articles', 'Edit_Articles']);
        $user->givePermissionTo(['View_Articles']);

        // Optionally, you can create a default user with a specific role:
        $adminUser = \App\Models\User::firstOrCreate(
            ['email' => 'admin@example.com'],
                [
                         'name' => 'Admin',
                         'password' => Hash::make('password'),
                        ]
            );

        $editorUser = \App\Models\User::firstOrCreate(
            ['email' => 'editor@example.com'],
                [
                         'name' => 'Editor',
                         'password' => Hash::make('password'),
                        ]
            );
        
        $normalUser = \App\Models\User::firstOrCreate(
            ['email' => 'user@example.com'],
                [
                         'name' => 'User',
                         'password' => Hash::make('password'),
                        ]
            );
            

        $adminUser->assignRole('admin');
        $editorUser->assignRole('editor');
        $normalUser->assignRole('user');
    }
}
