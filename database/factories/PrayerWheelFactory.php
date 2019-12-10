<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\PrayerWheel::class, function (Faker $faker) {
    return [
        'weekendID' => function () {
            return factory(App\Weekend::class)->create()->id;
        },
    ];
});
