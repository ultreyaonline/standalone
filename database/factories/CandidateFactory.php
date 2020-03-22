<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Candidate::class, function (Faker $faker) {
    return [
        'm_user_id' => function () {
            return factory(\App\User::class)->states('male')->create([
                'weekend' => 'FAKE2',
                'okay_to_send_serenade_and_palanca_details' => false,
                'interested_in_serving' => false,
                'active' => false,
                'allow_address_share' => false,
                'receive_prayer_wheel_invites' => false,
                'receive_email_reunion' => false,
                'receive_email_sequela' => false,
                'receive_email_community_news' => false,
                'receive_email_weekend_general' => false,
            ])->id;
        },
        'w_user_id' => function () {
            return factory(\App\User::class)->states('female')->create([
                'weekend' => 'FAKE2',
                'okay_to_send_serenade_and_palanca_details' => false,
                'interested_in_serving' => false,
                'active' => false,
                'allow_address_share' => false,
                'receive_prayer_wheel_invites' => false,
                'receive_email_reunion' => false,
                'receive_email_sequela' => false,
                'receive_email_community_news' => false,
                'receive_email_weekend_general' => false,
            ])->id;
        },
        'weekend' => 'FAKE2',
        'm_age' => $faker->randomNumber(),
        'w_age' => $faker->randomNumber(),
        'm_pronunciation' => $faker->word,
        'w_pronunciation' => $faker->word,
        'sponsor_acknowledgement_sent' => $faker->boolean,
        'hash_sponsor_confirm' => $faker->word,
        'sponsor_confirmed_details' => false,
        'fees_paid' => $faker->boolean,
        'payment_details' => $faker->word,
        'ready_to_mail' => $faker->boolean,
        'invitation_mailed' => $faker->boolean,
        'm_response_card_returned' => $faker->boolean,
        'm_special_dorm' => $faker->word,
        'm_special_diet' => $faker->word,
        'm_special_prayer' => $faker->text,
        'm_special_medications' => $faker->word,
        'm_smoker' => $faker->boolean,
        'm_emergency_name' => $faker->word,
        'm_emergency_phone' => $faker->word,
        'm_arrival_poc_person' => $faker->word,
        'm_arrival_poc_phone' => $faker->word,
        'm_married' => $faker->boolean,
        'm_vocational_minister' => $faker->boolean,
        'w_response_card_returned' => $faker->boolean,
        'w_special_dorm' => $faker->word,
        'w_special_diet' => $faker->word,
        'w_special_prayer' => $faker->text,
        'w_special_medications' => $faker->word,
        'w_smoker' => $faker->boolean,
        'w_emergency_name' => $faker->word,
        'w_emergency_phone' => $faker->word,
        'w_arrival_poc_person' => $faker->word,
        'w_arrival_poc_phone' => $faker->word,
        'w_married' => $faker->boolean,
        'w_vocational_minister' => $faker->boolean,
        'completed' => $faker->boolean,
        'm_packing_list_email_sent' => $faker->dateTimeBetween(),
        'w_packing_list_email_sent' => $faker->dateTimeBetween(),
        'm_confirmation_email_sent' => $faker->dateTimeBetween(),
        'w_confirmation_email_sent' => $faker->dateTimeBetween(),
        'm_special_notes' => $faker->text,
        'w_special_notes' => $faker->text,
    ];
});

$factory->state(App\Candidate::class, 'male', function (Faker $faker) {
    return [
        // default state generates a couple, so return null for other gender
        'w_user_id' => null,
    ];
});

$factory->state(App\Candidate::class, 'female', function (Faker $faker) {
    return [
        'm_user_id' => null,
    ];
});

$factory->state(App\Candidate::class, 'couple', function (Faker $faker) {
    return [
        // default state generates a couple, so nothing special required here
    ];
});
