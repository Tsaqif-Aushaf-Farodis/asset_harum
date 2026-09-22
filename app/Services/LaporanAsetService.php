<?php

namespace App\Services;

use App\Models\Anggaran;
use App\Models\PengadaanBarang;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Pembangun data laporan nilai aset, penyusutan, dan realisasi anggaran.
 * Dipakai bersama oleh halaman laporan dan export Excel agar angkanya identik.
 */
class LaporanAsetService
{
    public static function tanggalHitung(array $f): Carbon
    {
        return !empty($f['per_tanggal']) ? Carbon::parse($f['per_tanggal'])->startOfDay() : Carbon::today();
    }

    /**
     * Nilai aset: Peralatan (nilai buku) dan/atau Perlengkapan (nilai persediaan).
     *
     * @param  array{jenis?: mixed, lokasi_id?: mixed, kategori_id?: mixed, per_tanggal?: mixed}  $f
     */
    public static function nilaiAset(array $f): array
    {
        $asOf = self::tanggalHitung($f);

        $query = PengadaanBarang::aktif()
            ->with(['barang.kategori', 'lokasi.lokasi', 'satuan'])
            ->withSum('pemakaian', 'jumlah');

        if (($f['jenis'] ?? '') === 'peralatan') {
            $query->peralatan();
        } elseif (($f['jenis'] ?? '') === 'perlengkapan') {
            $query->perlengkapan();
        }

        if (!empty($f['lokasi_id'])) {
            $query->where('lokasi_id', $f['lokasi_id']);
        }

        if (!empty($f['kategori_id'])) {
            $query->whereHas('barang', fn ($q) => $q->where('kategori_barang_id', $f['kategori_id']));
        }

        $rows = $query->orderBy('kode_inventaris')->get()->map(function (PengadaanBarang $p) use ($asOf) {
            $perolehan = (float) $p->total_harga;
            $sekarang = $p->nilaiSaatIni($asOf);

            return (object) [
                'pengadaan' => $p,
                'jenis' => $p->isPerlengkapan() ? 'Perlengkapan' : 'Peralatan',
                'nilai_perolehan' => $perolehan,
                'pengurang' => round($perolehan - $sekarang, 2),
                'nilai_saat_ini' => $sekarang,
            ];
        });

        $ringkas = fn (Collection $items) => [
            'jumlah' => $items->count(),
            'perolehan' => $items->sum('nilai_perolehan'),
            'nilai' => $items->sum('nilai_saat_ini'),
        ];

        return [
            'as_of' => $asOf,
            'rows' => $rows,
            'summary' => [
                'total_aset' => $rows->count(),
                'total_perolehan' => $rows->sum('nilai_perolehan'),
                'total_nilai' => $rows->sum('nilai_saat_ini'),
                'per_kategori' => $rows->groupBy(fn ($r) => $r->pengadaan->barang->kategori->nama_kategori_barang ?? '-')->map($ringkas),
                'per_lokasi' => $rows->groupBy(fn ($r) => $r->pengadaan->lokasi->nama_sub_lokasi ?? '-')->map($ringkas),
            ],
        ];
    }

    /**
     * Laporan penyusutan Peralatan per tanggal tertentu.
     *
     * @param  array{lokasi_id?: mixed, kategori_id?: mixed, per_tanggal?: mixed, semua?: mixed}  $f
     */
    public static function penyusutan(array $f): array
    {
        $asOf = self::tanggalHitung($f);

        $query = PengadaanBarang::peralatan()->aktif()->with(['barang.kategori', 'lokasi.lokasi']);

        if (!empty($f['lokasi_id'])) {
            $query->where('lokasi_id', $f['lokasi_id']);
        }

        if (!empty($f['kategori_id'])) {
            $query->whereHas('barang', fn ($q) => $q->where('kategori_barang_id', $f['kategori_id']));
        }

        $rows = $query->orderBy('kode_inventaris')->get()
            ->map(fn (PengadaanBarang $p) => (object) ['pengadaan' => $p, 'susut' => $p->penyusutan($asOf)]);

        // Default hanya aset yang disusutkan; "semua" menampilkan juga yang tidak disusutkan.
        if (empty($f['semua'])) {
            $rows = $rows->filter(fn ($r) => $r->susut['disusutkan'])->values();
        }

        return [
            'as_of' => $asOf,
            'rows' => $rows,
            'summary' => [
                'jumlah' => $rows->count(),
                'nilai_perolehan' => $rows->sum(fn ($r) => $r->susut['nilai_perolehan']),
                'akumulasi' => $rows->sum(fn ($r) => $r->susut['akumulasi']),
                'nilai_buku' => $rows->sum(fn ($r) => $r->susut['nilai_buku']),
            ],
        ];
    }

    /**
     * Realisasi anggaran per lokasi untuk satu tahun, plus baris "belum dikaitkan".
     *
     * @param  array{tahun?: mixed, lokasi_id?: mixed}  $f
     */
    public static function realisasiAnggaran(array $f): array
    {
        $tahun = !empty($f['tahun']) ? (int) $f['tahun'] : (int) date('Y');

        $query = Anggaran::with('lokasi')->withRealisasi()->where('tahun', $tahun)->orderBy('lokasi_id');
        if (!empty($f['lokasi_id'])) {
            $query->where('lokasi_id', $f['lokasi_id']);
        }
        $rows = $query->get();

        $tanpa = PengadaanBarang::whereNull('anggaran_id')
            ->where('status', '!=', 'hibah')
            ->whereYear('tanggal_pengadaan', $tahun);
        $tanpaAnggaran = [
            'jumlah' => (clone $tanpa)->count(),
            'nilai' => (float) $tanpa->sum('total_harga'),
        ];

        $pagu = $rows->sum(fn ($a) => (float) $a->pagu);
        $realisasi = $rows->sum(fn ($a) => $a->realisasi);

        return [
            'tahun' => $tahun,
            'rows' => $rows,
            'tanpa_anggaran' => $tanpaAnggaran,
            'summary' => [
                'pagu' => $pagu,
                'realisasi' => $realisasi,
                'sisa' => $pagu - $realisasi,
                'persen' => $pagu > 0 ? round($realisasi / $pagu * 100, 2) : 0.0,
            ],
        ];
    }
}
