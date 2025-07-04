<?php

namespace App\Http\Controllers;

use App\Models\PengadaanBarang;
use App\Models\MasterBarang;
use App\Models\MasterLokasi;
use App\Models\MasterSubLokasi;
use App\Models\MasterStatus;
use App\Models\MasterSatuan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use \Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Woo\GridView\DataProviders\EloquentDataProvider;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PengadaanBarangController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:pengadaan-barang view', only: ['index', 'show']),
            new Middleware('permission:pengadaan-barang create', only: ['create', 'store']),
            new Middleware('permission:pengadaan-barang edit', only: ['edit', 'update']),
            new Middleware('permission:pengadaan-barang delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $query = PengadaanBarang::query();

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
        
        $pengadaanBarang = $query->paginate(10);

        if ($request->header('HX-Request')) {
            return view('pengadaan-barang.includes.index-table', compact('pengadaanBarang'));
        }

        return view('pengadaan-barang.index', compact('pengadaanBarang', 'columns', 'selectedColumns'));
    }

    public function create(): View
    {
        $pengadaanBarang = new PengadaanBarang();
        $barangList = MasterBarang::pluck('nama_barang', 'id')->toArray();
        $lokasiList = MasterSubLokasi::with('lokasi')->get()->mapWithKeys(function ($item) {
            $label = $item->lokasi->nama_lokasi . ' - ' . $item->nama_sub_lokasi;
            return [$item->id => $label];
        })->toArray();
        $statusList = MasterStatus::pluck('nama_status', 'id')->toArray();
        $satuanList = MasterSatuan::pluck('nama_satuan', 'id')->toArray();


        return view('pengadaan-barang.create', compact('pengadaanBarang', 'barangList', 'lokasiList', 'statusList', 'satuanList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'barang_id' => 'required|integer',
            'lokasi_id' => 'required|integer',
            'sumber' => 'required|string|max:100',
            'status' => 'required|in:baru,bekas,hibah',
            'status_id' => 'required|integer',
            'tanggal_pengadaan' => 'required|date',
            'jumlah' => 'required|integer',
            'satuan_id' => 'required|integer',
            'harga_satuan' => 'required|numeric',
            'total_harga' => 'required|numeric',
            'keterangan' => 'nullable|string',
        ]);

        $barangData = MasterBarang::with('kategori')->find($validatedData['barang_id']);

        $kodeInventaris = $barangData->kode_barang . '-' .
            $barangData->kategori->kode_kategori_barang . '-' .
            MasterSubLokasi::find($validatedData['lokasi_id'])->kode_sub_lokasi . '-' .
            date('m', strtotime($validatedData['tanggal_pengadaan'])) . '-' .
            date('Y', strtotime($validatedData['tanggal_pengadaan'])) . '-' .
            PengadaanBarang::whereMonth('tanggal_pengadaan', date('m', strtotime($validatedData['tanggal_pengadaan'])))
                ->whereYear('tanggal_pengadaan', date('Y', strtotime($validatedData['tanggal_pengadaan'])))
                ->count() + 1;
        $validatedData['kode_inventaris'] = $kodeInventaris;
        $validatedData['created_by'] = auth()->id();

        
        try {
            PengadaanBarang::create($validatedData);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat membuat data.');
        }

        return redirect()->route('pengadaan-barang.index')
            ->with('success', 'Pengadaan Barang berhasil dibuat');
    }

    public function show(PengadaanBarang $pengadaanBarang): View
    {
        return view('pengadaan-barang.show', compact('pengadaanBarang'));
    }

    public function edit(PengadaanBarang $pengadaanBarang): View
    {
        return view('pengadaan-barang.edit', compact('pengadaanBarang'));
    }

    public function update(Request $request, PengadaanBarang $pengadaanBarang): RedirectResponse
    {
        $validatedData = $request->validate([
            	'kode_inventaris' => 'required|string|max:255',
	'barang_id' => 'required|integer',
	'lokasi_id' => 'required|integer',
	'sumber' => 'required|string|max:100',
	'status_id' => 'required|integer',
	'tanggal_pengadaan' => 'required|date',
	'jumlah' => 'required|integer',
	'satuan_id' => 'required|integer',
	'harga_satuan' => 'required|numeric',
	'total_harga' => 'required|numeric',
	'keterangan' => 'nullable|string',
        ]);

        try {
            $pengadaanBarang->update($validatedData);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->back()
                    ->withInput($request->all())
                    ->with('error', 'Data pengadaan barang ini sudah digunakan dan tidak dapat diperbarui.');
            }
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }

        return redirect()->route('pengadaan-barang.index')
            ->with('success', 'Pengadaan Barang berhasil diperbarui');
    }

    public function destroy(PengadaanBarang $pengadaanBarang): RedirectResponse
    {
        try {
            $pengadaanBarang->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('pengadaan-barang.index')
                    ->with('error', 'Data pengadaan barang ini sudah digunakan dan tidak dapat dihapus.');
            }
            return redirect()->route('pengadaan-barang.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }

        return redirect()->route('pengadaan-barang.index')
            ->with('success', 'Pengadaan Barang berhasil dihapus');
    }

    public function generateQrCode(PengadaanBarang $pengadaanBarang): View
    {
        $qrCode = QrCode::size(200)->generate($pengadaanBarang->kode_inventaris);

        return view('pengadaan-barang.qr-code', compact('pengadaanBarang', 'qrCode'));
    }
  
}