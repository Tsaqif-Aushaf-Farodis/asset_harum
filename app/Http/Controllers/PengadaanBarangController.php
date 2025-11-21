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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $query = PengadaanBarang::with(['barang', 'lokasi', 'kategori', 'satuan', 'statusKondisi', 'createdBy'])
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan lokasi
        if ($request->filled('lokasi_id')) {
            $query->where('lokasi_id', $request->lokasi_id);
        }

        // Filter berdasarkan kategori (via relasi barang)
        if ($request->filled('kategori_id')) {
            $query->whereHas('barang', function($q) use ($request) {
                $q->where('kategori_id', $request->kategori_id);
            });
        }

        // Filter berdasarkan status kondisi
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        // Filter berdasarkan status aktif
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Filter berdasarkan tahun perolehan
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_pengadaan', $request->tahun);
        }

        // Search global
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_inventaris', 'like', "%$search%")
                  ->orWhere('keterangan', 'like', "%$search%")
                  ->orWhereHas('barang', function($sq) use ($search) {
                      $sq->where('nama_barang', 'like', "%$search%");
                  });
            });
        }
        
        $pengadaanBarang = $query->paginate(20);

        // Statistik untuk dashboard
        $statistics = [
            'total_aset' => PengadaanBarang::count(),
            'total_aktif' => PengadaanBarang::where('is_active', true)->count(),
            'total_dipinjam' => PengadaanBarang::where('is_borrowed', true)->count(),
            'total_nilai' => PengadaanBarang::sum('total_harga'),
        ];

        // Data untuk filter dropdown
        $lokasiList = MasterSubLokasi::with('lokasi')->get();
        $kategoriList = \App\Models\KategoriBarang::all();
        $statusList = MasterStatus::all();
        $tahunList = PengadaanBarang::selectRaw('YEAR(tanggal_pengadaan) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        if ($request->header('HX-Request')) {
            return view('pengadaan-barang.includes.index-table', compact('pengadaanBarang'));
        }

        return view('pengadaan-barang.index', compact(
            'pengadaanBarang', 
            'statistics', 
            'lokasiList', 
            'kategoriList', 
            'statusList', 
            'tahunList'
        ));
    }

    public function create(): View
    {
        $pengadaanBarang = new PengadaanBarang();
        $barangList = MasterBarang::approved()
            ->pluck('nama_barang', 'id')
            ->toArray();
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
            'barang_id' => 'required|exists:master_barang,id',
            'lokasi_id' => 'required|exists:master_sub_lokasi,id',
            'sumber' => 'required|string|max:100',
            'status' => 'required|in:baru,bekas,hibah',
            'status_id' => 'required|exists:master_status,id',
            'tanggal_pengadaan' => 'required|date|before_or_equal:today',
            'jumlah' => 'required|integer|min:1',
            'satuan_id' => 'required|exists:master_satuan,id',
            'harga_satuan' => 'required|numeric|min:0',
            'total_harga' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'permohonan_id' => 'nullable|exists:permohonan,id',
        ]);

        DB::beginTransaction();
        try {
            $barangData = MasterBarang::with('kategori')->findOrFail($validatedData['barang_id']);
            $lokasiData = MasterSubLokasi::findOrFail($validatedData['lokasi_id']);
            
            $tanggalPengadaan = $validatedData['tanggal_pengadaan'];
            $month = date('m', strtotime($tanggalPengadaan));
            $year = date('Y', strtotime($tanggalPengadaan));
            
            // Generate nomor urut
            $nomorUrut = PengadaanBarang::whereMonth('tanggal_pengadaan', $month)
                ->whereYear('tanggal_pengadaan', $year)
                ->count() + 1;

            // Format: KODE_BARANG-KODE_KATEGORI-KODE_LOKASI-MM-YYYY-URUT
            $kodeInventaris = sprintf(
                '%s-%s-%s-%s-%s-%04d',
                $barangData->kode_barang,
                $barangData->kategori->kode_kategori_barang,
                $lokasiData->kode_sub_lokasi,
                $month,
                $year,
                $nomorUrut
            );

            $validatedData['kode_inventaris'] = $kodeInventaris;
            $validatedData['is_active'] = true;
            $validatedData['is_borrowed'] = false;
            $validatedData['created_by'] = Auth::id();

            $pengadaan = PengadaanBarang::create($validatedData);
            
            DB::commit();
            return redirect()->route('pengadaan-barang.index')
                ->with('success', 'Pengadaan Barang berhasil dibuat dengan kode: ' . $kodeInventaris);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat membuat data: ' . $e->getMessage());
        }
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
        // Cek apakah barang sedang dipinjam
        if ($pengadaanBarang->is_borrowed) {
            return redirect()->back()
                ->with('error', 'Barang yang sedang dipinjam tidak dapat diubah.');
        }

        $validatedData = $request->validate([
            'kode_inventaris' => 'required|string|max:255|unique:pengadaan_barang,kode_inventaris,' . $pengadaanBarang->id,
            'barang_id' => 'required|exists:master_barang,id',
            'lokasi_id' => 'required|exists:master_sub_lokasi,id',
            'sumber' => 'required|string|max:100',
            'status' => 'required|in:baru,bekas,hibah',
            'status_id' => 'required|exists:master_status,id',
            'tanggal_pengadaan' => 'required|date|before_or_equal:today',
            'jumlah' => 'required|integer|min:1',
            'satuan_id' => 'required|exists:master_satuan,id',
            'harga_satuan' => 'required|numeric|min:0',
            'total_harga' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validatedData['updated_by'] = Auth::id();

        DB::beginTransaction();
        try {
            $pengadaanBarang->update($validatedData);
            
            DB::commit();
            return redirect()->route('pengadaan-barang.index')
                ->with('success', 'Pengadaan Barang berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy(PengadaanBarang $pengadaanBarang): RedirectResponse
    {
        // Cek apakah barang sedang dipinjam
        if ($pengadaanBarang->is_borrowed) {
            return redirect()->route('pengadaan-barang.index')
                ->with('error', 'Barang yang sedang dipinjam tidak dapat dihapus.');
        }

        // Cek apakah ada mutasi terkait
        if ($pengadaanBarang->mutasiAset()->exists()) {
            return redirect()->route('pengadaan-barang.index')
                ->with('error', 'Data pengadaan barang ini memiliki riwayat mutasi dan tidak dapat dihapus.');
        }

        DB::beginTransaction();
        try {
            $pengadaanBarang->delete();
            
            DB::commit();
            return redirect()->route('pengadaan-barang.index')
                ->with('success', 'Pengadaan Barang berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('pengadaan-barang.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    public function generateQrCode(PengadaanBarang $pengadaanBarang): View
    {
        $qrCode = QrCode::size(200)->generate($pengadaanBarang->kode_inventaris);

        return view('pengadaan-barang.qr-code', compact('pengadaanBarang', 'qrCode'));
    }
  
}