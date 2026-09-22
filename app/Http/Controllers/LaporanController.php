<?php

namespace App\Http\Controllers;

use App\Models\PengadaanBarang;
use App\Models\MutasiAset;
use App\Models\OpnameSession;
use App\Models\Peminjaman;
use App\Models\MasterSubLokasi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanInventarisExport;
use App\Exports\LaporanMutasiExport;
use App\Exports\LaporanPeminjamanExport;
use App\Exports\LaporanNilaiAsetExport;
use App\Exports\LaporanPenyusutanExport;
use App\Exports\LaporanStokPerlengkapanExport;
use App\Exports\LaporanPemakaianPerlengkapanExport;
use App\Exports\LaporanRealisasiAnggaranExport;
use App\Http\Controllers\PemakaianPerlengkapanController;
use App\Models\Anggaran;
use App\Models\KategoriBarang;
use App\Models\MasterBarang;
use App\Models\MasterLokasi;
use App\Models\PemakaianPerlengkapan;
use App\Services\LaporanAsetService;
use App\Services\PerlengkapanService;

class LaporanController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:laporan view', only: ['index','inventaris','mutasi','opname','peminjaman','nilaiAset','penyusutan','stokPerlengkapan','pemakaianPerlengkapan','realisasiAnggaran']),
            new Middleware('permission:laporan export', only: ['exportInventaris','exportMutasi','exportPeminjaman','exportNilaiAset','exportPenyusutan','exportStokPerlengkapan','exportPemakaianPerlengkapan','exportRealisasiAnggaran']),
        ];
    }

    public function index()
    {
        return view('laporan.index');
    }

    public function inventaris(Request $request)
    {
        $query = PengadaanBarang::with(['barang.kategori', 'lokasi', 'satuan', 'statusKondisi']);

        if ($request->filled('lokasi_id')) {
            $query->where('lokasi_id', $request->lokasi_id);
        }

        if ($request->filled('kategori_id')) {
            $query->whereHas('barang', fn ($q) => $q->where('kategori_barang_id', $request->kategori_id));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('tahun_perolehan')) {
            $query->whereYear('tanggal_pengadaan', $request->tahun_perolehan);
        }

        // Default hanya Peralatan (aset); pilih "Semua" untuk menyertakan Perlengkapan.
        $jenis = $request->has('jenis') ? (string) $request->jenis : 'peralatan';
        if ($jenis === 'peralatan') {
            $query->peralatan();
        } elseif ($jenis === 'perlengkapan') {
            $query->perlengkapan();
        }

        $inventaris = $query->orderBy('kode_inventaris')->paginate(50)->withQueryString();
        $lokasi = MasterSubLokasi::all();

        return view('laporan.inventaris', compact('inventaris', 'lokasi', 'jenis'));
    }

    public function mutasi(Request $request)
    {
        $query = MutasiAset::with(['pengadaan.barang', 'lokasiAsal', 'lokasiTujuan', 'createdBy', 'approvedBy']);

        if ($request->filled('jenis_mutasi')) {
            $query->where('jenis_mutasi', $request->jenis_mutasi);
        }

        if ($request->filled('status')) {
            $query->where('status_mutasi', $request->status);
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal_mutasi', [$request->tanggal_mulai, $request->tanggal_akhir]);
        }

        $mutasi = $query->orderBy('tanggal_mutasi', 'desc')->paginate(50);

        return view('laporan.mutasi', compact('mutasi'));
    }

    public function opname(Request $request)
    {
        $query = OpnameSession::with(['lokasi', 'createdBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal_mulai', [$request->tanggal_mulai, $request->tanggal_akhir]);
        }

        $opname = $query->orderBy('tanggal_mulai', 'desc')->paginate(50);

        return view('laporan.opname', compact('opname'));
    }

    public function peminjaman(Request $request)
    {
        $query = Peminjaman::with(['pengadaan.barang', 'peminjam', 'kondisiKembali', 'approvedBy']);

        if ($request->filled('status_peminjaman')) {
            $query->where('status_peminjaman', $request->status_peminjaman);
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal_pinjam', [$request->tanggal_mulai, $request->tanggal_akhir]);
        }

        $peminjaman = $query->orderBy('tanggal_pinjam', 'desc')->paginate(50);

        return view('laporan.peminjaman', compact('peminjaman'));
    }

    public function nilaiAset(Request $request)
    {
        $filters = $request->only(['jenis', 'lokasi_id', 'kategori_id', 'per_tanggal']);
        $data = LaporanAsetService::nilaiAset($filters);

        $inventaris = $data['rows'];
        $summary = $data['summary'];
        $asOf = $data['as_of'];
        $lokasi = MasterSubLokasi::all();
        $kategori = KategoriBarang::orderBy('nama_kategori_barang')->get();

        return view('laporan.nilai-aset', compact('inventaris', 'summary', 'lokasi', 'kategori', 'asOf'));
    }

    public function penyusutan(Request $request)
    {
        $data = LaporanAsetService::penyusutan($request->only(['lokasi_id', 'kategori_id', 'per_tanggal', 'semua']));

        $rows = $data['rows'];
        $summary = $data['summary'];
        $asOf = $data['as_of'];
        $lokasi = MasterSubLokasi::all();
        $kategori = KategoriBarang::orderBy('nama_kategori_barang')->get();

        return view('laporan.penyusutan', compact('rows', 'summary', 'asOf', 'lokasi', 'kategori'));
    }

    public function stokPerlengkapan(Request $request)
    {
        $batch = PerlengkapanService::batch($request->only(['lokasi_id', 'barang_id', 'search', 'hanya_sisa']));
        $ringkasan = PerlengkapanService::ringkasan($batch);
        $lokasi = MasterSubLokasi::with('lokasi')->get();
        $barangList = MasterBarang::perlengkapan()->orderBy('nama_barang')->pluck('nama_barang', 'id');

        return view('laporan.stok-perlengkapan', compact('batch', 'ringkasan', 'lokasi', 'barangList'));
    }

    public function pemakaianPerlengkapan(Request $request)
    {
        $filters = $request->only(['tanggal_mulai', 'tanggal_akhir', 'barang_id', 'lokasi_id', 'pemakai']);

        $query = PemakaianPerlengkapanController::filterQuery(
            PemakaianPerlengkapan::with(['pengadaan.barang', 'pengadaan.satuan', 'lokasi.lokasi']),
            $filters
        )->orderByDesc('tanggal_pemakaian')->orderByDesc('id');

        $totalNilai = (float) (clone $query)->selectRaw('COALESCE(SUM(jumlah * harga_satuan), 0) as total')->reorder()->value('total');
        $totalJumlah = (int) (clone $query)->reorder()->sum('jumlah');
        $pemakaian = $query->paginate(50)->withQueryString();

        $lokasi = MasterSubLokasi::with('lokasi')->get();
        $barangList = MasterBarang::perlengkapan()->orderBy('nama_barang')->pluck('nama_barang', 'id');

        return view('laporan.pemakaian-perlengkapan', compact('pemakaian', 'totalNilai', 'totalJumlah', 'lokasi', 'barangList'));
    }

    public function realisasiAnggaran(Request $request)
    {
        $data = LaporanAsetService::realisasiAnggaran($request->only(['tahun', 'lokasi_id']));

        $tahunList = Anggaran::select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');
        if (!$tahunList->contains($data['tahun'])) {
            $tahunList->prepend($data['tahun']);
        }
        $lokasiList = MasterLokasi::orderBy('nama_lokasi')->pluck('nama_lokasi', 'id');

        return view('laporan.realisasi-anggaran', array_merge($data, compact('tahunList', 'lokasiList')));
    }
    public function exportInventaris(Request $request)
    {
        $filters = $request->only(['lokasi_id', 'kategori_id', 'is_active', 'tahun_perolehan', 'jenis']);
        return Excel::download(new LaporanInventarisExport($filters), 'laporan-inventaris-' . date('Y-m-d') . '.xlsx');
    }

    public function exportMutasi(Request $request)
    {
        $filters = $request->only(['jenis_mutasi', 'status', 'tanggal_mulai', 'tanggal_akhir']);
        return Excel::download(new LaporanMutasiExport($filters), 'laporan-mutasi-' . date('Y-m-d') . '.xlsx');
    }

    public function exportPeminjaman(Request $request)
    {
        $filters = $request->only(['status_peminjaman', 'tanggal_mulai', 'tanggal_akhir']);
        return Excel::download(new LaporanPeminjamanExport($filters), 'laporan-peminjaman-' . date('Y-m-d') . '.xlsx');
    }

    public function exportNilaiAset(Request $request)
    {
        $filters = $request->only(['jenis', 'lokasi_id', 'kategori_id', 'per_tanggal']);
        return Excel::download(new LaporanNilaiAsetExport($filters), 'laporan-nilai-aset-' . date('Y-m-d') . '.xlsx');
    }
    public function exportPenyusutan(Request $request)
    {
        $filters = $request->only(['lokasi_id', 'kategori_id', 'per_tanggal', 'semua']);
        return Excel::download(new LaporanPenyusutanExport($filters), 'laporan-penyusutan-' . date('Y-m-d') . '.xlsx');
    }

    public function exportStokPerlengkapan(Request $request)
    {
        $filters = $request->only(['lokasi_id', 'barang_id', 'search', 'hanya_sisa']);
        return Excel::download(new LaporanStokPerlengkapanExport($filters), 'laporan-stok-perlengkapan-' . date('Y-m-d') . '.xlsx');
    }

    public function exportPemakaianPerlengkapan(Request $request)
    {
        $filters = $request->only(['tanggal_mulai', 'tanggal_akhir', 'barang_id', 'lokasi_id', 'pemakai']);
        return Excel::download(new LaporanPemakaianPerlengkapanExport($filters), 'laporan-pemakaian-perlengkapan-' . date('Y-m-d') . '.xlsx');
    }

    public function exportRealisasiAnggaran(Request $request)
    {
        $filters = $request->only(['tahun', 'lokasi_id']);
        return Excel::download(new LaporanRealisasiAnggaranExport($filters), 'laporan-realisasi-anggaran-' . date('Y-m-d') . '.xlsx');
    }
}
