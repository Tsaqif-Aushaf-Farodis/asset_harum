<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instansi extends Model
{
    protected $table = 'instansi';

    protected $fillable = [
        'nama_instansi',
        'alamat',
        'telepon',
        'email',
        'logo',
        'deskripsi',
    ];

    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }
}
