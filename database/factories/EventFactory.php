<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $type = $this->faker->randomElement(['secuela', 'reunion', 'secretariat', 'weekend']);

        return [
            'event_key' => $this->faker->uuid,
            'type' => $type,
            'name' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'location_name' => $this->faker->word,
            'location_url' => $this->faker->url,
            'address_street' => $this->faker->streetAddress,
            'address_city' => $this->faker->city,
            'address_province' => $this->faker->word,
            'address_postal' => $this->faker->postcode,
            'map_url_link' => $this->faker->url,
            'contact_name' => $this->faker->name,
            'contact_email' => $this->faker->freeEmail,
            'contact_phone' => $this->faker->phoneNumber,
            'start_datetime' => $start = $this->faker->dateTimeBetween('now', '3 months'),
            'end_datetime' => $start->add(new \DateInterval('P1D')),
            'is_enabled' => 1,
            'is_public' => ($type == 'secuela' || $type == 'weekend') ? 1 : 0,
            'contact_id' => \App\Models\User::factory(),
            'posted_by' => \App\Models\User::factory(),
            'recurring_end_datetime' => null,
            'expiration_date' => null,
        ];
    }
}
