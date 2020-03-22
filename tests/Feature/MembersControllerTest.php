<?php

namespace Tests\Feature;

use App\User;
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
    }

    /** @test */
    public function a_new_member_can_be_created_when_required_validations_pass()
    {
        $admin = factory(User::class)->states('active')
            ->create(['first' => 'admin', 'last' => 'user'])
            ->assignRole('Admin');

        factory(User::class)->states('active')->create(['first' => 'foo', 'last' => 'bar']); // generic

        $response = $this->actingAs($admin)
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
}
