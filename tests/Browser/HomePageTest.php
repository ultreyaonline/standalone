<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomePageTest extends DuskTestCase
{
//    use RefreshDatabase;

    /** @test */
    public function home_page_displays_app_name()
    {
        // display the URL the test is using ... helpful for quick debugging of environment files
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080);
            $browser->visit('/')
                    ->assertSee(config('site.community_long_name'));
        });
    }
}
