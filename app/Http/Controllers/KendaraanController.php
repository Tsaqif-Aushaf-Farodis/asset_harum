<?php

namespace App\Http\Controllers;

use App\Models\PengadaanBarang;
use App\Models\KendaraanDetail;
use App\Models\MasterBarang;
use App\Models\MasterLokasi;
use App\Models\MasterSubLokasi;
use App\Models\MasterStatus;
use App\Models\MasterSatuan;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use \Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class KendaraanController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:kendaraan view', only: ['index', 'show']),
            new Middleware('permission:kendaraan create', only: ['create', 'store']),
            new Middleware('permission:kendaraan edit', only: ['edit', 'update']),
            new Middleware('permission:kendaraan delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $query = PengadaanBarang::whereHas('barang.kategori', function($q) {
            $q->where('nama_kategori_barang', 'LIKE', '%kendaraan%');
        });

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
        
        $kendaraanData = $query->with(['kendaraanDetail', 'barang', 'lokasi.lokasi', 'status'])->paginate(10);

        if ($request->header('HX-Request')) {
            return view('kendaraan.includes.index-table', compact('kendaraanData'));
        }

        return view('kendaraan.index', compact('kendaraanData', 'columns', 'selectedColumns'));
    }

    public function create(): View
    {
        $pengadaanBarang = new PengadaanBarang();
        $kendaraanDetail = new KendaraanDetail();
        
        // Filter barang yang kategorinya kendaraan
        $barangList = MasterBarang::whereHas('kategori', function($q) {
            $q->where('nama_kategori_barang', 'LIKE', '%kendaraan%');
        })->pluck('nama_barang', 'id')->toArray();
        
        $lokasiList = MasterSubLokasi::with('lokasi')->get()->mapWithKeys(function ($item) {
            $label = $item->lokasi->nama_lokasi . ' - ' . $item->nama_sub_lokasi;
            return [$item->id => $label];
        })->toArray();
        
        $statusList = MasterStatus::pluck('nama_status', 'id')->toArray();
        $satuanList = MasterSatuan::pluck('nama_satuan', 'id')->toArray();

        return view('kendaraan.create', compact('pengadaanBarang', 'kendaraanDetail', 'barangList', 'lokasiList', 'statusList', 'satuanList'));
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
            // Kendaraan detail fields
            'merk' => 'required|string|max:100',
            'tipe' => 'required|string|max:100',
            'tahun_perakitan' => 'required|integer',
            'no_polisi' => 'nullable|string|max:20',
            'no_rangka' => 'required|string|max:50',
            'no_mesin' => 'required|string|max:50',
            'kapasitas_cc' => 'nullable|integer',
            'warna' => 'required|string|max:50',
            'kondisi' => 'required|string|max:100',
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

        $pengadaanData = array_intersect_key($validatedData, array_flip([
            'barang_id', 'lokasi_id', 'sumber', 'status', 'status_id', 
            'tanggal_pengadaan', 'jumlah', 'satuan_id', 'harga_satuan', 
            'total_harga', 'keterangan'
        ]));
        
        $pengadaanData['kode_inventaris'] = $kodeInventaris;
        $pengadaanData['created_by'] = auth()->id();

        try {
            $pengadaan = PengadaanBarang::create($pengadaanData);
            
            $kendaraanDetailData = array_intersect_key($validatedData, array_flip([
                'merk', 'tipe', 'tahun_perakitan', 'no_polisi', 'no_rangka', 
                'no_mesin', 'kapasitas_cc', 'warna', 'kondisi'
            ]));
            $kendaraanDetailData['pengadaan_id'] = $pengadaan->id;
            
            KendaraanDetail::create($kendaraanDetailData);
            
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat membuat data.');
        }

        return redirect()->route('kendaraan.index')
            ->with('success', 'Data Kendaraan berhasil dibuat');
    }

    public function show(PengadaanBarang $kendaraan): View
    {
        $kendaraan->load(['kendaraanDetail', 'barang', 'lokasi.lokasi', 'status']);
        return view('kendaraan.show', compact('kendaraan'));
    }

    public function edit(PengadaanBarang $kendaraan): View
    {
        $kendaraan->load('kendaraanDetail');
        
        $barangList = MasterBarang::whereHas('kategori', function($q) {
            $q->where('nama_kategori_barang', 'LIKE', '%kendaraan%');
        })->pluck('nama_barang', 'id')->toArray();
        
        $lokasiList = MasterSubLokasi::with('lokasi')->get()->mapWithKeys(function ($item) {
            $label = $item->lokasi->nama_lokasi . ' - ' . $item->nama_sub_lokasi;
            return [$item->id => $label];
        })->toArray();
        
        $statusList = MasterStatus::pluck('nama_status', 'id')->toArray();
        $satuanList = MasterSatuan::pluck('nama_satuan', 'id')->toArray();

        return view('kendaraan.edit', compact('kendaraan', 'barangList', 'lokasiList', 'statusList', 'satuanList'));
    }

    public function update(Request $request, PengadaanBarang $kendaraan): RedirectResponse
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
            // Kendaraan detail fields
            'merk' => 'required|string|max:100',
            'tipe' => 'required|string|max:100',
            'tahun_perakitan' => 'required|integer',
            'no_polisi' => 'nullable|string|max:20',
            'no_rangka' => 'required|string|max:50',
            'no_mesin' => 'required|string|max:50',
            'kapasitas_cc' => 'nullable|integer',
            'warna' => 'required|string|max:50',
            'kondisi' => 'required|string|max:100',
        ]);

        try {
            $pengadaanData = array_intersect_key($validatedData, array_flip([
                'kode_inventaris', 'barang_id', 'lokasi_id', 'sumber', 'status_id', 
                'tanggal_pengadaan', 'jumlah', 'satuan_id', 'harga_satuan', 
                'total_harga', 'keterangan'
            ]));
            
            $kendaraan->update($pengadaanData);
            
            $kendaraanDetailData = array_intersect_key($validatedData, array_flip([
                'merk', 'tipe', 'tahun_perakitan', 'no_polisi', 'no_rangka', 
                'no_mesin', 'kapasitas_cc', 'warna', 'kondisi'
            ]));
            
            if ($kendaraan->kendaraanDetail) {
                $kendaraan->kendaraanDetail->update($kendaraanDetailData);
            } else {
                $kendaraanDetailData['pengadaan_id'] = $kendaraan->id;
                KendaraanDetail::create($kendaraanDetailData);
            }
            
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->back()
                    ->withInput($request->all())
                    ->with('error', 'Data kendaraan ini sudah digunakan dan tidak dapat diperbarui.');
            }
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }

        return redirect()->route('kendaraan.index')
            ->with('success', 'Data Kendaraan berhasil diperbarui');
    }

    public function destroy(PengadaanBarang $kendaraan): RedirectResponse
    {
        try {
            $kendaraan->kendaraanDetail?->delete();
            $kendaraan->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('kendaraan.index')
                    ->with('error', 'Data kendaraan ini sudah digunakan dan tidak dapat dihapus.');
            }
            return redirect()->route('kendaraan.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }

        return redirect()->route('kendaraan.index')
            ->with('success', 'Data Kendaraan berhasil dihapus');
    }

    public function generateQrCode(PengadaanBarang $kendaraan): View
    {
        $qrCode = QrCode::size(200)->generate($kendaraan->kode_inventaris);
        return view('kendaraan.qr-code', compact('kendaraan', 'qrCode'));
    }
}
