<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * This is to seed some GENERAL banners. (Weekend-specific banners are part of the Weekend model.)
 */
class BannerSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'title'  => "Red",
            'banner_url' => 'https://via.placeholder.com/400/0000FF',
        ];
        \App\Models\Banner::create($data);

        $data = [
            'title'  => "Cat 1",
            'banner_url' => 'https://placekitten.com/400/400',
        ];
        \App\Models\Banner::create($data);

        $data = [
            'title'  => "Cat 2",
            'banner_url' => 'https://placekitten.com/400/300',
        ];
        \App\Models\Banner::create($data);

        $data = [
            'title'  => "Green",
            'banner_url' => 'https://via.placeholder.com/400/008000',
        ];
        \App\Models\Banner::create($data);
    }
}
