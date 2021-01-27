<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class FailedLoginAttempt extends Model
{
    use LogsActivity;
    use HasFactory;

    protected $fillable = [
        'user_id', 'username', 'ip_address',
    ];

    protected static $logFillable = true;

    public static function record($username, $ip, $user = null)
    {
        return static::create([
            'username' => $username,
            'ip_address' => $ip,
            'user_id' => is_null($user) ? null : $user->id,
        ]);
    }
}
