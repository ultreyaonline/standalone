<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class FailedLoginAttempt extends Model
{
    use LogsActivity;

    protected $fillable = [
        'user_id', 'username', 'ip_address',
    ];

    protected static $logFillable = true;

    public static function record($user = null, $username, $ip)
    {
        return static::create([
            'user_id' => is_null($user) ? null : $user->id,
            'username' => $username,
            'ip_address' => $ip
        ]);
    }
}
