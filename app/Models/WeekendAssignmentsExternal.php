<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class WeekendAssignmentsExternal extends Model
{
    use LogsActivity;
    use HasFactory;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('team-assignments')
            ->logAll()
            ->logOnlyDirty();
    }

}
