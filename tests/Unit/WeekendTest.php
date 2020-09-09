<?php

namespace Tests\Unit;

use App\Models\Weekend;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WeekendTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function weekend_has_ended_attribute_tests_false_for_an_active_weekend()
    {
        $weekend = Weekend::factory()->create([
            'start_date' => Carbon::now(),
            'end_date'   => Carbon::now()->addDays(3),
        ]);
        $this->assertFalse(Weekend::find($weekend->id)->has_ended);
    }

    /** @test */
    public function weekend_has_ended_attribute_tests_false_for_unstarted_weekend()
    {
        $weekend = Weekend::factory()->create([
            'start_date' => Carbon::now()->addDays(20),
            'end_date'   => Carbon::now()->addDays(23),
        ]);
        $this->assertFalse(Weekend::find($weekend->id)->has_ended);
    }

    /** @test */
    public function weekend_has_ended_attribute_tests_true_for_finished_weekend()
    {
        $weekend = Weekend::factory()->create([
            'start_date' => Carbon::now()->addDays(-20),
            'end_date'   => Carbon::now()->addDays(-17),
        ]);
        $this->assertTrue(Weekend::find($weekend->id)->has_ended);
    }

    /** @test */
    public function weekend_ended_over_a_month_ago_attribute_tests_true_for_two_months_ago()
    {
        $weekend = Weekend::factory()->create([
            'start_date' => Carbon::now()->addDays(-63),
            'end_date'   => Carbon::now()->addDays(-60),
        ]);
        $this->assertTrue(Weekend::find($weekend->id)->ended_over_a_month_ago);
    }

    /** @test */
    public function weekend_ended_over_a_month_ago_attribute_tests_false_for_three_days_ago()
    {
        $weekend = Weekend::factory()->create([
            'start_date' => Carbon::now()->addDays(-6),
            'end_date'   => Carbon::now()->addDays(-3),
        ]);
        $this->assertFalse(Weekend::find($weekend->id)->ended_over_a_month_ago);
    }

    /** @test */
    public function weekend_ended_over_a_month_ago_attribute_tests_false_for_future()
    {
        $weekend = Weekend::factory()->create([
            'start_date' => Carbon::now()->addDays(5),
            'end_date'   => Carbon::now()->addDays(8),
        ]);
        $this->assertFalse(Weekend::find($weekend->id)->ended_over_a_month_ago);
    }

    /** @test */
    public function weekend_ended_this_month_attribute_is_true_within_20_days()
    {
        $weekend = Weekend::factory()->create([
            'start_date' => Carbon::now()->addDays(-23),
            'end_date'   => Carbon::now()->addDays(-20),
        ]);
        $this->assertTrue(Weekend::find($weekend->id)->ended_this_month);
    }

    /** @test */
    public function weekend_ended_this_month_attribute_is_false_after_35_days()
    {
        $weekend = Weekend::factory()->create([
            'start_date' => Carbon::now()->addDays(-32),
            'end_date'   => Carbon::now()->addDays(-35),
        ]);
        $this->assertFalse(Weekend::find($weekend->id)->ended_this_month);
    }

    /** @test */
    public function weekend_ended_this_month_attribute_is_false_for_future()
    {
        $weekend = Weekend::factory()->create([
            'start_date' => Carbon::now()->addDays(45),
            'end_date'   => Carbon::now()->addDays(48),
        ]);
        $this->assertFalse(Weekend::find($weekend->id)->ended_this_month);
    }
}
