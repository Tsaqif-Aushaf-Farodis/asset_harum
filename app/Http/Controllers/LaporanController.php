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

class LaporanController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:laporan-view', only: ['index','inventaris','mutasi','opname','peminjaman','nilaiAset']),
            new Middleware('permission:laporan-export', only: ['exportInventaris','exportMutasi','exportPeminjaman','exportNilaiAset']),
        ];
    }

    public function index()
    {
        return view('laporan.index');
    }

    public function inventaris(Request $request)
    {
        $query = PengadaanBarang::with(['lokasi', 'kategori', 'satuan']);

        if ($request->has('lokasi_id')) {
            $query->where('lokasi_id', $request->lokasi_id);
        }

        if ($request->has('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('tahun_perolehan')) {
            $query->whereYear('tanggal_perolehan', $request->tahun_perolehan);
        }

        $inventaris = $query->orderBy('kode_inventaris')->paginate(50);
        $lokasi = MasterSubLokasi::all();

        return view('laporan.inventaris', compact('inventaris', 'lokasi'));
    }

    public function mutasi(Request $request)
    {
        $query = MutasiAset::with(['pengadaan', 'lokasiAsal', 'lokasiTujuan', 'createdBy', 'approvedBy']);

        if ($request->has('jenis_mutasi')) {
            $query->where('jenis_mutasi', $request->jenis_mutasi);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('tanggal_mulai') && $request->has('tanggal_akhir')) {
            $query->whereBetween('tanggal_mutasi', [$request->tanggal_mulai, $request->tanggal_akhir]);
        }

        $mutasi = $query->orderBy('tanggal_mutasi', 'desc')->paginate(50);

        return view('laporan.mutasi', compact('mutasi'));
    }

    public function opname(Request $request)
    {
        $query = OpnameSession::with(['lokasi', 'createdBy']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('tanggal_mulai') && $request->has('tanggal_akhir')) {
            $query->whereBetween('tanggal_mulai', [$request->tanggal_mulai, $request->tanggal_akhir]);
        }

        $opname = $query->orderBy('tanggal_mulai', 'desc')->paginate(50);

        return view('laporan.opname', compact('opname'));
    }

    public function peminjaman(Request $request)
    {
        $query = Peminjaman::with(['pengadaan', 'peminjam', 'approvedBy']);

        if ($request->has('status_peminjaman')) {
            $query->where('status_peminjaman', $request->status_peminjaman);
        }

        if ($request->has('tanggal_mulai') && $request->has('tanggal_akhir')) {
            $query->whereBetween('tanggal_pinjam', [$request->tanggal_mulai, $request->tanggal_akhir]);
        }

        $peminjaman = $query->orderBy('tanggal_pinjam', 'desc')->paginate(50);

        return view('laporan.peminjaman', compact('peminjaman'));
    }

    public function nilaiAset(Request $request)
    {
        $query = PengadaanBarang::aktif()->with(['lokasi', 'kategori']);

        if ($request->has('lokasi_id')) {
            $query->where('lokasi_id', $request->lokasi_id);
        }

        if ($request->has('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        $inventaris = $query->orderBy('kode_inventaris')->get();

        $summary = [
            'total_aset' => $inventaris->count(),
            'total_nilai' => $inventaris->sum('harga_satuan'),
            'per_kategori' => $inventaris->groupBy('kategori.nama_kategori')->map(function($items) {
                return [
                    'jumlah' => $items->count(),
                    'nilai' => $items->sum('harga_satuan'),
                ];
            }),
            'per_lokasi' => $inventaris->groupBy('lokasi.nama_sub_lokasi')->map(function($items) {
                return [
                    'jumlah' => $items->count(),
                    'nilai' => $items->sum('harga_satuan'),
                ];
            }),
        ];

        $lokasi = MasterSubLokasi::all();

        return view('laporan.nilai-aset', compact('inventaris', 'summary', 'lokasi'));
    }

    public function exportInventaris(Request $request)
    {
        $filters = $request->only(['lokasi_id', 'kategori_id', 'is_active', 'tahun_perolehan']);
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
        $filters = $request->only(['lokasi_id', 'kategori_id']);
        return Excel::download(new LaporanNilaiAsetExport($filters), 'laporan-nilai-aset-' . date('Y-m-d') . '.xlsx');
    }
}
