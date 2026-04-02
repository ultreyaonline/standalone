<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class PrayerWheelSignup
 *
 * Represents a single member's commitment to pray during a specific one-hour slot
 * on a retreat weekend's Prayer Wheel. (Members often sign up for many slots, often on different days.)
 *
 * ## Timeslot system
 * Slots are stored as an integer offset from Thursday 5:00pm (the weekend's base time).
 * Slot 1 = Thursday 6pm, slot 2 = Thursday 7pm, ..., slot 72 = Sunday 5pm.
 * The full slot definition list lives in PrayerWheel::getTimeSlots().
 *
 * The `slot_datetime` accessor converts a slot integer back into an absolute Carbon timestamp
 * using the linked weekend's start_date as the anchor.
 *
 * ## Acknowledgement vs. Reminder flags
 * - `acknowledged_at`: set when SendPrayerWheelAcknowledgements runs and emails the member.
 *   Once set, the member will not receive another acknowledgement for that slot.
 * - `reminded_at`: (currently NEVER ACTUALLY SET: the stamping code is commented out in the job).
 *   Despite the column name, reminders are effectively sent daily. See the job class
 *   SendPrayerWheelReminderEmails for the full explanation.
 *
 * ## Eager loading
 * $with includes 'weekend', 'user', 'wheel', and 'wheel.weekend' by default.
 * This is intentional for performance in the reminder/acknowledgement jobs, which
 * loop over large collections and need all four relationships. Be aware this adds
 * overhead if you're querying sign-ups in contexts that don't need all relationships.
 *
 * @property int         $id
 * @property int         $wheel_id       FK → prayer_wheels.id
 * @property int         $timeslot       Integer offset (1–72)
 * @property int         $memberID       FK → users.id
 * @property int|null    $weekendID      FK → weekends.id (denormalised for query speed)
 * @property Carbon|null $acknowledged_at  Set after acknowledgement email sent
 * @property Carbon|null $reminded_at      Never set (see class note above)
 *
 * @package App
 */
class PrayerWheelSignup extends Model
{
    use LogsActivity;
    use HasFactory;

    protected static $logName = 'prayer-wheels';
    protected static $logAttributes = ['*'];

    protected $table = 'prayer_wheel_signups';

    /**
     * Touch the parent wheel's updated_at whenever a signup is created or modified.
     * This keeps the wheel's cache-busting timestamp current.
     */
    protected $touches = ['wheel'];

    protected $guarded = [];

    /**
     * Eager-load these relationships on every query.
     * Necessary for performance in the scheduled email jobs that iterate many sign-ups.
     * Consider using ->without([...]) if you need a lighter query in other contexts.
     */
    protected $with = ['weekend', 'user', 'wheel', 'wheel.weekend'];

    protected $casts = [
        'acknowledged_at' => 'datetime',
        'reminded_at' => 'datetime',
    ];

//    $table->unsignedInteger('wheel_id');
//    $table->unsignedTinyInteger('timeslot');
//    $table->unsignedInteger('memberID')->nullable();

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * The Prayer Wheel this signup belongs to.
     */
    public function wheel(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PrayerWheel::class, 'wheel_id', 'id');
    }

    /**
     * The Weekend directly linked to this signup (denormalised FK).
     *
     * Note: the wheel also has a weekend relationship. Both paths lead to the same weekend.
     * The direct weekendID here exists for query performance (avoids a join through prayer_wheels).
     */
    public function weekend(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Weekend::class, 'weekendID', 'id');
    }

    /**
     * Alias for user() — same relationship, different name.
     */
    public function member(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'memberID');
    }

    /**
     * The community member who signed up for this prayer slot.
     *
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'memberID');
    }

    // -------------------------------------------------------------------------
    // Query Scopes
    // -------------------------------------------------------------------------

    /**
     * Filter sign-ups to a specific weekend, ordered by timeslot.
     *
     * @param Builder $query
     * @param int $weekend  The weekendID
     * @return Builder
     */
    public function scopeForWeekend(Builder $query, int $weekend)
    {
        return $query->where('weekendID', $weekend)
            ->orderBy('timeslot', 'asc');
    }

    // -------------------------------------------------------------------------
    // Timeslot helpers
    // -------------------------------------------------------------------------

    /**
     * Return the full static timeslot definition list from PrayerWheel.
     *
     * Each entry has: position (int), index (string), day (string), hour (string), hour_to (string).
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTimeslots()
    {
        return PrayerWheel::getTimeSlots();
        // position  1
        // day  Thursday
        // hour 6:00pm
        // hour_to 6pm-7pm
    }

    /**
     * Accessor alias: $signup->timeslots returns the full slot list.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTimeslotsAttribute()
    {
        return $this->getTimeslots();
    }

    /**
     * The slot definition entry for THIS signup's timeslot integer.
     *
     * Returns an array with keys: position, index, day, hour, hour_to.
     * Example: ['position' => 15, 'index' => 'f8', 'day' => 'Friday', 'hour' => '8:00am', 'hour_to' => '8am-9am']
     *
     * @return array|null
     */
    public function getPositionDetailsAttribute()
    {
        return $this->getTimeslots()->where('position', $this->timeslot)->first();
    }

    /**
     * Human-readable slot time range, e.g. "8am-9am".
     *
     * @return string
     */
    public function getSpotnameAttribute()
    {
        $timedetails = $this->getPositionDetailsAttribute();

        return $timedetails['hour_to'];
    }

    /**
     * The day name for this slot, e.g. "Friday".
     *
     * @return string
     */
    public function getSpotdayAttribute()
    {
        $timedetails = $this->getPositionDetailsAttribute();

        return $timedetails['day'];
    }

    /**
     * Absolute Carbon datetime for this slot.
     *
     * Calculated as: weekend start_date at 17:00 + slot offset (hours).
     * Slot 1 = Thursday 6pm (start + 1 hour from 5pm), slot 24 = Friday 5pm, etc.
     *
     * Uses the direct weekend relationship if available, falls back to wheel.weekend.
     *
     * @return Carbon
     */
    public function getSlotDatetimeAttribute()
    {
        $weekend_datetime = $this->weekend ? $this->weekend->start_date : $this->wheel->weekend->start_date;
        $base = $weekend_datetime->setTimeFromTimeString('17:00:00');
        return $base->addHour((int)$this->timeslot);
    }

    /**
     * Human-readable formatted slot datetime, e.g. "Friday April 14, 2023  @ 8:00 am".
     *
     * @return string
     */
    public function getSlotDatetimeFormattedAttribute()
    {
        return $this->getSlotDatetimeAttribute()->format('l F j, Y  @ g:i a');
    }

    /**
     * Whether this prayer slot falls on today.
     *
     * @return bool
     */
    public function getSlotIsTodayAttribute()
    {
        return $this->getSlotDatetimeAttribute()->isToday();
    }

    /**
     * Whether this prayer slot is in the future (has not yet started).
     *
     * @return bool
     */
    public function getSlotIsFutureAttribute()
    {
        return $this->getSlotDatetimeAttribute()->isFuture();
    }

    /**
     * Whether this slot falls within the active weekend window.
     *
     * "Active" is defined as: now is between (weekend_start - 12 hours) and (weekend_start + 73 hours).
     *
     * Note: Carbon is not immutable — addHours/subHours mutate the instance.
     * The code defensively copies the start date to avoid mutation side effects.
     *
     * @return bool
     */
    public function getSlotIsDuringAnActiveWeekendAttribute()
    {
        $weekend_start = $this->weekend ? $this->weekend->start_date : $this->wheel->weekend->start_date;
        $weekend_end = $this->weekend ? $this->weekend->start_date : $this->wheel->weekend->start_date;
        // done this way because Carbon isn't date-time-immutable
        $weekend_start = $weekend_start->subHours(12);
        $weekend_end = $weekend_end->addHours(73);
        return Carbon::now()->between($weekend_start, $weekend_end);
    }

    /**
     * Short date range string for the linked weekend, e.g. "April 14-17, 2023".
     *
     * Falls back to wheel.weekend if direct weekend relationship is null.
     *
     * @return string
     */
    public function getWeekendDatesAttribute()
    {
        return $this->weekend ? $this->weekend->short_date_range : $this->wheel->weekend->short_date_range;
    }

    /**
     * Full name of the linked weekend.
     *
     * @return string
     */
    public function getWeekendNameAttribute()
    {
        return $this->weekend ? $this->weekend->weekend_full_name : $this->wheel->weekend->weekend_full_name;
    }

    // -------------------------------------------------------------------------
    // Email helpers
    // -------------------------------------------------------------------------

    /**
     * The User model to send acknowledgement/reminder emails to.
     *
     * @return User
     */
    public function getMailtoRecipientAttribute()
    {
        return $this->user;
// This might be a way to send emails to non-member signups, if we build out the rest of that feature:
//        return $this->member ?? $this->non_member_email ?? new User(['email' => $this->non_member_email, 'name' => $this->non_member_name, 'first' => $this->non_member_name, 'last' => '']);
    }

    /**
     * Display name of the person who signed up.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->user->name;
//        return $this->member ? $this->member->name : $this->non_member_name;
    }

    /**
     * Email address of the person who signed up.
     *
     * @return string
     */
    public function getEmailAttribute()
    {
        return $this->user->email;
//        return $this->member ? $this->member->email : $this->non_member_email;
    }

    /**
     * Whether the member has opted in to receive prayer wheel reminder emails.
     *
     * Reads from users.receive_prayer_wheel_reminders.
     *
     * @return bool
     */
    public function getRemindersRequestedAttribute()
    {
        return $this->user->receive_prayer_wheel_reminders;
//        return $this->member->receive_prayer_wheel_reminders;
    }
}
