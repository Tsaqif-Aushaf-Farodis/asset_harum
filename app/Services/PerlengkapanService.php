<?php

namespace App\Services;

use App\Models\MasterBarang;
use App\Models\PemakaianPerlengkapan;
use App\Models\PengadaanBarang;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Stok & pemakaian Perlengkapan.
 * Stok = jumlah masuk (pengadaan) − Σ pemakaian; tidak ada kolom sisa tersimpan.
 */
class PerlengkapanService
{
    /**
     * Batch stok masuk (pengadaan barang berjenis Perlengkapan) beserta sisa stoknya.
     *
     * @param  array{lokasi_id?: mixed, barang_id?: mixed, search?: mixed, hanya_sisa?: mixed}  $filters
     */
    public static function batch(array $filters = []): Collection
    {
        $query = PengadaanBarang::perlengkapan()
            ->aktif()
            ->with(['barang.kategori', 'satuan', 'lokasi.lokasi'])
            ->withSum('pemakaian', 'jumlah')
            ->orderBy('tanggal_pengadaan')
            ->orderBy('id');

        if (!empty($filters['lokasi_id'])) {
            $query->where('lokasi_id', $filters['lokasi_id']);
        }

        if (!empty($filters['barang_id'])) {
            $query->where('barang_id', $filters['barang_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('kode_inventaris', 'like', "%$search%")
                    ->orWhereHas('barang', fn ($b) => $b->where('nama_barang', 'like', "%$search%"));
            });
        }

        $batch = $query->get();

        if (!empty($filters['hanya_sisa'])) {
            $batch = $batch->filter(fn ($b) => $b->stok_tersedia > 0)->values();
        }

        return $batch;
    }

    /** Ringkasan per barang dari kumpulan batch. */
    public static function ringkasan(Collection $batch): Collection
    {
        return $batch->groupBy('barang_id')->map(function (Collection $items) {
            $barang = $items->first()->barang;

            return (object) [
                'barang' => $barang,
                'satuan' => $items->first()->satuan?->nama_satuan,
                'masuk' => $items->sum('jumlah'),
                'terpakai' => $items->sum(fn ($b) => $b->stok_terpakai),
                'sisa' => $items->sum(fn ($b) => $b->stok_tersedia),
                'nilai_persediaan' => $items->sum(fn ($b) => $b->nilaiSaatIni()),
            ];
        })->sortBy(fn ($r) => $r->barang->nama_barang)->values();
    }

    /** Total sisa stok per barang_id (semua batch aktif). */
    public static function sisaPerBarang(): Collection
    {
        return self::batch()->groupBy('barang_id')->map(fn ($items) => $items->sum(fn ($b) => $b->stok_tersedia));
    }

    /**
     * Catat pemakaian perlengkapan. Stok diambil dari batch tertua yang masih
     * punya sisa (FIFO); bila melewati satu batch dibuat beberapa baris pemakaian.
     * Seluruhnya dalam satu transaksi, dengan kunci baris batch agar stok tidak
     * bisa menjadi negatif pada input bersamaan.
     *
     * @return Collection<int, PemakaianPerlengkapan>
     */
    public function catat(
        int $barangId,
        int $jumlah,
        $tanggalPemakaian,
        int $lokasiId,
        string $pemakai,
        ?string $keperluan,
        ?int $userId
    ): Collection {
        $barang = MasterBarang::find($barangId);
        if (!$barang || !$barang->isPerlengkapan()) {
            throw ValidationException::withMessages(['barang_id' => 'Barang yang dipilih bukan Perlengkapan.']);
        }

        $tanggal = Carbon::parse($tanggalPemakaian)->toDateString();

        return DB::transaction(function () use ($barangId, $jumlah, $tanggal, $lokasiId, $pemakai, $keperluan, $userId) {
            $batch = PengadaanBarang::where('barang_id', $barangId)
                ->aktif()
                ->whereDate('tanggal_pengadaan', '<=', $tanggal)
                ->orderBy('tanggal_pengadaan')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $sisaBatch = [];
            foreach ($batch as $b) {
                $terpakai = (int) PemakaianPerlengkapan::where('pengadaan_id', $b->id)->sum('jumlah');
                $sisaBatch[$b->id] = max(0, (int) $b->jumlah - $terpakai);
            }

            $tersedia = array_sum($sisaBatch);
            if ($jumlah > $tersedia) {
                throw ValidationException::withMessages([
                    'jumlah' => "Stok tidak cukup. Tersedia {$tersedia} pada tanggal tersebut, diminta {$jumlah}.",
                ]);
            }

            $hasil = collect();
            $perlu = $jumlah;

            foreach ($batch as $b) {
                if ($perlu <= 0) {
                    break;
                }

                $ambil = min($perlu, $sisaBatch[$b->id]);
                if ($ambil <= 0) {
                    continue;
                }

                $hasil->push(PemakaianPerlengkapan::create([
                    'pengadaan_id' => $b->id,
                    'jumlah' => $ambil,
                    'tanggal_pemakaian' => $tanggal,
                    'lokasi_id' => $lokasiId,
                    'pemakai' => $pemakai,
                    'keperluan' => $keperluan,
                    'harga_satuan' => $b->harga_satuan,
                    'created_by' => $userId,
                ]));

                $perlu -= $ambil;
            }

            return $hasil;
        });
    }
}
