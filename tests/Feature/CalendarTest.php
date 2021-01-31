<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_public_calendar_without_showing_private_entries(): void
    {
        $this->withoutExceptionHandling();

        $date = Carbon::tomorrow();
        Event::factory()->enabled()->notPublic()->create([
            'type' => 'secretariat',
            'name' => 'Secretariat Meeting',
            'start_datetime' => $date,
            'end_datetime' => $date->addHours(3),
        ]);

        $response = $this->get('/calendar');

        $response->assertStatus(200);

        $response->assertSee('pcoming'); // left off the U for case-insensitive test
        $response->assertDontSee('Secretariat Meeting');
    }

    /** @test */
    public function members_can_see_member_events(): void
    {
        $this->seed();

        $response = $this->signIn()
            ->get('/events');

        $response->assertStatus(200);

        $response->assertSee('Events');
        $response->assertViewIs('events.index');
    }

    /** @test */
    public function members_can_see_secretariat_meetings(): void
    {
        $this->seed();

        $date = Carbon::tomorrow();
        Event::factory()->enabled()->notPublic()->create([
            'type' => 'secretariat',
            'name' => 'Secretariat Test Meeting',
            'start_datetime' => $date,
            'end_datetime' => $date->addHours(3),
        ]);

        $response = $this->signIn()
            ->get('/events');

        $response->assertStatus(200);

        $response->assertSee('Secretariat Test Meeting');
        $response->assertViewIs('events.index');
    }

    /** @test */
    public function authorized_members_can_create_calendar_events(): void
    {
        $this->seed();

        $user = \App\Models\User::factory()->create();
        $user->assignRole('Member');
        $user->assignRole('Admin');

        $response = $this->signIn($user)
            ->post(route('events.store'), [
                'name' => 'TestEvent#1',
                'type' => 'secretariat',
                'start_datetime' => '2031-01-01 19:00:00',
                'end_datetime' => '2031-01-01 21:00:00',
                'expiration_date' => '2031-01-31 21:00:00',
                'is_enabled' => '1',
                'is_public' => '0',
                'contact_email' => 'test@example.com',
                'location_url' => 'http://location.example.com',
                'location_name' => 'EventLocation',
            ]);

        $response->assertRedirect('/events');

        $response = $this->signIn()
            ->get('/events');

        $response->assertSee('TestEvent#1');
    }
}
