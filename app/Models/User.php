<?php

namespace App\Models;

use App\Enums\WeekendVisibleTo;
use App\Events\UserAdded;
use App\Events\UserDeleted;
use App\Helpers\UniqueId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Lab404\Impersonate\Models\Impersonate;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Newsletter\Facades\Newsletter;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 *
 * Represents every person in the system: pescadores (community members), candidates
 * (retreat attendees in the pre-weekend process), and administrators. One table for all.
 *
 * ## KEY DESIGN NOTES
 *
 * ### users.weekend — string, NOT a foreign key
 * The `weekend` column stores the attended weekend as a plain string, e.g. "ANYTD #47".
 * This matches Weekend::shortname. There is no FK to the weekends table by design.
 * Do NOT add a FK constraint without a full data migration.
 *
 * ### Candidates are also Users
 * When a candidate registers, a User record is created first. The Candidate model then
 * links to it via m_user_id / w_user_id. After the weekend, the User becomes a pescador;
 * the Candidate record is effectively retired.
 *
 * ### Gender normalisation
 * boot() normalises gender to uppercase M/W on create and update. 'F' is converted to 'W'.
 *
 * ### Email opt-in flags
 * Several boolean columns control automated email eligibility:
 *   receive_prayer_wheel_invites, receive_prayer_wheel_reminders,
 *   receive_email_weekend_general, receive_email_community_news,
 *   okay_to_send_serenade_and_palanca_details, unsubscribe
 *
 * ### Audit logging
 * All changes are logged via spatie/laravel-activitylog, except password and remember_token.
 *
 * ### Model events registered in boot()
 * - creating:  normalise gender, set created_by
 * - created:   fire UserAdded event → notification emails
 * - updating:  normalise gender
 * - deleting:  cascade-delete related records, clear spouse link, delete avatar file
 * - deleted:   fire UserDeleted event
 *
 * @property int         $id
 * @property string      $first
 * @property string      $last
 * @property string      $email
 * @property string      $username
 * @property string      $gender          'M' or 'W' only
 * @property string|null $weekend         Short name string, e.g. "ANYTD #47"
 * @property string|null $community       Community acronym, e.g. "ANYTD"
 * @property int|null    $spouseID        FK → users.id
 * @property int|null    $sponsorID       FK → users.id
 * @property string|null $sponsor         Legacy free-text sponsor name (prefer sponsorID)
 * @property bool        $active
 * @property bool        $qualified_sd
 * @property bool        $interested_in_serving
 * @property bool        $receive_prayer_wheel_reminders
 * @property Carbon|null $last_login_at
 * @property string|null $uidhash         UUID for unsubscribe links; lazily generated
 *
 * @package App
 */
