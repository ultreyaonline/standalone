<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InitialTest extends TestCase
{
//    /**
//     * For testing Validation rules against Endpoints
//     *
//     * @param $field
//     */
//    private function assertValidationError($field)
//    {
//        $this->assertResponseStatus(422);
//        $this->assertArrayHasKey($field, $this->decodeResponseJson());
//    }

// @TODO - note this - example way to check test dependencies

//    protected function setUp(): void
//    {
//        if (empty(getenv('ENV_VARIABLE'))) {
//            $this->markTestSkipped('ENV_VARIABLE not configured for running integration tests.');
//        }
//    }

    /**
     * A basic Feature test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
