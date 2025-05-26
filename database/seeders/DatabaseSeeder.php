<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call your roles and permissions seeder
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            ArticleSeeder::class,
        ]);

    }
}

