<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class WhosOnlineTest extends TestCase
{
    use RefreshDatabase;


        // @TODO -- need to switch to taggable cache so this can be properly tested, and cache resets done without destroying production
        // @TODO -- ideally be able to override cache key for testing purposes

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->clearPermissionsCache();
        Cache::forget('user-is-online-1');
        Cache::forget('user-is-online-2');
        Cache::forget('user-is-online-3');
        Cache::forget('user-is-online-4');
    }

    /** @test */
    public function unloggedin_users_should_not_show_online()
    {
        $user = \App\Models\User::factory()->create(['first'=>'Bob', 'last'=>'Smith','email'=>'bobby@example.com']);
        $this->assertFalse($user->isOnline());
    }

    /** @test */
    public function login_makes_so_the_cache_key_can_be_retrieved_and_user_is_marked_as_online()
    {
        $response = $this->withoutExceptionHandling()
            ->signIn()
            ->get('/home');

        $this->assertEquals('user-is-online-1', $this->user->getWhosOnlineKey());
        $this->assertTrue($this->user->isOnline());
    }

    /** @test */
    public function should_clear_cache_when_user_does_logout()
    {
        $response = $this->withoutExceptionHandling()
            ->signIn()
            ->post(route('logout'));

        $response->assertRedirect('/login');
        $this->assertGuest();
        $this->assertFalse($this->user->isOnline());
    }

    // @TODO - test the WhosOnlineServiceProvider view-composer
}
