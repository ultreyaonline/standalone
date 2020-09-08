<?php

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Weekend::class, function (Faker $faker) {

    $mf        = $faker->randomElement(['M', 'W']);
    $gender    = ($mf == 'M') ? "Men's" : "Women's";
    $number    = $faker->numberBetween(50, 75);
    $startdate = $faker->dateTimeBetween('now', '18 months')->setTime(18,0);
    $enddate   = $startdate->add(new DateInterval('P3D'));

    return [
        'weekend_full_name'            => config('site.community_acronym') . $gender . ' #' . $number,
        'weekend_number'               => $number,
        'weekend_MF'                   => $mf,
        'tresdias_community'           => config('site.community_acronym'),
        'start_date'                   => $startdate,
        'end_date'                     => $enddate,
        'candidate_arrival_time'       => $startdate,
        'serenade_arrival_time'        => $enddate,
        'closing_arrival_time'         => $enddate,
        'closing_scheduled_start_time' => $enddate,
        'sendoff_couple_name' => $faker->word,
        'sendoff_location'             => 'Demo Camp',
        'weekend_location'             => 'Demo Camp',
        'rectorID'                     => factory(\App\Models\User::class),
        'weekend_verse_text'           => $faker->sentence,
        'weekend_verse_reference'      => $faker->word . $faker->numberBetween(2, 18) . ":" . $faker->numberBetween(5, 55),
        'weekend_theme'                => $faker->sentences(2, true),
        'banner_url'                   => $faker->imageUrl(480, 120, 'nature', true, config('site.community_acronym') . $gender . ' ' . $number),
        'team_meetings'                => 'Practice Camp',
        'visibility_flag'              => 1,
        'maximum_candidates'           => 24,
        'candidate_cost'               => 250,
        'team_fees'                    => 250,
        'emergency_poc_name'           => 'someone',
        'emergency_poc_email'          => 'mail@example.com',
        'emergency_poc_phone'          => '555-1212',
        'emergency_poc_id'             => factory(\App\Models\User::class),
        // 'id' => function () {
        //     return factory(App\Models\PrayerWheel::class)->create()->id;
        // },
    ];
});

$factory->state(App\Models\Weekend::class, 'mens', function (Faker $faker) {

    $mf        = 'M';
    $gender    = ($mf == 'M') ? "Men's" : "Women's";
    $number    = $faker->numberBetween(50, 75);

    return [
        'weekend_full_name'            => config('site.community_acronym') . $gender . ' #' . $number,
        'weekend_number'               => $number,
        'weekend_MF'                   => $mf,
    ];
});

$factory->state(App\Models\Weekend::class, 'womens', function (Faker $faker) {

    $mf        = 'W';
    $gender    = ($mf == 'M') ? "Men's" : "Women's";
    $number    = $faker->numberBetween(50, 75);

    return [
        'weekend_full_name'            => config('site.community_acronym') . $gender . ' #' . $number,
        'weekend_number'               => $number,
        'weekend_MF'                   => $mf,
    ];
});
