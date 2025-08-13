<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BangunanDetail extends Model
{
    use HasFactory;

    protected $table = 'bangunan_details';

    protected $fillable = [
        'pengadaan_id',
        'alamat',
        'luas',
        'jumlah_lantai',
        'bahan_bangunan',
        'nomor_imb',
        'tanggal_imb',
        'kondisi'
    ];

    public function pengadaan()
    {
        return $this->belongsTo(PengadaanBarang::class, 'pengadaan_id');
    }
    
}
