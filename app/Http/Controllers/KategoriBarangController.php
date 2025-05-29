<?php

namespace App\Http\Controllers;

use App\Models\KategoriBarang;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use \Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Woo\GridView\DataProviders\EloquentDataProvider;

class KategoriBarangController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:kategori-barang view', only: ['index', 'show']),
            new Middleware('permission:kategori-barang create', only: ['create', 'store']),
            new Middleware('permission:kategori-barang edit', only: ['edit', 'update']),
            new Middleware('permission:kategori-barang delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $query = KategoriBarang::query();

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
        
        $kategoriBarang = $query->paginate(10);

        if ($request->header('HX-Request')) {
            return view('kategori-barang.includes.index-table', compact('kategoriBarang'));
        }

        return view('kategori-barang.index', compact('kategoriBarang', 'columns', 'selectedColumns'));
    }

    public function create(): View
    {
        $kategoriBarang = new KategoriBarang();

        return view('kategori-barang.create', compact('kategoriBarang'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            	'kode_kategori_barang' => 'required|string|max:255',
	'nama_kategori_barang' => 'required|string|max:255',
	'deskripsi_kategori_barang' => 'nullable|string',
	'status_kategori_barang' => 'required|string|in:aktif,nonaktif',
        ]);

        try {
            KategoriBarang::create($validatedData);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat membuat data.');
        }

        return redirect()->route('kategori-barang.index')
            ->with('success', 'Kategori Barang berhasil dibuat');
    }

    public function show(KategoriBarang $kategoriBarang): View
    {
        return view('kategori-barang.show', compact('kategoriBarang'));
    }

    public function edit(KategoriBarang $kategoriBarang): View
    {
        return view('kategori-barang.edit', compact('kategoriBarang'));
    }

    public function update(Request $request, KategoriBarang $kategoriBarang): RedirectResponse
    {
        $validatedData = $request->validate([
            	'kode_kategori_barang' => 'required|string|max:255',
	'nama_kategori_barang' => 'required|string|max:255',
	'deskripsi_kategori_barang' => 'nullable|string',
	'status_kategori_barang' => 'required|string|in:aktif,nonaktif',
        ]);

        try {
            $kategoriBarang->update($validatedData);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->back()
                    ->withInput($request->all())
                    ->with('error', 'Data kategori barang ini sudah digunakan dan tidak dapat diperbarui.');
            }
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }

        return redirect()->route('kategori-barang.index')
            ->with('success', 'Kategori Barang berhasil diperbarui');
    }

    public function destroy(KategoriBarang $kategoriBarang): RedirectResponse
    {
        try {
            $kategoriBarang->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('kategori-barang.index')
                    ->with('error', 'Data kategori barang ini sudah digunakan dan tidak dapat dihapus.');
            }
            return redirect()->route('kategori-barang.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }

        return redirect()->route('kategori-barang.index')
            ->with('success', 'Kategori Barang berhasil dihapus');
    }
}