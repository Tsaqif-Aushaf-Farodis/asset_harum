<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UsersLoginLog
 * 
 * @property int $id
 * @property int|null $user_id
 * @property string $username
 * @property string $ip_address
 * @property string $user_agent
 * @property string|null $browser
 * @property string|null $platform
 * @property string|null $device
 * @property string $status
 * @property string|null $failed_reason
 * @property Carbon|null $created_at
 * 
 * @property User|null $user
 *
 * @package App\Models
 */
class UsersLoginLog extends Model
{
	protected $table = 'users_login_logs';
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'username',
		'ip_address',
		'user_agent',
		'browser',
		'platform',
		'device',
		'status',
		'failed_reason',
		'created_at'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
