<?php

namespace Tests\Feature;

use App\Enums\WeekendVisibleTo;
use App\WeekendAssignments;
use App\WeekendRoles;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeamAssignmentsTest extends TestCase
{
    use RefreshDatabase;

    /* @TODO tests to be added:
     *
     * - head cha can make assignments
     * - nobody can change the Rector by hacking
     *
     * - assignments are not visible if weekend is not Active
     */




    /** @test */
    public function rector_can_make_team_assignments()
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

        $user = factory(\App\User::class)->states('female')->create();

        $response = $this->signIn($rector)
            ->post(action('TeamAssignmentController@store', $weekend->id), [
                'memberID' => $user->id,
                'roleID' => 10,
                'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
                'comments' => 'Comment'
            ]);

        $this->assertDatabaseHas('weekend_assignments', [
            'memberID' => $user->id,
            'roleID' => 10,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
            'comments' => 'Comment'
        ]);
    }

    // @TODO - add another test for when weekend "status" is lower than 'HeadChas'
    /** @test */
    public function head_cha_can_make_team_assignments()
    {
        $this->seed(); // mainly to get the Roles and Permissions defined

        $rector = factory(\App\User::class)->states('female')->create();
        $weekend = factory(\App\Weekend::class)->states('womens')->create([
            'rectorID' => $rector->id,
            'visibility_flag' => WeekendVisibleTo::HeadChas,
            ]);
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => 1,
            'memberID' => $rector->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);
        // assign a head cha to that weekend
        $headcha = factory(\App\User::class)->states('female')->create();
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => 2,
            'memberID' => $headcha->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        $user = factory(\App\User::class)->states('female')->create();

        $response = $this->signIn($headcha)
            ->post(action('TeamAssignmentController@store', $weekend->id), [
                'memberID' => $user->id,
                'roleID' => 10,
                'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
                'comments' => 'Comment'
            ]);

        $this->assertDatabaseHas('weekend_assignments', [
            'memberID' => $user->id,
            'roleID' => 10,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
            'comments' => 'Comment'
        ]);
    }


    /** @test */
    public function an_unconfirmed_assignment_is_only_visible_to_the_rector_and_headcha_and_rover()
    {
        $this->seed(); // mainly to get the Roles and Permissions defined

        // assign a rector to a weekend
        $rector = factory(\App\User::class)->states('female')->create();
        $weekend = factory(\App\Weekend::class)->states('womens')->create([
            'visibility_flag' => WeekendVisibleTo::Calendar,
            'rectorID' => $rector->id,
            ]);
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => 1,
            'memberID' => $rector->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        // assign a head cha to that weekend
        $headcha = factory(\App\User::class)->states('female')->create();
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => 2,
            'memberID' => $headcha->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        // assign a rover to that weekend
        $rover = factory(\App\User::class)->states('female')->create();
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => 5,
            'memberID' => $rover->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        // assign a team member to that weekend
        $user = factory(\App\User::class)->states('female')->create();
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => 10,
            'memberID' => $user->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::CalledSpoke,
        ]);

        // if the user signs in, they cannot see the assignment
        $this->signIn($user)->get('/members/' . $user->id)->assertDontSee(e(WeekendRoles::where('id', 10)->first()->RoleName));
        $this->actingAs($user)->get('/weekend/' . $weekend->id . '/team')
            ->assertDontSee(e(WeekendRoles::where('id', 10)->first()->RoleName))
            ->assertDontSee(e($user->name) . '</a></td>');

        // But the rector may see the team-member assignment
        $this->signIn($rector)->get('/team/' . $weekend->id . '/edit')->assertSee(e($user->name));
        $this->actingAs($rector)->get('/weekend/' . $weekend->id . '/team')
            ->assertDontSee(e($user->name) . '</a></td>');

        // And the rover may see the team-member assignment
        $this->signIn($rover)->get('/team/' . $weekend->id . '/edit')->assertSee(e($user->name));
        $this->actingAs($rover)->get('/weekend/' . $weekend->id . '/team')
            ->assertDontSee(e($user->name) . '</a></td>');

        // And the head cha may see the team-member assignment
        $this->signIn($headcha)->get('/team/' . $weekend->id . '/edit')->assertSee(e($user->name));
        $this->actingAs($headcha)->get('/weekend/' . $weekend->id . '/team')
            ->assertDontSee(e($user->name) . '</a></td>');
    }

    /** @test */
    public function a_confirmed_assignment_is_visible_to_all_if_the_weekend_is_visible_to_community()
    {
        $this->seed(); // mainly to get the Roles and Permissions defined

        // assign a rector to a weekend
        $rector = factory(\App\User::class)->states('female')->create();
        $weekend = factory(\App\Weekend::class)->states('womens')->create([
            'visibility_flag' => WeekendVisibleTo::Calendar,
            'rectorID' => $rector->id,
            ]);
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => 1,
            'memberID' => $rector->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        // assign a team member to that weekend
        $user = factory(\App\User::class)->states('female')->create();
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => 10,
            'memberID' => $user->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        // if the user signs in, they cannot see the assignment
        $this->signIn($user)->get('/members/' . $user->id)->assertDontSee(e(WeekendRoles::where('id', 10)->first()->RoleName));
        $this->actingAs($user)->get('/weekend/' . $weekend->id . '/team')
            ->assertDontSee(e(WeekendRoles::where('id', 10)->first()->RoleName))
            ->assertDontSee(e($user->name) . '</a></td>');

        // But the rector may see the assignment
        $this->signIn($rector)->get('/team/' . $weekend->id . '/edit')->assertSee(e($user->name));
        $this->actingAs($rector)->get('/weekend/' . $weekend->id . '/team')
            ->assertDontSee(e($user->name) . '</a></td>');
    }
}
