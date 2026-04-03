<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
//    @TODO: Tests won't seed properly if you use the WithoutModelEvents trait.
//    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // required permissions and roles data
        $this->call(RolesAndPermissionsSeeder::class);

        // optional demo data
//        $this->call(DemoSeeder::class);
    }
}
