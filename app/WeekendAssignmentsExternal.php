<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class WeekendAssignmentsExternal extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;

    // memberID
    // WeekendName
    // RoleName

    protected $table = 'weekend_assignments_external';
    protected $fillable = ['memberID', 'WeekendName', 'RoleName'];

    public function user()
    {
        return $this->belongsTo(User::class, 'memberID');
    }

    public function getWeekendShortnameAttribute()
    {
        return trim(preg_replace('/(Men|Women)[:\'s\s]*/', '', $this->attributes['WeekendName']));
    }
}
