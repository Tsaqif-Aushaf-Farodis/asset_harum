<?php

namespace App\Http\Controllers;

use App\Http\Requests\Roles\{StoreRoleRequest, UpdateRoleRequest};
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Permission;
use Woo\GridView\DataProviders\EloquentDataProvider;

class RoleAndPermissionController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:role & permission view', only: ['index', 'show']),
            new Middleware('permission:role & permission create', only: ['create', 'store']),
            new Middleware('permission:role & permission edit', only: ['edit', 'store']),
            new Middleware('permission:role & permission delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $query = Role::query();

        $query->orderBy('updated_at', 'desc');

        $dataProvider = new EloquentDataProvider($query);
        $perPage = 15;

        return view('roles.index', compact('dataProvider', 'perPage'))
            ->with('i', ($request->query('page', 1) - 1) * $perPage);
    }

    public function refreshPermission()
    {
        try {
            DB::transaction(function () {
                // Bersihkan cache di config
                Artisan::call('config:clear');

                // Forget cached roles and permissions
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

                // Simpan permission yang sudah ada sebelumnya
                $existingPermissions = Permission::pluck('name')->toArray();

                // Cari konfigurasi permission baru
                $newPermissions = collect(config('permission.permissions'))
                    ->flatMap(fn($permission) => $permission['access'])
                    ->toArray();

                // Hapus permission yang tidak ada di konfigurasi baru
                $permissionsToDelete = array_diff($existingPermissions, $newPermissions);

                // Tambahkan permission baru yang belum ada
                $permissionsToAdd = array_diff($newPermissions, $existingPermissions);

                // echo '<pre>';
                // print_r($existingPermissions);
                // print_r($newPermissions);
                // print_r($permissionsToDelete);
                // print_r($permissionsToAdd);
                // echo '</pre>';
                // die;

                // operasi update dan delete permission
                Permission::whereIn('name', $permissionsToDelete)->delete();

                foreach ($permissionsToAdd as $permission) {
                    Permission::create(['name' => $permission]);
                }
            });

            return redirect()->back()->with('success', 'Berhasil memperbarui permission.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui permission.')->withErrors($e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', ' min:2', 'max:30', ' unique:roles,name'],
            'permissions' => ['required']
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->givePermissionTo($request->permissions);

        return to_route('roles.index')->with('success', __('The role was created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {
        $role = Role::with('permissions')->findOrFail($id);

        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $role = Role::with('permissions')->findOrFail($id);

        return view('roles.edit', compact('role'))
        ->with('existingPermissions',  Permission::where('guard_name', 'web')->pluck('name')->toArray());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'min:2', 'max:30', 'unique:roles,name,' . $id],
            'permissions' => ['required']
        ]);

        $role = Role::findOrFail($id);
        $role->update(['name' => $request->name, 'updated_at' => now()]);
        $role->syncPermissions($request->permissions);

        return Redirect::route('roles.edit', $id)
            ->with('success', __('The role was updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $role = Role::withCount('users')->findOrFail($id);

        if ($role->users_count < 1) {
            $role->delete();

            return to_route('roles.index')->with('success', __('The role was deleted successfully.'));
        }

        return to_route('roles.index')->with('error', __('Can`t delete role.'));
    }
}
