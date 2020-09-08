<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\TeamFeePayments::class, function (Faker $faker) {
    return [
        'weekendID' => function () {
            return factory(App\Models\Weekend::class)->create()->id;
        },
        'memberID' => function () {
            return factory(App\Models\User::class)->create()->id;
        },
        'total_paid' => $faker->randomFloat(),
        'date_paid' => $faker->date(),
        'complete' => $faker->randomNumber(),
        'comments' => $faker->text,
        'recorded_by' => $faker->word,
    ];
});
