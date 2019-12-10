<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StaticPagesTest extends TestCase
{
    /** @test */
    public function it_displays_home_page()
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        $response->assertSee(config('site.community_acronym'));
    }

    /** @test */
    public function it_displays_about_page()
    {
        $response = $this->get('/about');

        $response->assertStatus(200);

        $response->assertSee('What is Tres Dias?');
    }

    /** @test */
    public function it_can_display_beliefs_page()
    {
        $response = $this->get('/believe');

        $response->assertStatus(200);

        $response->assertSee('What we believe');
    }

    /** @test */
    public function it_can_display_history_page()
    {
        $response = $this->get('/history');

        $response->assertStatus(200);

        $response->assertSee('Cursillo');
    }
}
