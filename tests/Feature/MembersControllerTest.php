<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembersControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->admin = User::factory()->active()
            ->create(['first' => 'admin', 'last' => 'user'])
            ->assignRole('Admin');
    }

    /** @test */
    public function a_new_member_can_be_created_when_required_validations_pass()
    {
        User::factory()->active()->create(['first' => 'foo', 'last' => 'bar']); // generic

        $response = $this->signIn($this->admin)
//            ->withoutExceptionHandling()
            ->post(action('App\Http\Controllers\MembersController@store'), $attributes = [
                'first' => 'valid',
                'last' => 'testMemberFound',
                'email' => 'valid@example.com',
                'username' => 'ValidExample',
                'gender' => 'M',
            ]);
        $member = User::firstWhere($attributes);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/members/' . $member->id);
    }

    /** @test */
    public function a_member_with_no_candidate_record_can_be_deleted()
    {
        $user = User::factory()->male()->create();

        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('candidates', ['m_user_id' => $user->id]);

        $this->signIn($this->admin)
            ->delete(action('App\Http\Controllers\MembersController@destroy', ['memberID' => $user->id]));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

}
