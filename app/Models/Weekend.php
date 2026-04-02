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
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class Weekend
 *
 * Represents a single Tres Dias retreat event (typically Thursday evening through Sunday).
 * Men's and Women's weekends are tracked as separate records distinguished by `weekend_MF`.
 *
 * A weekend is identified by its community acronym + number + gender, e.g. "ANYTD #47M".
 * This short name (without gender) is stored as a plain string on `users.weekend` for
 * candidates and pescadores — there is NO foreign key between users and weekends.
 *
 * ## Visibility system
 * Access to team details is gated by `visibility_flag` (see `App\Enums\WeekendVisibleTo`).
 * The global scope on `WeekendAssignments` also references this — team queries may return
 * nothing if the weekend's flag is below `WeekendVisibleTo::Community` (6).
 *
 * ## Candidates vs. Team
 * - Team members live in `weekend_assignments` → `WeekendAssignments` model.
 * - Candidates are found by querying users where `weekend = $this->shortname`.
 *
 * @property int    $id
 * @property string $weekend_full_name
 * @property int    $weekend_number
 * @property string $weekend_MF           'M' = Men's, 'W' = Women's
 * @property string $tresdias_community   Community acronym, e.g. 'ANYTD'
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property int    $rectorID             FK → users.id
 * @property int    $visibility_flag      See WeekendVisibleTo enum (0–6)
 * @property string|null $teamphoto
 * @property string|null $banner_url
 *
 * @package App
 */
class Weekend extends Model implements HasMedia
{
    use InteractsWithMedia;
    use LogsActivity;
    use HasFactory;

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

    // -------------------------------------------------------------------------
    // Query Scopes
    // -------------------------------------------------------------------------

    /**
     * Filter by weekend number and gender.
     * Usage: Weekend::NumberAndGender(47, 'M')->first()
     *
     * @param Builder $query
     * @param int|string $number
     * @param string $gender  'M' or 'W'
     */
    public function scopeNumberAndGender(Builder $query, $number, $gender)
    {
        return $query->where('weekend_MF', strtoupper($gender))
            ->where('weekend_number', $number);
    }

    /**
     * Weekends that should appear on the public calendar.
     *
     * Includes weekends that ended up to 21 days ago so recently-completed weekends
     * remain visible briefly.
     */
    public function scopeNextweekend(Builder $query)
    {
        return $query->where('visibility_flag', '>=', WeekendVisibleTo::Calendar)
            ->where('end_date', '>', Carbon::yesterday()->subDays((int)config('site.weekend_shows_finished_for_x_days', 7)))
            ->orderBy('start_date', 'asc');
    }

    /**
     * Weekends visible to the currently authenticated user, ascending.
     *
     * Admins / users with create/add permissions see all weekends.
     * Others see weekends with visibility >= Calendar, plus any they are Rector of.
     *
     * @param Builder $query
     * @param int|null $userID  Defaults to auth()->id()
     */
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

    /**
     * Same as scopeActive() but ordered newest-first.
     *
     * @param Builder $query
     * @param int|null $userID  Defaults to auth()->id()
     */
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

    /** Weekends whose end_date is in the past. */
    public function scopeEnded(Builder $query)
    {
        return $query->where('end_date', '<', Carbon::now());
    }

    /** Upcoming weekends (end_date in the future) with Calendar or higher visibility. */
    public function scopeFuture(Builder $query)
    {
        return $query->where('visibility_flag', '>=', WeekendVisibleTo::Calendar)
            ->where('end_date', '>', Carbon::now())
            ->orderBy('start_date', 'asc');
    }

    /** Upcoming weekends regardless of visibility — used for admin views. */
    public function scopeFutureAnyStatus(Builder $query)
    {
        return $query->where('end_date', '>', Carbon::now())->orderBy('start_date', 'asc');
    }

    /**
     * Filter to the local community only (config('site.local_community_filter')).
     *
     * Extended community members from other Tres Dias chapters can serve on local-community weekends,
     * but their home weekends are excluded from local-only listings.
     */
    public function scopeLocal(Builder $query)
    {
        $community = config('site.local_community_filter', config('site.community_acronym'));
        return $query->where('tresdias_community', $community);
    }

    /** Order results by weekend_number ascending. */
    public function scopeByNumber(Builder $query)
    {
        return $query->orderBy('weekend_number', 'asc');
    }

    /** Weekends that started within the last 30 days. */
    public function scopeStartedThisMonth(Builder $query)
    {
        return $query->where('start_date', '>', Carbon::now()->addDays(-30));
    }

    // -------------------------------------------------------------------------
    // Computed Attributes
    // -------------------------------------------------------------------------

