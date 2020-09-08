<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\PrayerWheelSignup::class, function (Faker $faker) {
    return [
        'weekendID' => function () {
            return factory(App\Models\Weekend::class)->create()->id;
        },
        'timeslot' => $faker->numberBetween(1, 72),
        'memberID' => function () {
            return factory(App\Models\User::class)->create()->id;
        },
        'wheel_id' => function () {
            return factory(App\Models\PrayerWheel::class)->create()->id;
        },
        'acknowledged_at' => $faker->dateTimeBetween(),
        'reminded_at' => $faker->dateTimeBetween(),
    ];
});
