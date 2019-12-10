<?php

namespace Tests\Feature;

use App\Enums\WeekendVisibleTo;
use App\User;
use App\Weekend;
use Tests\TestCase;
use App\PrayerWheel;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WeekendTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function members_can_see_weekend_page()
    {
        $this->seed();

        $weekend = create(Weekend::class, ['weekend_theme'=>'Times of Refreshing', 'visibility_flag' => WeekendVisibleTo::ThemeVisible]);
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
        $this->seed();

        $weekend = create(Weekend::class, ['weekend_MF'=>'W', 'weekend_theme'=>'Times of Refreshing', 'visibility_flag' => WeekendVisibleTo::ThemeVisible]);
        PrayerWheel::create(['weekendID' => $weekend->id]);

        $candidate = create(User::class, ['gender'=>'W', 'weekend'=>$weekend->short_name]);

        $response = $this->signIn()
            ->get('/weekend/'.$weekend->id);

        $response->assertStatus(200);

        $response->assertSee('Candidate Names for ' . e($weekend->short_name . '-' . $weekend->weekend_MF));
        $response->assertSee(e($candidate->last));
        $response->assertViewIs('weekend.show');
    }
}
