<?php

namespace Tests\Feature\Api;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewMemberTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function requesting_an_invalid_user_triggers_model_not_found_exception()
    {
        $this->withoutExceptionHandling();
        try {
            $this->json('GET', '/api/users/123');
        } catch (ModelNotFoundException $exception) {
            $this->assertEquals('No query results for model [App\User] 123', $exception->getMessage());
            return;
        }

        $this->fail('ModelNotFoundException should be triggered.');
    }

    /** @test */
    public function requesting_an_invalid_user_returns_no_query_results_error()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $response = $this->json('GET', '/api/users/123');
        $response->assertStatus(404);
        $response->assertHeader('Content-Type', 'application/json');

        // NOTE: Would need to change this assertion if we customize the App\Exception\Handler to return the fallback route when ModelNotFoundException is thrown
        $response->assertJson([
            'message' => 'No query results for model [App\User] 123'
        ]);
    }

    /** @test */
    public function invalid_user_uri_triggers_fallback_route()
    {
        $response = $this->json('GET', '/api/users/invalid-user-id');
        $response->assertStatus(404);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertJson([
            'message' => 'Not Found.'
        ]);
    }
}
