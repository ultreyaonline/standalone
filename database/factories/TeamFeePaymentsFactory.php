<?php

namespace Database\Factories;

use App\Models\TeamFeePayments;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TeamFeePaymentsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TeamFeePayments::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'weekendID' => function () {
                return App\Models\Weekend::factory();
            },
            'memberID' => function () {
                return App\Models\User::factory();
            },
            'total_paid' => $this->faker->randomFloat(),
            'date_paid' => $this->faker->date(),
            'complete' => $this->faker->randomNumber(),
            'comments' => $this->faker->text,
            'recorded_by' => $this->faker->word,
        ];
    }
}
