<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterBarang extends Model
{
    use HasFactory;

    protected $table = 'master_barang';

    protected $fillable = [
       'kode_barang',
        'nama_barang',
        'merk_barang',
        'tipe_barang',
        'tahun_barang',
        'deskripsi_barang',
        'kategori_barang_id',
        'is_active',
        'status_permohonan',
        'created_by',
        'updated_by',
    ];

    /**
     * Scope untuk filter barang yang sudah approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status_permohonan', 'approved')
                     ->where('is_active', true);
    }

    /**
     * Scope untuk filter barang pending
     */
    public function scopePending($query)
    {
        return $query->where('status_permohonan', 'pending')
                     ->where('is_active', true);
    }

    /**
     * Relasi ke tabel kategori_barang
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriBarang::class, 'kategori_barang_id');
    }

    public function detailPermohonans()
    {
        return $this->hasMany(DetailPermohonan::class, 'barang_id');
    }

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
