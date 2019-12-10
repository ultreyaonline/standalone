<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WebsiteInstructionsEmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_login_username_is_included_in_the_email()
    {
        $user = factory(\App\User::class)->create();

        $email = new \App\Mail\WebsiteLoginInstructions($user);
        $html = $email->render();

        $this->assertStringContainsString('Your present login username is: ' . $user->username, $html);
    }
}
