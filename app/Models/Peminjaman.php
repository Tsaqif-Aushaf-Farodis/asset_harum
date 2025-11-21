<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';

    protected $fillable = [
        'pengadaan_id',
        'peminjam_id',
        'peminjam_nama',
        'peminjam_nip',
        'peminjam_instansi',
        'peminjam_telepon',
        'keperluan',
        'tanggal_pinjam',
        'tanggal_rencana_kembali',
        'tanggal_kembali_aktual',
        'kondisi_pinjam_id',
        'kondisi_kembali_id',
        'status_peminjaman',
        'dokumen_peminjaman',
        'catatan',
        'created_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_rencana_kembali' => 'date',
        'tanggal_kembali_aktual' => 'date',
        'approved_at' => 'datetime'
    ];

    public function pengadaan()
    {
        return $this->belongsTo(PengadaanBarang::class, 'pengadaan_id');
    }

    public function peminjam()
    {
        return $this->belongsTo(User::class, 'peminjam_id');
    }

    public function kondisiPinjam()
    {
        return $this->belongsTo(MasterStatus::class, 'kondisi_pinjam_id');
    }

    public function kondisiKembali()
    {
        return $this->belongsTo(MasterStatus::class, 'kondisi_kembali_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status_peminjaman', 'borrowed')
                    ->where('tanggal_rencana_kembali', '<', now())
                    ->whereNull('tanggal_kembali_aktual');
    }
}
