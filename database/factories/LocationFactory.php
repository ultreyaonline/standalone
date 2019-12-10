<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Location::class, function (Faker $faker) {
    return [
        'location_name' => $location_name = $faker->text,
        'slug' => Str::slug($location_name),
        'location_url' => $faker->url,
        'address_street' => $faker->streetAddress,
        'address_city' => $faker->city,
        'address_province' => $faker->state,
        'address_postal' => $faker->postcode,
        'map_url_link' => $faker->imageUrl(),
        'contact_name' => $faker->name,
        'contact_email' => $faker->email,
        'contact_phone' => $faker->phoneNumber,
    ];
});
