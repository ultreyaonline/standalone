<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SecretariatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'president'     => 1,
            'vicepresident' => 1,
            'treasurer'     => 1,
            'secretary'     => 1,
            'finsecretary'  => 1,
            'preweekend'    => 1,
            'weekend'       => 1,
            'postweekend'   => 1,
            'palanca'       => 1,
            'mleader'       => 1,
            'wleader'       => 1,
            'pastpresident' => 1,
            'sadvisor'      => 1,
        ];
        \App\Models\Secretariat::create($data);
    }
}
