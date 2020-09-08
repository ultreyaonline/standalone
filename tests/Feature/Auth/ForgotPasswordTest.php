<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ForgotPasswordTest extends TestCase {
    use RefreshDatabase;

    protected function passwordRequestRoute() {
        return route('password.request');
    }

    protected function passwordEmailGetRoute() {
        return route('password.email');
    }

    protected function passwordEmailPostRoute() {
        return route('password.email');
    }

    /** @test */
    function user_can_view_an_email_password_form() {
        $response = $this->get($this->passwordRequestRoute());

        $response->assertSuccessful();
        $response->assertViewIs('auth.passwords.email');
    }

    /** @test */
    function user_receives_an_email_with_a_password_reset_link() {
        Notification::fake();
        $user = factory(User::class)->create([
            'email' => 'john@example.com',
            'username' => 'john@example.com',
        ]);

        $response = $this->post($this->passwordEmailPostRoute(), [
            'username' => 'john@example.com',
        ]);

        $this->assertNotNull($token = DB::table('password_resets')->first());
        Notification::assertSentTo($user, \Illuminate\Auth\Notifications\ResetPassword::class, function ($notification, $channels) use ($token) {
            return Hash::check($notification->token, $token->token) === true;
        });
    }

    /** @test */
    function user_does_not_receive_email_when_not_registered() {
        $this->expectException(ValidationException::class);
        Notification::fake();

        $response = $this->from($this->passwordEmailGetRoute())->post($this->passwordEmailPostRoute(), [
            'username' => 'nobody@example.com',
        ]);

        $response->assertRedirect($this->passwordEmailGetRoute());
        $response->assertSessionHasErrors('username');
        Notification::assertNotSentTo(factory(User::class)->make(['email' => 'nobody@example.com']), \Illuminate\Auth\Notifications\ResetPassword::class);
    }

    /** @test */
    function email_is_required() {
        $this->expectException(ValidationException::class);
        $response = $this->from($this->passwordEmailGetRoute())->post($this->passwordEmailPostRoute(), []);

        $response->assertRedirect($this->passwordEmailGetRoute());
        $response->assertSessionHasErrors('username');
    }

    /** @test */
    function email_is_a_valid_email() {
        $this->expectException(ValidationException::class);
        $response = $this->from($this->passwordEmailGetRoute())->post($this->passwordEmailPostRoute(), [
            'username' => 'invalid-email',
        ]);

        $response->assertRedirect($this->passwordEmailGetRoute());
        $response->assertSessionHasErrors('username');
    }
}
