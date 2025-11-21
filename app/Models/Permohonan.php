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
        'approved_by',
        'approved_at',
        'catatan_approval',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function details()
    {
        return $this->hasMany(DetailPermohonan::class, 'permohonan_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function pengadaanBarang()
    {
        return $this->hasMany(PengadaanBarang::class, 'permohonan_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeTahunAnggaran($query, $tahun)
    {
        return $query->where('tahun_anggaran', $tahun);
    }
}
