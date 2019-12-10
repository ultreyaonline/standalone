<?php

use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Seed the Locations table
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Location::class)->create([
            'location_name'    => 'First Baptist Church',
            'slug'             => 'first-baptist',
            'location_url'     => 'http://example.com/firstchurch/',
            'address_street'   => '123 Church Street',
            'address_city'     => 'Somewhere',
            'address_province' => 'FL',
            'address_postal'   => '',
            'map_url_link'     => 'https://www.google.com/maps/someplace',
            'contact_name'     => 'Post-Weekend Committee',
            'contact_email'    => 'postweekend@example.com',
            'contact_phone'    => '',
        ]);
    }
}