    /** @return bool  True if start_date has passed */
    public function getHasStartedAttribute()
    {
        return $this->attributes['start_date'] < Carbon::now();
    }

    /** @return bool  True if end_date has passed */
    public function getHasEndedAttribute()
    {
        return $this->attributes['end_date'] < Carbon::now();
    }

    /** @return bool  True if ended but within the last ~28 days */
    public function getEndedThisMonthAttribute()
    {
        return $this->hasEnded && !$this->ended_over_a_month_ago;
    }

    /**
     * True if the weekend ended more than 28 days ago.
     *
     * Used as a safeguard in Prayer Wheel jobs to avoid emailing for long-past weekends.
     *
     * @return bool
     */
    public function getEndedOverAMonthAgoAttribute()
    {
        // the end-date plus a month is still greater than today
        return $this->end_date->addDays(28) < Carbon::now();
    }

    /** @return bool  True if end_date is today */
    public function getEndsTodayAttribute()
    {
        return $this->end_date->isToday();
    }

    /** @return string  'M' or 'W' — alias for weekend_MF */
    public function getGenderAttribute()
    {
        return $this->attributes['weekend_MF'];
    }

    /**
     * Short display name without gender, e.g. "ANYTD #47".
     *
     * This exact format is stored in users.weekend to link candidates to their weekend.
     *
     * @return string
     */
    public function getShortnameAttribute()
    {
        return $this->attributes['tresdias_community'] . ' #' . $this->attributes['weekend_number'];
    }

