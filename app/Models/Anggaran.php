<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Anggaran extends Model
{
    protected $table = 'anggaran';

    protected $fillable = [
        'tahun',
        'lokasi_id',
        'pagu',
        'keterangan',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'pagu' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function lokasi()
    {
        return $this->belongsTo(MasterLokasi::class, 'lokasi_id');
    }

    public function pengadaan()
    {
        return $this->hasMany(PengadaanBarang::class, 'anggaran_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Tambahkan kolom `realisasi_total` (Σ total_harga pengadaan ter-link, hibah dikecualikan).
     */
    public function scopeWithRealisasi(Builder $query): Builder
    {
        return $query->withSum(['pengadaan as realisasi_total' => function ($q) {
            $q->where('status', '!=', 'hibah');
        }], 'total_harga');
    }

    public function getLabelAttribute(): string
    {
        return 'Anggaran ' . $this->tahun . ' – ' . ($this->lokasi->nama_lokasi ?? '-');
    }

    public function getRealisasiAttribute(): float
    {
        if (array_key_exists('realisasi_total', $this->attributes)) {
            return (float) $this->attributes['realisasi_total'];
        }

        return (float) $this->pengadaan()->where('status', '!=', 'hibah')->sum('total_harga');
    }

    public function getSisaAttribute(): float
    {
        return (float) $this->pagu - $this->realisasi;
    }

    public function getPersenRealisasiAttribute(): float
    {
        $pagu = (float) $this->pagu;

        return $pagu > 0 ? round($this->realisasi / $pagu * 100, 2) : 0.0;
    }
}
