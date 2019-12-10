<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Secretariat::class, function (Faker $faker) {
    return [
        'president' => $faker->randomNumber(),
        'vicepresident' => $faker->randomNumber(),
        'treasurer' => $faker->randomNumber(),
        'secretary' => $faker->randomNumber(),
        'finsecretary' => $faker->randomNumber(),
        'preweekend' => $faker->randomNumber(),
        'weekend' => $faker->randomNumber(),
        'postweekend' => $faker->randomNumber(),
        'palanca' => $faker->randomNumber(),
        'mleader' => $faker->randomNumber(),
        'wleader' => $faker->randomNumber(),
        'pastpresident' => $faker->randomNumber(),
        'sadvisor' => $faker->randomNumber(),
    ];
});
