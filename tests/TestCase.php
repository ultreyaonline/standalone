<?php

namespace Tests;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();

        Notification::fake();

        // disable Events (since they send a bunch of emails)
        // @TODO -- add tests for these, and maybe disable based on environment?
        Event::fake(
            [
                \App\Events\CandidateDeleted::class,
                \App\Events\UserAdded::class,
                \App\Events\UserDeleted::class
            ]);

        // note: permissions cache is reset in the roles/permissions seeder

        $this->duringSetup();
    }

    protected function duringSetup()
    {
        //
    }

    protected function withoutVerifyCSRFMiddleware()
    {
        return $this->withoutMiddleware(VerifyCsrfToken::class);
    }



    protected function signInAsGuest($user = null)
    {
        $this->user = $user ?: $this->user ?: \App\Models\User::factory()->create();
        $this->actingAs($this->user);

        return $this;
    }

    protected function signIn($user = null)
    {
        $this->user = $user ?: $this->user ?: \App\Models\User::factory()->create();
        $this->user->assignRole('Member');
        $this->actingAs($this->user);
        $this->session(['auth.password_confirmed_at' => time()]);

        return $this;
    }

    private function fromUrl($url)
    {
        session()->setPreviousUrl(url($url));
        return $this;
    }
}
