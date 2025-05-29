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
            User::query()->delete();

            User::firstOrCreate(
                [
                'name' => 'Prabubima',
                'username' => 'prabubima',
                'password' => Hash::make('b15millah')
                ]);
            

            // Bersihkan cache di config
            Artisan::call('config:clear');

            // Forget cached roles and permissions
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            // Create or retrieve the Admin role
            $roleAdmin = Role::firstOrCreate(['name' => 'Admin']);

            // Hapus semua permission sebelum seeding ulang
            Permission::query()->delete();

            foreach (config('permission.permissions') as $permission) {
                foreach ($permission['access'] as $access) {
                    Permission::create(['name' => $access]);
                }
            }

            // Assign the new permissions to the Admin role
            $roleAdmin->givePermissionTo(Permission::all());

            $userAdmin = User::query()->where(['username' => 'admin'])->first();
            $userAdmin->assignRole('admin');

            // Role untuk admin unit
            $roleAdminSekolah = Role::firstOrCreate(['name' => 'Peserta Tryout']);

            $roleAdminSekolah->givePermissionTo(Permission::all());
            // $userAdminSekolah = User::query()->where(['username' => 'pamungkas'])->first();
            // $userAdminSekolah->assignRole('peserta tryout');
        });
    }
}
