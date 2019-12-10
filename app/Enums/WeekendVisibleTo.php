<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * Enum WeekendVisibleTo
 *
 * Blade, to display description of a given status:
 * <p>{{ \App\Enums\WeekendVisibleTo::getDescription($weekend->visibility_flag) }}</p>
 *
 * Validation:
 *     use App\Enums\WeekendVisibleTo;
 *     use BenSampo\Enum\Rules\EnumValue;
 *     $this->validate($request, [
 *          'type' => ['required', new EnumValue(WeekendVisibleTo::class)],
 *      ]);
 *
 * See documentation at: https://sampo.co.uk/blog/using-enums-in-laravel and repo at https://github.com/BenSampo/laravel-enum#methods
 */

final class WeekendVisibleTo extends Enum
{
    public const AdminOnly = 0;
    public const Calendar = 1;
    public const RectorOnly = 2;
    public const ThemeVisible = 3;
    public const HeadChas = 4;
    public const SectionHeads = 5;
    public const Community = 6;

    /**
     * Get the description for an enum value
     *
     * @param  int  $value
     * @return string
     */
    public static function getDescription($value): string
    {
        switch ($value) {
            case self::AdminOnly:
                return 'Admin Only (Default)';
            break;
            case self::Calendar:
                return 'Calendar Only';
            break;
            case self::RectorOnly:
                return 'Calendar but no Theme or Team details';
            break;
            case self::ThemeVisible:
                return 'Theme visible, but no Team details';
            break;
            case self::HeadChas:
                return 'Rector and Head Cha can see Team';
            break;
            case self::SectionHeads:
                return 'Rector and all Section Heads can see Team';
            break;
            case self::Community:
                return 'Everyone can see all confirmed details';
            break;
            default:
                return self::getKey($value);
        }
    }
}
