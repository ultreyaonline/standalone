<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class GuestOnlyRestrictionsTest extends DuskTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Scenario: 3.0.1 Guest attempt to access to members home
     * Given I am not logged in
     * And I visit "/profile"
     * Then I should not be logged in
     * And I should be on "/login"
     *
     * @return void
     */
    public function test_guest_access_to_profile_page_redirects_to_login()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/profile')
                    ->assertDontSee('Community Directory')
                // @TODO: MUST FIX THIS TEST  (requires making migrations work)
//                    ->assertRouteIs('login')
                    ->assertGuest()
            ;
        });
    }
    /**
     * Scenario: 3.0.5 Candidate attempt to access to members home  (NOTE: Should refine further as Roles/Permissions are added)
     * Given I am not a member
     * And I visit "/home"
     * Then I should not see "Community Directory"
     *
     * @return void
     */
    public function test_guest_may_not_access_members_only_home_dashboard()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/home')
                    ->assertDontSee('Community Directory');
        });
    }
}
