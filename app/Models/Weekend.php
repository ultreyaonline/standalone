<?php

namespace App\Models;

use App\Enums\WeekendVisibleTo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Weekend extends Model implements HasMedia
{
    use InteractsWithMedia;
    use LogsActivity;
    use HasFactory;

    protected static $logName = 'weekends';
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;

    protected $fillable = [
        'weekend_full_name',
        'weekend_number',
        'weekend_MF',
        'tresdias_community',
        'start_date',
        'end_date',
        'sendoff_location',
        'sendoff_couple_name',
        'sendoff_couple_email',
        'sendoff_couple_id1',
        'sendoff_couple_id2',
        'weekend_location',
        'candidate_arrival_time',
        'sendoff_start_time',
        'maximum_candidates',
        'candidate_cost',
        'team_fees',
        'rectorID',
        'weekend_verse_text',
        'weekend_verse_reference',
        'weekend_theme',
        'banner_url',
        'team_meetings',
        'table_palanca_guideline_text',
        'serenade_arrival_time',
        'serenade_practice_location',
        'serenade_scheduled_start_time',
        'serenade_lead_contact',
        'serenade_coordinator',
        'serenade_musician',
        'serenade_songbook_maker',
        'closing_arrival_time',
        'closing_scheduled_start_time',
        'emergency_poc_name',
        'emergency_poc_email',
        'emergency_poc_phone',
        'emergency_poc_id',
        'visibility_flag',
        'teamphoto',
        'share_1_doc_url',
        'share_1_doc_label',
        'share_2_doc_url',
        'share_2_doc_label',
        'share_3_doc_url',
        'share_3_doc_label',
        'share_4_doc_url',
        'share_4_doc_label',
        'share_5_doc_url',
        'share_5_doc_label',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'candidate_arrival_time' => 'datetime',
        'sendoff_start_time' => 'datetime',
        'closing_arrival_time' => 'datetime',
        'closing_scheduled_start_time' => 'datetime',
        'serenade_arrival_time' => 'datetime',
        'serenade_scheduled_start_time' => 'datetime',
        'visibility_flag' => 'integer',
    ];

    public function scopeNumberAndGender(Builder $query, $number, $gender)
    {
        return $query->where('weekend_MF', strtoupper($gender))
            ->where('weekend_number', $number);
    }

    public function scopeNextweekend(Builder $query)
    {
        return $query->where('visibility_flag', '>=', WeekendVisibleTo::Calendar)
            ->where('end_date', '>', Carbon::yesterday()->subDays((int)config('site.weekend_shows_finished_for_x_days', 7)))
            ->orderBy('start_date', 'asc');
    }

    public function scopeActive(Builder $query, $userID = null)
    {
        if (auth()->user()) {
            if (auth()->user()->can('create weekends') || auth()->user()->can('see hidden weekends') || auth()->user()->can('add candidates')) {
                return $query->orderBy('start_date', 'asc');
            }
        }

        $query = $query->where('visibility_flag', '>=', WeekendVisibleTo::Calendar);

        if (!$userID) {
            $userID = auth()->id();
        }

        if ($userID) {
            $query = $query->orWhere('RectorID', $userID);
        }

        // @TODO: add admin override, perhaps with @can('create weekends'), similar to below

        return $query->orderBy('start_date');
    }

    public function scopeActiveDescending(Builder $query, $userID = null)
    {
        if (auth()->user()) {
            if (auth()->user()->can('create weekends') || auth()->user()->can('see hidden weekends') || auth()->user()->can('add candidates')) {
                return $query->orderBy('start_date', 'desc');
            }
        }

        // @TODO: This allows showing the Weekend on the Calendar page if no User is specified, or restricts the Dropdown selector if a User is passed.
        $visibility = WeekendVisibleTo::Calendar;
        if ($userID) {
            $visibility = WeekendVisibleTo::RectorOnly;
        }
        $query = $query->where('visibility_flag', '>=', WeekendVisibleTo::Calendar);

        if (!$userID) {
            $userID = auth()->id();
        }

        if ($userID) {
            $query = $query->orWhere('RectorID', $userID);
        }

        return $query->orderBy('start_date', 'desc');
    }

    public function scopeEnded(Builder $query)
    {
        return $query->where('end_date', '<', Carbon::now());
    }

    public function scopeFuture(Builder $query)
    {
        return $query->where('visibility_flag', '>=', WeekendVisibleTo::Calendar)
            ->where('end_date', '>', Carbon::now())
            ->orderBy('start_date', 'asc');
    }

    public function scopeFutureAnyStatus(Builder $query)
    {
        return $query
            ->where('end_date', '>', Carbon::now())
            ->orderBy('start_date', 'asc');
    }

    public function scopeLocal(Builder $query)
    {
        $community = config('site.local_community_filter', config('site.community_acronym'));
        return $query->where('tresdias_community', $community);
    }

    public function scopeByNumber(Builder $query)
    {
        return $query->orderBy('weekend_number', 'asc');
    }

    public function scopeStartedThisMonth(Builder $query)
    {
        return $query->where('start_date', '>', Carbon::now()->addDays(-30));
    }



    /*********** ATTRIBUTES *********/

    public function getHasStartedAttribute()
    {
        return $this->attributes['start_date'] < Carbon::now();
    }

    public function getHasEndedAttribute()
    {
        return $this->attributes['end_date'] < Carbon::now();
    }

    public function getEndedThisMonthAttribute()
    {
        return $this->hasEnded && !$this->ended_over_a_month_ago;
    }

    public function getEndedOverAMonthAgoAttribute()
    {
        // the end-date plus a month is still greater than today
        return $this->end_date->addDays(28) < Carbon::now();
    }

    public function getEndsTodayAttribute()
    {
        return $this->end_date->isToday();
    }

    public function getGenderAttribute()
    {
        return $this->attributes['weekend_MF'];
    }

    public function getShortnameAttribute()
    {
        return $this->attributes['tresdias_community'] . ' #' . $this->attributes['weekend_number'];
    }

    public function getNumberSlugAttribute()
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower($this->shortname));
    }

    public function getSlugAttribute()
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower($this->shortname . $this->gender));
    }

    public function getLongNameWithNumberAttribute()
    {
        $community = config('site.community_long_name');
        return $community . ' ' . ($this->attributes['weekend_MF'] == 'M' ? 'Men' : 'Women') . "'s #" . $this->attributes['weekend_number'];
    }

    public function getLongNameWithNumberPlusWeekendAttribute()
    {
        return config('site.community_long_name') . ($this->attributes['weekend_MF'] == 'M' ? 'Men' : 'Women') . "'s Weekend #" . $this->attributes['weekend_number'];
    }

    public function getNameWithGenderAndNumberAttribute()
    {
        return ($this->attributes['weekend_MF'] == 'M' ? 'Men' : 'Women') . "'s #" . $this->attributes['weekend_number'];
    }

    public function getArrivalTimeAttribute()
    {
        return $this->attributes['candidate_arrival_time']->format('g:i a');
    }

    public function getShortDateRangeAttribute()
    {
        // ie: April 14-17, 2016 or May 31-June 3, 2016
        $val = Carbon::parse($this->attributes['start_date'])->format('M j') . '-';

        if (Carbon::parse($this->attributes['start_date'])->format('M') === Carbon::parse($this->attributes['end_date'])->format('M')) {
            $val .= Carbon::parse($this->attributes['end_date'])->format('j, Y');
        } else {
            $val .= Carbon::parse($this->attributes['end_date'])->format('M j, Y');
        }
        return $val;
    }

    public function getShortDateRangeWithoutYearAttribute()
    {
        // ie: "May 31-June 3"
        $val = Carbon::parse($this->attributes['start_date'])->format('F j') . '-';
        if (Carbon::parse($this->attributes['start_date'])->format('F') === Carbon::parse($this->attributes['end_date'])->format('F')) {
            $val .= Carbon::parse($this->attributes['end_date']) ->format('j');
        } else {
            $val .= Carbon::parse($this->attributes['end_date']) ->format('F j');
        }
        return $val;
    }

    public function getLongDateRangeWithWeekdaysAttribute()
    {
        // ie: "Thursday May 31 to Sunday June 3"
        $val = Carbon::parse($this->attributes['start_date'])->format('l F j') . ' to ';
        $val .= Carbon::parse($this->attributes['end_date']) ->format('l F j, Y');
        return $val;
    }

    public function getEndTimeAttribute()
    {
        return $this->attributes['end_date']->format('g:i a');
    }

    public function getEmergencyContact1Attribute()
    {
        return $this->attributes['emergency_poc_name'] ?? $this->attributes['emergency_poc_id'] ? User::find($this->attributes['emergency_poc_id'])->name : '';
    }

    public function getEmergencyPhone1Attribute()
    {
        return $this->attributes['emergency_poc_phone'] ?? $this->attributes['emergency_poc_id'] ? User::find($this->attributes['emergency_poc_id'])->cellphone : '';
    }

    public function getEmergencyContact2Attribute()
    {
        return config('site.emergency_contact_text', '');
    }

    public function getEmergencyPhone2Attribute()
    {
        return config('site.emergency_contact_number', '');
    }

    public function getWeatherForecastAttribute()
    {
        return "The weather is typically cool, 7-12ºC, wth possible rain.";
    }

    public function getSendoffLocationAttribute()
    {
        if (empty($this->attributes['sendoff_location'])) {
            return '';
        }

        return $this->attributes['sendoff_location'];
    }

    public function getWeekendLocationAttribute()
    {
        if (empty($this->attributes['weekend_location'])) {
            return '';
        }

        return $this->attributes['weekend_location'];
    }

    public function getCandidateCostAttribute()
    {
        if (empty($this->attributes['candidate_cost'])) {
            return '';
        }

        return number_format($this->attributes['candidate_cost'], 0);
    }

    // @TODO -- for this and others above, where the attribute is in Eloquent, pass $value instead of $this->attribute['foo'] lookup
    public function getTeamFeesAttribute()
    {
        if (empty($this->attributes['team_fees'])) {
            return '';
        }

        return number_format($this->attributes['team_fees'], 0);
    }


    /**
     * Calculate number of tables and candidates, for rollo-room-palanca guidelines
     *
     * @return string
     */
    public function getTotalrolloroomAttribute()
    {
        $candidate_tables = ceil($this->candidates->count() / 4); // typically 4 candidates per table
        if ($candidate_tables > 6) {
            $candidate_tables = 6; // max 6 tables
        }

        $table_assistants = $candidate_tables * 2; // two professors per table
        $bodies_at_tables = $this->candidates->count() + $table_assistants;

        return $bodies_at_tables . ' people at ' . $candidate_tables . ' candidate tables
        (plus tables for Rector + Head Cha + SDs + AV chas)';
    }

    public function getTotalteamAttribute()
    {
        return $this->team_all_visibility->unique('memberID')->count();
    }

    public function getTotalcandidatesAttribute()
    {
        return $this->candidates->count();
    }

    public function getTotalteamandcandidatesAttribute()
    {
        return $this->candidates->count() + $this->team_all_visibility->unique('memberID')->count();
    }

    public function getTeamAttribute()
    {
        return WeekendAssignments::select('weekend_assignments.*', 'weekend_roles.RoleName', 'weekend_roles.sortorder', 'users.last', 'users.first')
            ->join('weekend_roles', 'weekend_assignments.roleID', '=', 'weekend_roles.id')
            ->join('users', 'users.id', '=', 'weekend_assignments.memberID')
            ->where('weekendID', $this->id)
            ->where('confirmed', \App\Enums\TeamAssignmentStatus::Accepted)
            ->orderBy('weekend_roles.sortorder', 'asc')
            ->orderBy('users.last', 'asc')
            ->orderBy('users.first', 'asc')
            ->with(['user', 'role'])
            ->get();
    }

    public function getTeamAllVisibilityAttribute()
    {
        return WeekendAssignments::select('weekend_assignments.*', 'weekend_roles.RoleName', 'weekend_roles.sortorder', 'users.last', 'users.first')
            ->join('weekend_roles', 'weekend_assignments.roleID', '=', 'weekend_roles.id')
            ->join('users', 'users.id', '=', 'weekend_assignments.memberID')
            ->where('weekendID', $this->id)
            ->where('confirmed', \App\Enums\TeamAssignmentStatus::Accepted)
            ->withoutGlobalScope('visibleWeekendsOnly')
            ->orderBy('weekend_roles.sortorder', 'asc')
            ->orderBy('users.last', 'asc')
            ->orderBy('users.first', 'asc')
            ->with(['user', 'role'])
            ->get();
    }

    public function getTeamUniqueAttribute()
    {
        return $this->team_all_visibility->unique('memberID');
    }

    public function getHeadChaAttribute()
    {
        // note: this returns a collection. Get values by calling contains() or first() on it, or looping thru each()
        return $this->team_all_visibility->where('roleID', 2)->pluck('memberID');
    }

    public function getAhChaAttribute()
    {
        // note: this returns a collection. Get values by calling contains() on it, or looping thru each()
        return $this->team_all_visibility->where('roleID', 3)->pluck('memberID');
    }

    public function getRoverAttribute()
    {
        return $this->team_all_visibility->where('roleID', 5)->pluck('memberID')->first();
    }

    public function getWeekendLeadersAttribute()
    {
        // note: this returns a collection; search using contains() or each()
        return $this->team_all_visibility->whereIn('roleID', [1, 2, 3])->pluck('memberID');
    }

    public function getMayTrackTeamPaymentsAttribute()
    {
        return $this->team->whereIn('roleID', [2, 3])->pluck('memberID')->contains(auth()->id());
    }

    public function getLocalAttribute()
    {
        return User::whereIn('id', $this->teamUnique->pluck('memberID'))
            ->where('community', config('site.local_community_filter', config('site.community_acronym')))
            ->count();
    }

    public function getTeamMembersWhoAttendedOneOfOurWeekendsAttribute()
    {
        return User::query()
        ->whereIn('id', $this->teamUnique->pluck('memberID'))
        ->where('weekend', config('site.local_community_filter', config('site.community_acronym')))
        ->count();
    }

    public function getCandidatesAttribute()
    {
        $short_name = $this->tresdias_community . ' #' . $this->weekend_number;
        $mf         = $this->weekend_MF;
        $candidates = User::where('gender', $mf)
            ->where('weekend', $short_name)
            ->orderBy('first')
            ->orderBy('last')
            ->get();

        return $candidates;
    }

    public function getSponsorsAttribute()
    {
        $short_name = $this->tresdias_community . ' #' . $this->weekend_number;
        $mf         = $this->weekend_MF;
        $candidates = User::where('gender', $mf)
            ->where('weekend', $short_name)
            ->orderBy('first')
            ->orderBy('last')
            ->get();

        $sponsorIds = $candidates->pluck('sponsorID');
        foreach ($sponsorIds as $sponsorid) {
            $sponsor = User::find($sponsorid);
            if ($sponsor && !in_array($sponsor->spouseID, (array)$sponsorIds)) {
                $sponsorIds[] = $sponsor->spouseID;
            }
        }
        $sponsors = User::whereIn('id', $sponsorIds)->orderBy('last')->orderBy('first')->get();

        return $sponsors;
    }

    public function getBannerUrlAttribute()
    {
        if ($this->getMedia('banner')->count()) {
            return $this->getFirstMediaUrl('banner', 'resized');
        }

        return $this->attributes['banner_url'] ?? null;
    }

    public function getBannerUrlOriginalAttribute()
    {
        if ($this->getMedia('banner')->count()) {
            return $this->getFirstMediaUrl('banner');
        }

        return $this->attributes['banner_url'] ?? null;
    }

    public function getTeamPhotoAttribute()
    {
        if ($this->getMedia('teamphoto')->count()) {
            return $this->getFirstMediaUrl('teamphoto', 'resized');
        }

        return $this->attributes['teamphoto'] ?? null;
    }

    public function getTeamPhotoOriginalAttribute()
    {
        if ($this->getMedia('teamphoto')->count()) {
            return $this->getFirstMediaUrl('teamphoto');
        }

        return $this->attributes['teamphoto'] ?? null;
    }


    /**
     * Determine who can "see" the team, even if it's not released yet
     * NOTE: c/f the \App\Policies\TeamAssignmentsPolicy for EDIT rights.
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function teamCanBeViewedBy(User $user)
    {
        if ($this->rectorID === $user->id) {
            return true;
        }

        if ($user->hasAnyRole(['President', 'Admin', 'Super-Admin', 'Emerging Community Liaison'])) {
            return true;
        }

        if ($user->hasAnyRole(['Mens Leader', 'Rector Selection']) && $this->weekend_MF === 'M' && $user->gender === 'M') {
            return true;
        }

        if ($user->hasAnyRole(['Womens Leader', 'Rector Selection']) && $this->weekend_MF === 'W' && $user->gender === 'W') {
            return true;
        }

        if ($this->visibility_flag >= WeekendVisibleTo::HeadChas && $this->head_cha->contains($user->id)) {
            return true;
        }

        if ($this->visibility_flag >= WeekendVisibleTo::HeadChas && $this->rover === $user->id) {
            return true;
        }

        if ($user->can('edit team member assignments')) {
            if ($user->gender === $this->weekend_MF) {
                return true;
            }
        }

        return false;
    }


    public function rector()
    {
        return $this->belongsTo(User::class, 'rectorID');
    }

    public function emergency_poc()
    {
        return $this->belongsTo(User::class, 'emergency_poc_id');
    }

    public function prayerwheel()
    {
        return $this->belongsTo(PrayerWheel::class, 'id', 'weekendID');
    }


    /////////////

    /**
     * Team fee payments and position assignments data, used by TeamFeesController and for calculating Payment Statistics display on Weekend page.
     *
     * @return array
     */
    public function getFeePaymentsData()
    {
        // Get all payments for this Weekend
        $payments = TeamFeePayments::where('weekendID', $this->id)->with('user')->get();

        // Get all team member assignments for this weekend
        $assignments = WeekendAssignments::query()
            ->withoutGlobalScope('visibleWeekendsOnly')
            ->where('weekendID', $this->id)
            ->where('confirmed', '>=', \App\Enums\TeamAssignmentStatus::Accepted)
            ->with('user', 'weekend', 'role')
            ->get()

            // Filter out duplicates (ie: members assigned to multiple roles)
            ->unique('memberID');

        // filter out SDs if they're exempt
        if (empty(config('site.team_fees_spiritual_directors_pay'))) {
            $assignments = $assignments->filter(function ($assignment) {
                return !Str::contains($assignment->role->RoleName, 'Spiritual Director');
            });
        }

        // exclude drops (payments for which there is not a confirmed assignment)
        $payments = $payments->filter(function ($pmt) use ($assignments){
            foreach($assignments as $a) {
                if ($a->user->id === $pmt->user->id) {
                    return true;
                }
            }
        });

        return [$payments, $assignments];
    }

    public function feePaymentStatistics()
    {
        [$payments, $assignments] = $this->getFeePaymentsData();

        $local_payments = $payments->filter(function ($pmt) {
            return $pmt->user->community === config('site.local_community_filter') ? $pmt->total_paid : 0;
        });
        $extended_payments = $payments->filter(function ($pmt) {
            return $pmt->user->community !== config('site.local_community_filter') ? $pmt->total_paid : 0;
        });

        $local_assignments = $assignments->filter(function ($position) {
            return $position->user->community === config('site.local_community_filter') ? 1 : 0;
        });
        $extended_assignments = $assignments->filter(function ($position) {
            return $position->user->community !== config('site.local_community_filter') ? 1 : 0;
        });

        $local_percent = $local_assignments->count() > 0 ? round($local_payments->count() / $local_assignments->count(), 2) * 100 : 0;
        $extended_percent = $extended_assignments->count() > 0 ? round($extended_payments->count() / $extended_assignments->count(), 2) * 100 : 0;

        return [
            'local_paid' => $local_payments->count(),
            'extended_paid' => $extended_payments->count(),
            'local_positions' => $local_assignments->count(),
            'extended_positions' => $extended_assignments->count(),
            'local_percent' => $local_percent,
            'extended_percent' => $extended_percent,
            ];
    }




    /**
     * Register Spatie Media-Library collections
     */
    public function registerMediaCollections(): void
    {
        // Banner is a single image, so subsequent images replace prior ones
        $this
            ->addMediaCollection('banner')
            ->singleFile();

        // Team Photo is a single image, so subsequent images replace prior ones
        $this
            ->addMediaCollection('teamphoto')
            ->singleFile();
    }

    /**
     * Register standardized media resizing conversion
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('resized')
            ->width(800)
            ->height(600)
            //->orientation(Manipulations::ORIENTATION_AUTO)
        ;
    }

    /**
     * Delete media collections on Delete
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function (Weekend $weekend) {
            $weekend->clearMediaCollection('banner');
            $weekend->clearMediaCollection('teamphoto');

            return $weekend;
        });
    }
}
