<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Community extends Model
{
    protected $table = 'tresdias_communities';

    /**
     * Lookup a community name from abbreviation
     * @param string $abbreviation
     * @return string
     */
    public static function expandNameFromAbbreviation(string $abbreviation)
    {
        if (Str::contains($abbreviation, ['Tres Dias', 'Emmaus', 'Banquet'])) {
            return $abbreviation;
        }

        $community = self::firstWhere('abbreviation', $abbreviation);

        if ($community) {
            return $community->community_name;
        }

        return $abbreviation;
    }

    /**
     * Lookup long community name from an abbreviated Weekend Name string
     *
     * @param string $weekendName
     * @return string
     */
    public static function expandWeekendNameFromAbbreviation(string $weekendName): string
    {
        if (Str::contains($weekendName, ['Tres Dias', 'Emmaus', 'Banquet'])) {
            return $weekendName;
        }

        if (preg_match('/([A-Za-z ]{3,})[\s#]*(\d+)?/', $weekendName, $matches)) {
            $community = self::firstWhere('abbreviation', $matches[1]);

            if ($community) {
                return $community->community_name . (isset($matches[2]) ? ' #' . $matches[2] : '');
            }
        }

        return $weekendName;
    }

}
