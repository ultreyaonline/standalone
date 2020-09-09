<?php

namespace Database\Factories;

use App\Models\PrayerWheelSignup;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PrayerWheelSignupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PrayerWheelSignup::class;

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
            'timeslot' => $this->faker->numberBetween(1, 72),
            'memberID' => function () {
                return App\Models\User::factory();
            },
            'wheel_id' => function () {
                return App\Models\PrayerWheel::factory();
            },
            'acknowledged_at' => $this->faker->dateTimeBetween(),
            'reminded_at' => $this->faker->dateTimeBetween(),
        ];
    }
}
