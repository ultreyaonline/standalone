<?php

namespace Tests\Feature;

use App\Enums\WeekendVisibleTo;
use DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MemberNoticesTest extends TestCase
{
    use RefreshDatabase;

    protected $member_attributes;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);

        // now re-register all the roles and permissions
        $this->clearPermissionsCache();

        $this->member_attributes = [
            'active' => true,
            'unsubscribe' => false,
        ];
    }

    /** @test */
    public function a_members_candidate_verifications_can_be_seen_on_own_profile_page()
    {
        // @TODO: both husband AND wife should be able to see this
        // @TODO should be able to respond to it here too
        // - create spouse record with spouseID, link both spouses, login as spouse, test view

        Mail::fake();

        Config::set('site.preweekend_sponsor_confirmations_enabled', true);

        $weekend = factory(\App\Weekend::class)->states('womens')->create(['visibility_flag' => WeekendVisibleTo::Community]);

        $member = create(\App\User::class);
        $spouse = create(\App\User::class, ['spouseID' => $member->id]);
        $member->spouseID = $spouse->id;
        $member->save();

        // add a candidate
        $candidate = factory(\App\User::class)->states('female')->create([
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
            'sponsorID' => $member->id,
        ]);
        $candidate_model = factory(\App\Candidate::class)->create([
            'weekend' => $weekend->shortname,
            'w_user_id' => $candidate->id,
            'sponsor_confirmed_details' => false,
        ]);

        $response = $this->withoutExceptionHandling()
            ->signIn($member)
            ->get('/home');

        $response->assertStatus(200);
        $response->assertSee('Candidate Confirmation Required for ' . e($member->name));
        $response->assertSee('<li>' . e($candidate->name));

        // turn off and test that it doesn't display now
        Config::set('site.preweekend_sponsor_confirmations_enabled', false);
        $response = $this->withoutExceptionHandling()
            ->get('/home');

        $response->assertStatus(200);
        $response->assertDontSee('Candidate Confirmation Required for ' . e($member->name));
        $response->assertDontSee('<li>' . e($candidate->name));

    }
}
