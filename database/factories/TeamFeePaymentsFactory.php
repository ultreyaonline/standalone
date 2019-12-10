<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\TeamFeePayments::class, function (Faker $faker) {
    return [
        'weekendID' => function () {
            return factory(App\Weekend::class)->create()->id;
        },
        'memberID' => function () {
            return factory(App\User::class)->create()->id;
        },
        'total_paid' => $faker->randomFloat(),
        'date_paid' => $faker->date(),
        'complete' => $faker->randomNumber(),
        'comments' => $faker->text,
        'recorded_by' => $faker->word,
    ];
});
