<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Section::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'sort_order' => $faker->randomNumber(),
        'enabled' => $faker->boolean,
    ];
});
