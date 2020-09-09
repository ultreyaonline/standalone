<?php

namespace Database\Factories;

use App\Models\PrayerWheel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PrayerWheelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PrayerWheel::class;

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
        ];
    }
}
