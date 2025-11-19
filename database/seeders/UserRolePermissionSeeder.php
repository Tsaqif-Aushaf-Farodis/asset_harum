<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * `php artisan db:seed --class=UserRolePermissionSeeder`
     */
   public function run(): void
    {
        DB::transaction(function () {
            User::firstOrCreate(
                ['username' => 'admin'],
                [
                    'name' => 'Admin',
                    'password' => Hash::make('b15millah')
                ]
            );

            User::firstOrCreate(
                ['username' => 'prabubima'],
                [
                    'name' => 'Prabubima',
                    'password' => Hash::make('B15millah!')
                ]
            );
        });
}

}
