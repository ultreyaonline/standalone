<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Weekend;
use DatabaseSeeder;
use Tests\TestCase;
use App\Models\WeekendAssignments;
use Illuminate\Support\Carbon;
use App\Enums\WeekendVisibleTo;
use App\Mail\MessageToTeamMembers;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeamEmailsTest extends TestCase
{
    use RefreshDatabase;

    protected $member_attributes;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);

        // now re-register all the roles and permissions
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->registerPermissions();

        $this->member_attributes = [
            'active' => true,
            'unsubscribe' => false,
        ];
    }

    /** @test */
    public function section_head_of_open_weekend_can_email_entire_team()
    {
        Mail::fake();

        // assign a rector to a weekend
        $rector = factory(\App\Models\User::class)->states('female')->create();
        $rector->assignRole('Member');
        $weekend = factory(\App\Models\Weekend::class)->states('womens')->create(['visibility_flag' => WeekendVisibleTo::Community]);
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => 1,
            'memberID' => $rector->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        // assign a section head to that weekend
        $sectionHead = factory(\App\Models\User::class)->states('female')->create();
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => 27, // head chapel
            'memberID' => $sectionHead->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        // assign a confirmed team member to that weekend
        $user = factory(\App\Models\User::class)->states('female')->create($this->member_attributes);
        $user->assignRole('Member');
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => 10, // rollista:piety
            'memberID' => $user->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        // add a candidate
        $candidate = factory(\App\Models\User::class)->states('female')->create([
            'weekend' => $weekend->shortname,
            'okay_to_send_serenade_and_palanca_details' => false,
            'interested_in_serving' => false,
            'active' => false,
            'allow_address_share' => false,
            'receive_prayer_wheel_invites' => false,
            'receive_email_reunion' => false,
            'receive_email_sequela' => false,
            'receive_email_community_news' => false,
            'receive_email_weekend_general' => false,
        ]);
        $candidate_model = factory(\App\Models\Candidate::class)->create(['weekend' => $weekend->shortname, 'w_user_id' => $candidate->id]);


        // section head can see "send email to entire team" dialog
        $this->signIn($sectionHead)->get('/weekend/' . $weekend->id)->assertSee('Email the Team');
        $this->get('/team/' . $weekend->id . '/email')
            ->assertSee('Message to Team')
            ->assertViewIs('emails.team_message_compose');

        // section head sends email to team
        $this->post(action('App\Http\Controllers\CommunicationController@emailTeamMembers', $weekend), [
            'subject' => 'TheSubject',
            'message' => 'TheMessage',
// 0 means not sent;  We can "test" the tests by setting this to 1 and expecting the "NotQueued" below to fail.
//            'include_candidates' => '0',
        ]);


        // test that the email is queued for rollista
        Mail::assertQueued(MessageToTeamMembers::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
        // queued for Rector (in this case, just "another team member")
        Mail::assertQueued(MessageToTeamMembers::class, function ($mail) use ($rector) {
            return $mail->hasTo($rector->email);
        });

        // NOT queued for candidate
        Mail::assertNotQueued(MessageToTeamMembers::class, function ($mail) use ($candidate) {
            return $mail->hasTo($candidate->email);
        });



        // a Rollista shouldn't see the Email The Team option
        $this->actingAs($user)->get('/weekend/' . $weekend->id)->assertDontSee('Email the Team');

        // nor be able to send an email
        $this->post(action('App\Http\Controllers\CommunicationController@emailTeamMembers', $weekend), [
            'subject' => 'TheSubject',
            'message' => 'TheMessage',
        ])->assertRedirect();
    }


    /** @test */
    public function section_head_of_open_weekend_can_email_a_section()
    {
        Mail::fake();

        // assign a rector to a weekend
        $rector = factory(\App\Models\User::class)->states('female')->create();
        $rector->assignRole('Member');
        $weekend = factory(\App\Models\Weekend::class)->states('womens')->create(['visibility_flag' => WeekendVisibleTo::Community]);
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => 1,
            'memberID' => $rector->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        // assign a section head to that weekend
        $sectionHead = factory(\App\Models\User::class)->states('female')->create();
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => 27, // head chapel
            'memberID' => $sectionHead->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        // assign a confirmed team member in that same section
        $user = factory(\App\Models\User::class)->states('female')->create($this->member_attributes);
        $user->assignRole('Member');
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => 29, // chapel cha
            'memberID' => $user->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        // assign a confirmed team member in that same section
        $user2 = factory(\App\Models\User::class)->states('female')->create($this->member_attributes);
        $user2->assignRole('Member');
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => 32, // dorm cha
            'memberID' => $user2->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        // add a candidate
        $candidate = factory(\App\Models\User::class)->states('female')->create([
            'weekend' => $weekend->shortname,
            'okay_to_send_serenade_and_palanca_details' => false,
            'interested_in_serving' => false,
            'active' => false,
            'allow_address_share' => false,
            'receive_prayer_wheel_invites' => false,
            'receive_email_reunion' => false,
            'receive_email_sequela' => false,
            'receive_email_community_news' => false,
            'receive_email_weekend_general' => false,
        ]);
        $candidate_model = factory(\App\Models\Candidate::class)->create(['weekend' => $weekend->shortname, 'w_user_id' => $candidate->id]);


        // section head can see selection of sections
        $this->signIn($sectionHead)
            ->get('/team/' . $weekend->id . '/email')
            ->assertSee('Table Cha')
            ->assertViewIs('emails.team_message_compose');

        $this->post(action('App\Http\Controllers\CommunicationController@emailTeamMembers', $weekend), [
            'subject' => 'TheSubject',
            'message' => 'TheMessage',
            'section' => '5', // 5 = Chapel
// 0 means not sent;  We can "test" the tests by setting this to 1 and expecting the "NotQueued" below to fail.
//            'include_candidates' => '0',
        ]);


        // test that the email is queued for section member
        Mail::assertQueued(MessageToTeamMembers::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });

        // NOT queued for other section
        Mail::assertNotQueued(MessageToTeamMembers::class, function ($mail) use ($user2) {
            return $mail->hasTo($user2->email);
        });

        // NOT queued for candidate
        Mail::assertNotQueued(MessageToTeamMembers::class, function ($mail) use ($candidate) {
            return $mail->hasTo($candidate->email);
        });
    }



    /** @test */
    public function an_email_to_the_team_can_include_candidates_if_selected()
    {
        Mail::fake();

        // assign a rector to a weekend
        $rector = factory(\App\Models\User::class)->states('female')->create();
        $rector->assignRole('Member');
        $weekend = factory(\App\Models\Weekend::class)->states('womens')->create(['visibility_flag' => WeekendVisibleTo::Community]);
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => 1,
            'memberID' => $rector->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        // assign a section head to that weekend
        $sectionHead = factory(\App\Models\User::class)->states('female')->create();
        WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID' => 27, // head chapel
            'memberID' => $sectionHead->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        // add a candidate
        $candidate = factory(\App\Models\User::class)->states('female')->create([
            'weekend' => $weekend->shortname,
            'okay_to_send_serenade_and_palanca_details' => false,
            'interested_in_serving' => false,
            'active' => false,
            'allow_address_share' => false,
            'receive_prayer_wheel_invites' => false,
            'receive_email_reunion' => false,
            'receive_email_sequela' => false,
            'receive_email_community_news' => false,
            'receive_email_weekend_general' => false,
        ]);
        $candidate_model = factory(\App\Models\Candidate::class)->create(['weekend' => $weekend->shortname, 'w_user_id' => $candidate->id]);

        // add a 2nd candidate without an email address
        $candidate2 = factory(\App\Models\User::class)->states('female')->create([
            'weekend' => $weekend->shortname,
            'email' => '',
            'okay_to_send_serenade_and_palanca_details' => false,
            'interested_in_serving' => false,
            'active' => false,
            'allow_address_share' => false,
            'receive_prayer_wheel_invites' => false,
            'receive_email_reunion' => false,
            'receive_email_sequela' => false,
            'receive_email_community_news' => false,
            'receive_email_weekend_general' => false,
        ]);
        $candidate_model = factory(\App\Models\Candidate::class)->create(['weekend' => $weekend->shortname, 'w_user_id' => $candidate2->id]);


        // section head can see "send email to entire team" dialog
        $this->signIn($sectionHead)->get('/weekend/' . $weekend->id)->assertSee('Email the Team');
        $this->get('/team/' . $weekend->id . '/email')
            ->assertSee('Message to Team')
            ->assertViewIs('emails.team_message_compose');

        // section head sends email to team
        $this->post(action('App\Http\Controllers\CommunicationController@emailTeamMembers', $weekend), [
            'subject' => 'TheSubject',
            'message' => 'TheMessage',
            'include_candidates' => '1',
        ]);


        // queued for Rector (in this case, just "another team member")
        Mail::assertQueued(MessageToTeamMembers::class, function ($mail) use ($rector) {
            return $mail->hasTo($rector->email);
        });

        // queued for candidate1
        Mail::assertQueued(MessageToTeamMembers::class, function ($mail) use ($candidate) {
            return $mail->hasTo($candidate->email);
        });

        // NOT queued for candidate2
        Mail::assertNotQueued(MessageToTeamMembers::class, function ($mail) use ($candidate2) {
            return $mail->hasTo($candidate2->email);
        });
    }
}
