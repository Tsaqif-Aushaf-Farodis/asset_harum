<?php

namespace App\Models;

use App\Services\NilaiBarangService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PengadaanBarang extends Model
{
    protected $table = 'pengadaan_barang';

    protected $fillable = [
        'kode_inventaris',
        'barang_id',
        'lokasi_id',
        'sumber',
        'status',
        'status_id',
        'tanggal_pengadaan',
        'jumlah',
        'satuan_id',
        'harga_satuan',
        'total_harga',
        'keterangan',
        'permohonan_id',
        'detail_permohonan_id',
        'anggaran_id',
        'disusutkan',
        'is_active',
        'is_borrowed',
        'current_user',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'disusutkan' => 'boolean',
    ];

    public function barang()
    {
        return $this->belongsTo(MasterBarang::class, 'barang_id');
    }

    public function kategori()
    {
        return $this->hasOneThrough(
            KategoriBarang::class,
            MasterBarang::class,
            'id', // Foreign key on MasterBarang
            'id', // Foreign key on KategoriBarang
            'barang_id', // Local key on PengadaanBarang
            'kategori_barang_id' // Local key on MasterBarang
        );
    }

    public function lokasi()
    {
        return $this->belongsTo(MasterSubLokasi::class, 'lokasi_id');
    }

    public function subLokasi()
    {
        return $this->belongsTo(MasterSubLokasi::class, 'lokasi_id');
    }
   
    public function statusKondisi()
    {
        return $this->belongsTo(MasterStatus::class, 'status_id');
    }
    
    public function status()
    {
        return $this->belongsTo(MasterStatus::class, 'status_id');
    }
    public function satuan()
    {
        return $this->belongsTo(MasterSatuan::class, 'satuan_id');
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

     public function tanahDetail()
    {
        return $this->hasOne(TanahDetail::class, 'pengadaan_id');
    }

    public function bangunanDetail()
    {
        return $this->hasOne(BangunanDetail::class, 'pengadaan_id');
    }

    public function kendaraanDetail()
    {
        return $this->hasOne(KendaraanDetail::class, 'pengadaan_id');
    }

    public function permohonan()
    {
        return $this->belongsTo(Permohonan::class, 'permohonan_id');
    }

    public function detailPermohonan()
    {
        return $this->belongsTo(DetailPermohonan::class, 'detail_permohonan_id');
    }

    public function mutasiAset()
    {
        return $this->hasMany(MutasiAset::class, 'pengadaan_id');
    }

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'pengadaan_id');
    }

    public function opnameDetail()
    {
        return $this->hasMany(OpnameDetail::class, 'pengadaan_id');
    }

    public function anggaran()
    {
        return $this->belongsTo(Anggaran::class, 'anggaran_id');
    }

    public function pemakaian()
    {
        return $this->hasMany(PemakaianPerlengkapan::class, 'pengadaan_id');
    }

    /** Hanya pengadaan barang berjenis Peralatan (aset). */
    public function scopePeralatan($query)
    {
        return $query->whereHas('barang', fn ($q) => $q->where('jenis_barang', MasterBarang::JENIS_PERALATAN));
    }

    /** Hanya pengadaan barang berjenis Perlengkapan (batch stok masuk). */
    public function scopePerlengkapan($query)
    {
        return $query->whereHas('barang', fn ($q) => $q->where('jenis_barang', MasterBarang::JENIS_PERLENGKAPAN));
    }

    public function scopeButuhPerawatan($query)
    {
        return $query->whereHas('barang', fn ($q) => $q->where('butuh_perawatan', true));
    }

    public function isPerlengkapan(): bool
    {
        return (bool) $this->barang?->isPerlengkapan();
    }

    /**
     * Apakah aset ini disusutkan: penimpa per aset, jika NULL ikut Master Barang.
     * Perlengkapan tidak pernah disusutkan.
     */
    public function getDisusutkanEfektifAttribute(): bool
    {
        if (!$this->barang || $this->barang->isPerlengkapan()) {
            return false;
        }

        return (bool) ($this->disusutkan ?? $this->barang->disusutkan);
    }

    public function getDisusutkanDiaturManualAttribute(): bool
    {
        return $this->disusutkan !== null;
    }

    /** Detail penyusutan per tanggal tertentu (default hari ini). */
    public function penyusutan(?Carbon $asOf = null): array
    {
        $barang = $this->barang;

        return NilaiBarangService::penyusutan(
            (float) $this->total_harga,
            Carbon::parse($this->tanggal_pengadaan),
            $barang?->masa_pemakaian_bulan,
            (int) ($barang?->interval_penyusutan_tahun ?: 1),
            $this->disusutkan_efektif,
            $asOf ?? Carbon::today(),
        );
    }

    /** Total jumlah yang sudah dipakai (Perlengkapan). Memakai withSum bila sudah dimuat. */
    public function getStokTerpakaiAttribute(): int
    {
        if (array_key_exists('pemakaian_sum_jumlah', $this->attributes)) {
            return (int) $this->attributes['pemakaian_sum_jumlah'];
        }

        return (int) $this->pemakaian()->sum('jumlah');
    }

    public function getStokTersediaAttribute(): int
    {
        return max(0, (int) $this->jumlah - $this->stok_terpakai);
    }

    /**
     * Nilai barang saat ini.
     * Peralatan → nilai buku. Perlengkapan → nilai persediaan (sisa × harga satuan);
     * yang sudah dipakai bernilai 0.
     */
    public function nilaiSaatIni(?Carbon $asOf = null): float
    {
        if ($this->isPerlengkapan()) {
            return NilaiBarangService::nilaiPersediaan($this->stok_tersedia, (float) $this->harga_satuan);
        }

        return $this->penyusutan($asOf)['nilai_buku'];
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDipinjam($query)
    {
        return $query->where('is_borrowed', true);
    }
}
