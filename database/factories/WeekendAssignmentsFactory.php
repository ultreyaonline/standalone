<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\WeekendAssignments::class, function (Faker $faker) {
    return [
        'weekendID' => function () {
            return factory(App\Weekend::class)->create()->id;
        },
        'memberID' => function () {
            return factory(App\User::class)->create()->id;
        },
        'roleID' => function () {
            return factory(App\WeekendRoles::class)->create()->id;
        },
        'confirmed' => $faker->numberBetween(0,5),
        'comments' => $faker->word,
    ];
});
