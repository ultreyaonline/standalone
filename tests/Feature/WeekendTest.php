<?php

namespace Tests\Feature;

use App\Enums\WeekendVisibleTo;
use App\Models\User;
use App\Models\Weekend;
use Tests\TestCase;
use App\Models\PrayerWheel;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WeekendTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    /** @test */
    public function members_can_see_weekend_page()
    {
        $weekend = Weekend::factory([
            'weekend_theme'=>'Times of Refreshing',
            'visibility_flag' => WeekendVisibleTo::ThemeVisible]
        )->create();
        PrayerWheel::create(['weekendID' => $weekend->id]);

        $response = $this->signIn()
            ->get('/weekend/'.$weekend->id);

        $response->assertStatus(200);

        $response->assertSee('Times of Refreshing');
        $response->assertSee('Team Meetings');
        $response->assertViewIs('weekend.show');
    }

    /** @test */
    public function members_can_see_weekend_candidate_list()
    {
        $weekend = Weekend::factory([
            'weekend_MF' => 'W',
            'weekend_theme' => 'Times of Refreshing',
            'visibility_flag' => WeekendVisibleTo::ThemeVisible
        ])->create();
        PrayerWheel::create(['weekendID' => $weekend->id]);

        $candidate = User::factory(['gender'=>'W', 'weekend'=>$weekend->short_name])->create();

        $response = $this->signIn()
            ->get('/weekend/'.$weekend->id);

        $response->assertStatus(200);

        $response->assertSee('Candidate Names for ' . $weekend->short_name . '-' . $weekend->weekend_MF);
        $response->assertSee($candidate->last);
        $response->assertViewIs('weekend.show');
    }
}
