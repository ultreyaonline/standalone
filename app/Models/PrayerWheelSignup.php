<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Traits\LogsActivity;

class PrayerWheelSignup extends Model
{
    use LogsActivity;
    use HasFactory;

    protected static $logName = 'prayer-wheels';
    protected static $logAttributes = ['*'];

    protected $table = 'prayer_wheel_signups';
    protected $touches = ['wheel'];
    protected $guarded = [];

    // This is used for optimal querying for reminders, and for wheel-display
    protected $with = ['weekend', 'user', 'wheel', 'wheel.weekend'];

    protected $casts = [
        'acknowledged_at' => 'datetime',
        'reminded_at' => 'datetime',
    ];

//    $table->unsignedInteger('wheel_id');
//    $table->unsignedTinyInteger('timeslot');
//    $table->unsignedInteger('memberID')->nullable();

    public function wheel(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PrayerWheel::class, 'wheel_id', 'id');
    }

    public function weekend(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Weekend::class, 'weekendID', 'id');
    }

    public function member(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'memberID');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'memberID');
    }

    public function scopeForWeekend(Builder $query, int $weekend)
    {
        return $query->where('weekendID', $weekend)
            ->orderBy('timeslot', 'asc');
    }

    public function getTimeslots()
    {
        return PrayerWheel::getTimeSlots();
        // position  1
        // day  Thursday
        // hour 6:00pm
        // hour_to 6pm-7pm
    }

    public function getTimeslotsAttribute()
    {
        return $this->getTimeslots();
    }

    public function getPositionDetailsAttribute()
    {
        return $this->getTimeslots()->where('position', $this->timeslot)->first();
    }
    public function getSpotnameAttribute()
    {
        $timedetails = $this->getPositionDetailsAttribute();

        return $timedetails['hour_to'];
    }

    public function getSpotdayAttribute()
    {
        $timedetails = $this->getPositionDetailsAttribute();

        return $timedetails['day'];
    }

    public function getSlotDatetimeAttribute()
    {
        $weekend_datetime = $this->weekend ? $this->weekend->start_date : $this->wheel->weekend->start_date;
        $base = $weekend_datetime->setTimeFromTimeString('17:00:00');
        return $base->addHour((int)$this->timeslot);
    }

    public function getSlotDatetimeFormattedAttribute()
    {
        return $this->getSlotDatetimeAttribute()->format('l F j, Y  @ g:i a');
    }

    public function getSlotIsTodayAttribute()
    {
        return $this->getSlotDatetimeAttribute()->isToday();
    }

    public function getSlotIsFutureAttribute()
    {
        return $this->getSlotDatetimeAttribute()->isFuture();
    }

    public function getSlotIsDuringAnActiveWeekendAttribute()
    {
        $weekend_start = $this->weekend ? $this->weekend->start_date : $this->wheel->weekend->start_date;
        $weekend_end = $this->weekend ? $this->weekend->start_date : $this->wheel->weekend->start_date;
        // done this way because Carbon isn't date-time-immutable
        $weekend_start = $weekend_start->subHours(12);
        $weekend_end = $weekend_end->addHours(73);
        return Carbon::now()->between($weekend_start, $weekend_end);
    }

    public function getWeekendDatesAttribute()
    {
        return $this->weekend ? $this->weekend->short_date_range : $this->wheel->weekend->short_date_range;
    }

    public function getWeekendNameAttribute()
    {
        return $this->weekend ? $this->weekend->weekend_full_name : $this->wheel->weekend->weekend_full_name;
    }



    public function getMailtoRecipientAttribute()
    {
        return $this->user;
//        return $this->member ?? $this->non_member_email ?? new User(['email' => $this->non_member_email, 'name' => $this->non_member_name, 'first' => $this->non_member_name, 'last' => '']);
    }

    public function getNameAttribute()
    {
        return $this->user->name;
//        return $this->member ? $this->member->name : $this->non_member_name;
    }

    public function getEmailAttribute()
    {
        return $this->user->email;
//        return $this->member ? $this->member->email : $this->non_member_email;
    }

    public function getRemindersRequestedAttribute()
    {
        return $this->user->receive_prayer_wheel_reminders;
//        return $this->member->receive_prayer_wheel_reminders;
    }
}
