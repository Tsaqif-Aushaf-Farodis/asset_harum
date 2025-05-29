<?php

namespace App\Http\Controllers;

use App\Models\MasterBarang;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use \Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Woo\GridView\DataProviders\EloquentDataProvider;

class MasterBarangController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:master-barang view', only: ['index', 'show']),
            new Middleware('permission:master-barang create', only: ['create', 'store']),
            new Middleware('permission:master-barang edit', only: ['edit', 'update']),
            new Middleware('permission:master-barang delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $query = MasterBarang::query();

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
        
        $masterBarang = $query->paginate(10);

        if ($request->header('HX-Request')) {
            return view('master-barang.includes.index-table', compact('masterBarang'));
        }

        return view('master-barang.index', compact('masterBarang', 'columns', 'selectedColumns'));
    }

    public function create(): View
    {
        $masterBarang = new MasterBarang();

        return view('master-barang.create', compact('masterBarang'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            	'kode_barang' => 'required|string|max:255',
	'nama_barang' => 'required|string|max:255',
	'deskripsi_barang' => 'nullable|string',
	'kategori_barang_id' => 'required|integer',
        ]);

        try {
            MasterBarang::create($validatedData);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat membuat data.');
        }

        return redirect()->route('master-barang.index')
            ->with('success', 'Master Barang berhasil dibuat');
    }

    public function show(MasterBarang $masterBarang): View
    {
        return view('master-barang.show', compact('masterBarang'));
    }

    public function edit(MasterBarang $masterBarang): View
    {
        return view('master-barang.edit', compact('masterBarang'));
    }

    public function update(Request $request, MasterBarang $masterBarang): RedirectResponse
    {
        $validatedData = $request->validate([
            	'kode_barang' => 'required|string|max:255',
	'nama_barang' => 'required|string|max:255',
	'deskripsi_barang' => 'nullable|string',
	'kategori_barang_id' => 'required|integer',
        ]);

        try {
            $masterBarang->update($validatedData);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->back()
                    ->withInput($request->all())
                    ->with('error', 'Data master barang ini sudah digunakan dan tidak dapat diperbarui.');
            }
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }

        return redirect()->route('master-barang.index')
            ->with('success', 'Master Barang berhasil diperbarui');
    }

    public function destroy(MasterBarang $masterBarang): RedirectResponse
    {
        try {
            $masterBarang->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('master-barang.index')
                    ->with('error', 'Data master barang ini sudah digunakan dan tidak dapat dihapus.');
            }
            return redirect()->route('master-barang.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }

        return redirect()->route('master-barang.index')
            ->with('success', 'Master Barang berhasil dihapus');
    }
}