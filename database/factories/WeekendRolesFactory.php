<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\WeekendRoles::class, function (Faker $faker) {
    return [
        'RoleName' => $faker->word,
        'ReportName' => $faker->word,
        'sortorder' => $faker->randomNumber(),
        'section_id' => function () {
            return factory(App\Models\Section::class)->create()->id;
        },
        'head_id' => $faker->randomNumber(),
        'isCoreTalk' => $faker->boolean,
        'isBasicTalk' => $faker->boolean,
        'excludeAsFormerRector' => $faker->boolean,
        'requiredForRector' => $faker->boolean,
        'isDeptHead' => $faker->boolean,
        'canEmailEntireTeam' => $faker->boolean,
    ];
});
