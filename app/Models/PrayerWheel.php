<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class PrayerWheel extends Model
{
    use LogsActivity;
    use HasFactory;

    protected static $logAttributes = ['*'];

    protected $table = 'prayer_wheels';

    protected $guarded = [];

//$table->increments('id');
//$table->unsignedInteger('weekendID')->nullable();
//$table->string('customwheel_name')->nullable();

    public function weekend(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Weekend::class, 'weekendID', 'id');
    }

    public function signups(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PrayerWheelSignup::class, 'wheel_id', 'id')->orderBy('timeslot');
    }


    public static function getTimeSlots(): \Illuminate\Support\Collection
    {
        return collect([
            ['position' => 1, 'index' => 't18', 'day' => 'Thursday', 'hour' => '6:00pm', 'hour_to' => '6pm-7pm'],
            ['position' => 2, 'index' => 't19', 'day' => 'Thursday', 'hour' => '7:00pm', 'hour_to' => '7pm-8pm'],
            ['position' => 3, 'index' => 't20', 'day' => 'Thursday', 'hour' => '8:00pm', 'hour_to' => '8pm-9pm'],
            ['position' => 4, 'index' => 't21', 'day' => 'Thursday', 'hour' => '9:00pm', 'hour_to' => '9pm-10pm'],
            ['position' => 5, 'index' => 't22', 'day' => 'Thursday', 'hour' => '10:00pm', 'hour_to' => '10pm-11pm'],
            ['position' => 6, 'index' => 't23', 'day' => 'Thursday', 'hour' => '11:00pm', 'hour_to' => '11pm-12am'],
            ['position' => 7, 'index' => 'f0', 'day' => 'Friday', 'hour' => '12:00am', 'hour_to' => '12am-1am'],
            ['position' => 8, 'index' => 'f1', 'day' => 'Friday', 'hour' => '1:00am', 'hour_to' => '1am-2am'],
            ['position' => 9, 'index' => 'f2', 'day' => 'Friday', 'hour' => '2:00am', 'hour_to' => '2am-3am'],
            ['position' => 10, 'index' => 'f3', 'day' => 'Friday', 'hour' => '3:00am', 'hour_to' => '3am-4am'],
            ['position' => 11, 'index' => 'f4', 'day' => 'Friday', 'hour' => '4:00am', 'hour_to' => '4am-5am'],
            ['position' => 12, 'index' => 'f5', 'day' => 'Friday', 'hour' => '5:00am', 'hour_to' => '5am-6am'],
            ['position' => 13, 'index' => 'f6', 'day' => 'Friday', 'hour' => '6:00am', 'hour_to' => '6am-7am'],
            ['position' => 14, 'index' => 'f7', 'day' => 'Friday', 'hour' => '7:00am', 'hour_to' => '7am-8am'],
            ['position' => 15, 'index' => 'f8', 'day' => 'Friday', 'hour' => '8:00am', 'hour_to' => '8am-9am'],
            ['position' => 16, 'index' => 'f9', 'day' => 'Friday', 'hour' => '9:00am', 'hour_to' => '9am-10am'],
            ['position' => 17, 'index' => 'f10', 'day' => 'Friday', 'hour' => '10:00am', 'hour_to' => '10am-11am'],
            ['position' => 18, 'index' => 'f11', 'day' => 'Friday', 'hour' => '11:00am', 'hour_to' => '11am-12pm'],
            ['position' => 19, 'index' => 'f12', 'day' => 'Friday', 'hour' => '12:00pm', 'hour_to' => '12pm-1pm'],
            ['position' => 20, 'index' => 'f13', 'day' => 'Friday', 'hour' => '1:00pm', 'hour_to' => '1pm-2pm'],
            ['position' => 21, 'index' => 'f14', 'day' => 'Friday', 'hour' => '2:00pm', 'hour_to' => '2pm-3pm'],
            ['position' => 22, 'index' => 'f15', 'day' => 'Friday', 'hour' => '3:00pm', 'hour_to' => '3pm-4pm'],
            ['position' => 23, 'index' => 'f16', 'day' => 'Friday', 'hour' => '4:00pm', 'hour_to' => '4pm-5pm'],
            ['position' => 24, 'index' => 'f17', 'day' => 'Friday', 'hour' => '5:00pm', 'hour_to' => '5pm-6pm'],
            ['position' => 25, 'index' => 'f18', 'day' => 'Friday', 'hour' => '6:00pm', 'hour_to' => '6pm-7pm'],
            ['position' => 26, 'index' => 'f19', 'day' => 'Friday', 'hour' => '7:00pm', 'hour_to' => '7pm-8pm'],
            ['position' => 27, 'index' => 'f20', 'day' => 'Friday', 'hour' => '8:00pm', 'hour_to' => '8pm-9pm'],
            ['position' => 28, 'index' => 'f21', 'day' => 'Friday', 'hour' => '9:00pm', 'hour_to' => '9pm-10pm'],
            ['position' => 29, 'index' => 'f22', 'day' => 'Friday', 'hour' => '10:00pm', 'hour_to' => '10pm-11pm'],
            ['position' => 30, 'index' => 'f23', 'day' => 'Friday', 'hour' => '11:00pm', 'hour_to' => '11pm-12am'],
            ['position' => 31, 'index' => 's0', 'day' => 'Saturday', 'hour' => '12:00am', 'hour_to' => '12am-1am'],
            ['position' => 32, 'index' => 's1', 'day' => 'Saturday', 'hour' => '1:00am', 'hour_to' => '1am-2am'],
            ['position' => 33, 'index' => 's2', 'day' => 'Saturday', 'hour' => '2:00am', 'hour_to' => '2am-3am'],
            ['position' => 34, 'index' => 's3', 'day' => 'Saturday', 'hour' => '3:00am', 'hour_to' => '3am-4am'],
            ['position' => 35, 'index' => 's4', 'day' => 'Saturday', 'hour' => '4:00am', 'hour_to' => '4am-5am'],
            ['position' => 36, 'index' => 's5', 'day' => 'Saturday', 'hour' => '5:00am', 'hour_to' => '5am-6am'],
            ['position' => 37, 'index' => 's6', 'day' => 'Saturday', 'hour' => '6:00am', 'hour_to' => '6am-7am'],
            ['position' => 38, 'index' => 's7', 'day' => 'Saturday', 'hour' => '7:00am', 'hour_to' => '7am-8am'],
            ['position' => 39, 'index' => 's8', 'day' => 'Saturday', 'hour' => '8:00am', 'hour_to' => '8am-9am'],
            ['position' => 40, 'index' => 's9', 'day' => 'Saturday', 'hour' => '9:00am', 'hour_to' => '9am-10am'],
            ['position' => 41, 'index' => 's10', 'day' => 'Saturday', 'hour' => '10:00am', 'hour_to' => '10am-11am'],
            ['position' => 42, 'index' => 's11', 'day' => 'Saturday', 'hour' => '11:00am', 'hour_to' => '11am-12pm'],
            ['position' => 43, 'index' => 's12', 'day' => 'Saturday', 'hour' => '12:00pm', 'hour_to' => '12pm-1pm'],
            ['position' => 44, 'index' => 's13', 'day' => 'Saturday', 'hour' => '1:00pm', 'hour_to' => '1pm-2pm'],
            ['position' => 45, 'index' => 's14', 'day' => 'Saturday', 'hour' => '2:00pm', 'hour_to' => '2pm-3pm'],
            ['position' => 46, 'index' => 's15', 'day' => 'Saturday', 'hour' => '3:00pm', 'hour_to' => '3pm-4pm'],
            ['position' => 47, 'index' => 's16', 'day' => 'Saturday', 'hour' => '4:00pm', 'hour_to' => '4pm-5pm'],
            ['position' => 48, 'index' => 's17', 'day' => 'Saturday', 'hour' => '5:00pm', 'hour_to' => '5pm-6pm'],
            ['position' => 49, 'index' => 's18', 'day' => 'Saturday', 'hour' => '6:00pm', 'hour_to' => '6pm-7pm'],
            ['position' => 50, 'index' => 's19', 'day' => 'Saturday', 'hour' => '7:00pm', 'hour_to' => '7pm-8pm'],
            ['position' => 51, 'index' => 's20', 'day' => 'Saturday', 'hour' => '8:00pm', 'hour_to' => '8pm-9pm'],
            ['position' => 52, 'index' => 's21', 'day' => 'Saturday', 'hour' => '9:00pm', 'hour_to' => '9pm-10pm'],
            ['position' => 53, 'index' => 's22', 'day' => 'Saturday', 'hour' => '10:00pm', 'hour_to' => '10pm-11pm'],
            ['position' => 54, 'index' => 's23', 'day' => 'Saturday', 'hour' => '11:00pm', 'hour_to' => '11pm-12am'],
            ['position' => 55, 'index' => 'u0', 'day' => 'Sunday', 'hour' => '12:00am', 'hour_to' => '12am-1am'],
            ['position' => 56, 'index' => 'u1', 'day' => 'Sunday', 'hour' => '1:00am', 'hour_to' => '1am-2am'],
            ['position' => 57, 'index' => 'u2', 'day' => 'Sunday', 'hour' => '2:00am', 'hour_to' => '2am-3am'],
            ['position' => 58, 'index' => 'u3', 'day' => 'Sunday', 'hour' => '3:00am', 'hour_to' => '3am-4am'],
            ['position' => 59, 'index' => 'u4', 'day' => 'Sunday', 'hour' => '4:00am', 'hour_to' => '4am-5am'],
            ['position' => 60, 'index' => 'u5', 'day' => 'Sunday', 'hour' => '5:00am', 'hour_to' => '5am-6am'],
            ['position' => 61, 'index' => 'u6', 'day' => 'Sunday', 'hour' => '6:00am', 'hour_to' => '6am-7am'],
            ['position' => 62, 'index' => 'u7', 'day' => 'Sunday', 'hour' => '7:00am', 'hour_to' => '7am-8am'],
            ['position' => 63, 'index' => 'u8', 'day' => 'Sunday', 'hour' => '8:00am', 'hour_to' => '8am-9am'],
            ['position' => 64, 'index' => 'u9', 'day' => 'Sunday', 'hour' => '9:00am', 'hour_to' => '9am-10am'],
            ['position' => 65, 'index' => 'u10', 'day' => 'Sunday', 'hour' => '10:00am', 'hour_to' => '10am-11am'],
            ['position' => 66, 'index' => 'u11', 'day' => 'Sunday', 'hour' => '11:00am', 'hour_to' => '11am-12pm'],
            ['position' => 67, 'index' => 'u12', 'day' => 'Sunday', 'hour' => '12:00pm', 'hour_to' => '12pm-1pm'],
            ['position' => 68, 'index' => 'u13', 'day' => 'Sunday', 'hour' => '1:00pm', 'hour_to' => '1pm-2pm'],
            ['position' => 69, 'index' => 'u14', 'day' => 'Sunday', 'hour' => '2:00pm', 'hour_to' => '2pm-3pm'],
            ['position' => 70, 'index' => 'u15', 'day' => 'Sunday', 'hour' => '3:00pm', 'hour_to' => '3pm-4pm'],
            ['position' => 71, 'index' => 'u16', 'day' => 'Sunday', 'hour' => '4:00pm', 'hour_to' => '4pm-5pm'],
            ['position' => 72, 'index' => 'u17', 'day' => 'Sunday', 'hour' => '5:00pm', 'hour_to' => '5pm-6pm'],
        ]);
    }
}
