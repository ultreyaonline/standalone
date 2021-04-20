<?php

namespace Database\Factories;

use App\Models\WeekendRoles;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WeekendRolesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WeekendRoles::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'RoleName' => $this->faker->word(),
            'ReportName' => $this->faker->word(),
            'sortorder' => $this->faker->randomNumber(),
            'section_id' => function () {
                return App\Models\Section::factory();
            },
            'head_id' => $this->faker->randomNumber(),
            'isCoreTalk' => $this->faker->boolean(),
            'isBasicTalk' => $this->faker->boolean(),
            'excludeAsFormerRector' => $this->faker->boolean(),
            'requiredForRector' => $this->faker->boolean(),
            'isDeptHead' => $this->faker->boolean(),
            'canEmailEntireTeam' => $this->faker->boolean(),
        ];
    }
}
