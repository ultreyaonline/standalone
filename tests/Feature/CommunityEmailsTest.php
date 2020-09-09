<?php

namespace Tests\Feature;

use App\Mail\MessageToCommunity;
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

class CommunityEmailsTest extends TestCase
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
            'receive_prayer_wheel_invites' => true,
            'receive_email_reunion' => true,
            'receive_email_sequela' => true,
            'receive_email_community_news' => true,
            'receive_email_weekend_general' => true,
            'community' => config('site.local_community_filter'),
        ];
    }

    /** @test */
    public function secretariat_member_can_see_button_and_form_to_email_entire_community()
    {
        Mail::fake();

        $user = factory(\App\Models\User::class)->create($this->member_attributes);
        $user->assignRole('Member');
        $user->assignRole('Secretariat');

        $this->actingAs($user)->get('/home')
            ->assertSee('Communication / Email');

        $this->get('/email-everyone')
            ->assertSee(e('Message all ' . config('site.community_acronym') . ' Community Members'), false)
            ->assertViewIs('emails.community_message_compose');
    }

    /** @test */
    public function secretariat_member_can_send_email_to_email_entire_community()
    {
        Mail::fake();

        $user = factory(\App\Models\User::class)->create($this->member_attributes);
        $user->assignRole('Member');
        $user->assignRole('Secretariat');

        // add a community member
        $member = factory(\App\Models\User::class)->create($this->member_attributes);
        $member->assignRole('Member');

        $this->actingAs($user)
            ->post(action('App\Http\Controllers\CommunicationController@emailEntireCommunity'), [
            'subject' => 'TheSubject',
            'message' => 'TheMessage',
        ]);

        // test that the email is queued
        Mail::assertQueued(MessageToCommunity::class, function ($mail) use ($member) {
            return $mail->hasTo($member->email);
        });
    }

    /** @test */
    public function emails_to_entire_community_exclude_non_members_and_unsubscribes()
    {
        Mail::fake();

        $user = factory(\App\Models\User::class)->create($this->member_attributes);
        $user->assignRole('Member');
        $user->assignRole('Secretariat');

        // active member, for sanity check that emails actually "do" get queued
        $active_member = factory(\App\Models\User::class)->states('female')->create($this->member_attributes);
        $active_member->assignRole('Member');

        $blank_email_member = factory(\App\Models\User::class)->states('female')->create($this->member_attributes);
        $blank_email_member->email = '';
        $blank_email_member->assignRole('Member');

        $non_member = factory(\App\Models\User::class)->states('female')->create($this->member_attributes);

        $inactive_member = factory(\App\Models\User::class)->states('female')->create($this->member_attributes);
        $inactive_member->assignRole('Member');
        $inactive_member->active = false;
        $inactive_member->save();

        $unsubscribed_member = factory(\App\Models\User::class)->states('female')->create($this->member_attributes);
        $unsubscribed_member->assignRole('Member');
        $unsubscribed_member->unsubscribe = true;
        $unsubscribed_member->save();

        $other_gender_member = factory(\App\Models\User::class)->states('male')->create($this->member_attributes);
        $other_gender_member->assignRole('Member');
        $other_gender_member->receive_email_community_news = false;
        $other_gender_member->save();

        $other_community_member = factory(\App\Models\User::class)->states('female')->create($this->member_attributes);
        $other_community_member->assignRole('Member');
        $other_community_member->community = 'OTHERCMTY';
        $other_community_member->save();

        $no_news_member = factory(\App\Models\User::class)->states('female')->create($this->member_attributes);
        $no_news_member->assignRole('Member');
        $no_news_member->receive_email_community_news = false;
        $no_news_member->save();

        $no_surprises_member = factory(\App\Models\User::class)->states('female')->create($this->member_attributes);
        $no_surprises_member->assignRole('Member');
        $no_surprises_member->okay_to_send_serenade_and_palanca_details = false;
        $no_surprises_member->save();

        // @TODO - test spouse of non-attended member, using same and different email addresses
        // @TODO - restrict attachment handling, and only process attachments if authorized

        $this->actingAs($user)
            ->withoutExceptionHandling()
            ->post(action('App\Http\Controllers\CommunicationController@emailEntireCommunity'), [
                'subject' => 'TheSubject',
                'message' => 'TheMessage',
                'mail_to_gender' => 'W',
                'contains_surprises' => 'no', // yes if testing okay_to_send_serenade_and_palanca_details
                'community_local' => 'local',
                'community_other' => 'no',
                'notice_type' => 'community', // receive_email_community_news
//                'notice_type' => 'weekend', // receive_email_weekend_general
//                'notice_type' => 'sequela', // receive_email_sequela
//                'notice_type' => 'reunion', // receive_email_reunion
            ]);


        // test that the email is queued
        Mail::assertQueued(MessageToCommunity::class, function ($mail) use ($active_member) {
            return $mail->hasTo($active_member->email);
        });

        // and these are not queued
        Mail::assertNotQueued(MessageToTeamMembers::class, function ($mail) use ($blank_email_member) {
            return $mail->hasTo($blank_email_member->email);
        });
        Mail::assertNotQueued(MessageToTeamMembers::class, function ($mail) use ($non_member) {
            return $mail->hasTo($non_member->email);
        });
        Mail::assertNotQueued(MessageToTeamMembers::class, function ($mail) use ($inactive_member) {
            return $mail->hasTo($inactive_member->email);
        });
        Mail::assertNotQueued(MessageToTeamMembers::class, function ($mail) use ($unsubscribed_member) {
            return $mail->hasTo($unsubscribed_member->email);
        });
        Mail::assertNotQueued(MessageToTeamMembers::class, function ($mail) use ($other_gender_member) {
            return $mail->hasTo($other_gender_member->email);
        });
        Mail::assertNotQueued(MessageToTeamMembers::class, function ($mail) use ($other_community_member) {
            return $mail->hasTo($other_community_member->email);
        });
        Mail::assertNotQueued(MessageToTeamMembers::class, function ($mail) use ($no_news_member) {
            return $mail->hasTo($no_news_member->email);
        });
        Mail::assertNotQueued(MessageToTeamMembers::class, function ($mail) use ($no_surprises_member) {
            return $mail->hasTo($no_surprises_member->email);
        });
    }
}
