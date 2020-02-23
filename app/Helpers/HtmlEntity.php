<?php
namespace App\Helpers;

class HtmlEntity
{
    public static function phoneNonBreaking($string): string
    {
        return e(str_replace(['-','.'], '&#8209;', preg_replace('/[^\w\s:&#;)(-.]/', '', $string)), false);
    }

    public static function spacesNonBreaking($string): string
    {
        return e(str_replace(' ', '&nbsp;', $string), false);
    }

    public static function spacesStripNonBreaking($string): string
    {
        return e(str_replace('&nbsp;', ' ', $string), false);
    }
}
