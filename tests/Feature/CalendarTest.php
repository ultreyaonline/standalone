<?php

namespace Tests\Feature;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_public_calendar()
    {
        $this->withoutExceptionHandling();

        $date = Carbon::tomorrow();
        factory(\App\Event::class)->create(['type' => 'secretariat', 'name' => 'Secretariat Meeting', 'start_datetime' => $date, 'end_datetime' => $date->addHours(3), 'is_public' => 0, 'is_enabled' => 1]);

        $response = $this->get('/calendar');

        $response->assertStatus(200);

        $response->assertSee('pcoming'); // left off the U for case-insensitive test
        $response->assertDontSee('Secretariat');
    }

    /** @test */
    public function members_can_see_member_events()
    {
        $this->seed();

        $response = $this->signIn()
            ->get('/events');

        $response->assertStatus(200);

        $response->assertSee('Events');
        $response->assertViewIs('events.index');
    }

    /** @test */
    public function members_can_see_secretariat_meetings()
    {
        $this->seed();

        $date = Carbon::tomorrow();
        factory(\App\Event::class)->create(['type' => 'secretariat', 'name' => 'Secretariat Meeting', 'start_datetime' => $date, 'end_datetime' => $date->addHours(3), 'is_public' => 0, 'is_enabled' => 1]);

        $response = $this->signIn()
            ->get('/events');

        $response->assertStatus(200);

        $response->assertSee('Secretariat Meeting');
        $response->assertViewIs('events.index');
    }
}
