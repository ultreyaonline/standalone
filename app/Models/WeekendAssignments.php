<?php

namespace App\Models;

use App\Enums\WeekendVisibleTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class WeekendAssignments
 *
 * Represents a single team member's assignment to a role on a retreat weekend.
 * This is the pivot/join model between Users, Weekends, and WeekendRoles.
 *
 * ## Column summary
 * - weekendID  → FK to weekends.id
 * - memberID   → FK to users.id
 * - roleID     → FK to weekend_roles.id
 * - confirmed  → integer status (see App\Enums\TeamAssignmentStatus: 0=Pending ... 4=Accepted ... 8=DonatedFees)
 * - comments   → free-text notes on the assignment
 *
 * ## ⚠️ CRITICAL: Global scope "visibleWeekendsOnly"
 *
 * A global scope is registered in boot() that restricts ALL queries on this model to
 * weekends with visibility_flag >= WeekendVisibleTo::Community (6).
 *
 * This means if you query WeekendAssignments and get no results for a weekend you know
 * has assignments, the weekend's visibility_flag is probably below 6.
 *
 * To bypass the scope (needed for admin/Rector views of unreleased weekends):
 *   WeekendAssignments::withoutGlobalScope('visibleWeekendsOnly')->where(...)->get()
 *
 * The Weekend model's team_all_visibility accessor does this automatically.
 *
 * @property int    $id
 * @property int    $weekendID
 * @property int    $memberID
 * @property int    $roleID
 * @property int    $confirmed   See TeamAssignmentStatus enum
 * @property string $comments
 *
 * @package App
 */
class WeekendAssignments extends Model
{
    use LogsActivity;
    use HasFactory;

//integer('weekendID')->unsigned()->references('id')->on('weekends')->index('byweekendid');
//integer('memberID')->unsigned()->references('id')->on('users')->index('bymember');
//integer('roleID')->unsigned()->references('id')->on('weekend_roles')->index('byrole');
//integer('confirmed')->default(0)->index('byconfirmed');
//string('comments')->default('');
//timestamps();

    protected $fillable = ['weekendID', 'memberID', 'roleID', 'confirmed', 'comments'];
    protected $casts = ['confirmed' => 'integer'];

    /** Eager-load the role relationship on every query to avoid N+1 in team roster views */
    protected $with = 'role';

    /**
     * Route Model Binding key.
     *
     * Uses weekendID instead of the primary key for URL binding.
     * This means route model binding on {weekendAssignment} resolves by weekendID.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'weekendID';
    }

    /**
     * Boot the model and register the global scope.
     *
     * The 'visibleWeekendsOnly' global scope filters all queries to weekends
     * with visibility_flag >= Community. Use withoutGlobalScope('visibleWeekendsOnly')
     * to bypass this when you need data for non-released weekends.
     */
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

    // -------------------------------------------------------------------------
    // Eloquent Relationships
    // -------------------------------------------------------------------------

    /**
     * The community member assigned to this role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'memberID');
    }

    /**
     * The role definition for this assignment (e.g. "Rector", "Head Cha", "SD").
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(WeekendRoles::class, 'roleID');
    }

    /**
     * The retreat weekend this assignment belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function weekend()
    {
        return $this->belongsTo(Weekend::class, 'weekendID');
    }

    // -------------------------------------------------------------------------
    // Computed Attributes
    // -------------------------------------------------------------------------

    /**
     * Whether this assignment record was created or updated within the last 21 days.
     *
     * Used in the UI to highlight recently changed team assignments.
     *
     * @return bool
     */
    public function getModifiedInLastThreeWeeksAttribute()
    {
        return $this->attributes['updated_at'] > Carbon::now()->addDays(-21);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('team-assignments')
            ->logAll()
            ->logOnlyDirty();
    }
}
