<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_return_a_unique_hashid_attribute()
    {
        $user = \App\Models\User::factory()->create(['uidhash' => null]);

        $this->assertNotNull($user->uidhash);
    }
}
