<?php

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\WeekendAssignments::class, function (Faker $faker) {

    return [
        'weekendID' => $faker->numberBetween(4, 24),
        'memberID'  => factory(App\User::class)->create(),
        'roleID'    => $faker->numberBetween(2, 45),
        'confirmed' => $faker->numberBetween(0, 5),
    ];
});
