<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class WeekendSeeder extends Seeder
{
    public function run()
    {

        $data = [
            'weekend_full_name'  => "ANYTD Men's #1",
            'weekend_number'     => '1',
            'weekend_MF'         => 'M',
            'tresdias_community' => 'ANYTD',
            'start_date' => '2017-09-14 18:00:00',
            'end_date' => '2017-09-17 17:30:00',
            'sendoff_location' => 'Our Retreat Center',
            'sendoff_couple_name' => 'Jack and Jennifer Smith',
            'sendoff_couple_email' => null,
            'sendoff_couple_id1' => null,
            'sendoff_couple_id2' => null,
            'weekend_location' => 'Our Retreat Center',
            'candidate_arrival_time' => '2017-09-14 17:30:00',
            'sendoff_start_time' => '2017-09-14 18:00:00',
            'maximum_candidates' => 24,
            'candidate_cost' => '200.00',
            'team_fees' => '200.00',
            'rectorID' => 1,
            'weekend_verse_text' => 'Blessed are the meek for they shall inherit the earth',
            'weekend_verse_reference' => 'Matthew 5:5',
            'weekend_theme' => 'In quietness and confidence we are strengthened',
            'banner_url' => 'http://lorempixel.com/600/400/sports/1',
            'visibility_flag' => 6,
        ];
        App\Models\Weekend::create($data);


        $data = [
            'weekend_full_name'  => "ANYTD Women's #1",
            'weekend_number'     => '1',
            'weekend_MF'         => 'W',
            'tresdias_community' => 'ANYTD',
            'start_date' => '2017-09-21 18:00:00',
            'end_date' => '2017-09-24 17:30:00',
            'sendoff_location' => 'Our Retreat Center',
            'sendoff_couple_name' => 'SENDOFF COUPLE',
            'sendoff_couple_email' => 'mail@example.com',
            'weekend_location' => 'Our Retreat Center',
            'candidate_arrival_time' => '2017-09-21 17:30:00',
            'sendoff_start_time' => '2017-09-21 18:00:00',
            'maximum_candidates' => 24,
            'candidate_cost' => '200.00',
            'team_fees' => '200.00',
            'rectorID' => 3,
            'weekend_verse_text' => 'Jesus Wept',
            'weekend_verse_reference' => 'John 11',
            'weekend_theme' => 'Refreshing in Christ',
            'banner_url' => 'http://lorempixel.com/600/400/sports/2',
            'visibility_flag' => 6,
        ];
        App\Models\Weekend::create($data);

        $data = [
            'weekend_full_name'  => "ANYTD Men's #2",
            'weekend_number'     => '2',
            'weekend_MF'         => 'M',
            'tresdias_community' => 'ANYTD',
            'start_date' => '2018-04-05 18:00:00',
            'end_date' => '2018-04-08 17:30:00',
            'sendoff_location' => 'Our Retreat Center',
            'sendoff_couple_name' => 'SENDOFF COUPLE',
            'sendoff_couple_email' => 'mail@example.com',
            'weekend_location' => 'Our Retreat Center',
            'candidate_arrival_time' => '2018-04-05 17:30:00',
            'sendoff_start_time' => '2018-04-05 18:00:00',
            'maximum_candidates' => 24,
            'candidate_cost' => '200.00',
            'team_fees' => '200.00',
            'rectorID' => 2,
            'weekend_verse_text' => 'I am the vine, you are the branches; abide in me and you will bear much fruit',
            'weekend_verse_reference' => 'John 15:15',
            'weekend_theme' => 'Fruitful in Abiding',
            'banner_url' => 'http://lorempixel.com/600/400/sports/3',
            'visibility_flag' => 6,
        ];
        App\Models\Weekend::create($data);


        $data = [
            'weekend_full_name'  => "ANYTD Women's #2",
            'weekend_number'     => '2',
            'weekend_MF'         => 'W',
            'tresdias_community' => 'ANYTD',
            'start_date' => '2018-04-12 18:00:00',
            'end_date' => '2018-04-15 17:30:00',
            'sendoff_location' => 'Our Retreat Center',
            'sendoff_couple_name' => 'SENDOFF COUPLE',
            'sendoff_couple_email' => 'mail@example.com',
            'weekend_location' => 'Our Retreat Center',
            'candidate_arrival_time' => '2018-04-12 17:30:00',
            'sendoff_start_time' => '2018-04-12 18:00:00',
            'maximum_candidates' => 24,
            'candidate_cost' => '200.00',
            'team_fees' => '200.00',
            'rectorID' => 4,
            'weekend_verse_text' => 'It is by Grace you are saved, a gift from God',
            'weekend_verse_reference' => 'Ephesians 2:8',
            'weekend_theme' => 'Graciously Saved, Abiding by Faith',
            'banner_url' => 'http://lorempixel.com/600/400/sports/4',
            'visibility_flag' => 6,
        ];
        App\Models\Weekend::create($data);

    }
}
