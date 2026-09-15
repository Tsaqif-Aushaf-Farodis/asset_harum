<?php

namespace App\Http\Controllers;

use App\Models\MasterSubLokasi;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use \Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Woo\GridView\DataProviders\EloquentDataProvider;

class MasterSubLokasiController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:master-sub-lokasi view', only: ['index', 'show']),
            new Middleware('permission:master-sub-lokasi create', only: ['create', 'store']),
            new Middleware('permission:master-sub-lokasi edit', only: ['edit', 'update']),
            new Middleware('permission:master-sub-lokasi delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $query = MasterSubLokasi::query();

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
        
        $masterSubLokasi = $query->paginate(10);

        if ($request->header('HX-Request')) {
            return view('master-sub-lokasi.includes.index-table', compact('masterSubLokasi'));
        }

        return view('master-sub-lokasi.index', compact('masterSubLokasi', 'columns', 'selectedColumns'));
    }

    public function create(): View
    {
        $masterSubLokasi = new MasterSubLokasi();

        $lokasiList = \App\Models\MasterLokasi::all()->pluck('nama_lokasi', 'id');  

        return view('master-sub-lokasi.create', compact('masterSubLokasi', 'lokasiList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            	'lokasi_id' => 'required|integer',
	'kode_sub_lokasi' => 'required|string|max:255',
	'nama_sub_lokasi' => 'required|string|max:255',
	'keterangan' => 'nullable|string',
        ]);

        try {
            MasterSubLokasi::create($validatedData);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat membuat data.');
        }

        return redirect()->route('master-sub-lokasi.index')
            ->with('success', 'Master Sub Lokasi berhasil dibuat');
    }

    public function show(MasterSubLokasi $masterSubLokasi): View
    {
        return view('master-sub-lokasi.show', compact('masterSubLokasi'));
    }

    public function edit(MasterSubLokasi $masterSubLokasi): View
    {
        $lokasiList = \App\Models\MasterLokasi::all()->pluck('nama_lokasi', 'id');

        return view('master-sub-lokasi.edit', compact('masterSubLokasi', 'lokasiList'));
    }

    public function update(Request $request, MasterSubLokasi $masterSubLokasi): RedirectResponse
    {
        $validatedData = $request->validate([
            	'lokasi_id' => 'required|integer',
	'kode_sub_lokasi' => 'required|string|max:255',
	'nama_sub_lokasi' => 'required|string|max:255',
	'keterangan' => 'nullable|string',
        ]);

        try {
            $masterSubLokasi->update($validatedData);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->back()
                    ->withInput($request->all())
                    ->with('error', 'Data master sub lokasi ini sudah digunakan dan tidak dapat diperbarui.');
            }
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }

        return redirect()->route('master-sub-lokasi.index')
            ->with('success', 'Master Sub Lokasi berhasil diperbarui');
    }

    public function destroy(MasterSubLokasi $masterSubLokasi): RedirectResponse
    {
        try {
            $masterSubLokasi->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('master-sub-lokasi.index')
                    ->with('error', 'Data master sub lokasi ini sudah digunakan dan tidak dapat dihapus.');
            }
            return redirect()->route('master-sub-lokasi.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }

        return redirect()->route('master-sub-lokasi.index')
            ->with('success', 'Master Sub Lokasi berhasil dihapus');
    }
}