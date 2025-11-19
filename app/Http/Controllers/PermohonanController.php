<?php

namespace App\Http\Controllers;

use App\Models\Permohonan;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use \Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Woo\GridView\DataProviders\EloquentDataProvider;

class PermohonanController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:permohonan view', only: ['index', 'show']),
            new Middleware('permission:permohonan create', only: ['create', 'store']),
            new Middleware('permission:permohonan edit', only: ['edit', 'update']),
            new Middleware('permission:permohonan delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $query = Permohonan::query();

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
        
        $permohonan = $query->paginate(10);

        if ($request->header('HX-Request')) {
            return view('permohonan.includes.index-table', compact('permohonan'));
        }

        return view('permohonan.index', compact('permohonan', 'columns', 'selectedColumns'));
    }

    public function create(): View
    {
        $permohonan = new Permohonan();

        return view('permohonan.create', compact('permohonan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            	'bidang' => 'required|string|max:255',
                'tahun_anggaran' => 'required|string',
                'unit_kegiatan' => 'required|string|max:255',
                'keterangan' => 'nullable|string|max:255',
        ]);


        try {
            Permohonan::create($validatedData);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat membuat data.');
        }

        return redirect()->route('permohonan.index')
            ->with('success', 'Permohonan berhasil dibuat');
    }

    public function show(Permohonan $permohonan): View
    {
        return view('permohonan.show', compact('permohonan'));
    }

    public function edit(Permohonan $permohonan): View
    {
        return view('permohonan.edit', compact('permohonan'));
    }

    public function update(Request $request, Permohonan $permohonan): RedirectResponse
    {
        $validatedData = $request->validate([
            	'bidang' => 'required|string|max:255',
	'tahun_anggaran' => 'required|string',
	'unit_kegiatan' => 'required|string|max:255',
	'keterangan' => 'nullable|string|max:255',
	'status' => 'required|string|max:255',
        ]);

        try {
            $permohonan->update($validatedData);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->back()
                    ->withInput($request->all())
                    ->with('error', 'Data permohonan ini sudah digunakan dan tidak dapat diperbarui.');
            }
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }

        return redirect()->route('permohonan.index')
            ->with('success', 'Permohonan berhasil diperbarui');
    }

    public function destroy(Permohonan $permohonan): RedirectResponse
    {
        try {
            $permohonan->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('permohonan.index')
                    ->with('error', 'Data permohonan ini sudah digunakan dan tidak dapat dihapus.');
            }
            return redirect()->route('permohonan.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }

        return redirect()->route('permohonan.index')
            ->with('success', 'Permohonan berhasil dihapus');
    }
}