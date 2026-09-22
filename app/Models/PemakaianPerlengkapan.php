<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PemakaianPerlengkapan extends Model
{
    use SoftDeletes;

    protected $table = 'pemakaian_perlengkapan';

    protected $fillable = [
        'pengadaan_id',
        'jumlah',
        'tanggal_pemakaian',
        'lokasi_id',
        'pemakai',
        'keperluan',
        'harga_satuan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_pemakaian' => 'date',
        'harga_satuan' => 'decimal:2',
    ];

    public function pengadaan()
    {
        return $this->belongsTo(PengadaanBarang::class, 'pengadaan_id');
    }

    public function lokasi()
    {
        return $this->belongsTo(MasterSubLokasi::class, 'lokasi_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Nilai yang terpakai = jumlah × harga satuan (snapshot). */
    public function getNilaiTerpakaiAttribute(): float
    {
        return round($this->jumlah * (float) $this->harga_satuan, 2);
    }
}
