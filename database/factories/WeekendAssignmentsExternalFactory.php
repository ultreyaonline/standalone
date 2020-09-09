<?php

namespace Database\Factories;

use App\Models\WeekendAssignmentsExternal;
use Illuminate\Database\Eloquent\Factories\Factory;

class WeekendAssignmentsExternalFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WeekendAssignmentsExternal::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'memberID' => function () {
                return App\Models\User::factory();
            },
            'WeekendName' => $this->faker->word,
            'RoleName' => $this->faker->word,
        ];
    }
}
