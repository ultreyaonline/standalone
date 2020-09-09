<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'event_key'        => 'SEC16',
            'type'             => 'secuela',
            'name'             => 'Fall Secuela',
            'description'      => 'Join us for a time of food (pot luck), fellowship, testimonies, and updates about the TD community.',
            'location_name'    => 'First Baptist Church',
            'location_url'     => 'http://example.com/firstchurch/',
            'address_street'   => '123 Church Street',
            'address_city'     => 'Somewhere',
            'address_province' => 'FL',
            'address_postal'   => '',
            'map_url_link'     => 'https://www.google.com/maps/someplace',
            'contact_name'     => 'Post-Weekend Committee',
            'contact_email'    => 'postweekend@example.com',
            'contact_phone'    => '',
            'start_datetime'   => '2018-01-28 16:00:00',
            'end_datetime'     => '2018-01-28 18:30:00',
            'expiration_date'  => '2018-01-28 18:30:00',
            'is_enabled'       => 1,
            'is_public'        => 1,
            'contact_id'       => 1,
            'posted_by'        => 1,
        ];
        App\Models\Event::create($data);
    }
}
