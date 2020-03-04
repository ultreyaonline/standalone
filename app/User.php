<?php

namespace App;

use App\Enums\WeekendVisibleTo;
use App\Events\UserAdded;
use App\Events\UserDeleted;
use App\Helpers\UniqueId;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Builder;
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
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    use Impersonate;
    use Notifiable;
    use HasRoles;
    use CausesActivity;
    use LogsActivity;
    use HasMediaTrait;

    protected $fillable = [
      'email','password', 'first', 'last', 'address1', 'address2', 'city', 'state', 'postalcode', 'country',
      'homephone', 'cellphone', 'workphone', 'spouseID', 'church', 'weekend',
      'sponsor', 'sponsorID', 'gender', 'community', 'interested_in_serving', 'active',
      'inactive_comments', 'skills', 'qualified_sd', 'avatar', 'username', 'emergency_contact_details'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'active'                                    => 'boolean',
        'qualified_sd'                              => 'boolean',
        'interested_in_serving'                     => 'boolean',
        'receive_prayer_wheel_invites'              => 'boolean',
        'receive_prayer_wheel_reminders'            => 'boolean',
        'receive_email_weekend_general'             => 'boolean',
        'receive_email_community_news'              => 'boolean',
        'okay_to_send_serenade_and_palanca_details' => 'boolean',
        'unsubscribe'                               => 'boolean',
    ];

    protected $dates = [
        'last_login_at',
        'unsubscribe_date',
    ];

    /**
     * The attributes that should be hidden from json arrays
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = [ 'password', 'remember_token'];
    protected static $logOnlyDirty = true;


    public function isOnline(): bool
    {
        return Cache::has($this->getWhosOnlineKey());
    }

    public function getWhosOnlineKey(): string
    {
        return 'user-is-online-' . $this->id;
    }


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

    /** mainly used for quickly sorting by last,first */
    public function getLastfirstAttribute()
    {
        return $this->attributes['last'] . ' ' . $this->attributes['first'];
    }

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

    public function getInactiveAttribute()
    {
        return ! $this->attributes['active'];
    }

    public function getUidhashAttribute()
    {
        if (! empty($this->attributes['uidhash'])) {
            return $this->attributes['uidhash'];
        }

//        $faker = Factory::create(config('app.locale'));
//        $val = $faker->uuid;

        $hash = UniqueId::generate(10, $this, 'uidhash');

//        DB::update('UPDATE ' . $this->getTable() . ' SET uidhash = "' . $hash . '" where id =' . (int)$this->id);
        $this->attributes['uidhash'] = $hash;
        // persist to db (only if exists, so that unit tests can run in memory)
        if ($this->exists) {
            $this->save();
        }
        return $hash;
    }

    public function getInMailchimpAttribute()
    {
        if (!empty($this->attributes['email']) && config('newsletter.apiKey')) {
            return \Spatie\Newsletter\NewsletterFacade::isSubscribed($this->attributes['email'], config('newsletter.defaultListName'));
        }
        return null;
    }

    public function getWeekendLongNameAttribute()
    {
        $weekendName = $this->attributes['weekend'];

        return Community::expandWeekendNameFromAbbreviation($weekendName);
    }

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

    public function scopeActive(Builder $query)
    {
        return $query->where('active', 1);
    }

    public function scopeInactive(Builder $query)
    {
        return $query->where('active', 0);
    }

    public function scopeNotunsubscribed(Builder $query)
    {
        return $query->where('unsubscribe', 0);
    }

    public function scopeOnlyLocal(Builder $query)
    {
        return $query->where('community', '=', config('site.local_community_filter', config('site.community_acronym')));
    }

    public function scopeOnlyNonlocal(Builder $query)
    {
        return $query->where('community', '!=', config('site.local_community_filter', config('site.community_acronym')));
    }

    /**
     * Search all relevant fields for the specified lookup value
     * Fields included are: name, email, city, state, homephone, cellphone, workphone, church, weekend, skills
     *
     * @param Builder $query
     * @param $searchString
     *  @return Builder|static
     */
    public function scopeSearch(Builder $query, $searchString)
    {
        $slug = str_replace('-', ' ', strtolower($searchString));

        $lookups = preg_split('/ /', $searchString);

        $search = $query->where('active', 1);

        foreach ($lookups as $lookup) {
            $search = $search->where(function ($query) use ($slug, $lookup) {
                return $query->where('first', 'like', '%' . $lookup . '%')
                    ->orWhere('last', 'like', '%' . $lookup . '%')
//              ->orWhere('name', 'like', '%' . $lookup . '%')
                    ->orWhere('email', 'like', '%' . $lookup . '%')
                    ->orWhere('weekend', $lookup)
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
        if ($userIDToEdit === $this->id) {
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

    public function weekendAssignmentsAnyVisibility()
    {
        return $this->hasMany(WeekendAssignments::class, 'memberID')->withoutGlobalScope('visibleWeekendsOnly');
    }

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

    public function spouse()
    {
        return $this->belongsTo(User::class, 'spouseID');
    }

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


    // Override method in Illuminate\Auth\Passwords\CanResetPassword trait
    /**
     * Send the password reset notification, using our App's custom notification object.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Return true or false if the user can impersonate another user.
     *
     */
    public function canImpersonate(): bool
    {
        return $this->hasRole('Admin') || $this->hasRole('Super-Admin');
    }

    /**
     * Return true or false if the user can be impersonated.
     * Here we deny impersonation of oneself as that would be pointless.
     */
    public function canBeImpersonated(): bool
    {
        return $this->id != Auth::id();
    }

    /**
     * Override to include "name" attribute, for compatibility with core/default Laravel behavior
     * (Beneficial for error logging, etc)
     * @return array
     */
    public function toArray()
    {
        $retVal = parent::toArray();
        $retVal['name'] = $this->name;
        return $retVal;
    }

    /**
     * Checks if this User is assigned as a Rector to a weekend which hasn't ended yet.
     *
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
     * Checks if this User is assigned as a Rover to a weekend which hasn't ended yet.
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
    public function registerMediaCollections()
    {
        // Avatar is a single image, so subsequent images replace prior ones
        $this
            ->addMediaCollection('avatar')
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this
                    ->addMediaConversion('avatar')
                    ->width(800)
                    ->height(600)
//                    ->orientation(Manipulations::ORIENTATION_AUTO)
                    ;
            });
    }




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
}
