<?php

namespace App\Console\Commands;

use App\Models\KategoriBarang;
use App\Models\MasterBarang;
use App\Models\MutasiAset;
use App\Models\OpnameDetail;
use App\Models\Peminjaman;
use App\Models\PengadaanBarang;
use Illuminate\Console\Command;

/**
 * Mengisi jenis, saklar penyusutan, umur pakai, dan interval pada master barang
 * lama berdasarkan KATEGORI, memakai tabel usulan awal (dapat diubah per barang
 * di Master Barang setelahnya).
 *
 * Default hanya menampilkan laporan (dry-run). Tambahkan --apply untuk menyimpan.
 */
class TerapkanUmurPakaian extends Command
{
    protected $signature = 'simaset:terapkan-umur-pakai
        {--apply : Simpan perubahan (tanpa opsi ini hanya laporan dry-run)}
        {--timpa : Timpa juga master barang yang sudah pernah diatur (default: lewati)}';

    protected $description = 'Isi jenis/penyusutan master barang lama berdasarkan kategori (dry-run bila tanpa --apply)';

    /**
     * Tabel usulan awal: nama kategori (huruf kecil) => [jenis, disusutkan, umur pakai (tahun), interval (tahun)].
     * Angka bersifat usulan berdasarkan kelaziman umum, bukan aturan resmi.
     */
    private const USULAN = [
        'elektronik' => ['peralatan', true, 4, 1],
        'komputer & it' => ['peralatan', true, 4, 1],
        'furniture' => ['peralatan', true, 5, 1],
        'kendaraan' => ['peralatan', true, 8, 1],
        'bangunan' => ['peralatan', true, 20, 1],
        'tanah' => ['peralatan', false, null, 1],
        'olahraga' => ['peralatan', true, 2, 1],
        'kitab & buku' => ['peralatan', false, null, 1],
        'alat tulis kantor' => ['perlengkapan', false, null, 1],
    ];

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');
        $timpa = (bool) $this->option('timpa');

        $this->info($apply ? 'MODE APPLY: perubahan akan disimpan.' : 'MODE DRY-RUN: tidak ada yang disimpan (tambahkan --apply untuk menyimpan).');

        $kategori = KategoriBarang::orderBy('nama_kategori_barang')->get();
        $tidakCocok = [];
        $ringkasan = [];
        $reklasifikasi = [];

        foreach ($kategori as $kat) {
            $aturan = self::USULAN[mb_strtolower(trim($kat->nama_kategori_barang))] ?? null;
            if (!$aturan) {
                $tidakCocok[] = $kat->nama_kategori_barang;
                continue;
            }

            [$jenis, $disusutkan, $tahun, $interval] = $aturan;

            $barang = MasterBarang::where('kategori_barang_id', $kat->id)->get();
            $diubah = 0;
            $dilewati = 0;

            foreach ($barang as $b) {
                // Anggap "sudah diatur" bila sudah berjenis perlengkapan atau punya pengaturan penyusutan.
                $sudahDiatur = $b->jenis_barang !== 'peralatan' || $b->disusutkan || $b->masa_pemakaian_bulan;
                if ($sudahDiatur && !$timpa) {
                    $dilewati++;
                    continue;
                }

                if ($b->jenis_barang !== $jenis && $b->pengadaan()->exists()) {
                    $reklasifikasi[] = $b;
                }

                $diubah++;

                if ($apply) {
                    $b->update([
                        'jenis_barang' => $jenis,
                        'disusutkan' => $disusutkan,
                        'masa_pemakaian_bulan' => $tahun ? $tahun * 12 : null,
                        'interval_penyusutan_tahun' => $interval,
                    ]);
                }
            }

            $ringkasan[] = [
                $kat->nama_kategori_barang,
                ucfirst($jenis),
                $disusutkan ? 'Ya, ' . $tahun . ' th (tiap ' . $interval . ' th)' : 'Tidak',
                $barang->count(),
                $diubah,
                $dilewati,
            ];
        }

        $this->table(['Kategori', 'Jenis', 'Disusutkan', 'Jumlah Barang', $apply ? 'Diubah' : 'Akan Diubah', 'Dilewati'], $ringkasan);

        if ($tidakCocok) {
            $this->warn('Kategori tanpa aturan usulan (tidak diubah): ' . implode(', ', $tidakCocok));
        }

        if ($reklasifikasi) {
            $this->newLine();
            $this->warn('Barang yang PINDAH JENIS dan sudah punya pengadaan (perlu tinjauan):');
            $baris = [];
            foreach ($reklasifikasi as $b) {
                $ids = PengadaanBarang::where('barang_id', $b->id)->pluck('id');
                $baris[] = [
                    $b->kode_barang,
                    $b->nama_barang,
                    $ids->count(),
                    MutasiAset::whereIn('pengadaan_id', $ids)->count(),
                    Peminjaman::whereIn('pengadaan_id', $ids)->count(),
                    OpnameDetail::whereIn('pengadaan_id', $ids)->count(),
                ];
            }
            $this->table(['Kode', 'Barang', 'Pengadaan', 'Mutasi', 'Peminjaman', 'Opname'], $baris);
            $this->line('Pengadaan tersebut akan menjadi batch stok Perlengkapan penuh (0 pemakaian) dan hilang dari daftar aset. Riwayat mutasi/peminjaman/opname lama tetap tersimpan.');
        }

        if (!$apply) {
            $this->newLine();
            $this->comment('Dry-run selesai. Jalankan dengan --apply setelah tabel usulan disetujui.');
        }

        return self::SUCCESS;
    }
}
