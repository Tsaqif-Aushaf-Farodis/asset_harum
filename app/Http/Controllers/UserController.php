<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use App\Generators\Services\ImageService;
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use App\Http\Requests\Users\{StoreUserRequest, StoreUserUnitRequest, StoreUserUnitSekolahRequest, UpdateUserRequest, UpdateUserUnitSekolahRequest};
use App\Models\UnitSekolah;
use App\Models\UsersUnitSekolah;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Woo\GridView\DataProviders\EloquentDataProvider;

class UserController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:user view', only: ['index', 'show']),
            new Middleware('permission:user create', only: ['create', 'store']),
            new Middleware('permission:user edit', only: ['edit', 'update']),
            new Middleware('permission:user delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = User::query()->select("users.*");

        if ($request->has('nama')) {
            $query->where('siswa.nama', 'LIKE', '%' . $request->input('nama') . '%'); // pastikan untuk menggunakan nama kolom yang benar
        }

        // Cek jika ada filter role_id
        if (!empty($request->filters['role_id'])) {
            $query->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->where('model_has_roles.role_id', '=', $request->filters['role_id'])
                ->where('model_has_roles.model_type', '=', User::class)
                ->select('users.*')
                ->distinct();
        }

        $query->orderBy('updated_at', 'desc');

        $dataProvider = new EloquentDataProvider($query);
        $perPage = 15;

        return view('users.index', compact('dataProvider', 'perPage'))
            ->with('roles', Role::all()->pluck('name', 'id')->toArray())
            ->with('i', ($request->query('page', 1) - 1) * $perPage);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('users.create')
            ->with('roles', Role::select('id', 'name')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'min:3', 'max:255'],
            'username' => ['required', 'unique:users,username'],
            'role' => ['required', 'exists:roles,id'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $validated['password'] = Hash::make($request->password);

            $user = User::create($validated);

            $role = Role::select('id', 'name')->find($request->role);

            $user->assignRole($role->name);

            return redirect()->route('users.index')
                ->with('success', __('The user was created successfully.'));
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): View
    {
        $user->load('roles:id,name');

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        $user->load('roles:id,name');

        return view('users.edit', compact('user'))
            ->with('roles', Role::select('id', 'name')->get());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'min:3', 'max:255'],
            'username' => ['required', 'unique:users,username,' . $user->id],
            'role' => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        return DB::transaction(function () use ($request, $user, $validated) {
            if (!$request->password) {
                unset($validated['password']);
            } else {
                $validated['password'] = Hash::make($request->password);
            }

            $user->update($validated);

            $role = Role::select('id', 'name')->find($request->role);

            $user->syncRoles($role->name);

            return redirect()->route('users.index')
                ->with('success', __('The user was updated successfully.'));
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            return DB::transaction(function () use ($user) {
                $user->delete();

                return redirect()->route('users.index')
                    ->with('success', __('The user was deleted successfully.'));
            });
        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', __("The user can't be deleted because it's related to another table."));
        }
    }
}
