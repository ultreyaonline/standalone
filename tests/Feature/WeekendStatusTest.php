<?php

namespace Tests\Feature;

use App\Enums\WeekendVisibleTo;
use App\WeekendAssignments;
use App\WeekendRoles;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WeekendStatusTest extends TestCase
{
    use RefreshDatabase;

    /* @TODO tests to be added:
     *
     * Test all ENUM levels, both positive and negative
     * WeekendVisibleTo::
     *   :: AdminOnly = 0;
     *   :: Calendar = 1;
     *   :: Rector = 2;
     *   :: HeadChas = 3;
     *   :: SectionHeads = 4;
     *   :: Community = 5;
     */

    // @TODO places to inspect:
    // - calendar page
    // - weekends dropdown on Weekend page
    // - URL change to Weekends endpoint
    // - prayerwheel dropdown
    // - preweekend dropdown
    // - team-fee payments dropdown
    // - team member assignment history list on their profile page
    // - that weekend's rector's service history list



    /** @test */
    public function a_new_weekend_is_visible_to_admin()
    {
        $this->seed(); // mainly to get the Roles and Permissions defined

        $weekend = factory(\App\Weekend::class)->states('womens')->create(['visibility_flag' => WeekendVisibleTo::AdminOnly]);

        $user = factory(\App\User::class)->states('female')->create();
        $admin = factory(\App\User::class)->states('female')->create();
        $admin->assignRole('Admin');

        $responseUser = $this->signIn($user)
            ->get('/calendar')
            ->assertDontSee(e($weekend->long_name_with_number));

        $responseAdmin = $this->signIn($admin)
            ->get('/weekend/'.$weekend->id)->assertSee(e($weekend->weekend_full_name));
    }

    /** @test */
    public function a_calendar_only_weekend_is_visible_on_calendar_and_for_admins_but_not_other_dropdowns()
    {
        $this->seed(); // mainly to get the Roles and Permissions defined

        $weekend = factory(\App\Weekend::class)->states('womens')->create(['visibility_flag' => WeekendVisibleTo::Calendar]);

        $admin = factory(\App\User::class)->states('female')->create();
        $admin->assignRole('Admin');

        $user = factory(\App\User::class)->states('female')->create();

        $this->signIn($admin)->get('/calendar')->assertSee(e($weekend->long_name_with_number));
        $this->actingAs($admin)->get('/weekend/'.$weekend->id)->assertSee(e($weekend->weekend_full_name));

        $this->signIn($user)->get('/calendar')->assertSee(e($weekend->long_name_with_number));
        $this->actingAs($user)->get('/weekend/'.$weekend->id)->assertSee(e($weekend->weekend_full_name));

        // @TODO - link a prayerwheel, and test visibility
//        $this->actingAs($user)->get('/prayerwheel')->assertSee(e($weekend->weekend_full_name));
    }

    /** @test */
    public function a_hidden_weekends_theme_details_are_only_visible_to_rector()
    {
        $this->seed(); // mainly to get the Roles and Permissions defined

        // assign a rector to a weekend
        $rector = factory(\App\User::class)->states('female')->create();
        $weekend = factory(\App\Weekend::class)->states('womens')->create([
            'visibility_flag' => WeekendVisibleTo::RectorOnly,
            'rectorID' => $rector->id,
            ]);
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => 1,
            'memberID' => $rector->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        $user = factory(\App\User::class)->states('female')->create();

        // if the user signs in, they cannot see the theme details
        $this->signIn($user)->get('/weekend/' . $weekend->id)->assertDontSee(e($weekend->weekend_theme));

        // the rector may see the assignment
        $this->signIn($rector)->get('/weekend/' . $weekend->id . '/edit')->assertSee(e($weekend->weekend_theme));
    }

    /** @test */
    public function a_hidden_weekends_team_assignments_are_only_visible_to_rector()
    {
        $this->seed(); // mainly to get the Roles and Permissions defined

        // assign a rector to a weekend
        $rector = factory(\App\User::class)->states('female')->create();
        $weekend = factory(\App\Weekend::class)->states('womens')->create([
            'visibility_flag' => WeekendVisibleTo::ThemeVisible,
            'rectorID' => $rector->id,
            ]);
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => 1,
            'memberID' => $rector->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        // assign a confirmed team member to that weekend
        $user = factory(\App\User::class)->states('female')->create();
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => 10, // 10=Piety, 23=Table Cha
            'memberID' => $user->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        // if the user signs in, they cannot see the assignment, even though it's confirmed, because of the weekend status
        $this->signIn($user)->get('/members/' . $user->id)->assertDontSee(e(WeekendRoles::where('id', 10)->first()->RoleName));
        // but they can still see the core details on the weekend page, without the Team links
        $this->get('/weekend/'.$weekend->id)->assertDontSee('Roster')->assertSee('Candidate Fee');

        // the rector may see the assignment
        $this->signIn($rector)->get('/team/' . $weekend->id . '/edit')->assertSee(e($user->name));
    }


    /** @test */
    public function a_weekends_team_assignments_are_only_visible_to_headchas_when_status_is_headchas()
    {
        $this->seed(); // mainly to get the Roles and Permissions defined

        // assign a rector to a weekend
        $rector = factory(\App\User::class)->states('female')->create();
        $weekend = factory(\App\Weekend::class)->states('womens')->create([
            'visibility_flag' => WeekendVisibleTo::HeadChas,
            'rectorID' => $rector->id,
            ]);
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => 1,
            'memberID' => $rector->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        // assign a confirmed section head to that weekend
        $user = factory(\App\User::class)->states('female')->create();
        $user_role = 2; // 2=Head Cha
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => $user_role,
            'memberID' => $user->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        // assign a confirmed team member to that weekend
        $user2 = factory(\App\User::class)->states('female')->create();
        $user2_role = 23; // 23=Table Cha
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => $user2_role,
            'memberID' => $user2->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        // if the headcha signs in, they cannot see their personal assignment, even though it's confirmed, because of the weekend status
        $this->signIn($user)->get('/members/' . $user->id)->assertDontSee(e(WeekendRoles::where('id', $user_role)->first()->RoleName));
        // but they can still see the Team
        $this->get('/weekend/'.$weekend->id)->assertSee('Roster')->assertSee('Candidate Fee');

        // if the non-head user signs in, they cannot see the assignment, even though it's confirmed, because of the weekend status
        $this->signIn($user2)->get('/members/' . $user2->id)->assertDontSee(e(WeekendRoles::where('id', $user2_role)->first()->RoleName));
        // but they can still see the core details on the weekend page, without the Team links
        $this->get('/weekend/'.$weekend->id)->assertDontSee('Roster')->assertSee('Candidate Fee');

        // the rector may see the assignment
        $this->signIn($rector)->get('/team/' . $weekend->id . '/edit')->assertSee(e($user2->name));
    }


    /** @test */
    public function a_weekends_team_assignments_are_only_visible_to_section_heads_when_status_is_sectionheads()
    {
        $this->seed(); // mainly to get the Roles and Permissions defined

        // assign a rector to a weekend
        $rector = factory(\App\User::class)->states('female')->create();
        $weekend = factory(\App\Weekend::class)->states('womens')->create([
            'visibility_flag' => WeekendVisibleTo::SectionHeads,
            'rectorID' => $rector->id,
            ]);
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => 1,
            'memberID' => $rector->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        // assign a confirmed section head to that weekend
        $user = factory(\App\User::class)->states('female')->create();
        $user_role = 21; // 21=Head Table Cha
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => $user_role,
            'memberID' => $user->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        // assign a confirmed team member to that weekend
        $user2 = factory(\App\User::class)->states('female')->create();
        $user2_role = 23; // 23=Table Cha
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => $user2_role,
            'memberID' => $user2->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        // if the non-section-head user signs in, they cannot see the assignment, even though it's confirmed, because of the weekend status
        $this->signIn($user2)->get('/members/' . $user2->id)->assertDontSee(e(WeekendRoles::where('id', $user2_role)->first()->RoleName));
        // but they can still see the core details on the weekend page, without the Team links
        $this->get('/weekend/'.$weekend->id)->assertDontSee('Roster')->assertSee('Candidate Fee');

        // if the user signs in, they cannot see their personal assignment, even though it's confirmed, because of the weekend status
        $this->signIn($user)->get('/members/' . $user->id)->assertDontSee(e(WeekendRoles::where('id', $user_role)->first()->RoleName));
        // but they can still see the Team
        $this->get('/weekend/'.$weekend->id)->assertSee('Roster')->assertSee('Candidate Fee');

        // the rector may see the assignment
        $this->signIn($rector)->get('/team/' . $weekend->id . '/edit')->assertSee(e($user2->name));
    }

    /** @test */
    public function the_team_of_a_weekend_set_to_community_visible_status_can_be_seen_by_members()
    {
        $this->seed(); // mainly to get the Roles and Permissions defined

        // assign a rector to a weekend
        $rector = factory(\App\User::class)->states('female')->create();
        $weekend = factory(\App\Weekend::class)->states('womens')->create([
            'visibility_flag' => WeekendVisibleTo::Community,
            'rectorID' => $rector->id,
            ]);
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => 1,
            'memberID' => $rector->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        // assign a confirmed team member to that weekend
        $user = factory(\App\User::class)->states('female')->create();
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => 10, // 10=Piety, 23=Table Cha
            'memberID' => $user->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        // if the user signs in, they can see the assignment
        $this->signIn($user)->get('/members/' . $user->id)->assertSee(e(WeekendRoles::where('id', 10)->first()->RoleName));

        // the rector may see the assignment
        $this->signIn($rector)->get('/team/' . $weekend->id . '/edit')->assertSee(e($user->name));
    }
}
