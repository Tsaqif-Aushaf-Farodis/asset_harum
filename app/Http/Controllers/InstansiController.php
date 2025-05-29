<?php

namespace App\Http\Controllers;

use App\Models\Instansi;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use \Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Woo\GridView\DataProviders\EloquentDataProvider;

class InstansiController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:instansi view', only: ['index', 'show']),
            new Middleware('permission:instansi create', only: ['create', 'store']),
            new Middleware('permission:instansi edit', only: ['edit', 'update']),
            new Middleware('permission:instansi delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $query = Instansi::query();

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
        
        $instansi = $query->paginate(10);

        if ($request->header('HX-Request')) {
            return view('instansi.includes.index-table', compact('instansi'));
        }

        return view('instansi.index', compact('instansi', 'columns', 'selectedColumns'));
    }

    public function create(): View
    {
        $instansi = new Instansi();

        return view('instansi.create', compact('instansi'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            	'nama_instansi' => 'required|string|max:255',
	'alamat' => 'nullable|string|max:255',
	'telepon' => 'nullable|string|max:255',
	'email' => 'nullable|string|max:255',
	'logo' => 'nullable|string|max:255',
	'deskripsi' => 'nullable|string|max:255',
        ]);

        try {
            Instansi::create($validatedData);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat membuat data.');
        }

        return redirect()->route('instansi.index')
            ->with('success', 'Instansi berhasil dibuat');
    }

    public function show(Instansi $instansi): View
    {
        return view('instansi.show', compact('instansi'));
    }

    public function edit(Instansi $instansi): View
    {
        return view('instansi.edit', compact('instansi'));
    }

    public function update(Request $request, Instansi $instansi): RedirectResponse
    {
        $validatedData = $request->validate([
            	'nama_instansi' => 'required|string|max:255',
	'alamat' => 'nullable|string|max:255',
	'telepon' => 'nullable|string|max:255',
	'email' => 'nullable|string|max:255',
	'logo' => 'nullable|string|max:255',
	'deskripsi' => 'nullable|string|max:255',
        ]);

        try {
            $instansi->update($validatedData);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->back()
                    ->withInput($request->all())
                    ->with('error', 'Data instansi ini sudah digunakan dan tidak dapat diperbarui.');
            }
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }

        return redirect()->route('instansi.index')
            ->with('success', 'Instansi berhasil diperbarui');
    }

    public function destroy(Instansi $instansi): RedirectResponse
    {
        try {
            $instansi->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('instansi.index')
                    ->with('error', 'Data instansi ini sudah digunakan dan tidak dapat dihapus.');
            }
            return redirect()->route('instansi.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }

        return redirect()->route('instansi.index')
            ->with('success', 'Instansi berhasil dihapus');
    }
}