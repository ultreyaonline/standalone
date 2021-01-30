<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Seed the database for demo/example purposes
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(EventSeeder::class);
        $this->call(WeekendSeeder::class);
        $this->call(BannerSeeder::class);
        $this->call(LocationSeeder::class);
    }
}
