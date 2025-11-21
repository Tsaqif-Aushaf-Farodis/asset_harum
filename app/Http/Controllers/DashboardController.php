<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\PengadaanBarang;
use App\Models\MasterBarang;
use App\Models\Permohonan;
use App\Models\MutasiAset;
use App\Models\OpnameSession;
use App\Models\Peminjaman;
use App\Models\KategoriBarang;
use App\Models\MasterLokasi;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Statistik Utama
        $totalAset = PengadaanBarang::where('is_active', true)->count();
        $totalNilaiAset = PengadaanBarang::where('is_active', true)->sum('total_harga');
        $totalBarang = MasterBarang::where('is_active', true)->count();
        $totalLokasi = MasterLokasi::count();

        // Statistik Permohonan
        $permohonanStats = [
            'total' => Permohonan::count(),
            'pending' => Permohonan::where('status', 'pending')->count(),
            'approved' => Permohonan::where('status', 'approved')->count(),
            'rejected' => Permohonan::where('status', 'rejected')->count(),
        ];

        // Statistik Barang berdasarkan Status Permohonan
        $barangStatusStats = [
            'pending' => MasterBarang::where('status_permohonan', 'pending')->count(),
            'approved' => MasterBarang::where('status_permohonan', 'approved')->count(),
            'rejected' => MasterBarang::where('status_permohonan', 'rejected')->count(),
        ];

        // Statistik Inventaris/Pengadaan
        $inventarisStats = [
            'total' => PengadaanBarang::count(),
            'aktif' => PengadaanBarang::where('is_active', true)->count(),
            'dipinjam' => PengadaanBarang::where('is_borrowed', true)->count(),
            'tahun_ini' => PengadaanBarang::whereYear('tanggal_pengadaan', date('Y'))->count(),
        ];

        // Statistik Mutasi
        $mutasiStats = [
            'total' => MutasiAset::count(),
            'bulan_ini' => MutasiAset::whereYear('tanggal_mutasi', date('Y'))
                ->whereMonth('tanggal_mutasi', date('m'))
                ->count(),
        ];

        // Statistik Peminjaman
        $peminjamanStats = [
            'total' => Peminjaman::count(),
            'aktif' => Peminjaman::where('status_peminjaman', 'borrowed')->count(),
            'dikembalikan' => Peminjaman::where('status_peminjaman', 'returned')->count(),
            'terlambat' => Peminjaman::where('status_peminjaman', 'overdue')->count(),
        ];

        // Statistik Opname
        $opnameStats = [
            'total_sesi' => OpnameSession::count(),
            'selesai' => OpnameSession::where('status', 'completed')->count(),
            'proses' => OpnameSession::where('status', 'ongoing')->count(),
        ];

        // Top 5 Kategori dengan Aset Terbanyak
        $topKategori = PengadaanBarang::select('master_barang.kategori_barang_id', 'kategori_barang.nama_kategori_barang', DB::raw('COUNT(*) as total'))
            ->join('master_barang', 'pengadaan_barang.barang_id', '=', 'master_barang.id')
            ->join('kategori_barang', 'master_barang.kategori_barang_id', '=', 'kategori_barang.id')
            ->where('pengadaan_barang.is_active', true)
            ->groupBy('master_barang.kategori_barang_id', 'kategori_barang.nama_kategori_barang')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // Top 5 Lokasi dengan Aset Terbanyak
        $topLokasi = PengadaanBarang::select('master_sub_lokasi.lokasi_id', 'master_lokasi.nama_lokasi', DB::raw('COUNT(*) as total'))
            ->join('master_sub_lokasi', 'pengadaan_barang.lokasi_id', '=', 'master_sub_lokasi.id')
            ->join('master_lokasi', 'master_sub_lokasi.lokasi_id', '=', 'master_lokasi.id')
            ->where('pengadaan_barang.is_active', true)
            ->groupBy('master_sub_lokasi.lokasi_id', 'master_lokasi.nama_lokasi')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // Grafik Pengadaan Per Bulan (6 bulan terakhir)
        $pengadaanPerBulan = PengadaanBarang::select(
                DB::raw('MONTH(tanggal_pengadaan) as bulan'),
                DB::raw('YEAR(tanggal_pengadaan) as tahun'),
                DB::raw('COUNT(*) as total')
            )
            ->where('tanggal_pengadaan', '>=', now()->subMonths(6))
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();

        // Permohonan Terbaru (5 terakhir)
        $permohonanTerbaru = Permohonan::with(['createdBy', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Aset Terbaru (5 terakhir)
        $asetTerbaru = PengadaanBarang::with(['barang', 'subLokasi.lokasi'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalAset',
            'totalNilaiAset',
            'totalBarang',
            'totalLokasi',
            'permohonanStats',
            'barangStatusStats',
            'inventarisStats',
            'mutasiStats',
            'peminjamanStats',
            'opnameStats',
            'topKategori',
            'topLokasi',
            'pengadaanPerBulan',
            'permohonanTerbaru',
            'asetTerbaru'
        ));
    }
}

