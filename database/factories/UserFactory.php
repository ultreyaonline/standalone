<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(App\User::class, function (Faker $faker) {
//    static $password;

    $mf = $faker->randomElement(['M', 'W']);

    $email = $faker->unique()->safeEmail;

    return [
        'first'             => ($mf == 'M' ? $faker->firstNameMale : $faker->firstNameFemale),
        'last'              => $faker->lastName,
        'email'             => $email,
        'username'          => $email,
        // 'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
//        'password'          => $password ?: $password = bcrypt('password'),
        'remember_token'    => Str::random(10),
        'gender'            => $mf,
        'address1'          => $faker->streetAddress,
        // 'address2' => $faker->secondaryAddress,
        'city'              => $faker->city,
        'state'             => $faker->word,
        'postalcode'        => $faker->postcode,
        'homephone'         => $faker->phoneNumber,
        'cellphone'         => $faker->phoneNumber,
        'workphone'         => $faker->phoneNumber,
        'weekend'           => config('site.community_acronym'). ' #' . $faker->randomNumber(2),
        'church'            => $faker->sentence(3),
        'community'         => config('site.community_acronym'),
        'avatar'            => $faker->imageUrl(182, 110, 'nature', true),
        //imageUrl($width = 640, $height = 480, $category = null, $randomize = true)
        'skills'            => $faker->sentence,
        'qualified_sd'      => $faker->boolean(10),
        'active'            => $faker->boolean(90),
        'inactive_comments' => null,
        'created_by'        => 'Faker',

        // 'spouseID' => function () {
        //     return factory(App\User::class)->create()->id;
        // },
        'sponsor' => $faker->name,
        // 'sponsorID' => function () {
        //     return factory(App\User::class)->create()->id;
        // },

        'interested_in_serving' => $faker->boolean(90),
        'okay_to_send_serenade_and_palanca_details' => $faker->boolean(90),
        'allow_address_share' => $faker->boolean(40),
        'receive_email_weekend_general' => $faker->boolean(90),
        'receive_email_community_news' => $faker->boolean(90),
        'receive_email_sequela' => $faker->boolean(90),
        'receive_email_reunion' => $faker->boolean(90),
        'receive_prayer_wheel_invites' => $faker->boolean(70),
        'receive_prayer_wheel_reminders' => $faker->boolean(80),
        'unsubscribe' => 0,
        'unsubscribe_date' => null,
        'last_login_at' => null,
        'emergency_contact_details' => $faker->sentence,
//        'uidhash' => substr($faker->uuid, 0, 64),
//        'updated_by' => $faker->word,

    ];
});
$factory->state(App\User::class, 'generic', function (Faker $faker) {
    return [];
});

$factory->state(App\User::class, 'male', function (Faker $faker) {
    return [
        'first'  => $faker->firstNameMale,
        'gender' => 'M',
        'active' => true,
    ];
});

$factory->state(App\User::class, 'female', function (Faker $faker) {
    return [
        'first'  => $faker->firstNameFemale,
        'gender' => 'W',
        'active' => true,
    ];
});

$factory->state(App\User::class, 'active', function (Faker $faker) {
    return [
        'active' => true,
    ];
});

$factory->state(App\User::class, 'inactive', function (Faker $faker) {
    return [
        'active' => false,
    ];
});

// @TODO - afterCreatingState can be incorporated only if Roles have been seeded
//$factory->afterCreatingState(App\User::class, 'active', function ($user, $faker) {
//    $user->assignRole('Member');
//});

