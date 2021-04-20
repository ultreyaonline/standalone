<?php
namespace Database\Factories;

use App\Models\Candidate;
use Illuminate\Database\Eloquent\Factories\Factory;

class CandidateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Candidate::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'm_user_id' => function () {
                return \App\Models\User::factory()->male()->create([
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
                return \App\Models\User::factory()->female()->create([
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
            'm_age' => $this->faker->randomNumber(),
            'w_age' => $this->faker->randomNumber(),
            'm_pronunciation' => $this->faker->word(),
            'w_pronunciation' => $this->faker->word(),
            'sponsor_acknowledgement_sent' => $this->faker->boolean(),
            'hash_sponsor_confirm' => $this->faker->word(),
            'sponsor_confirmed_details' => false,
            'fees_paid' => $this->faker->boolean(),
            'payment_details' => $this->faker->word(),
            'ready_to_mail' => $this->faker->boolean(),
            'invitation_mailed' => $this->faker->boolean(),
            'm_response_card_returned' => $this->faker->boolean(),
            'm_special_dorm' => $this->faker->word(),
            'm_special_diet' => $this->faker->word(),
            'm_special_prayer' => $this->faker->text(),
            'm_special_medications' => $this->faker->word(),
            'm_smoker' => $this->faker->boolean(),
            'm_emergency_name' => $this->faker->word(),
            'm_emergency_phone' => $this->faker->word(),
            'm_arrival_poc_person' => $this->faker->word(),
            'm_arrival_poc_phone' => $this->faker->word(),
            'm_married' => $this->faker->boolean(),
            'm_vocational_minister' => $this->faker->boolean(),
            'w_response_card_returned' => $this->faker->boolean(),
            'w_special_dorm' => $this->faker->word(),
            'w_special_diet' => $this->faker->word(),
            'w_special_prayer' => $this->faker->text(),
            'w_special_medications' => $this->faker->word(),
            'w_smoker' => $this->faker->boolean(),
            'w_emergency_name' => $this->faker->word(),
            'w_emergency_phone' => $this->faker->word(),
            'w_arrival_poc_person' => $this->faker->word(),
            'w_arrival_poc_phone' => $this->faker->word(),
            'w_married' => $this->faker->boolean(),
            'w_vocational_minister' => $this->faker->boolean(),
            'completed' => $this->faker->boolean(),
            'm_packing_list_email_sent' => $this->faker->dateTimeBetween(),
            'w_packing_list_email_sent' => $this->faker->dateTimeBetween(),
            'm_confirmation_email_sent' => $this->faker->dateTimeBetween(),
            'w_confirmation_email_sent' => $this->faker->dateTimeBetween(),
            'm_special_notes' => $this->faker->text(),
            'w_special_notes' => $this->faker->text(),
        ];
    }

    public function male()
    {
        return $this->state([
            // default state generates a couple, so return null for other gender
            'w_user_id' => null,
        ]);
    }

    public function female()
    {
        return $this->state([
            'm_user_id' => null,
        ]);
    }

    public function couple()
    {
        return $this->state([
            // default state generates a couple, so nothing special required here
        ]);
    }
}
