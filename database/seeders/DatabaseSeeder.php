<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User, Role & Permission
        $this->call(UserRolePermissionSeeder::class);
        $this->call(PermissionSeeder::class);
        
        // Master Data
        $this->call(MasterDataSeeder::class);
    }
}
