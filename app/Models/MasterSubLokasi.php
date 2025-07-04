<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSubLokasi extends Model
{
    protected $table = 'master_sub_lokasi';

    protected $fillable = [
        'lokasi_id',
        'kode_sub_lokasi',
        'nama_sub_lokasi',
        'keterangan',
    ];

    public function lokasi()
    {
        return $this->belongsTo(MasterLokasi::class, 'lokasi_id');
    }
}
