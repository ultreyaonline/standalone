<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\WeekendAssignmentsExternal::class, function (Faker $faker) {
    return [
        'memberID' => function () {
            return factory(App\User::class)->create()->id;
        },
        'WeekendName' => $faker->word,
        'RoleName' => $faker->word,
    ];
});
