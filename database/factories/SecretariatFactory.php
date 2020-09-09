<?php

namespace Database\Factories;

use App\Models\Secretariat;
use Illuminate\Database\Eloquent\Factories\Factory;

class SecretariatFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Secretariat::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        return [
            'president' => $this->faker->randomNumber(),
            'vicepresident' => $this->faker->randomNumber(),
            'treasurer' => $this->faker->randomNumber(),
            'secretary' => $this->faker->randomNumber(),
            'finsecretary' => $this->faker->randomNumber(),
            'preweekend' => $this->faker->randomNumber(),
            'weekend' => $this->faker->randomNumber(),
            'postweekend' => $this->faker->randomNumber(),
            'palanca' => $this->faker->randomNumber(),
            'mleader' => $this->faker->randomNumber(),
            'wleader' => $this->faker->randomNumber(),
            'pastpresident' => $this->faker->randomNumber(),
            'sadvisor' => $this->faker->randomNumber(),
        ];
    }
}
