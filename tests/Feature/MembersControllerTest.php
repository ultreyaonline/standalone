<?php

namespace Tests\Feature;

use App\Models\User;
use DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembersControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->registerPermissions();

        $this->admin = factory(\App\Models\User::class)->states('active')
            ->create(['first' => 'admin', 'last' => 'user'])
            ->assignRole('Admin');
    }

    /** @test */
    public function a_new_member_can_be_created_when_required_validations_pass()
    {
        factory(User::class)->states('active')->create(['first' => 'foo', 'last' => 'bar']); // generic

        $response = $this->actingAs($this->admin)
//            ->withoutExceptionHandling()
            ->post(action('MembersController@store'), $attributes = [
                'first' => 'valid',
                'last' => 'testMemberFound',
                'email' => 'valid@example.com',
                'username' => 'ValidExample',
                'gender' => 'M',
            ]);
        $member = User::where($attributes)->first();

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/members/' . $member->id);
    }

    /** @test */
    public function a_member_with_no_candidate_record_can_be_deleted()
    {
        $user = factory(\App\Models\User::class)->states('male')->create();

        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('candidates', ['m_user_id' => $user->id]);

        $this->actingAs($this->admin)
            ->delete(action('MembersController@destroy', ['memberID' => $user->id]));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

}
