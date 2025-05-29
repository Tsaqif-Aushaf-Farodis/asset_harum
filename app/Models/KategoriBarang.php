<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriBarang extends Model
{
    protected $table = 'kategori_barang';

    protected $fillable = [
        'kode_kategori_barang',
        'nama_kategori_barang',
        'deskripsi_kategori_barang',
        'status_kategori_barang',
    ];

    public function getStatusLabelAttribute()
    {
        return $this->status_kategori_barang === 'aktif' ? 'Aktif' : 'Nonaktif';
    }
}
