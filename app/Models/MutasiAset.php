<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiAset extends Model
{
    protected $table = 'mutasi_aset';

    protected $fillable = [
        'pengadaan_id',
        'jenis_mutasi',
        'lokasi_asal_id',
        'lokasi_tujuan_id',
        'pengguna_asal',
        'pengguna_tujuan',
        'tanggal_mutasi',
        'alasan',
        'dokumen_pendukung',
        'status_mutasi',
        'created_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'tanggal_mutasi' => 'date',
        'approved_at' => 'datetime'
    ];

    public function pengadaan()
    {
        return $this->belongsTo(PengadaanBarang::class, 'pengadaan_id');
    }

    public function lokasiAsal()
    {
        return $this->belongsTo(MasterSubLokasi::class, 'lokasi_asal_id');
    }

    public function lokasiTujuan()
    {
        return $this->belongsTo(MasterSubLokasi::class, 'lokasi_tujuan_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
