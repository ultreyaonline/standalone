<?php

namespace Tests\Feature;

use Illuminate\Auth\AuthenticationException;
use Tests\TestCase;
use PHPUnit\Framework\Assert as PHPUnit;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MembersOnlyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_requires_login_for_members_area()
    {
        $response = $this->withExceptionHandling()
            ->get('/profile');

        $response->assertRedirect('login');
        PhpUnit::assertTrue(auth()->guest());
    }

    /** @test */
    public function members_must_login()
    {
//        $this->expectException(AuthenticationException::class);
        $this->withoutExceptionHandling();
        try {
            $this->get('/profile');
        } catch (\Exception $e) {
            $this->assertInstanceOf(AuthenticationException::class, $e);
        }
    }

    /** @test */
    public function nonmembers_cannot_see_restricted_content()
    {
        $this->seed();
        $this->clearPermissionsCache();

        $response = $this->withoutExceptionHandling()
            ->signInAsGuest()
            ->get('/home');

        $response->assertStatus(200);
        $response->assertDontSee('Community Directory');
    }

    /** @test */
    public function members_can_see_restricted_content()
    {
        $this->seed();
        $this->clearPermissionsCache();

        $response = $this->withoutExceptionHandling()
            ->signIn()
            ->get('/home');

        $response->assertStatus(200);
        $response->assertSee('Community Directory');
        $response->assertSee(e($this->user->name));
        $response->assertSee('Palanca');
    }

    /** @test */
    public function members_can_see_vocabulary()
    {
        $this->seed();
        $this->clearPermissionsCache();

        $response = $this->withoutExceptionHandling()
            ->signIn()
            ->get('/vocabulary');

        $response->assertStatus(200);
        $response->assertSee('ABRAZO');
    }

    /** @test */
    public function members_can_see_profile_page()
    {
        $this->seed();
        $user = create(\App\User::class, ['first'=>'Bob', 'last'=>'Smith','email'=>'bobby@example.com']);
        $response = $this->signIn($user)
            ->get('/members/'.$user->id);
        $response->assertSee('Bob Smith')
            ->assertSee('bobby@example.com');
    }

    /** @test */
    public function it_can_show_secretariat_page()
    {
        $this->seed();
        $response = $this->signIn()
            ->get('/secretariat');

        $response->assertSee('Secretariat')
            ->assertSee('Community');
    }
}
