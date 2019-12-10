<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\PrayerWheelSignup::class, function (Faker $faker) {
    return [
        'weekendID' => function () {
            return factory(App\Weekend::class)->create()->id;
        },
        'timeslot' => $faker->numberBetween(1, 72),
        'memberID' => function () {
            return factory(App\User::class)->create()->id;
        },
        'wheel_id' => function () {
            return factory(App\PrayerWheel::class)->create()->id;
        },
        'acknowledged_at' => $faker->dateTimeBetween(),
        'reminded_at' => $faker->dateTimeBetween(),
    ];
});
