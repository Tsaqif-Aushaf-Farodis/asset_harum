<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use \Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Woo\GridView\DataProviders\EloquentDataProvider;

class MasterStatusController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:master-status view', only: ['index', 'show']),
            new Middleware('permission:master-status create', only: ['create', 'store']),
            new Middleware('permission:master-status edit', only: ['edit', 'update']),
            new Middleware('permission:master-status delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $query = MasterStatus::query();

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
        
        $masterStatus = $query->paginate(10);

        if ($request->header('HX-Request')) {
            return view('master-status.includes.index-table', compact('masterStatus'));
        }

        return view('master-status.index', compact('masterStatus', 'columns', 'selectedColumns'));
    }

    public function create(): View
    {
        $masterStatus = new MasterStatus();

        return view('master-status.create', compact('masterStatus'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            	'kode_status' => 'required|string|max:255',
	'nama_status' => 'required|string|max:255',
        ]);

        try {
            MasterStatus::create($validatedData);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat membuat data.');
        }

        return redirect()->route('master-status.index')
            ->with('success', 'Master Status berhasil dibuat');
    }

    public function show(MasterStatus $masterStatus): View
    {
        return view('master-status.show', compact('masterStatus'));
    }

    public function edit(MasterStatus $masterStatus): View
    {
        return view('master-status.edit', compact('masterStatus'));
    }

    public function update(Request $request, MasterStatus $masterStatus): RedirectResponse
    {
        $validatedData = $request->validate([
            	'kode_status' => 'required|string|max:255',
	'nama_status' => 'required|string|max:255',
        ]);

        try {
            $masterStatus->update($validatedData);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->back()
                    ->withInput($request->all())
                    ->with('error', 'Data master status ini sudah digunakan dan tidak dapat diperbarui.');
            }
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }

        return redirect()->route('master-status.index')
            ->with('success', 'Master Status berhasil diperbarui');
    }

    public function destroy(MasterStatus $masterStatus): RedirectResponse
    {
        try {
            $masterStatus->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('master-status.index')
                    ->with('error', 'Data master status ini sudah digunakan dan tidak dapat dihapus.');
            }
            return redirect()->route('master-status.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }

        return redirect()->route('master-status.index')
            ->with('success', 'Master Status berhasil dihapus');
    }
}