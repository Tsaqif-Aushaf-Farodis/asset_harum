<?php

namespace App\Http\Controllers;

use App\Models\MasterSatuan;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use \Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Woo\GridView\DataProviders\EloquentDataProvider;

class MasterSatuanController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:master-satuan view', only: ['index', 'show']),
            new Middleware('permission:master-satuan create', only: ['create', 'store']),
            new Middleware('permission:master-satuan edit', only: ['edit', 'update']),
            new Middleware('permission:master-satuan delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $query = MasterSatuan::query();

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
        
        $masterSatuan = $query->paginate(10);

        if ($request->header('HX-Request')) {
            return view('master-satuan.includes.index-table', compact('masterSatuan'));
        }

        return view('master-satuan.index', compact('masterSatuan', 'columns', 'selectedColumns'));
    }

    public function create(): View
    {
        $masterSatuan = new MasterSatuan();

        return view('master-satuan.create', compact('masterSatuan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            
        ]);

        try {
            MasterSatuan::create($validatedData);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat membuat data.');
        }

        return redirect()->route('master-satuan.index')
            ->with('success', 'Master Satuan berhasil dibuat');
    }

    public function show(MasterSatuan $masterSatuan): View
    {
        return view('master-satuan.show', compact('masterSatuan'));
    }

    public function edit(MasterSatuan $masterSatuan): View
    {
        return view('master-satuan.edit', compact('masterSatuan'));
    }

    public function update(Request $request, MasterSatuan $masterSatuan): RedirectResponse
    {
        $validatedData = $request->validate([
            
        ]);

        try {
            $masterSatuan->update($validatedData);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->back()
                    ->withInput($request->all())
                    ->with('error', 'Data master satuan ini sudah digunakan dan tidak dapat diperbarui.');
            }
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }

        return redirect()->route('master-satuan.index')
            ->with('success', 'Master Satuan berhasil diperbarui');
    }

    public function destroy(MasterSatuan $masterSatuan): RedirectResponse
    {
        try {
            $masterSatuan->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('master-satuan.index')
                    ->with('error', 'Data master satuan ini sudah digunakan dan tidak dapat dihapus.');
            }
            return redirect()->route('master-satuan.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }

        return redirect()->route('master-satuan.index')
            ->with('success', 'Master Satuan berhasil dihapus');
    }
}