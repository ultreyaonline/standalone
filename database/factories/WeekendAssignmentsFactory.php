<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\WeekendAssignments::class, function (Faker $faker) {
    return [
        'weekendID' => function () {
            return factory(App\Models\Weekend::class)->create()->id;
        },
        'memberID' => function () {
            return factory(App\Models\User::class)->create()->id;
        },
        'roleID' => function () {
            return factory(App\Models\WeekendRoles::class)->create()->id;
        },
        'confirmed' => $faker->numberBetween(0,5),
        'comments' => $faker->word,
    ];
});