class User extends Authenticatable implements HasMedia
{
    use Impersonate;
    use Notifiable;
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use HasRoles;
    use CausesActivity;
    use LogsActivity;
    use InteractsWithMedia;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
      'email','password', 'first', 'last', 'address1', 'address2', 'city', 'state', 'postalcode', 'country',
      'homephone', 'cellphone', 'workphone', 'spouseID', 'church', 'weekend',
      'sponsor', 'sponsorID', 'gender', 'community', 'interested_in_serving', 'active',
      'inactive_comments', 'skills', 'qualified_sd', 'avatar', 'username', 'emergency_contact_details'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
        'active'                         => 'boolean',
        'qualified_sd'                   => 'boolean',
        'interested_in_serving'          => 'boolean',
        'receive_prayer_wheel_invites'   => 'boolean',
        'receive_prayer_wheel_reminders' => 'boolean',
        'receive_email_weekend_general'  => 'boolean',
        'receive_email_community_news'   => 'boolean',
        'okay_to_send_serenade_and_palanca_details' => 'boolean',
        'unsubscribe'      => 'boolean',
        'last_login_at'    => 'datetime',
        'unsubscribe_date' => 'datetime',
        //'password' => 'hashed',
        ];
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Whether this user is currently online (active within the last N minutes).
     * Presence is tracked by LogLastUserActivity middleware via a Redis cache key.
     */
    public function isOnline(): bool
    {
        return Cache::has($this->getWhosOnlineKey());
    }

    /**
     * The Redis cache key for this user's online presence, e.g. "user-is-online-42".
     *
     */
    public function getWhosOnlineKey(): string
    {
        return 'user-is-online-' . $this->id;
    }

    /**
     * Whether this user has the 'Member' role (i.e. is a pescador / community member).
     *
     */
    public function isMember(): bool
    {
        return $this->hasRole('Member');
    }

    /**
     * Attribute for when full "name" is requested. Simply concatenates first and last to be the name.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->attributes['first'] . ' ' . $this->attributes['last'];
    }

    /** @return string  "Last First" format — useful for sorting */
    public function getLastfirstAttribute()
    {
        return $this->attributes['last'] . ' ' . $this->attributes['first'];
    }

    /** @return string  Single-line address, e.g. "123 Main St, Anytown, ON A1B 2C3" */
    public function getAddressAttribute()
    {
        return $this->attributes['address1']
            . ($this->attributes['address2'] ? ' ' . $this->attributes['address2'] : '')
            . ', ' . $this->attributes['city']
            . ', ' . $this->attributes['state']
            . ' ' . $this->attributes['postalcode']
            . ' ' . $this->attributes['country'];
    }

    public function getAddressFormattedAttribute()
    {
        $br = "<br>\n";
        return e($this->attributes['address1']) . $br
            . ($this->attributes['address2'] ? e($this->attributes['address2']) . $br : '')
            . e($this->attributes['city'])
            . ', ' . e($this->attributes['state']) . $br
            . e($this->attributes['postalcode']) . ' '
            . e($this->attributes['country']);
    }

    /**
     * Build a Map URL from the street address
     * @return string
     */
    public function getMapLinkAttribute()
    {
        $address = $this->attributes['address1']
            . ', ' . $this->attributes['city']
            . ', ' . $this->attributes['state']
            . ', ' . $this->attributes['country'];
        $address_url = config('site.map_url') . str_replace(' ', '+', trim($address, ', '));
        return $address_url;
    }

    public function getSpousenameAttribute()
    {
        if (empty($this->attributes['spouseID'])) {
            return ' (No spouse on file)';
        }

        $spouse = User::find($this->attributes['spouseID']);
        if (! $spouse) {
            return ' (ERROR: CHECK WITH ADMINISTRATOR)';
        }

        return $spouse->first . ' ' . $spouse->last;
    }

    public function getSpouseWeekendHasEndedAttribute()
    {
        if (! $this->spouse) {
            return null;
        }
        $spouse_weekend_shortname = $this->spouse->weekend;

        preg_match('/([A-Z]{3,})\s?#(\d+)/', $spouse_weekend_shortname, $matches);

        if (!isset($matches[1], $matches[2])) {
            return null;
        }

        $weekend = Weekend::where('weekend_MF', $this->spouse->gender)
            ->where('tresdias_community', $matches[1])
            ->where('weekend_number', $matches[2])
            ->first();

        if (! $weekend) {
            return null;
        }

        return $weekend->hasEnded || $weekend->endsToday;
    }

    public function getSponsorAttribute($value)
    {
        if (empty($this->attributes['sponsor']) && ! empty($this->attributes['sponsorID'])) {
            $sponsor = User::find($this->attributes['sponsorID']);
            return $sponsor->first . ' ' . $sponsor->last;
        }
        return $value;
    }

    public function getSponsorTextAttribute($value)
    {
        return $this->attributes['sponsor'] ?? '';
    }

    public function getSponsorPhoneAttribute($value)
    {
        if (empty($this->attributes['sponsor']) && ! empty($this->attributes['sponsorID'])) {
            $sponsor = User::find($this->attributes['sponsorID']);
            return ($sponsor->cellphone ? 'C:&nbsp;'.$sponsor->cellphone : '') . ' ' . ($sponsor->homephone ? 'H:&nbsp;' . $sponsor->homephone : '');
        }
        return $value;
    }

    public function getSponsoreesAttribute()
    {
        $query = User::where('sponsorID', $this->id);
        if (!empty($this->attributes['spouseID'])) {
            $query = $query->orWhere(function ($query) {
                return $query
                ->where('sponsorID', $this->attributes['spouseID'])
                ->where('id', '!=', $this->id);
            });
        }
        $query = $query->orderBy('last', 'asc')->orderBy('first', 'asc')->orderBy('weekend', 'asc');

        return $query->get();
    }

    public function getPhoneAttribute($value)
    {
        return ($this->cellphone ? 'C:&nbsp;'.$this->cellphone : '') . ' ' . ($this->homephone && $this->homephone != $this->cellphone ? 'H:&nbsp;' . $this->homephone : '');
    }

    public function getAvatarAttribute()
    {
        if ($this->getMedia('avatar')->count()) {
            return $this->getFirstMediaUrl('avatar', 'avatar');
        }

        return 'https://www.gravatar.com/avatar/'.md5(Str::lower($this->email)).'.jpg?s=300&d=mp&r=g';
    }

    /**
     * Inverse of active — convenience accessor.
     *
     * @return bool
     */
    public function getInactiveAttribute()
    {
        return ! $this->attributes['active'];
    }

    /**
     * UUID hash used in unsubscribe links and external references.
     *
     * Lazily generated: if not stored, creates a UUID, writes it directly to the DB
     * (bypassing Eloquent events to avoid loops), and caches it on the instance.
     *
     * @return string
     */
    public function getUidhashAttribute()
    {
        if (! empty($this->attributes['uidhash'])) {
            return $this->attributes['uidhash'];
        }

//        $faker = Factory::create(config('app.locale'));
//        $val = $faker->uuid();

        $hash = UniqueId::generate(10, $this, 'uidhash');

        $this->attributes['uidhash'] = $hash;
        // persist to db (only if exists, so that unit tests can run in memory)
        if ($this->exists) {
            $this->save();
        }
        return $hash;
    }

    /**
     * Whether this user's email is subscribed in Mailchimp.
     *
     * Makes a live API call — avoid calling in loops.
     * Returns null if Mailchimp API key is not configured or email is empty.
     *
     * @return bool|null
     */
    public function getInMailchimpAttribute()
    {
        if (!empty($this->attributes['email']) && config('newsletter.driver') && config('newsletter.driver_arguments.apiKey')) {
            return Newsletter::isSubscribed($this->attributes['email'], config('newsletter.default_list_name'));
        }
        return null;
    }

    /**
     * Expands the weekend string acronym to a full community name.
     *
     * e.g. "ANYTD #47" → "Any Tres Dias #47"
     * Falls through to the raw string if the acronym is not in the switch.
     *
     * @TODO: create a 'communities' table for this mapping.
     *
     * @return string
     */
    public function getWeekendLongNameAttribute()
    {
        $weekendName = $this->attributes['weekend'];

        return Community::expandWeekendNameFromAbbreviation($weekendName);
    }

    /**
     * The Weekend model matching this user's attended weekend string.
     *
     * Parses users.weekend (e.g. "ANYTD #47") and queries the weekends table.
     * Returns null if the field is empty or can't be parsed.
     *
     * @return Weekend|null
     */
    public function getWeekendRecordAttribute()
    {
        if (empty($this->attributes['weekend'])) {
            return null;
        }

        preg_match('/([A-Za-z]{3,})\s?#?(\d+)?/', $this->attributes['weekend'], $matches);

        if (!isset($matches[1], $matches[2])) {
            return null;
        }

        $weekend = Weekend::where('weekend_MF', $this->attributes['gender'])
            ->where('tresdias_community', $matches[1])
            ->where('weekend_number', $matches[2])
            ->first();

        if (! $weekend) {
            return null;
        }

        return $weekend;
    }

    public function getModifiedInLastThreeWeeksAttribute()
    {
        return $this->attributes['updated_at'] > Carbon::now()->addDays(-21);
    }

    /**
     * Full service history combining local community assignments and external community assignments.
     *
     * Qualified SDs see full weekend names; others see short names only.
     *
     * @return array  Array of ['id' => int|null, 'name' => string, 'position' => string]
     */
    public function getServingHistoryAttribute()
    {
        $history = [];
        foreach ($this->weekendAssignments as $p) {
            if ($this->qualified_sd) {
                $name = $p->weekend->weekend_full_name;
            } else {
                $name = $p->weekend->shortname;
            }
            $history[] = ['id' => $p->weekend->id, 'name' => $name, 'position' => $p->role->RoleName];
        }
        foreach ($this->weekendAssignmentsExternal as $p) {
            if ($this->qualified_sd) {
                $name = $p->WeekendName;
            } else {
                $name = $p->weekend_shortname;
            }
            $history[] = ['id'=> null, 'name' => $name, 'position' => $p->RoleName];
        }
        return $history;
    }

    /**
     * Accepted team roles this user holds on a specific weekend.
     *
     * @param int  $weekend_id
     * @param bool $ignoreVisibleOnly  Set true to bypass the global visibility scope
     * @return \Illuminate\Support\Collection
     */
    public function rolesForWeekend($weekend_id, $ignoreVisibleOnly = false)
    {
        $query = WeekendAssignments::where('memberID', $this->id)
            ->where('weekendID', $weekend_id)
            ->where('confirmed', \App\Enums\TeamAssignmentStatus::Accepted)
            ->with('role');

        if ($ignoreVisibleOnly) {
            $query = $query->withoutGlobalScope('visibleWeekendsOnly');
        }

        return $query->get();
    }

    /** Filter to active members only. */
    public function scopeActive(Builder $query)
    {
        return $query->where('active', 1);
    }

    /** Filter to inactive members only. */
    public function scopeInactive(Builder $query)
    {
        return $query->where('active', 0);
    }

    /** Exclude members who have globally unsubscribed. */
    public function scopeNotunsubscribed(Builder $query)
    {
        return $query->where('unsubscribe', 0);
    }

    /** Filter to local community members only (config('site.local_community_filter')). */
    public function scopeOnlyLocal(Builder $query)
    {
        return $query->where('community', '=', config('site.local_community_filter', config('site.community_acronym')));
    }

    /**
     * Filter to extended (non-local) community members — those from other Tres Dias chapters.
     */
    public function scopeOnlyNonlocal(Builder $query)
    {
        return $query->where('community', '!=', config('site.local_community_filter', config('site.community_acronym')));
    }

    /**
     * Full-text member search across name, contact, and community fields.
     *
     * Splits on spaces, AND-chains all terms. Active members only. Ordered last, first.
     * Fields: first, last, email, weekend, state, city, church, cellphone, homephone, community, skills.
     *
     * @param Builder $query
     * @param string $searchString
     * @param bool $activeOnly
     * @return Builder|static
     */
    public function scopeSearch(Builder $query, $searchString, $activeOnly=true)
    {
        // @TODO accommodate quoted searches for words that need to be kept together.
        $lookups = preg_split('/ /', $searchString);

        $search = $query;

        if ($activeOnly) {
            $search = $query->where('active', 1);
        }

        foreach ($lookups as $lookup) {
            $search = $search->where(function ($query) use ($lookup) {
                return $query->where('first', 'like', '%' . $lookup . '%')
                    ->orWhere('last', 'like', '%' . $lookup . '%')
                    ->orWhere('email', 'like', '%' . $lookup . '%')
                    ->orWhere('weekend', 'like', '%' . $lookup . '%')
                    ->orWhere('state', $lookup)
                    ->orWhere('country', $lookup)
                    ->orWhere('city', 'like', '%' . $lookup . '%')
                    ->orWhere('church', 'like', '%' . $lookup . '%')
                    ->orWhere('cellphone', 'like', '%' . $lookup . '%')
                    ->orWhere('homephone', 'like', '%' . $lookup . '%')
                    ->orWhere('community', 'like', '%' . $lookup . '%')
                    ->orWhere('skills', 'like', '%' . $lookup . '%');
            });
        }

        return $search->orderBy('last', 'asc')->orderBy('first', 'asc');
    }

    // this static method exists for use by Livewire components
    public static function datatableSearch($searchString)
    {
        return empty($searchString) ? static::query()
            : (new static())->scopeSearch(static::query(), $searchString, false);
    }

    public function canViewUser($userIDToView): bool
    {
        if ($userIDToView === $this->id) {
            return true;
        }
        if ($this->can('view members')) {
            return true;
        }
        // since "edit" implies "view", enable it here too
        if ($this->canEditUser($userIDToView)) {
            return true;
        }
        return false;
    }

    public function canEditUser($userIDToEdit): bool
    {
        // using loose-comparison here in case ULID is used in future.
        if ($userIDToEdit == $this->id) {
            return true;
        }
        if ($this->can('edit members')) {
            return true;
        }
        if ($this->can('edit candidates')) {
            return true;
        }
        return false;
    }

    /**
     * Confirmed (Accepted) weekend assignments, filtered by the weekend visibility level.
     *
     * Users with 'see all weekend assignments regardless of weekend status' see all weekends.
     * Others only see assignments for Community-visible weekends (visibility_flag >= 6).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function weekendAssignments()
    {
        $level = WeekendVisibleTo::Community;
        if (($user = Auth::user()) && $user->can('see all weekend assignments regardless of weekend status')) {
            $level = 0;
        }

        return $this->hasMany(WeekendAssignments::class, 'memberID')
            ->where('confirmed', \App\Enums\TeamAssignmentStatus::Accepted)
            ->whereIn('weekendID', Weekend::where('visibility_flag', '>=', $level)->get()->pluck('id'))
            ->orderBy('weekendID');
    }

    /**
     * All weekend assignments for this user, bypassing the global visibility scope.
     * Includes all statuses (pending, dropped, etc.) — not filtered to Accepted only.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function weekendAssignmentsAnyVisibility()
    {
        return $this->hasMany(WeekendAssignments::class, 'memberID')->withoutGlobalScope('visibleWeekendsOnly');
    }

    /**
     * Service records at external Tres Dias communities.
     * These are stored separately as they have no Weekend model in this system.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function weekendAssignmentsExternal()
    {
        return $this->hasMany(WeekendAssignmentsExternal::class, 'memberID')->orderBy('weekendName');
    }

    public function failedLoginAttempts()
    {
        return $this->hasMany(FailedLoginAttempt::class, 'user_id')->orderBy('created_at');
    }

    public function lastFailedLoginAttempt()
    {
        return $this->hasOne(FailedLoginAttempt::class, 'user_id')->orderBy('created_at')->latest();
    }

    public function prayerWheelSignups()
    {
        return $this->hasMany(PrayerWheelSignup::class, 'memberID')->orderBy('wheel_id')->orderBy('timeslot');
    }

    public function teamFeePayments()
    {
        return $this->hasMany(TeamFeePayments::class, 'memberID')->orderBy('weekendID');
    }

    /**
     * This user's spouse (self-referential).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function spouse()
    {
        return $this->belongsTo(User::class, 'spouseID');
    }

    /**
     * The User who sponsored this person (FK on sponsorID).
     * Distinct from the legacy free-text 'sponsor' column.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function theirSponsor()
    {
        return $this->belongsTo(User::class, 'sponsorID');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'posted_by')->orderBy('start_datetime');
    }

    /**
     * override from CanResetPassword trait
     */
    public function getEmailForPasswordReset()
    {
        return Str::contains($this->username, ['@', '.']) ? $this->username : $this->email;
    }

    /**
     * Return true or false whether the user can impersonate another user.
     *
     */
    public function canImpersonate(): bool
    {
        return $this->hasRole('Admin') || $this->hasRole('Super-Admin');
    }

    /**
     * Return true or false whether the user can be impersonated.
     * Here we deny impersonation of oneself as that would be pointless.
     */
    public function canBeImpersonated(): bool
    {
        // Prevent impersonating Admins or Super-Admins, to avoid confusion and potential lockout scenarios. Admins can impersonate other Admins if needed, but not themselves.
        // Uncomment the following line to disable impersonation of other super-admins/admins.
        //return ! $this->hasAnyRole(['Admin', 'Super-Admin']);

        // prevent pointless self-impersonation, which can create a loop.
        return $this->id != Auth::id();
    }

    /**
     * Override toArray() to include the computed `name` virtual attribute.
     *
     * Ensures 'name' is present in JSON output and error logs where Laravel
     * expects a `name` field on user objects (e.g. Rollbar error context).
     *
     * @return array
     */
    public function toArray()
    {
        $retVal = parent::toArray();
        $retVal['name'] = $this->name;
        return $retVal;
    }

    /**
     * Whether this user is currently assigned as an active Rector (or optionally Head Cha).
     *
     * "Active" means the weekend has not ended more than 28 days ago.
     * Used to determine whether to display rector-specific UI elements.
     *
     * @param string $rectorOrHeadChaToo  'rector' checks roleID 1 only; 'head' adds roleID 2 (Head Cha)
     * @return bool
     */
    public function isAnActiveRector($rectorOrHeadChaToo = 'rector')
    {
        $roleIds = [1];
        if ($rectorOrHeadChaToo == 'head') {
            $roleIds[] = 2;
        }
        $leaderAssignments = $this->weekendAssignmentsAnyVisibility->whereIn('roleID', $roleIds);

        foreach ($leaderAssignments as $w) {
            if (Weekend::find($w->weekendID)->ended_over_a_month_ago === false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether this user is currently assigned as an active Rover (roleID 5).
     *
     * "Active" means the weekend has not ended more than 28 days ago.
     *
     * @return bool
     */
    public function isAnActiveRover()
    {
        $roverAssignments = $this->weekendAssignmentsAnyVisibility->where('roleID', 5);

        foreach ($roverAssignments as $w) {
            if (Weekend::find($w->weekendID)->ended_over_a_month_ago === false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Register Spatie Media-Library collections
     */
    public function registerMediaCollections(): void
    {
        // Avatar is a single image, so subsequent images replace prior ones
        $this
            ->addMediaCollection('avatar')
            ->singleFile();
    }

    /**
     * Register standardized media resizing conversion
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('avatar')
             ->width(800)
             ->height(600)
             //->orientation(Manipulations::ORIENTATION_AUTO)
             ;
    }



    /**
     * Register model lifecycle hooks.
     *
     * - creating:  Normalises gender to M/W (converts F→W), sets created_by.
     * - created:   Fires UserAdded event → notification emails to configured recipients.
     * - updating:  Re-normalises gender.
     * - deleting:  Cascade-deletes related records (login attempts, assignments, prayer signups,
     *              fee payments); clears spouse's spouseID link; deletes stored avatar file.
     * - deleted:   Fires UserDeleted event → notification emails.
     *
     * @TODO (in deleting): update sponsorees' sponsor name/nullify sponsorID
     * @TODO (in deleting): handle calendar events posted by this user
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (User $user) {
            if (!empty($user['gender'])) {
                // force M/W
                $user['gender'] = strtoupper($user['gender']);
                if ($user['gender'] === 'F') {
                    $user['gender'] = 'W';
                }
            }

            // set creator name
            if (empty($user['created_by'])) {
                $user['created_by'] = optional(Auth::user())->name ?? 'System';
            }

            return $user;
        });

        static::created(function (User $user) {
            event(UserAdded::class, ['user'=> $user, 'by'=> optional(Auth::user())->name ?? 'System' ]);
        });

        static::updating(function (User $user) {
            if (!empty($user['gender'])) {
                // force M/W
                $user['gender'] = strtoupper($user['gender']);
                if ($user['gender'] === 'F') {
                    $user['gender'] = 'W';
                }
            }

            return $user;
        });

//        static::updated(function($user) {
//            event(UserUpdated::class, ['user'=> $user->name, 'by'=> optional(Auth::user())->name ?? 'System' ]);
//        });

        static::deleting(function (User $user) {
            $user->failedLoginAttempts()->delete();
            $user->weekendAssignments()->delete();
            $user->weekendAssignmentsExternal()->delete();
            $user->prayerWheelSignups()->delete();
            $user->clearMediaCollection('avatar');

            // @TODO: create an audit trail, or remember historical payments somehow
            $user->teamFeePayments()->delete();

            // Foreign Key will also cascade this automatically:
//            if ($user->spouse) {
//                $user->spouse->update(['spouseID' => null]);
//            }

            // @TODO - for everyone they sponsored, update the sponsor name (if blank) before deleting, since the foreign key will null the sponsorID

            return $user;
        });

        static::deleted(function (User $user) {
            event(UserDeleted::class, ['who' => $user->name, 'by'=> optional(Auth::user())->name ?? 'System' ]);
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('members')
            ->logAll()
            ->dontLogIfAttributesChangedOnly(['password', 'remember_token'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
