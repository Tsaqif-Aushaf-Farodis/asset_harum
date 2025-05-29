<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterLokasi extends Model
{
    use HasFactory;

    protected $table = 'master_lokasi';

    protected $fillable = [
        'kode_lokasi',
        'nama_lokasi',
        'deskripsi_lokasi',
        'alamat_lokasi',
        'telepon_lokasi',
        'created_by',
        'updated_by',
    ];

    /**
     * Relasi ke user yang membuat data
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke user yang mengupdate data
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
