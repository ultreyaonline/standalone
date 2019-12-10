<?php

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Event::class, function (Faker $faker) {
    $type = $faker->randomElement(['secuela', 'reunion', 'secretariat', 'weekend']);

    return [
        'event_key'        => $faker->uuid,
        'type'             => $type,
        'name'             => $faker->sentence,
        'description'      => $faker->paragraph,
        'location_name'    => $faker->word,
        'location_url'     => $faker->url,
        'address_street'   => $faker->streetAddress,
        'address_city'     => $faker->city,
        'address_province' => $faker->word,
        'address_postal'   => $faker->postcode,
        'map_url_link'     => $faker->url,
        'contact_name'     => $faker->name,
        'contact_email'    => $faker->freeEmail,
        'contact_phone'    => $faker->phoneNumber,
        'start_datetime'   => $start = $faker->dateTimeBetween('now', '3 months'),
        'end_datetime'     => $start->add(new DateInterval('P1D')),
        'is_enabled'       => 1,
        'is_public'        => ($type == 'secuela' || $type == 'weekend') ? 1 : 0,
        'contact_id'       => factory(\App\User::class),
        'posted_by'        => factory(\App\User::class),
        'recurring_end_datetime' => null,
        'expiration_date' => null,
    ];
});
