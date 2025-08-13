<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TanahDetail extends Model
{
    use HasFactory;

    protected $table = 'tanah_details';

    protected $fillable = [
        'pengadaan_id',
        'luas',
        'status_tanah',
        'sertifikat_nomor',
        'sertifikat_tanggal',
        'penggunaan',
        'lokasi'
    ];

    public function pengadaan()
    {
        return $this->belongsTo(PengadaanBarang::class, 'pengadaan_id');
    }
}
