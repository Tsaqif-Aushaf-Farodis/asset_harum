<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterBarang extends Model
{
    use HasFactory;

    protected $table = 'master_barang';

    public const JENIS_PERALATAN = 'peralatan';
    public const JENIS_PERLENGKAPAN = 'perlengkapan';

    protected $fillable = [
       'kode_barang',
        'nama_barang',
        'merk_barang',
        'tipe_barang',
        'tahun_barang',
        'deskripsi_barang',
        'kategori_barang_id',
        'jenis_barang',
        'disusutkan',
        'masa_pemakaian_bulan',
        'interval_penyusutan_tahun',
        'butuh_perawatan',
        'is_active',
        'status_permohonan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'disusutkan' => 'boolean',
        'butuh_perawatan' => 'boolean',
        'masa_pemakaian_bulan' => 'integer',
        'interval_penyusutan_tahun' => 'integer',
    ];

    public function isPeralatan(): bool
    {
        return $this->jenis_barang !== self::JENIS_PERLENGKAPAN;
    }

    public function isPerlengkapan(): bool
    {
        return $this->jenis_barang === self::JENIS_PERLENGKAPAN;
    }

    public function scopePeralatan($query)
    {
        return $query->where('jenis_barang', self::JENIS_PERALATAN);
    }

    public function scopePerlengkapan($query)
    {
        return $query->where('jenis_barang', self::JENIS_PERLENGKAPAN);
    }

    /**
     * Masa pemakaian dalam satuan tampilan: TAHUN untuk Peralatan, BULAN untuk Perlengkapan.
     */
    public function getMasaPemakaianAttribute(): ?float
    {
        if (!$this->masa_pemakaian_bulan) {
            return null;
        }

        return $this->isPerlengkapan()
            ? (float) $this->masa_pemakaian_bulan
            : round($this->masa_pemakaian_bulan / 12, 2);
    }

    public function getMasaPemakaianLabelAttribute(): string
    {
        $masa = $this->masa_pemakaian;
        if ($masa === null) {
            return '-';
        }

        $angka = rtrim(rtrim(number_format($masa, 2, ',', '.'), '0'), ',');

        return $angka . ($this->isPerlengkapan() ? ' bulan' : ' tahun');
    }

    public function pengadaan()
    {
        return $this->hasMany(PengadaanBarang::class, 'barang_id');
    }

    /**
     * Scope untuk filter barang yang sudah approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status_permohonan', 'approved')
                     ->where('is_active', true);
    }

    /**
     * Scope untuk filter barang pending
     */
    public function scopePending($query)
    {
        return $query->where('status_permohonan', 'pending')
                     ->where('is_active', true);
    }

    /**
     * Relasi ke tabel kategori_barang
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriBarang::class, 'kategori_barang_id');
    }

    public function detailPermohonans()
    {
        return $this->hasMany(DetailPermohonan::class, 'barang_id');
    }

    /**
     * Relasi ke user yang membuat data
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke user yang mengupdate data
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
