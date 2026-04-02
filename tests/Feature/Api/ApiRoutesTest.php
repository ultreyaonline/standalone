<?php

namespace Tests\Feature\Api;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiRoutesTest extends TestCase
{
    #[Test]
    public function missing_api_routes_should_return_a_404()
    {
        $response = $this->get('/api/missing/route');

        $response->assertStatus(404);
// @TODO - force json
//        $response->assertHeader('Content-Type', 'application/json');
//        $response->assertJson([
//            'message' => 'Not Found.'
//        ]);
    }
}
