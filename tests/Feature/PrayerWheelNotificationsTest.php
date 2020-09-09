<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Weekend;
use DatabaseSeeder;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use App\Models\PrayerWheel;
use App\Models\PrayerWheelSignup;
use App\Mail\PrayerWheelInviteEmail;
use Illuminate\Support\Facades\Mail;
use App\Mail\PrayerWheelReminderEmail;
use App\Jobs\SendPrayerWheelReminderEmails;
use App\Jobs\SendPrayerWheelAcknowledgements;
use App\Mail\PrayerWheelAcknowledgementEmail;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PrayerWheelNotificationsTest extends TestCase
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
            'email'                          => 'john@example.com',
            'active'                         => true,
            'receive_prayer_wheel_invites'   => true,
            'receive_prayer_wheel_reminders' => true,
            'unsubscribe'                    => false,
        ];
    }

    /** @test */
    public function invitations_can_be_emailed()
    {
        $this->withoutExceptionHandling();

        Mail::fake();

        // make a prayer wheel to send invites for
        $weekend = Weekend::factory()->create();
        $wheel   = PrayerWheel::factory()->create(['weekendID' => $weekend->id]);

        // make a member for testing
        $member = User::factory()->male()->create($this->member_attributes);
        $member->assignRole('Member');

        // make a member for testing blank email addresses
        $member2 = User::factory()->male()->create($this->member_attributes);
        $member2->update(['email' => '']);
        $member2->assignRole('Member');

        // make a user who is authorized to send invites
        $user = User::factory()->create();
        $user->assignRole('PrayerWheel Coordinator');
        $this->signIn($user);

        $this->post('/prayerwheel/' . $wheel->id . '/invite', [
            'subject' => 'TheSubject',
            'message' => 'TheMessage',
        ]);

        Mail::assertQueued(PrayerWheelInviteEmail::class, function ($mail) {
            return $mail->hasTo('john@example.com');
        });
        Mail::assertNotQueued(PrayerWheelInviteEmail::class, function ($mail) use ($member2) {
            return $mail->hasTo($member2->email);
        });
    }





    // @TODO: add tests to ensure only the Coordinator can send invites





    /** @test */
    public function acknowledgement_emails_can_be_sent()
    {
        Mail::fake();

        $weekend = Weekend::factory()->create();
        $wheel   = PrayerWheel::factory()->create(['weekendID' => $weekend->id]);

        $memberA = User::factory()->male()->create();
        $memberA->assignRole('Member');
        $memberB = User::factory()->female()->create();
        $memberB->update(['email' => '']);
        $memberB->assignRole('Member');

        // sign the person up for a timeslot
        PrayerWheelSignup::create(['wheel_id' => $wheel->id, 'memberID' => $memberA->id, 'timeslot' => 5]);
        PrayerWheelSignup::create(['wheel_id' => $wheel->id, 'memberID' => $memberA->id, 'timeslot' => 11]);
        PrayerWheelSignup::create(['wheel_id' => $wheel->id, 'memberID' => $memberB->id, 'timeslot' => 25]);

        // fire the acknowledge-emails job
        SendPrayerWheelAcknowledgements::dispatch();

        // test that the right recipients get the emails
        Mail::assertQueued(PrayerWheelAcknowledgementEmail::class, 1);
        Mail::assertQueued(PrayerWheelAcknowledgementEmail::class, function ($mail) use ($memberA) {
            return $mail->hasTo($memberA->email);
        });
        Mail::assertNotQueued(PrayerWheelAcknowledgementEmail::class, function ($mail) use ($memberB) {
            return $mail->hasTo($memberB->email);
        });

        // @TODO -- ensure the emails contain the applicable "hour_to" description
//        ['index' => 't23', 'day' => 'Thursday', 'hour' => '11:00pm', 'hour_to' => '11pm-12am'],
//        ['index' => 'f15', 'day' => 'Friday', 'hour' => '3:00pm', 'hour_to' => '3pm-4pm'],
    }

    /** @test */
    public function members_with_unacknowledged_signups_get_emails()
    {
        Mail::fake();

        $weekend = Weekend::factory()->create();
        $wheel   = PrayerWheel::factory()->create(['weekendID' => $weekend->id]);

        $memberA = User::factory()->male()->create();
        $memberA->assignRole('Member');
        $memberB = User::factory()->female()->create();
        $memberB->assignRole('Member');

        // sign the person up for a timeslot
        PrayerWheelSignup::create(['wheel_id' => $wheel->id, 'memberID' => $memberA->id, 'timeslot' => 5, 'acknowledged_at' => Carbon::now()]);
        PrayerWheelSignup::create(['wheel_id' => $wheel->id, 'memberID' => $memberA->id, 'timeslot' => 11]);
        PrayerWheelSignup::create(['wheel_id' => $wheel->id, 'memberID' => $memberB->id, 'timeslot' => 25, 'acknowledged_at' => Carbon::now()]);

        SendPrayerWheelAcknowledgements::dispatch();

        // test that the right recipients get the emails
        Mail::assertQueued(PrayerWheelAcknowledgementEmail::class);
        Mail::assertQueued(PrayerWheelAcknowledgementEmail::class, function ($mail) use ($memberA) {
            return $mail->hasTo($memberA->email);
        });
        Mail::assertNotQueued(PrayerWheelAcknowledgementEmail::class, function ($mail) use ($memberB) {
            return $mail->hasTo($memberB->email);
        });

        // @TODO -- ensure the emails contain the applicable "hour_to" description
//        ['index' => 't23', 'day' => 'Thursday', 'hour' => '11:00pm', 'hour_to' => '11pm-12am'],
//        ['index' => 'f15', 'day' => 'Friday', 'hour' => '3:00pm', 'hour_to' => '3pm-4pm'],
    }


    /** @test */
    public function members_with_unacknowledged_signups_get_emails_once_and_not_again()
    {
        Mail::fake();

        $weekend = Weekend::factory()->create();
        $wheel   = PrayerWheel::factory()->create(['weekendID' => $weekend->id]);

        $memberA = User::factory()->male()->create();
        $memberA->assignRole('Member');
        $memberB = User::factory()->female()->create();
        $memberB->assignRole('Member');

        // sign up 2 people for combos of acknowledged timeslots
        PrayerWheelSignup::create(['wheel_id' => $wheel->id, 'memberID' => $memberA->id, 'timeslot' => 5, 'acknowledged_at' => Carbon::now()]);
        PrayerWheelSignup::create(['wheel_id' => $wheel->id, 'memberID' => $memberA->id, 'timeslot' => 11]);
        PrayerWheelSignup::create(['wheel_id' => $wheel->id, 'memberID' => $memberB->id, 'timeslot' => 25, 'acknowledged_at' => Carbon::now()]);

        SendPrayerWheelAcknowledgements::dispatch();

        // test that only A gets the email
        Mail::assertQueued(PrayerWheelAcknowledgementEmail::class, 1);
        Mail::assertQueued(PrayerWheelAcknowledgementEmail::class, function ($mail) use ($memberA) {
            return $mail->hasTo($memberA->email);
        });
        Mail::assertNotQueued(PrayerWheelAcknowledgementEmail::class, function ($mail) use ($memberB) {
            return $mail->hasTo($memberB->email);
        });

        // add another signup for B and dispatch again
        PrayerWheelSignup::create(['wheel_id' => $wheel->id, 'memberID' => $memberB->id, 'timeslot' => 44]);
        SendPrayerWheelAcknowledgements::dispatch();

        // should have one more email, this time for B
        Mail::assertQueued(PrayerWheelAcknowledgementEmail::class, 2);
        Mail::assertQueued(PrayerWheelAcknowledgementEmail::class, function ($mail) use ($memberB) {
            return $mail->hasTo($memberB->email);
        });


        // @TODO -- ensure the emails contain the applicable "hour_to" description
//        ['index' => 't23', 'day' => 'Thursday', 'hour' => '11:00pm', 'hour_to' => '11pm-12am'],
//        ['index' => 'f15', 'day' => 'Friday', 'hour' => '3:00pm', 'hour_to' => '3pm-4pm'],
    }




    /** @test */
    public function reminder_emails_can_be_sent()
    {
        Mail::fake();

        $weekend = Weekend::factory()->create();
        $wheel   = PrayerWheel::factory()->create(['weekendID' => $weekend->id]);

        Carbon::setTestNow($weekend->start_date);

        $memberA = User::factory()->male()->create(\array_merge($this->member_attributes, ['email' => 'john@example.com']));
        $memberA->assignRole('Member');
        $memberB = User::factory()->female()->create(\array_merge($this->member_attributes, ['email' => 'nobody@example.com', 'receive_prayer_wheel_reminders' => false]));
        $memberB->assignRole('Member');
        $memberC = User::factory()->female()->create(\array_merge($this->member_attributes, ['email' => 'jane@example.com']));
        $memberC->assignRole('Member');
        $memberD = User::factory()->female()->create(\array_merge($this->member_attributes, ['email' => '']));
        $memberD->update(['email' => '']);
        $memberD->assignRole('Member');

        // sign the person up for a timeslot
        PrayerWheelSignup::create(['wheel_id' => $wheel->id, 'memberID' => $memberA->id, 'timeslot' => 5]);
        PrayerWheelSignup::create(['wheel_id' => $wheel->id, 'memberID' => $memberB->id, 'timeslot' => 11]);
        PrayerWheelSignup::create(['wheel_id' => $wheel->id, 'memberID' => $memberC->id, 'timeslot' => 25]);
        PrayerWheelSignup::create(['wheel_id' => $wheel->id, 'memberID' => $memberD->id, 'timeslot' => 21]);

        // fire the reminder emails job
        SendPrayerWheelReminderEmails::dispatch();

        // test that the right recipients get the emails (not the unsubscribed, and not the empty-email-address)

        // @TODO sometimes this test fails to queue twice. Rerunning the test usually lets it pass. Need to investigate "why".
        Mail::assertQueued(PrayerWheelReminderEmail::class, $times = 2);

        Mail::assertQueued(PrayerWheelReminderEmail::class, function ($mail) {
            return $mail->hasTo('john@example.com');
        });
        Mail::assertQueued(PrayerWheelReminderEmail::class, function ($mail) {
            return $mail->hasTo('jane@example.com');
        });
        Mail::assertNotQueued(PrayerWheelReminderEmail::class, function ($mail) {
            return $mail->hasTo('nobody@example.com');
        });

        // @TODO -- ensure the emails contain the applicable "hour_to" description
//        ['index' => 't23', 'day' => 'Thursday', 'hour' => '11:00pm', 'hour_to' => '11pm-12am'],
//        ['index' => 'f15', 'day' => 'Friday', 'hour' => '3:00pm', 'hour_to' => '3pm-4pm'],
    }
}
