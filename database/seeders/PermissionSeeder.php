<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
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
    }
}
