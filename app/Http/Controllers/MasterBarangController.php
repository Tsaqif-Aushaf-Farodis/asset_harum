<?php

namespace App\Http\Controllers;

use App\Models\MasterBarang;
use App\Models\KategoriBarang;
use App\Models\User;
use App\Services\NilaiBarangService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
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
        $except = ['created_by', 'updated_by', 'disusutkan', 'butuh_perawatan', 'interval_penyusutan_tahun'];

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

        if (in_array($request->get('jenis'), [MasterBarang::JENIS_PERALATAN, MasterBarang::JENIS_PERLENGKAPAN], true)) {
            $query->where('jenis_barang', $request->get('jenis'));
        }

        $masterBarang = $query->with('kategori')->paginate(10);

        if ($request->header('HX-Request')) {
            return view('master-barang.includes.index-table', compact('masterBarang'));
        }

        return view('master-barang.index', compact('masterBarang', 'columns', 'selectedColumns'));
    }

    public function create(): View
    {
        $masterBarang = new MasterBarang();
        $kategoriList = KategoriBarang::pluck('nama_kategori_barang', 'id')->toArray();

        return view('master-barang.create', compact('masterBarang', 'kategoriList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $this->validatedData($request);

        $validatedData['created_by'] = auth()->id();

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
        $masterBarang->load('kategori');

        return view('master-barang.show', compact('masterBarang'));
    }

    public function edit(MasterBarang $masterBarang): View
    {
        $kategoriList = KategoriBarang::pluck('nama_kategori_barang', 'id')->toArray();

        return view('master-barang.edit', compact('masterBarang', 'kategoriList'));
    }

    public function update(Request $request, MasterBarang $masterBarang): RedirectResponse
    {
        $validatedData = $this->validatedData($request, $masterBarang);

        $validatedData['updated_by'] = auth()->id();

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

    /**
     * Validasi input Master Barang, termasuk jenis, penyusutan, dan perawatan.
     * Masa pemakaian diisi dalam TAHUN (Peralatan) atau BULAN (Perlengkapan),
     * lalu disimpan dalam bulan.
     */
    private function validatedData(Request $request, ?MasterBarang $masterBarang = null): array
    {
        $validatedData = $request->validate([
            'kode_barang' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'merk_barang' => 'nullable|string|max:255',
            'tipe_barang' => 'nullable|string|max:255',
            'tahun_barang' => 'nullable|string',
            'deskripsi_barang' => 'nullable|string',
            'kategori_barang_id' => 'required|integer|exists:kategori_barang,id',
            'jenis_barang' => 'required|in:' . MasterBarang::JENIS_PERALATAN . ',' . MasterBarang::JENIS_PERLENGKAPAN,
            'disusutkan' => 'nullable|boolean',
            'masa_pemakaian' => 'nullable|numeric',
            'interval_penyusutan_tahun' => 'nullable|integer|min:1',
            'butuh_perawatan' => 'nullable|boolean',
        ]);

        // Jenis tidak boleh diubah bila barang sudah dipakai pada pengadaan.
        if ($masterBarang
            && $masterBarang->jenis_barang !== $validatedData['jenis_barang']
            && $masterBarang->pengadaan()->exists()) {
            throw ValidationException::withMessages([
                'jenis_barang' => 'Jenis barang tidak dapat diubah karena barang ini sudah memiliki data pengadaan.',
            ]);
        }

        $pengaturan = NilaiBarangService::validasiPengaturan(
            $validatedData['jenis_barang'],
            $request->boolean('disusutkan'),
            $request->input('masa_pemakaian'),
            $request->input('interval_penyusutan_tahun'),
        );

        if ($pengaturan['errors']) {
            throw ValidationException::withMessages($pengaturan['errors']);
        }

        unset($validatedData['masa_pemakaian']);

        return array_merge($validatedData, $pengaturan['data'], [
            'butuh_perawatan' => $request->boolean('butuh_perawatan'),
        ]);
    }
}
