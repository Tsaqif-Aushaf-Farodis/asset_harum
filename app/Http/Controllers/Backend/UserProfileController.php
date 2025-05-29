<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use \Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Woo\GridView\DataProviders\EloquentDataProvider;

class UserProfileController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:user-profile view', only: ['index', 'show']),
            new Middleware('permission:user-profile create', only: ['create', 'store']),
            new Middleware('permission:user-profile edit', only: ['edit', 'update']),
            new Middleware('permission:user-profile delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $query = UserProfile::query();

        // tambahkan kolom yang mau dikecualikan di pencarian
        $except = ['created_by', 'updated_by'];

        $columns = collect($query->getModel()->getFillable())->filter(function ($item) use ($except) {
            return !in_array($item, $except);
        })->toArray();

        $selectedColumns = $request->get('col', $columns);

        if ($search = $request->get('search')) {
            $query->where(function ($query) use ($search, $selectedColumns) {
                foreach ($selectedColumns as $column) {
                    $query->orWhere($column, 'like', '%' . $search . '%');
                }
            });
        }

        $userProfile = $query->paginate(10);

        if ($request->header('HX-Request')) {
            return view('user-profile.includes.index-table', compact('userProfile'));
        }

        return view('user-profile.index', compact('userProfile', 'columns', 'selectedColumns'));
    }

    public function create(): View
    {
        $userProfile = new UserProfile();

        return view('user-profile.create', compact('userProfile'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'full_name' => 'nullable|string|max:255',
            'avatar_url' => 'nullable|string|max:255',
            'no_telp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string|max:255',
            'jenis_kelamin' => 'nullable|string|in:L,P',
        ]);

        try {
            UserProfile::create($validatedData);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat membuat data.');
        }

        return redirect()->route('user-profile.index')
            ->with('success', 'User Profile berhasil dibuat');
    }

    public function show(UserProfile $userProfile): View
    {
        return view('user-profile.show', compact('userProfile'));
    }

    public function edit(UserProfile $userProfile): View
    {
        return view('user-profile.edit', compact('userProfile'));
    }

    public function update(Request $request, UserProfile $userProfile): RedirectResponse
    {
        $validatedData = $request->validate([
            'full_name' => 'nullable|string|max:255',
            'avatar_url' => 'nullable|string|max:255',
            'no_telp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string|max:255',
            'jenis_kelamin' => 'nullable|string|in:L,P',
        ]);

        try {
            $userProfile->update($validatedData);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->back()
                    ->withInput($request->all())
                    ->with('error', 'Data user profile ini sudah digunakan dan tidak dapat diperbarui.');
            }
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }

        return redirect()->route('user-profile.index')
            ->with('success', 'User Profile berhasil diperbarui');
    }

    public function destroy(UserProfile $userProfile): RedirectResponse
    {
        try {
            $userProfile->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('user-profile.index')
                    ->with('error', 'Data user profile ini sudah digunakan dan tidak dapat dihapus.');
            }
            return redirect()->route('user-profile.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }

        return redirect()->route('user-profile.index')
            ->with('success', 'User Profile berhasil dihapus');
    }
}
