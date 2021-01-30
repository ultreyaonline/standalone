<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;

class Candidate extends Model
{
    use LogsActivity;
    use HasFactory;

    protected static $logName = 'pre-weekend';
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;

    protected $casts = [
        'married'                   => 'boolean',
        'vocational_minister'       => 'boolean',
        'sponsor_confirmed_details' => 'boolean',
        'fees_paid'                 => 'boolean',
        'ready_to_mail'             => 'boolean',
        'invitation_mailed'         => 'boolean',
        'm_response_card_returned'  => 'boolean',
        'w_response_card_returned'  => 'boolean',
        'm_smoker'                  => 'boolean',
        'w_smoker'                  => 'boolean',
        'completed'                 => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'm_user_id',
        'w_user_id',
        'm_age',
        'w_age',
        'm_emergency_name',
        'm_emergency_phone',
        'w_emergency_name',
        'w_emergency_phone',
        'm_pronunciation',
        'w_pronunciation',
        'm_married',
        'm_vocational_minister',
        'w_married',
        'w_vocational_minister',
        'sponsor_confirmed_details',
        'fees_paid',
        'ready_to_mail',
        'invitation_mailed',
        'm_response_card_returned',
        'm_special_dorm',
        'm_special_diet',
        'm_special_prayer',
        'm_special_medications',
        'm_smoker',
        'w_response_card_returned',
        'w_special_dorm',
        'w_special_diet',
        'w_special_prayer',
        'w_special_medications',
        'w_smoker',
        'payment_details',
        'm_arrival_poc_person',
        'm_arrival_poc_phone',
        'w_arrival_poc_person',
        'w_arrival_poc_phone',
        'm_special_notes',
        'w_special_notes',
        'weekend',
        'completed',
    ];

    public function getNamesAttribute()
    {
        $name = $this->man ? $this->man->first : '';

        // include surname if no spouse
        if ($this->man && !$this->woman) {
            $name .= ' ' . $this->man->last;
        }

        // if spouse has non-matching surname, include man's surname
        if ($this->man && $this->woman && $this->man->last != $this->woman->last) {
            $name .= ' ' . $this->man->last;
        }

        if ($this->man && $this->woman) {
            $name .= ' and ';
        }
        $name .= $this->woman ? $this->woman->name : '';

        return $name;
    }

    public function scopeMarried(Builder $query)
    {
        return $query->where('married', 1);
    }

    public function scopeSponsorConfirmed(Builder $query)
    {
        return $query->where('sponsor_confirmed_details', 1);
    }

    public function scopeReadyToMail(Builder $query)
    {
        return $query->where('ready_to_mail', 1);
    }

    public function scopeInvitationMailed(Builder $query)
    {
        return $query->where('invitation_mailed', 1);
    }

    public function scopeInvitationNotMailed(Builder $query)
    {
        return $query->where('invitation_mailed', '!=', 1);
    }

    public function scopeByMemberId(Builder $query, $id)
    {
        return $query->where('m_user_id', $id)->orWhere('w_user_id', $id);
    }


    public function getAddressFormattedAttribute()
    {
        if (!$this->attributes['m_user_id'] && !$this->attributes['w_user_id']) {
            return '';
        }

        return $this->man->address_formatted ?? $this->woman->address_formatted;
    }

    public function getSponsorAttribute()
    {
        if (!$this->attributes['m_user_id'] && !$this->attributes['w_user_id']) {
            return '';
        }

        return $this->man->sponsor ?? $this->woman->sponsor ?? null;
    }

    public function getCandidateWeekendAttribute()
    {
        if (!$this->attributes['m_user_id'] && !$this->attributes['w_user_id']) {
            return '';
        }

        return $this->man->weekend ?? $this->woman->weekend;
    }

    public function getWeekendDatesTextMAttribute()
    {
        $w = $text = null;

        if (! empty($this->attributes['m_user_id'])) {
            $w = $this->man->weekend;
        }

        if ($w) {
            $number = substr($w, strpos($w, '#')+1);
            $weekend = Weekend::NumberAndGender($number, 'M')->first();
            $text = e($weekend->long_name_with_number) . "<br>\n" . e($weekend->short_date_range);
        }

        return $text;
    }

    public function getWeekendDatesTextWAttribute()
    {
        $w = $text = null;

        if (! empty($this->attributes['w_user_id'])) {
            $w = $this->woman->weekend;
        }

        if ($w) {
            $number = substr($w, strpos($w, '#')+1);
            $weekend = Weekend::NumberAndGender($number, 'W')->first();
            $text = e($weekend->long_name_with_number) . "<br>\n" . e($weekend->short_date_range);
        }

        return $text;
    }

    public function man(): BelongsTo
    {
        return $this->belongsTo(User::class, 'm_user_id');
    }

    public function woman(): BelongsTo
    {
        return $this->belongsTo(User::class, 'w_user_id');
    }
}
