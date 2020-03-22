<?php

namespace Tests\Feature;

use DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CandidateControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->registerPermissions();

        $this->admin = factory(\App\User::class)->states('active')
            ->create(['first' => 'admin', 'last' => 'user'])
            ->assignRole('Admin');

    }

    /**
     * Enforce cleanup
     * Foreign keys will set things to null, but need to manually clean up the empty null record (where both spouses are now null)
     *
     * @test
     */
    public function deleting_candidates_also_cleans_up_empty_records()
    {
        // A. Single, deleted via Member Delete

        $candidate = factory(\App\Candidate::class)->states('male')->create();

        $this->assertDatabaseHas('users', ['id' => $candidate->m_user_id]);
        $this->assertDatabaseHas('candidates', ['m_user_id' => $candidate->m_user_id]);

        $this->actingAs($this->admin)
            ->delete(action('MembersController@destroy', ['memberID' => $candidate->m_user_id]));

        $this->assertDatabaseMissing('users', ['id' => $candidate->m_user_id]);
        $this->assertDatabaseMissing('candidates', ['m_user_id' => $candidate->m_user_id]);


        // B. Single, deleted via Candidate Delete

        $candidate = factory(\App\Candidate::class)->states('male')->create();

        $this->assertDatabaseHas('users', ['id' => $candidate->m_user_id]);
        $this->assertDatabaseHas('candidates', ['m_user_id' => $candidate->m_user_id]);

        $this->actingAs($this->admin)
            ->delete(action('CandidateController@destroy', ['candidate' => $candidate->id]));

        $this->assertDatabaseMissing('users', ['id' => $candidate->m_user_id]);
        $this->assertDatabaseMissing('candidates', ['m_user_id' => $candidate->m_user_id]);


        // C. Couple - deleted as a pair via Candidate Delete

        $candidate = factory(\App\Candidate::class)->states('couple')->create();

        $this->assertDatabaseHas('users', ['id' => $candidate->m_user_id]);
        $this->assertDatabaseHas('users', ['id' => $candidate->w_user_id]);
        $this->assertDatabaseHas('candidates', ['m_user_id' => $candidate->m_user_id]);
        $this->assertDatabaseHas('candidates', ['w_user_id' => $candidate->w_user_id]);
        $this->assertDatabaseHas('candidates', ['id' => $candidate->id]);

        $this->actingAs($this->admin)
            ->delete(action('CandidateController@destroy', ['candidate' => $candidate->id]));

        $this->assertDatabaseMissing('users', ['id' => $candidate->m_user_id]);
        $this->assertDatabaseMissing('users', ['id' => $candidate->w_user_id]);
        $this->assertDatabaseMissing('candidates', ['m_user_id' => $candidate->m_user_id]);
        $this->assertDatabaseMissing('candidates', ['w_user_id' => $candidate->w_user_id]);
        $this->assertDatabaseMissing('candidates', ['id' => $candidate->id]);


        // D. Couple - deleted individually via Member Delete

        $candidate = factory(\App\Candidate::class)->states('couple')->create();

        $this->assertDatabaseHas('users', ['id' => $candidate->m_user_id]);
        $this->assertDatabaseHas('users', ['id' => $candidate->w_user_id]);
        $this->assertDatabaseHas('candidates', ['m_user_id' => $candidate->m_user_id]);
        $this->assertDatabaseHas('candidates', ['w_user_id' => $candidate->w_user_id]);
        $this->assertDatabaseHas('candidates', ['id' => $candidate->id]);

        // delete man only
        $this->actingAs($this->admin)
            ->delete(action('MembersController@destroy', ['memberID' => $candidate->m_user_id]));

        $this->assertDatabaseMissing('users', ['id' => $candidate->m_user_id]);
        $this->assertDatabaseHas('users', ['id' => $candidate->w_user_id]);
        $this->assertDatabaseMissing('candidates', ['m_user_id' => $candidate->m_user_id]);
        $this->assertDatabaseHas('candidates', ['w_user_id' => $candidate->w_user_id]);
        $this->assertDatabaseHas('candidates', ['id' => $candidate->id]);

        // and now delete woman too
        $this->actingAs($this->admin)
            ->delete(action('MembersController@destroy', ['memberID' => $candidate->w_user_id]));

        $this->assertDatabaseMissing('users', ['id' => $candidate->m_user_id]);
        $this->assertDatabaseMissing('users', ['id' => $candidate->w_user_id]);
        $this->assertDatabaseMissing('candidates', ['m_user_id' => $candidate->m_user_id]);
        $this->assertDatabaseMissing('candidates', ['w_user_id' => $candidate->w_user_id]);
        $this->assertDatabaseMissing('candidates', ['id' => $candidate->id]);
    }
}
