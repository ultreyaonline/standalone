<?php

namespace Tests\Feature;

use App\Enums\WeekendVisibleTo;
use App\User;
use App\Weekend;
use App\TeamFeePayments;
use App\WeekendAssignments;
use DatabaseSeeder;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeamFeePaymentsTest extends TestCase
{
    use RefreshDatabase;

    protected $member_attributes, $member, $weekend;
    protected $treasurer_user;
    protected $simple_payment_attributes = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);

        // now re-register all the roles and permissions
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->registerPermissions();

        $this->member_attributes = [
            'email'    => 'john@example.com',
            'username' => 'john44@example.com',
            'active'   => true,
        ];

        $rector = factory(\App\User::class)->states('male')->create();
        $rector->assignRole('Member');

        // make a weekend
        $this->weekend = factory(Weekend::class)->create([
            'weekend_MF' => 'M',
            'visibility_flag' => WeekendVisibleTo::Community, 
            'rectorID' => $rector->id
        ]);
        WeekendAssignments::create([
            'weekendID' => $this->weekend->id,
            'roleID' => 1,
            'memberID' => $rector->id,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        // make member for testing
        $this->member = factory(User::class)->states('male')->create($this->member_attributes);
        $this->member->assignRole('Member');

        // assign to the weekend
        factory(WeekendAssignments::class)->create([
            'weekendID' => $this->weekend->id,
            'memberID'  => $this->member->id,
            'roleID'    => 10,
            'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
        ]);

        // get fresh copy, with updated relations
        $this->weekend = $this->weekend->fresh();

        // make a user who is authorized to access/record payments
        $this->treasurer_user = create(User::class, ['active' => true]);
        $this->treasurer_user->assignRole('Member');
//        $this->treasurer_user->assignRole('Treasurer');
        $this->treasurer_user->givePermissionTo('record team fees paid');

        // payment for testing
        $this->simple_payment_attributes = [
            'memberID'   => $this->member->id,
            'total_paid' => 200,
            'date_paid'  => '2018-02-02',
            'complete'   => 1,
            'comments'   => 'e-transfer',
        ];
    }

    /** @test */
    public function it_can_display_a_list_of_team_assignments()
    {
        $url = route('list_team_fees', $this->weekend->id);

        $response = $this->actingAs($this->treasurer_user)
            ->get($url);

        $response->assertSee($this->member->first);
        $response->assertSee($this->member->last);
    }

    /** @test */
    public function a_payment_can_be_recorded()
    {
        Mail::fake();

        // submit a payment
        $url = route('record_team_fee_payment', $this->weekend->id);

        // @TODO -- add tests for Head Cha and Asst Head Cha, instead of just TreasurerUser
// @TODO: test that emails are sent

        $response = $this->actingAs($this->treasurer_user)
            ->post($url, $this->simple_payment_attributes);

        // and verify it contains the submitted information
        tap(TeamFeePayments::first(), function ($payment) use ($response, $url) {
            $response->assertRedirect($url);

            $this->assertEquals($payment->memberID, $this->simple_payment_attributes['memberID']);
            $this->assertEquals($payment->total_paid, $this->simple_payment_attributes['total_paid']);
            $this->assertEquals($payment->date_paid, $this->simple_payment_attributes['date_paid']);
            $this->assertEquals($payment->complete, (bool)$this->simple_payment_attributes['complete']);
            $this->assertEquals($payment->comments, $this->simple_payment_attributes['comments']);
            $this->assertEquals($payment->recorded_by, $this->treasurer_user->name);
        });

// @TODO - use Dusk to test that the following happens. Can't do it in here since the Redirect is done with metatags.
//        // and results are seen on-screen
//        $response->assertSee($this->member->name);
//        $response->assertSee($this->simple_payment_attributes['total_paid']);
//        $response->assertSee($this->simple_payment_attributes['comments']);
//        $response->assertSee($this->treasurer_user->name);
    }
}
