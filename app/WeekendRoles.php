<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class WeekendRoles extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected $casts = [
        'isDeptHead'            => 'boolean',
        'canEmailEntireTeam'    => 'boolean',

        // used for rector planning:
        'isCoreTalk'            => 'boolean',
        'isBasicTalk'           => 'boolean',
        'excludeAsFormerRector' => 'boolean',
        'requiredForRector'     => 'boolean',
    ];

    protected $with = 'section';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('rolesBySortOrder', function (Builder $query) {
            $query->orderBy('sortorder', 'asc');
        });
    }

    /**
     * Set sort order to sortorder:ascending
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeSort(Builder $query)
    {
        return $query->orderBy('sortorder', 'asc');
    }

    /**
     * This is used to filter results where the "exclude roles/service records for/of former rectors"
     * flag is set ... which is utilized by the Leaders Worksheet report, used by Rectors when planning teams.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeRolesOfFormerRectors(Builder $query)
    {
        return $query->where('excludeAsFormerRector', 1);
    }

    public function scopeRolesRectorCanAssign(Builder $query)
    {
        return $query->whereNotIn('RoleName', [
            'Rector',
            'Candidate',
//            'Spiritual Director',
//            'Head Spiritual Director',
            ]);
    }

    public function scopeRolesSpiritualAdvisorCanAssign(Builder $query)
    {
        return $query->whereIn('RoleName', [
            'Spiritual Director',
            'Head Spiritual Director',
            'Spiritual Advisor',
        ]);
    }

    public function scopeAssignRectors(Builder $query)
    {
        return $query->whereIn('RoleName', ['Rector']);
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
}
