<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * Enum WeekendVisibleTo
 *
 * Controls the progressive disclosure of weekend details to community members.
 * Stored as an integer in weekends.visibility_flag.
 *
 * The values form an ascending scale — higher values reveal MORE information.
 * Each level is a superset of the level below it.
 *
 * ## Scale
 *
 * | Value | Constant       | Who sees team details                               |
 * |-------|----------------|-----------------------------------------------------|
 * | 0     | AdminOnly      | Nobody except admins (default for new weekends)     |
 * | 1     | Calendar       | Weekend appears on calendar; no team shown          |
 * | 2     | RectorOnly     | Rector can see their own team assignment            |
 * | 3     | ThemeVisible   | Weekend theme/verse shown publicly; no team         |
 * | 4     | HeadChas       | Rector + Head Cha + Rover can view the team roster  |
 * | 5     | SectionHeads   | All section heads can view the team roster          |
 * | 6     | Community      | Entire community can view confirmed details         |
 *
 * ## Usage in Blade
 * Display the description for a given value:
 *   {{ \App\Enums\WeekendVisibleTo::getDescription($weekend->visibility_flag) }}
 *
 * ## Usage in form validation
 *   use App\Enums\WeekendVisibleTo;
 *   use BenSampo\Enum\Rules\EnumValue;
 *   $this->validate($request, [
 *       'visibility_flag' => ['required', new EnumValue(WeekendVisibleTo::class)],
 *   ]);
 *
 * ## Critical impact on queries
 * The WeekendAssignments model has a global scope 'visibleWeekendsOnly' that restricts
 * team queries to weekends where visibility_flag >= Community (6). Any query on
 * WeekendAssignments that should return results for unreleased weekends must use:
 *   ->withoutGlobalScope('visibleWeekendsOnly')
 *
 * See documentation at: https://sampo.co.uk/blog/using-enums-in-laravel and repo at https://github.com/BenSampo/laravel-enum#methods
 * @see https://github.com/BenSampo/laravel-enum
 * @see App\Models\WeekendAssignments  (global scope)
 * @see App\Models\Weekend::teamCanBeViewedBy()
 */
final class WeekendVisibleTo extends Enum
{
    /** No public visibility; only accessible by admins. Default for newly created weekends. */
    public const AdminOnly = 0;

    /** Weekend appears on the public calendar with dates, but no team or theme details. */
    public const Calendar = 1;

    /** Rector can view their own team, but details are not yet shared with the team. */
    public const RectorOnly = 2;

    /** Weekend theme and verse are visible; team roster remains hidden. */
    public const ThemeVisible = 3;

    /** Rector, Head Cha, and Rover can see and edit the team roster. */
    public const HeadChas = 4;

    /** All section heads can see the team roster. */
    public const SectionHeads = 5;

    /** Full team details visible to all community members. Weekend is "announced". */
    public const Community = 6;

    /**
     * Return a human-readable description for a given visibility level.
     *
     * Used in the weekend edit form dropdown and for display in reports.
     *
     * @param int $value
     * @return string
     */
    public static function getDescription($value): string
    {
        switch ($value) {
            case self::AdminOnly:
                return 'Admin Only (Default)';
            case self::Calendar:
                return 'Calendar Only';
            case self::RectorOnly:
                return 'Calendar but no Theme or Team details';
            case self::ThemeVisible:
                return 'Theme visible, but no Team details';
            case self::HeadChas:
                return 'Rector and Head Cha can see Team';
            case self::SectionHeads:
                return 'Rector and all Section Heads can see Team';
            case self::Community:
                return 'Everyone can see all confirmed details';
            default:
                return self::getKey($value);
        }
    }
}
