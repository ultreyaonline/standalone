<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's core requirements.
     *
     * @return void
     */
    public function run()
    {
        // required permissions and roles data
        $this->call(RolesAndPermissionsSeeder::class);

        // optional demo data
//        $this->call(DemoSeeder::class);
    }
}
