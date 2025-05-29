<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserProfile
 *
 * @property int $user_id
 * @property string|null $full_name
 * @property string|null $avatar_url
 * @property string|null $no_telp
 * @property string|null $alamat
 * @property string|null $jenis_kelamin
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $created_by
 * @property string|null $updated_by
 *
 * @property User $user
 *
 * @package App\Models
 */
class UserProfile extends Model
{
    protected $table = 'user_profiles';
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    protected $casts = [
        'user_id' => 'int'
    ];

    protected $fillable = [
        'full_name',
        'avatar_url',
        'no_telp',
        'alamat',
        'jenis_kelamin',
        'created_by',
        'updated_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
