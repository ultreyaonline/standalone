<?php

namespace App;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Event extends Model
{
    use LogsActivity;
    use Cachable;

    protected $casts = [
        'is_enabled'   => 'boolean',
        'is_public'    => 'boolean',
        'is_recurring' => 'boolean',
    ];

    protected $dates = [
        'start_datetime',
        'end_datetime',
        'recurring_end_datetime',
        'expiration_date',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_key',
        'type',
        'name',
        'description',
        'location_name',
        'location_url',
        'address_street',
        'address_city',
        'address_province',
        'address_postal',
        'map_url_link',
        'contact_name',
        'contact_email',
        'contact_phone',
        'contact_id',
        'start_datetime',
        'end_datetime',
        'is_enabled',
        'is_public',
        'is_recurring',
        'recurring_end_datetime',
        'expiration_date',
        'posted_by',
    ];

    const TYPES = [
        'secuela' => 'Secuela',
        'reunion' => 'Reunion Group',
        'secretariat' => 'Secretariat Meeting',
//        'weekend' => 'Weekend (other community)',
        'other' => 'Other/General',
        ];

    /**
     * Scope a query to only include active events.
     *
     * @param $query
     * @return Builder
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('is_enabled', 1)
            ->where('end_datetime', '>', Carbon::now())
            ->orderBy('start_datetime');
    }

    /**
     * Scope a query to only include public events (visible to non-members)
     *
     * @param $query
     * @return Builder
     */
    public function scopePublic(Builder $query)
    {
        return $query->where('is_public', 1);
    }

    /**
     * Scope a query to only include public events (visible to non-members)
     *
     * @param $query
     * @return Builder
     */
    public function scopeWithoutReunionGroups(Builder $query)
    {
        return $query->where('type', '!=', 'reunion');
    }

    /**
     * Scope a query to only include public events (visible to non-members)
     *
     * @param $query
     * @return Builder
     */
    public function scopeOnlyReunionGroups(Builder $query)
    {
        return $query->where('type', 'reunion');
    }

    /**
     * Scope a query to only include specific kinds of events
     *
     * @param Builder $query
     * @param string $type secuela|reunion|other
     * @return Builder
     */
    public function scopeOfType(Builder $query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include enabled/active events (vs drafts)
     *
     * @param $query
     * @return Builder
     */
    public function scopeEnabled(Builder $query)
    {
        return $query->where('is_enabled', 1);
    }

    /**
     * This is an ID number for helping the Template display the correct ID for the Edit button/link to point to (since we display both Events AND Weekends in same list).
     *
     * @param $value
     * @return mixed
     */
    public function setEditIdAttribute($value)
    {
        return $this->attributes['edit_id'] = $value;
    }

    public function getEditIdAttribute()
    {
        return $this->attributes['edit_id'] ?? $this->id;
    }

    // @TODO - invoke an international address-formatter? like https://www.informatica.com/products/data-quality/data-as-a-service/address-verification/address-formats.html
    public function getAddressAttribute()
    {
        return ($this->attributes['address_street'] ? $this->attributes['address_street'] . ', ' : '').
               ($this->attributes['address_city'] ? $this->attributes['address_city'] . ', ' : '').
               $this->attributes['address_province'] . ' ' .
               $this->attributes['address_postal'];
    }

    /**
     * Build a Map URL from the street address, if no Map URL is provided
     * @return string
     */
    public function getMapLinkAttribute()
    {
        $address = $this->getAddressAttribute();

        $address_url = config('site.map_url') . str_replace(' ', '+', trim($address, ', '));
        return $this->attributes['map_url_link'] ?: $address_url;
    }

    public function getIsHistoricalAttribute()
    {
        return $this->end_datetime < Carbon::now();
    }

    public function getStartDateAttribute()
    {
        return Carbon::parse($this->attributes['start_datetime'])->toFormattedDateString();
    }
    public function getEndDateAttribute()
    {
        return Carbon::parse($this->attributes['end_datetime'])->toFormattedDateString();
    }

    public function getShortDateRangeAttribute()
    {
        // ie: April 14-17, 2016 or May 31-June 3, 2016
        $val = Carbon::parse($this->attributes['start_datetime'])->format('M j') . '-';

        if ($this->start_datetime->isSameMonth($this->end_datetime)) {
            $val .= Carbon::parse($this->attributes['end_datetime'])->format('j, Y');
        } else {
            $val .= Carbon::parse($this->attributes['end_datetime'])->format('M j, Y');
        }
        return $val;
    }

    public function getShortDateRangeWithTimeAttribute()
    {
        $startFormat = $endFormat = 'M j, Y g:i a';
//        if ($this->end_datetime->isSameYear($this->start_datetime)) $startFormat = 'M j g:i a';
        if ($this->end_datetime->isSameMonth($this->start_datetime)) $endFormat = 'M j Y g:i a';
        if ($this->end_datetime->isSameDay($this->start_datetime)) $endFormat = 'g:i a';

        return $this->start_datetime->format($startFormat)  . ' - ' . $this->end_datetime->format($endFormat);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'posted_by')->withDefault();
    }

    public function contactPerson()
    {
        return $this->belongsTo(User::class, 'contact_id')->withDefault();
    }

    public function getShouldDisplayForThisUserAttribute()
    {
        // is the event "public"
        if (!empty($this->attributes['is_public'])) {
            return (bool)$this->attributes['is_public'];
        }

        // or ... am I a member
        if (auth()->check() && auth()->user()->hasRole('Member')) {
            return true;
        }
    }
}
