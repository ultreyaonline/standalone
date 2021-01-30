<?php

namespace App\Models;

use App\Enums\WeekendVisibleTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Traits\LogsActivity;

class WeekendAssignments extends Model
{
    use LogsActivity;
    use HasFactory;

    protected static $logName = 'team-assignments';
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;

//integer('weekendID')->unsigned()->references('id')->on('weekends')->index('byweekendid');
//integer('memberID')->unsigned()->references('id')->on('users')->index('bymember');
//integer('roleID')->unsigned()->references('id')->on('weekend_roles')->index('byrole');
//integer('confirmed')->default(0)->index('byconfirmed');
//string('comments')->default('');
//timestamps();

    protected $fillable = ['weekendID', 'memberID', 'roleID', 'confirmed', 'comments'];
    protected $casts = ['confirmed' => 'integer'];
    protected $with = 'role';

    /**
     * Route Model Binding
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'weekendID';
    }


    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('visibleWeekendsOnly', function (Builder $builder) {
            $builder->whereIn(
                'weekendID',
                Weekend::where('visibility_flag', '>=', WeekendVisibleTo::Community)->get()->pluck('id')
            );
        });
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'memberID');
    }

    public function role()
    {
        return $this->belongsTo(WeekendRoles::class, 'roleID');
    }

    public function weekend()
    {
        return $this->belongsTo(Weekend::class, 'weekendID');
    }

    public function getModifiedInLastThreeWeeksAttribute()
    {
        return $this->attributes['updated_at'] > Carbon::now()->addDays(-21);
    }
}