    /** @return string  URL-safe slug without gender, e.g. "anytd47" */
    public function getNumberSlugAttribute()
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower($this->shortname));
    }

    /**
     * URL-safe slug including gender, e.g. "anytd47m".
     *
     * Used in prayerwheel routes (/prayerwheel/{weekend}) and candidate URLs.
     *
     * @return string
     */
    public function getSlugAttribute()
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower($this->shortname . $this->gender));
    }

    /** @return string  e.g. "Any Tres Dias Men's #47" */
    public function getLongNameWithNumberAttribute()
    {
        $community = config('site.community_long_name');
        return $community . ' ' . ($this->attributes['weekend_MF'] == 'M' ? 'Men' : 'Women') . "'s #" . $this->attributes['weekend_number'];
    }

    /** @return string  e.g. "Any Tres Dias Men's Weekend #47" */
    public function getLongNameWithNumberPlusWeekendAttribute()
    {
        return config('site.community_long_name') . ($this->attributes['weekend_MF'] == 'M' ? 'Men' : 'Women') . "'s Weekend #" . $this->attributes['weekend_number'];
    }

    /** @return string  e.g. "Men's #47" */
    public function getNameWithGenderAndNumberAttribute()
    {
        return ($this->attributes['weekend_MF'] == 'M' ? 'Men' : 'Women') . "'s #" . $this->attributes['weekend_number'];
    }

    /** @return string  Formatted candidate arrival time, e.g. "6:30 pm" */
    public function getArrivalTimeAttribute()
    {
        return $this->attributes['candidate_arrival_time']->format('g:i a');
    }

    /**
     * Human-readable date range, e.g. "April 14-17, 2016" or "May 31-June 3, 2016".
     * When start and end share the same month, the month name appears only once.
     *
     * @return string
     */
    public function getShortDateRangeAttribute()
    {
        $val = Carbon::parse($this->attributes['start_date'])->format('M j') . '-';

        if (Carbon::parse($this->attributes['start_date'])->format('M') === Carbon::parse($this->attributes['end_date'])->format('M')) {
            $val .= Carbon::parse($this->attributes['end_date'])->format('j, Y');
        } else {
            $val .= Carbon::parse($this->attributes['end_date'])->format('M j, Y');
        }
        return $val;
    }

    /** @return string  Date range without year, e.g. "May 31-June 3" */
    public function getShortDateRangeWithoutYearAttribute()
    {
        $val = Carbon::parse($this->attributes['start_date'])->format('F j') . '-';
        if (Carbon::parse($this->attributes['start_date'])->format('F') === Carbon::parse($this->attributes['end_date'])->format('F')) {
            $val .= Carbon::parse($this->attributes['end_date']) ->format('j');
        } else {
            $val .= Carbon::parse($this->attributes['end_date']) ->format('F j');
        }
        return $val;
    }

    /** @return string  e.g. "Thursday May 31 to Sunday June 3, 2016" */
    public function getLongDateRangeWithWeekdaysAttribute()
    {
        $val = Carbon::parse($this->attributes['start_date'])->format('l F j') . ' to ';
        $val .= Carbon::parse($this->attributes['end_date']) ->format('l F j, Y');
        return $val;
    }

    /** @return string  Formatted closing time, e.g. "3:00 pm" */
    public function getEndTimeAttribute()
    {
        return $this->attributes['end_date']->format('g:i a');
    }

    /**
     * Primary emergency contact name.
     * Returns free-text name if stored; otherwise looks up the linked User.
     *
     * @return string
     */
    public function getEmergencyContact1Attribute()
    {
        return $this->attributes['emergency_poc_name'] ?? $this->attributes['emergency_poc_id'] ? User::find($this->attributes['emergency_poc_id'])->name : '';
    }

    /**
     * Primary emergency contact phone.
     * Returns free-text phone if stored; otherwise looks up the linked User.
     *
     * @return string
     */
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

    /** @return string  Static weather forecast text shown in candidate materials */
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

    /** @return string  Candidate fee as a whole-dollar string, e.g. "250" */
    public function getCandidateCostAttribute()
    {
        if (empty($this->attributes['candidate_cost'])) {
            return '';
        }

        return number_format($this->attributes['candidate_cost'], 0);
    }

    /**
     * Team fee as a whole-dollar string, e.g. "150".
     *
     * @return string
     * @TODO Pass $value parameter instead of re-reading from $this->attributes
     */
    public function getTeamFeesAttribute()
    {
        if (empty($this->attributes['team_fees'])) {
            return '';
        }

        return number_format($this->attributes['team_fees'], 0);
    }

    /**
     * Summary string for rollo-room capacity planning (palanca preparation).
     *
     * Calculates candidate tables (max 6, ~4 candidates each) and total people in the room
     * (candidates + 2 table assistants per table).
     *
     * @return string  e.g. "28 people at 5 candidate tables (plus tables for Rector + Head Cha + SDs + AV chas)"
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

    /**
     * Total unique confirmed team members (a member in multiple roles is counted once).
     *
     * @return int
     */
    public function getTotalteamAttribute()
    {
        return $this->team_all_visibility->unique('memberID')->count();
    }

    /** @return int  Number of candidates registered for this weekend */
    public function getTotalcandidatesAttribute()
    {
        return $this->candidates->count();
    }

    /** @return int  Candidates + unique confirmed team members combined */
    public function getTotalteamandcandidatesAttribute()
    {
        return $this->candidates->count() + $this->team_all_visibility->unique('memberID')->count();
    }

    /**
     * Confirmed team assignments, subject to the WeekendAssignments global scope.
     *
     * The global scope only returns assignments for Community-visible weekends (visibility_flag >= 6).
     * For weekends that haven't been released to the community yet, use `$this->team_all_visibility`.
     *
     * @return \Illuminate\Support\Collection  WeekendAssignments with 'user' and 'role' eager-loaded
     */
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

    /**
     * Confirmed team assignments bypassing the global visibility scope.
     *
     * Use this when you need team data for a weekend whose visibility_flag is below 'Community',
     * e.g. building the roster for a Rector on a non-released weekend.
     *
     * @return \Illuminate\Support\Collection
     */
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

    /**
     * Unique confirmed team members (de-duplicated by memberID).
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTeamUniqueAttribute()
    {
        return $this->team_all_visibility->unique('memberID');
    }

    /**
     * IDs of all Head Cha (roleID=2) assignments for this weekend.
     *
     * Returns a Collection because multiple Head Chas are possible.
     * Use ->contains($userId) or ->first() on the result.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getHeadChaAttribute()
    {
        // note: this returns a collection. Get values by calling contains() or first() on it, or looping thru each()
        return $this->team_all_visibility->where('roleID', 2)->pluck('memberID');
    }

    /**
     * IDs of all AH (Assistant Head) Cha (roleID=3) assignments for this weekend.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAhChaAttribute()
    {
        // note: this returns a collection. Get values by calling contains() on it, or looping thru each()
        return $this->team_all_visibility->where('roleID', 3)->pluck('memberID');
    }

    /**
     * Member ID of the Rover (roleID=5) for this weekend, or null.
     *
     * @return int|null
     */
    public function getRoverAttribute()
    {
        return $this->team_all_visibility->where('roleID', 5)->pluck('memberID')->first();
    }

    public function getBackupRectorAttribute()
    {
        return $this->team_all_visibility->where('roleID', 4)->pluck('memberID');
    }

    /**
     * IDs of the weekend leaders: Rector (1), Head Cha (2), and AH Cha (3).
     *
     * @return \Illuminate\Support\Collection
     */
    public function getWeekendLeadersAttribute()
    {
        // note: this returns a collection; search using contains() or each()
        return $this->team_all_visibility->whereIn('roleID', [1, 2, 3])->pluck('memberID');
    }

    /**
     * True if the currently authenticated user (as Head or AH Cha) may track fee payments.
     *
     * @return bool
     */
    public function getMayTrackTeamPaymentsAttribute()
    {
        return $this->team->whereIn('roleID', [2, 3])->pluck('memberID')->contains(auth()->id());
    }

    /**
     * Count of local community team members on this weekend.
     *
     * "Local" = member's community matches config('site.local_community_filter').
     * Extended community members (from other Tres Dias chapters) are not counted.
     *
     * @return int
     */
    public function getLocalAttribute()
    {
        return User::whereIn('id', $this->teamUnique->pluck('memberID'))
            ->where('community', config('site.local_community_filter', config('site.community_acronym')))
            ->count();
    }

    /**
     * Count of team members whose original attendance weekend was a local-community weekend.
     *
     * Different from getLocalAttribute() which checks current community membership.
     *
     * @return int
     */
    public function getTeamMembersWhoAttendedOneOfOurWeekendsAttribute()
    {
        return User::query()
        ->whereIn('id', $this->teamUnique->pluck('memberID'))
        ->where('weekend', config('site.local_community_filter', config('site.community_acronym')))
        ->count();
    }

    /**
     * All candidates registered for this weekend.
     *
     * IMPORTANT: Candidates are linked by a STRING match on users.weekend = shortname.
     * There is no FK relationship between users and weekends for candidates.
     * Results are ordered by first name then last name.
     *
     * @return \Illuminate\Support\Collection  Collection of User models
     */
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

    /**
     * All sponsors for candidates registered for this weekend.
     *
     * For each candidate, retrieves their sponsor. Also includes the sponsor's spouse
     * if not already in the list (handles couples who sponsor together).
     *
     * @return \Illuminate\Support\Collection  Collection of User models, ordered by last name
     */
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

    /**
     * Full URL for the banner image.
     *
     * Handles both absolute URLs (stored as full http/https strings) and relative
     * storage paths (resolved via the local storage disk URL helper).
     *
     * @return string|null
     */
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

    /**
     * Full URL for the team photo.
     *
     * Same URL-resolution logic as banner_url.
     *
     * @return string|null
     */
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

    // -------------------------------------------------------------------------
    // Authorization helpers
    // -------------------------------------------------------------------------

    /**
     * Whether a given user may view this weekend's team roster.
     *
     * Note: this governs VIEW access only. EDIT access is controlled by
     * App\Policies\TeamAssignmentsPolicy.
     *
     * Access is granted if the user is:
     * - The Rector of this weekend
     * - An Admin, President, or Super-Admin
     * - A Men's/Women's Leader or Rector Selection member (matching weekend gender)
     * - A Head Cha or Rover (once visibility >= HeadChas level)
     * - A user with 'edit team member assignments' permission (matching gender)
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

        if ($this->visibility_flag >= WeekendVisibleTo::HeadChas && $this->backup_rector->contains($user->id)) {
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

    // -------------------------------------------------------------------------
    // Eloquent Relationships
    // -------------------------------------------------------------------------

    /**
     * The Rector (leader) of this weekend.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rector()
    {
        return $this->belongsTo(User::class, 'rectorID');
    }

    /**
     * The designated emergency point of contact (a community member User).
     *
     * May be null; some weekends store free-text emergency_poc_name instead.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function emergency_poc()
    {
        return $this->belongsTo(User::class, 'emergency_poc_id');
    }

    /**
     * The Prayer Wheel associated with this weekend.
     *
     * Note the non-standard key direction: uses Weekend.id matched against PrayerWheel.weekendID.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prayerwheel()
    {
        return $this->belongsTo(PrayerWheel::class, 'id', 'weekendID');
    }

    // -------------------------------------------------------------------------
    // Finance helpers
    // -------------------------------------------------------------------------

    /**
     * Fetch fee payment records and team assignment records for this weekend.
     *
     * Used by TeamFeePaymentsController and the payment statistics display on the Weekend page.
     *
     * Business rules:
     * - Only Accepted assignments (confirmed >= Accepted) are included
     * - Members assigned to multiple roles are de-duplicated
     * - Spiritual Directors may be excluded per config('site.team_fees_spiritual_directors_exempt')
     * - Payment records without a matching current assignment are filtered out ("orphaned drops")
     *
     * @return array{0: \Illuminate\Support\Collection, 1: \Illuminate\Support\Collection}
     *   [$payments, $assignments]
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

    /**
     * Compute team fee payment statistics split by local vs. extended community.
     *
     * "Local" = user's community matches config('site.local_community_filter').
     *
     * @return array{
     *   local_paid: int,
     *   extended_paid: int,
     *   local_positions: int,
     *   extended_positions: int,
     *   local_percent: float,
     *   extended_percent: float
     * }
     */
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('weekends')
            ->logAll()
            ->logOnlyDirty();
    }
}
