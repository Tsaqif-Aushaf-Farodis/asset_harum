<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterStatus extends Model
{
    protected $table = 'master_status';

    protected $fillable = [
        'kode_status',
        'nama_status',
    ];

    public function getRouteKeyName()
    {
        return 'kode_status';
    }
}
