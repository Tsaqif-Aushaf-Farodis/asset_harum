<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpnameDetail extends Model
{
    protected $table = 'opname_detail';

    protected $fillable = [
        'opname_session_id',
        'pengadaan_id',
        'kondisi_sistem',
        'kondisi_fisik',
        'status_keberadaan',
        'lokasi_fisik',
        'catatan',
        'foto_aset',
        'petugas_opname',
        'tanggal_cek'
    ];

    protected $casts = [
        'tanggal_cek' => 'date'
    ];

    public function session()
    {
        return $this->belongsTo(OpnameSession::class, 'opname_session_id');
    }

    public function pengadaan()
    {
        return $this->belongsTo(PengadaanBarang::class, 'pengadaan_id');
    }
}
