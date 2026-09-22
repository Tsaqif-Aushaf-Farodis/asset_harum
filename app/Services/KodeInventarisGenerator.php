<?php

namespace App\Services;

use App\Models\MasterBarang;
use App\Models\MasterSubLokasi;
use App\Models\PengadaanBarang;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Pembuat kode inventaris pengadaan barang.
 * Format: KODE_BARANG-KODE_KATEGORI-NAMA_SUB_LOKASI-MM-YYYY-URUT(4 digit)
 *
 * Nomor urut diambil dari nilai terbesar yang sudah dipakai pada bulan/tahun
 * yang sama (bukan sekadar count()), agar tidak bentrok setelah ada data dihapus.
 */
class KodeInventarisGenerator
{
    /** Nomor urut terakhir yang sudah terpakai pada bulan/tahun dari $tanggal. */
    public static function nomorTerakhir($tanggal): int
    {
        $tgl = Carbon::parse($tanggal);

        $kode = PengadaanBarang::whereMonth('tanggal_pengadaan', $tgl->month)
            ->whereYear('tanggal_pengadaan', $tgl->year)
            ->pluck('kode_inventaris');

        $maks = 0;
        foreach ($kode as $k) {
            if (preg_match('/-(\d+)$/', $k, $m)) {
                $maks = max($maks, (int) $m[1]);
            }
        }

        return max($maks, $kode->count());
    }

    public static function format(MasterBarang $barang, MasterSubLokasi $lokasi, $tanggal, int $urut): string
    {
        $tgl = Carbon::parse($tanggal);

        return sprintf(
            '%s-%s-%s-%s-%s-%04d',
            $barang->kode_barang,
            $barang->kategori->kode_kategori_barang,
            Str::studly($lokasi->nama_sub_lokasi),
            $tgl->format('m'),
            $tgl->format('Y'),
            $urut
        );
    }

    /**
     * Buat kode baru yang dijamin belum dipakai.
     * Untuk batch (import), berikan $urutTerakhir hasil pemanggilan sebelumnya
     * agar nomor bertambah tanpa query ulang.
     */
    public static function buat(MasterBarang $barang, MasterSubLokasi $lokasi, $tanggal, ?int &$urutTerakhir = null): string
    {
        $urut = ($urutTerakhir ?? self::nomorTerakhir($tanggal)) + 1;

        while (PengadaanBarang::where('kode_inventaris', self::format($barang, $lokasi, $tanggal, $urut))->exists()) {
            $urut++;
        }

        $urutTerakhir = $urut;

        return self::format($barang, $lokasi, $tanggal, $urut);
    }
}
