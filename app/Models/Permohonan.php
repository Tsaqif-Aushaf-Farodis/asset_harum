<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permohonan extends Model
{
    use HasFactory;

    protected $table = 'permohonan';

    protected $fillable = [
        'bidang',
        'tahun_anggaran',
        'unit_kegiatan',
        'keterangan',
        'status',
    ];

    public function details()
    {
        return $this->hasMany(DetailPermohonan::class, 'permohonan_id');
    }
}
