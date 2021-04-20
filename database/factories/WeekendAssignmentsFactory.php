<?php

namespace Database\Factories;

use App\Models\WeekendAssignments;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WeekendAssignmentsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WeekendAssignments::class;

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
            'roleID' => function () {
                return App\Models\WeekendRoles::factory();
            },
            'confirmed' => $this->faker->numberBetween(0, 5),
            'comments' => $this->faker->word(),
        ];

    }
}
