<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\PrayerWheel::class, function (Faker $faker) {
    return [
        'weekendID' => function () {
            return factory(App\Models\Weekend::class)->create()->id;
        },
    ];
});
