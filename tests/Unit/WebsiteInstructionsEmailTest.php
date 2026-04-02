<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WebsiteInstructionsEmailTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_login_username_is_included_in_the_email()
    {
        $user = \App\Models\User::factory()->create();

        $email = new \App\Mail\WebsiteLoginInstructions($user);
        $html = $email->render();

        $this->assertStringContainsString('Your present login username is: ' . $user->username, $html);
    }
}
