<?php

/*
 * Adapted from concepts in Bootstrap CMS.
 * (c) Graham Campbell <graham@mineuk.com>
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Enable Google Analytics
    |--------------------------------------------------------------------------
    |
    | This defines if Google Analytics is enabled.
    |
    | Requires a valid Google Analytics Tracking ID.
    |
    | Default to false.
    |
    */

    'google' => env('APP_ENV') == 'production' && env('GOOGLE_ANALYTICS_ID', '') != '',

    /*
    |--------------------------------------------------------------------------
    | Google Analytics Tracking ID
    |--------------------------------------------------------------------------
    |
    | This defines the Google Analytics Tracking ID to use.
    |
    | Default to 'UA-XXXXXXXX-X'.
    |
    */

    'googleid' => env('GOOGLE_ANALYTICS_ID', ''),

];
