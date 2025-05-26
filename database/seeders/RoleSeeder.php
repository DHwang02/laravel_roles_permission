<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run()
    {
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
