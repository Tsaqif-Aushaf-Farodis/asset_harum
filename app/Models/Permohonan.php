<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permohonan extends Model
{
    protected $table = 'permohonan';
    protected $guarded = [];

    public function details(): HasMany
    {
        return $this->hasMany(DetailPermohonan::class, 'permohonan_id');
    }
}
