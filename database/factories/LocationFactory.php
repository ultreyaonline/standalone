<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LocationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Location::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'location_name' => $location_name = $this->faker->text(),
            'slug' => Str::slug($location_name),
            'location_url' => $this->faker->url(),
            'address_street' => $this->faker->streetAddress(),
            'address_city' => $this->faker->city(),
            'address_province' => $this->faker->state(),
            'address_postal' => $this->faker->postcode(),
            'map_url_link' => $this->faker->imageUrl(),
            'contact_name' => $this->faker->name(),
            'contact_email' => $this->faker->email(),
            'contact_phone' => $this->faker->phoneNumber(),
        ];
    }
}
