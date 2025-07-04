<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengadaanBarang extends Model
{
    protected $table = 'pengadaan_barang';

    protected $fillable = [
        'kode_inventaris',
        'barang_id',
        'lokasi_id',
        'sumber',
        'status',
        'status_id',
        'tanggal_pengadaan',
        'jumlah',
        'satuan_id',
        'harga_satuan',
        'total_harga',
        'keterangan',
        'created_by',
        'updated_by'
    ];

    public function barang()
    {
        return $this->belongsTo(MasterBarang::class, 'barang_id');
    }

    public function lokasi()
    {
        return $this->belongsTo(MasterSubLokasi::class, 'lokasi_id');
    }
   
    public function statusKondisi()
    {
        return $this->belongsTo(MasterStatus::class, 'status_id');
    }
    public function satuan()
    {
        return $this->belongsTo(MasterSatuan::class, 'satuan_id');
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
