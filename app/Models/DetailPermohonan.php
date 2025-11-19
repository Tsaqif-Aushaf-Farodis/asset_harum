<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPermohonan extends Model
{
    use HasFactory;

    protected $table = 'detail_permohonan';

    protected $fillable = [
        'permohonan_id',
        'barang_id',
        'volume',
        'satuan',
        'harga',
        'jumlah',
        'kode_ma',
        'keterangan',
    ];

    public function permohonan()
    {
        return $this->belongsTo(Permohonan::class, 'permohonan_id');
    }

    public function barang()
    {
        return $this->belongsTo(MasterBarang::class, 'barang_id');
    }
}

