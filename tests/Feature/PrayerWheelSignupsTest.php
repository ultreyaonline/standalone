<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Weekend;
use App\Models\PrayerWheel;
use Tests\TestCase;
use App\Models\PrayerWheelSignup;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PrayerWheelSignupsTest extends TestCase
{
    use RefreshDatabase;

    protected $weekend, $wheel;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();
        $this->weekend = Weekend::factory()->create();
        $this->wheel   = PrayerWheel::factory()->create(['weekendID' => $this->weekend->id]);
    }

    /** @test */
    public function members_can_see_prayerwheel_page()
    {
        $response = $this->signIn()
            ->get('/prayerwheel');

        $response->assertSee('72-hour Prayer Wheel')
            ->assertStatus(200)
            ->assertSee('#'.$this->weekend->weekend_number);
    }

    /** @test */
    public function members_can_see_a_specific_wheel()
    {
        $response = $this->signIn()
            ->get('/prayerwheel/' . $this->wheel->id);

        $response->assertSee('choose an hour to pray for');
    }

    /** @test */
    public function members_can_sign_up_for_a_timeslot()
    {
        Mail::fake();

        $response = $this->signIn()
            ->patch('/prayerwheel/' . $this->wheel->id, ['memberID' => $this->user->id, 'hour' => 18]);

        $response->assertRedirect('/prayerwheel/' . $this->wheel->id);

        $response = $this->get('/prayerwheel/' . $this->wheel->id);

        $response->assertSee('span id="spot-' . '18' . '-' . $this->user->id . '">' . e($this->user->name), false);
    }

    /** @test */
    public function a_members_prayer_wheel_signups_can_be_seen_on_own_profile_page()
    {
        Mail::fake();

        $response = $this->signIn();
        PrayerWheelSignup::create(['memberID' => $this->user->id, 'timeslot' => 18, 'wheel_id' => $this->wheel->id]);

        $response = $this->withoutExceptionHandling()
            ->get('/home');

        $response->assertStatus(200);
        $response->assertSee('Prayer Wheel Signups for ' . e($this->user->name), false);
    }

    /** @test */
    public function members_can_delete_their_signup()
    {
        Mail::fake();

        // create user first, then sign them up
        $response = $this->signIn();
        PrayerWheelSignup::create(['memberID' => $this->user->id, 'timeslot' => 18, 'wheel_id' => $this->wheel->id]);

//        Test that the signup actually shows:
//        $response = $this->get('/prayerwheel/' . $this->wheel->id);
//        $response->assertSee('span id="' . '18' . '-' . $this->user->id . '">' . e($this->user->name), false);

        // trigger delete
        $response = $this->delete('/prayerwheel/' . $this->wheel->id, ['memberID' => $this->user->id, 'hour' => 18]);
        $response->assertRedirect('/prayerwheel/' . $this->wheel->id);

        // test that it's not seen
        $response = $this->get('/prayerwheel/' . $this->wheel->id);
        $response->assertDontSee('span id="' . '18' . '-' . $this->user->id . '">' . e($this->user->name), false);
    }

    /** @test */
    public function if_someone_else_has_already_signed_up_for_a_spot_then_it_is_properly_indicated()
    {
        Mail::fake();

        $otherUser = User::factory()->create();
        PrayerWheelSignup::create(['memberID' => $otherUser->id, 'timeslot' => 18, 'wheel_id' => $this->wheel->id]);

        $response = $this->signIn()
            ->get('/prayerwheel/' . $this->wheel->id);

        if (config('site.prayerwheel_names_visible_to_all')) {
            $response->assertSee('id="spot-' . '18' . '-' . $otherUser->id . '">' . e($otherUser->name), false);
        } else {
            $response->assertSee('id="viewPosition-' . '18' . '" class="d-print-none">' . 'This spot is taken', false);
        }
    }
}
