<?php

namespace App\Http\Controllers;

use App\Models\MasterLokasi;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use \Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Woo\GridView\DataProviders\EloquentDataProvider;

class MasterLokasiController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:master-lokasi view', only: ['index', 'show']),
            new Middleware('permission:master-lokasi create', only: ['create', 'store']),
            new Middleware('permission:master-lokasi edit', only: ['edit', 'update']),
            new Middleware('permission:master-lokasi delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $query = MasterLokasi::query();

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
        
        $masterLokasi = $query->paginate(10);

        if ($request->header('HX-Request')) {
            return view('master-lokasi.includes.index-table', compact('masterLokasi'));
        }

        return view('master-lokasi.index', compact('masterLokasi', 'columns', 'selectedColumns'));
    }

    public function create(): View
    {
        $masterLokasi = new MasterLokasi();

        return view('master-lokasi.create', compact('masterLokasi'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            	'kode_lokasi' => 'required|string|max:255',
	'nama_lokasi' => 'required|string|max:255',
	'deskripsi_lokasi' => 'nullable|string',
	'alamat_lokasi' => 'nullable|string|max:255',
	'telepon_lokasi' => 'nullable|string|max:255',
        ]);

        try {
            MasterLokasi::create($validatedData);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat membuat data.');
        }

        return redirect()->route('master-lokasi.index')
            ->with('success', 'Master Lokasi berhasil dibuat');
    }

    public function show(MasterLokasi $masterLokasi): View
    {
        return view('master-lokasi.show', compact('masterLokasi'));
    }

    public function edit(MasterLokasi $masterLokasi): View
    {
        return view('master-lokasi.edit', compact('masterLokasi'));
    }

    public function update(Request $request, MasterLokasi $masterLokasi): RedirectResponse
    {
        $validatedData = $request->validate([
            	'kode_lokasi' => 'required|string|max:255',
	'nama_lokasi' => 'required|string|max:255',
	'deskripsi_lokasi' => 'nullable|string',
	'alamat_lokasi' => 'nullable|string|max:255',
	'telepon_lokasi' => 'nullable|string|max:255',
        ]);

        try {
            $masterLokasi->update($validatedData);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->back()
                    ->withInput($request->all())
                    ->with('error', 'Data master lokasi ini sudah digunakan dan tidak dapat diperbarui.');
            }
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }

        return redirect()->route('master-lokasi.index')
            ->with('success', 'Master Lokasi berhasil diperbarui');
    }

    public function destroy(MasterLokasi $masterLokasi): RedirectResponse
    {
        try {
            $masterLokasi->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('master-lokasi.index')
                    ->with('error', 'Data master lokasi ini sudah digunakan dan tidak dapat dihapus.');
            }
            return redirect()->route('master-lokasi.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }

        return redirect()->route('master-lokasi.index')
            ->with('success', 'Master Lokasi berhasil dihapus');
    }
}