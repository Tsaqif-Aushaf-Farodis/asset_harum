<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpnameSession extends Model
{
    protected $table = 'opname_session';

    protected $fillable = [
        'nama_opname',
        'tanggal_mulai',
        'tanggal_selesai',
        'lokasi_id',
        'status',
        'keterangan',
        'created_by'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date'
    ];

    public function lokasi()
    {
        return $this->belongsTo(MasterSubLokasi::class, 'lokasi_id');
    }

    public function details()
    {
        return $this->hasMany(OpnameDetail::class, 'opname_session_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
