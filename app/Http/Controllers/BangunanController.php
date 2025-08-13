<?php

namespace App\Http\Controllers;

use App\Models\PengadaanBarang;
use App\Models\BangunanDetail;
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

class BangunanController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:bangunan view', only: ['index', 'show']),
            new Middleware('permission:bangunan create', only: ['create', 'store']),
            new Middleware('permission:bangunan edit', only: ['edit', 'update']),
            new Middleware('permission:bangunan delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $query = PengadaanBarang::whereHas('barang.kategori', function($q) {
            $q->where('nama_kategori_barang', 'LIKE', '%bangunan%');
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
        
        $bangunanData = $query->with(['bangunanDetail', 'barang', 'lokasi.lokasi', 'status'])->paginate(10);

        if ($request->header('HX-Request')) {
            return view('bangunan.includes.index-table', compact('bangunanData'));
        }

        return view('bangunan.index', compact('bangunanData', 'columns', 'selectedColumns'));
    }

    public function create(): View
    {
        $pengadaanBarang = new PengadaanBarang();
        $bangunanDetail = new BangunanDetail();
        
        // Filter barang yang kategorinya bangunan
        $barangList = MasterBarang::whereHas('kategori', function($q) {
            $q->where('nama_kategori_barang', 'LIKE', '%bangunan%');
        })->pluck('nama_barang', 'id')->toArray();
        
        $lokasiList = MasterSubLokasi::with('lokasi')->get()->mapWithKeys(function ($item) {
            $label = $item->lokasi->nama_lokasi . ' - ' . $item->nama_sub_lokasi;
            return [$item->id => $label];
        })->toArray();
        
        $statusList = MasterStatus::pluck('nama_status', 'id')->toArray();
        $satuanList = MasterSatuan::pluck('nama_satuan', 'id')->toArray();

        return view('bangunan.create', compact('pengadaanBarang', 'bangunanDetail', 'barangList', 'lokasiList', 'statusList', 'satuanList'));
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
            // Bangunan detail fields
            'alamat' => 'required|string|max:255',
            'luas' => 'required|numeric',
            'jumlah_lantai' => 'required|integer',
            'bahan_bangunan' => 'required|string|max:100',
            'nomor_imb' => 'nullable|string|max:100',
            'tanggal_imb' => 'nullable|date',
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
            
            $bangunanDetailData = array_intersect_key($validatedData, array_flip([
                'alamat', 'luas', 'jumlah_lantai', 'bahan_bangunan', 
                'nomor_imb', 'tanggal_imb', 'kondisi'
            ]));
            $bangunanDetailData['pengadaan_id'] = $pengadaan->id;
            
            BangunanDetail::create($bangunanDetailData);
            
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat membuat data.');
        }

        return redirect()->route('bangunan.index')
            ->with('success', 'Data Bangunan berhasil dibuat');
    }

    public function show(PengadaanBarang $bangunan): View
    {
        $bangunan->load(['bangunanDetail', 'barang', 'lokasi.lokasi', 'status']);
        return view('bangunan.show', compact('bangunan'));
    }

    public function edit(PengadaanBarang $bangunan): View
    {
        $bangunan->load('bangunanDetail');
        
        $barangList = MasterBarang::whereHas('kategori', function($q) {
            $q->where('nama_kategori_barang', 'LIKE', '%bangunan%');
        })->pluck('nama_barang', 'id')->toArray();
        
        $lokasiList = MasterSubLokasi::with('lokasi')->get()->mapWithKeys(function ($item) {
            $label = $item->lokasi->nama_lokasi . ' - ' . $item->nama_sub_lokasi;
            return [$item->id => $label];
        })->toArray();
        
        $statusList = MasterStatus::pluck('nama_status', 'id')->toArray();
        $satuanList = MasterSatuan::pluck('nama_satuan', 'id')->toArray();

        return view('bangunan.edit', compact('bangunan', 'barangList', 'lokasiList', 'statusList', 'satuanList'));
    }

    public function update(Request $request, PengadaanBarang $bangunan): RedirectResponse
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
            // Bangunan detail fields
            'alamat' => 'required|string|max:255',
            'luas' => 'required|numeric',
            'jumlah_lantai' => 'required|integer',
            'bahan_bangunan' => 'required|string|max:100',
            'nomor_imb' => 'nullable|string|max:100',
            'tanggal_imb' => 'nullable|date',
            'kondisi' => 'required|string|max:100',
        ]);

        try {
            $pengadaanData = array_intersect_key($validatedData, array_flip([
                'kode_inventaris', 'barang_id', 'lokasi_id', 'sumber', 'status_id', 
                'tanggal_pengadaan', 'jumlah', 'satuan_id', 'harga_satuan', 
                'total_harga', 'keterangan'
            ]));
            
            $bangunan->update($pengadaanData);
            
            $bangunanDetailData = array_intersect_key($validatedData, array_flip([
                'alamat', 'luas', 'jumlah_lantai', 'bahan_bangunan', 
                'nomor_imb', 'tanggal_imb', 'kondisi'
            ]));
            
            if ($bangunan->bangunanDetail) {
                $bangunan->bangunanDetail->update($bangunanDetailData);
            } else {
                $bangunanDetailData['pengadaan_id'] = $bangunan->id;
                BangunanDetail::create($bangunanDetailData);
            }
            
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->back()
                    ->withInput($request->all())
                    ->with('error', 'Data bangunan ini sudah digunakan dan tidak dapat diperbarui.');
            }
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }

        return redirect()->route('bangunan.index')
            ->with('success', 'Data Bangunan berhasil diperbarui');
    }

    public function destroy(PengadaanBarang $bangunan): RedirectResponse
    {
        try {
            $bangunan->bangunanDetail?->delete();
            $bangunan->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('bangunan.index')
                    ->with('error', 'Data bangunan ini sudah digunakan dan tidak dapat dihapus.');
            }
            return redirect()->route('bangunan.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }

        return redirect()->route('bangunan.index')
            ->with('success', 'Data Bangunan berhasil dihapus');
    }

    public function generateQrCode(PengadaanBarang $bangunan): View
    {
        $qrCode = QrCode::size(200)->generate($bangunan->kode_inventaris);
        return view('bangunan.qr-code', compact('bangunan', 'qrCode'));
    }
}
