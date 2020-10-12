<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


// @TODO - afterCreatingState can be incorporated only if Roles have been seeded
//    $user->assignRole('Member');


class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $mf = $this->faker->randomElement(['M', 'W']);

        $email = $this->faker->unique()->safeEmail;

        return [
            'first' => ($mf == 'M' ? $this->faker->firstNameMale : $this->faker->firstNameFemale),
            'last' => $this->faker->lastName,
            'email' => $email,
            'username' => $email,
            // 'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            //        'password'          => $password ?: $password = bcrypt('password'),
            'remember_token' => Str::random(10),
            'gender' => $mf,
            'address1' => $this->faker->streetAddress,
            // 'address2' => $this->>faker->secondaryAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->word,
            'postalcode' => $this->faker->postcode,
            'homephone' => $this->faker->phoneNumber,
            'cellphone' => $this->faker->phoneNumber,
            'workphone' => $this->faker->phoneNumber,
            'weekend' => config('site.community_acronym') . ' #' . $this->faker->randomNumber(2),
            'church' => $this->faker->sentence(3),
            'community' => config('site.community_acronym'),
            'avatar' => $this->faker->imageUrl(182, 110, 'nature', true),
            //imageUrl($width = 640, $height = 480, $category = null, $randomize = true)
            'skills' => $this->faker->sentence,
            'qualified_sd' => $this->faker->boolean(10),
            'active' => true,
            'inactive_comments' => null,
            'created_by' => 'Faker',

            // 'spouseID' => function () {
            //     return App\Models\User::factory()->create()->id;
            // },
            'sponsor' => $this->faker->name,
            // 'sponsorID' => function () {
            //     return App\Models\User::factory()->create()->id;
            // },

            'interested_in_serving' => $this->faker->boolean(90),
            'okay_to_send_serenade_and_palanca_details' => $this->faker->boolean(90),
            'allow_address_share' => $this->faker->boolean(40),
            'receive_email_weekend_general' => $this->faker->boolean(90),
            'receive_email_community_news' => $this->faker->boolean(90),
            'receive_email_sequela' => $this->faker->boolean(90),
            'receive_email_reunion' => $this->faker->boolean(90),
            'receive_prayer_wheel_invites' => $this->faker->boolean(70),
            'receive_prayer_wheel_reminders' => $this->faker->boolean(80),
            'unsubscribe' => 0,
            'unsubscribe_date' => null,
            'last_login_at' => null,
            'emergency_contact_details' => $this->faker->sentence,
            //        'uidhash' => substr($this->>faker->uuid, 0, 64),
            //        'updated_by' => $this->>faker->word,

        ];
    }

    public function generic()
    {
        return $this->state([]);
    }

    public function male()
    {
        return $this->state([
            'first' => $this->faker->firstNameMale,
            'gender' => 'M',
            'active' => true,
        ]);
    }

    public function female()
    {
        return $this->state([
            'first' => $this->faker->firstNameFemale,
            'gender' => 'W',
            'active' => true,
        ]);
    }

    public function active()
    {
        return $this->state([
            'active' => true,
        ]);
    }

    public function inactive()
    {
        return $this->state([
            'active' => false,
        ]);
    }

    public function randomlyInactive()
    {
        return $this->state([
            'active' => $active = $this->faker->boolean(90),
            'inactive_comments' => $active ? null : $this->faker->sentence(4),
        ]);
    }

    public function allEmailFlagsEnabled()
    {
        return $this->state([
            'active' => true,
            'unsubscribe' => false,
            'receive_prayer_wheel_invites' => true,
            'receive_email_reunion' => true,
            'receive_email_sequela' => true,
            'receive_email_community_news' => true,
            'receive_email_weekend_general' => true,
            'community' => config('site.local_community_filter'),
        ]);
    }

    public function asCandidate()
    {
        // @NOTE: Must still set `weekend` and `sponsorID` separately
        return $this->state([
            'okay_to_send_serenade_and_palanca_details' => false,
            'interested_in_serving' => false,
            'active' => false,
            'allow_address_share' => false,
            'receive_prayer_wheel_invites' => false,
            'receive_email_reunion' => false,
            'receive_email_sequela' => false,
            'receive_email_community_news' => false,
            'receive_email_weekend_general' => false,
            'community' => config('site.local_community_filter'),
        ]);
    }
}

