<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSatuan extends Model
{
    protected $table = 'master_satuan';

    protected $fillable = [
        'kode_satuan',
        'nama_satuan',
        'deskripsi_satuan',
        'is_active',
    ];

    public function masterBarang()
    {
        return $this->hasMany(MasterBarang::class, 'satuan_id');
    }
}
