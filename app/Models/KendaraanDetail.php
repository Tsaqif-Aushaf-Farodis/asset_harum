<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KendaraanDetail extends Model
{
    use HasFactory;

    protected $table = 'kendaraan_details';

    protected $fillable = [
        'pengadaan_id',
        'merk',
        'tipe',
        'tahun_perakitan',
        'no_polisi',
        'no_rangka',
        'no_mesin',
        'kapasitas_cc',
        'warna',
        'kondisi'
    ];

    public function pengadaan()
    {
        return $this->belongsTo(PengadaanBarang::class, 'pengadaan_id');
    }
}
