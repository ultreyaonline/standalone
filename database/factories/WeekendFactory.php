<?php

namespace Database\Factories;

use App\Models\Weekend;
use Illuminate\Database\Eloquent\Factories\Factory;

class WeekendFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Weekend::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $mf = $this->faker->randomElement(['M', 'W']);
        $gender = ($mf == 'M') ? "Men's" : "Women's";
        $number = $this->faker->numberBetween(50, 75);
        $startdate = $this->faker->dateTimeBetween('now', '18 months')->setTime(18, 0);
        $enddate = $startdate->add(new \DateInterval('P3D'));

        return [
            'weekend_full_name' => config('site.community_acronym') . $gender . ' #' . $number,
            'weekend_number' => $number,
            'weekend_MF' => $mf,
            'tresdias_community' => config('site.community_acronym'),
            'start_date' => $startdate,
            'end_date' => $enddate,
            'candidate_arrival_time' => $startdate,
            'serenade_arrival_time' => $enddate,
            'closing_arrival_time' => $enddate,
            'closing_scheduled_start_time' => $enddate,
            'sendoff_couple_name' => $this->faker->word,
            'sendoff_location' => 'Demo Camp',
            'weekend_location' => 'Demo Camp',
            'rectorID' => \App\Models\User::factory(),
            'weekend_verse_text' => $this->faker->sentence,
            'weekend_verse_reference' => $this->faker->word . $this->faker->numberBetween(2, 18) . ":" . $this->faker->numberBetween(5, 55),
            'weekend_theme' => $this->faker->sentences(2, true),
            'banner_url' => $this->faker->imageUrl(480, 120, 'nature', true, config('site.community_acronym') . $gender . ' ' . $number),
            'team_meetings' => 'Practice Camp',
            'visibility_flag' => 1,
            'maximum_candidates' => 24,
            'candidate_cost' => 250,
            'team_fees' => 250,
            'emergency_poc_name' => 'someone',
            'emergency_poc_email' => 'mail@example.com',
            'emergency_poc_phone' => '555-1212',
            'emergency_poc_id' => \App\Models\User::factory(),
            // 'id' => function () {
            //     return App\Models\PrayerWheel::factory()->create()->id;
            // },
        ];
    }

    public function mens()
    {
        $this->faker = $this->withFaker();
        $mf = 'M';
        $gender = ($mf == 'M') ? "Men's" : "Women's";
        $number = $this->faker->numberBetween(50, 75);

        return $this->state([
            'weekend_full_name' => config('site.community_acronym') . $gender . ' #' . $number,
            'weekend_number' => $number,
            'weekend_MF' => $mf,
        ]);
    }

    public function womens()
    {
        $mf = 'W';
        $gender = ($mf == 'M') ? "Men's" : "Women's";
        $number = $this->faker->numberBetween(50, 75);

        return $this->state([
            'weekend_full_name' => config('site.community_acronym') . $gender . ' #' . $number,
            'weekend_number' => $number,
            'weekend_MF' => $mf,
        ]);
    }
}
