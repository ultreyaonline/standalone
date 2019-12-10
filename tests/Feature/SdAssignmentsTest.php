<?php

namespace Tests\Feature;

use App\WeekendAssignments;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SdAssignmentsTest extends TestCase
{
    use RefreshDatabase;

    /* @TODO tests to be added:
     *
     * config switch ON = SA can see buttons and make assignments
     * config switch OFF = SA cannot see buttons nor make assignments
     * a non-SD can't be assigned to an SD role
     */




    /** @test */
    public function rector_can_make_sd_assignments()
    {
        $this->seed(); // mainly to get the Roles and Permissions defined

        $rector = factory(\App\User::class)->states('female')->create();
        $weekend = factory(\App\Weekend::class)->states('womens')->create(['rectorID' => $rector->id]);
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => 1,
            'memberID' => $rector->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        $sd = factory(\App\User::class)->create([
            'qualified_sd' => 1,
            'active' => 1,
        ]);

        $response = $this->signIn($rector)
            ->post(action('TeamAssignmentController@store', $weekend->id), [
                'memberID' => $sd->id,
                'roleID' => 6, // 6 = Head SD; 7 = SD
                'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
                'comments' => 'None'
            ]);

        $this->assertDatabaseHas('weekend_assignments', [
            'weekendID' => $weekend->id,
            'memberID' => $sd->id,
            'roleID' => 6, // 6 = Head SD; 7 = SD
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
            'comments' => 'None',
        ]);
    }
}
