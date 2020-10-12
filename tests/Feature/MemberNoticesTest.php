<?php

namespace Tests\Feature;

use App\Enums\WeekendVisibleTo;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MemberNoticesTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    /** @test */
    public function a_members_candidate_verifications_can_be_seen_on_own_profile_page()
    {
        // @TODO: both husband AND wife should be able to see this
        // @TODO should be able to respond to it here too
        // - create spouse record with spouseID, link both spouses, login as spouse, test view

        Mail::fake();

        Config::set('site.preweekend_sponsor_confirmations_enabled', true);

        $weekend = \App\Models\Weekend::factory()->womens()->create(['visibility_flag' => WeekendVisibleTo::Community]);

        $member = \App\Models\User::factory()->create();
        $spouse = \App\Models\User::factory(['spouseID' => $member->id])->create();
        $member->spouseID = $spouse->id;
        $member->save();

        // add a candidate
        $candidate = \App\Models\User::factory()->female()->asCandidate()->create([
            'weekend' => $weekend->shortname,
            'sponsorID' => $member->id,
        ]);
        $candidate_model = \App\Models\Candidate::factory()->create([
            'weekend' => $weekend->shortname,
            'w_user_id' => $candidate->id,
            'sponsor_confirmed_details' => false,
        ]);

        $response = $this->withoutExceptionHandling()
            ->signIn($member)
            ->get('/home');

        $response->assertStatus(200);
        $response->assertSee('Candidate Confirmation Required for ' . e($member->name), false);
        $response->assertSee('<li>' . e($candidate->name), false);

        // turn off and test that it doesn't display now
        Config::set('site.preweekend_sponsor_confirmations_enabled', false);
        $response = $this->withoutExceptionHandling()
            ->get('/home');

        $response->assertStatus(200);
        $response->assertDontSee('Candidate Confirmation Required for ' . e($member->name), false);
        $response->assertDontSee('<li>' . e($candidate->name), false);

    }
}
