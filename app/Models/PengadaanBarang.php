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
        'tanggal_perolehan',
        'jumlah',
        'satuan_id',
        'harga_satuan',
        'total_harga',
        'keterangan',
        'permohonan_id',
        'detail_permohonan_id',
        'is_active',
        'is_borrowed',
        'current_user',
        'created_by',
        'updated_by'
    ];

    public function barang()
    {
        return $this->belongsTo(MasterBarang::class, 'barang_id');
    }

    public function kategori()
    {
        return $this->hasOneThrough(
            KategoriBarang::class,
            MasterBarang::class,
            'id', // Foreign key on MasterBarang
            'id', // Foreign key on KategoriBarang
            'barang_id', // Local key on PengadaanBarang
            'kategori_barang_id' // Local key on MasterBarang
        );
    }

    public function lokasi()
    {
        return $this->belongsTo(MasterSubLokasi::class, 'lokasi_id');
    }

    public function subLokasi()
    {
        return $this->belongsTo(MasterSubLokasi::class, 'lokasi_id');
    }
   
    public function statusKondisi()
    {
        return $this->belongsTo(MasterStatus::class, 'status_id');
    }
    
    public function status()
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

     public function tanahDetail()
    {
        return $this->hasOne(TanahDetail::class, 'pengadaan_id');
    }

    public function bangunanDetail()
    {
        return $this->hasOne(BangunanDetail::class, 'pengadaan_id');
    }

    public function kendaraanDetail()
    {
        return $this->hasOne(KendaraanDetail::class, 'pengadaan_id');
    }

    public function permohonan()
    {
        return $this->belongsTo(Permohonan::class, 'permohonan_id');
    }

    public function detailPermohonan()
    {
        return $this->belongsTo(DetailPermohonan::class, 'detail_permohonan_id');
    }

    public function mutasiAset()
    {
        return $this->hasMany(MutasiAset::class, 'pengadaan_id');
    }

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'pengadaan_id');
    }

    public function opnameDetail()
    {
        return $this->hasMany(OpnameDetail::class, 'pengadaan_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDipinjam($query)
    {
        return $query->where('is_borrowed', true);
    }
}
