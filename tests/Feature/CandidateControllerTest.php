<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CandidateControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->admin = \App\Models\User::factory()->active()
            ->create(['first' => 'admin', 'last' => 'user'])
            ->assignRole('Admin');

    }

    /**
     * Enforce cleanup
     * Foreign keys will set things to null, but need to manually clean up the empty null record (where both spouses are now null)
     *
     * @test
     */
    public function deleting_single_candidate_as_member_also_cleans_up_empty_records()
    {
        // A. Single, deleted via Member Delete

        $candidate = \App\Models\Candidate::factory()->male()->create();

        $this->assertDatabaseHas('users', ['id' => $candidate->m_user_id]);
        $this->assertDatabaseHas('candidates', ['m_user_id' => $candidate->m_user_id]);

        $this->signIn($this->admin)
            ->delete(action('App\Http\Controllers\MembersController@destroy', ['memberID' => $candidate->m_user_id]));

        $this->assertDatabaseMissing('users', ['id' => $candidate->m_user_id]);
        $this->assertDatabaseMissing('candidates', ['m_user_id' => $candidate->m_user_id]);

    }

    public function deleting_single_candidate_also_cleans_up_empty_records()
    {
        // B. Single, deleted via Candidate Delete

        $candidate = \App\Models\Candidate::factory()->male()->create();

        $this->assertDatabaseHas('users', ['id' => $candidate->m_user_id]);
        $this->assertDatabaseHas('candidates', ['m_user_id' => $candidate->m_user_id]);

        $this->signIn($this->admin)
            ->delete(action('App\Http\Controllers\CandidateController@destroy', ['candidate' => $candidate->id]));

        $this->assertDatabaseMissing('users', ['id' => $candidate->m_user_id]);
        $this->assertDatabaseMissing('candidates', ['m_user_id' => $candidate->m_user_id]);

    }

    public function deleting_candidate_couple_also_cleans_up_empty_records()
    {
        // C. Couple - deleted as a pair via Candidate Delete

        $candidate = \App\Models\Candidate::factory()->couple()->create();

        $this->assertDatabaseHas('users', ['id' => $candidate->m_user_id]);
        $this->assertDatabaseHas('users', ['id' => $candidate->w_user_id]);
        $this->assertDatabaseHas('candidates', ['m_user_id' => $candidate->m_user_id]);
        $this->assertDatabaseHas('candidates', ['w_user_id' => $candidate->w_user_id]);
        $this->assertDatabaseHas('candidates', ['id' => $candidate->id]);

        $this->signIn($this->admin)
            ->delete(action('App\Http\Controllers\CandidateController@destroy', ['candidate' => $candidate->id]));

        $this->assertDatabaseMissing('users', ['id' => $candidate->m_user_id]);
        $this->assertDatabaseMissing('users', ['id' => $candidate->w_user_id]);
        $this->assertDatabaseMissing('candidates', ['m_user_id' => $candidate->m_user_id]);
        $this->assertDatabaseMissing('candidates', ['w_user_id' => $candidate->w_user_id]);
        $this->assertDatabaseMissing('candidates', ['id' => $candidate->id]);

    }

    public function deleting_candidate_couple_via_member_also_cleans_up_empty_records()
    {
        // D. Couple - deleted individually via Member Delete

        $candidate = \App\Models\Candidate::factory()->couple()->create();

        $this->assertDatabaseHas('users', ['id' => $candidate->m_user_id]);
        $this->assertDatabaseHas('users', ['id' => $candidate->w_user_id]);
        $this->assertDatabaseHas('candidates', ['m_user_id' => $candidate->m_user_id]);
        $this->assertDatabaseHas('candidates', ['w_user_id' => $candidate->w_user_id]);
        $this->assertDatabaseHas('candidates', ['id' => $candidate->id]);

        // delete man only
        $this->signIn($this->admin)
            ->delete(action('App\Http\Controllers\MembersController@destroy', ['memberID' => $candidate->m_user_id]));

        $this->assertDatabaseMissing('users', ['id' => $candidate->m_user_id]);
        $this->assertDatabaseHas('users', ['id' => $candidate->w_user_id]);
        $this->assertDatabaseMissing('candidates', ['m_user_id' => $candidate->m_user_id]);
        $this->assertDatabaseHas('candidates', ['w_user_id' => $candidate->w_user_id]);
        $this->assertDatabaseHas('candidates', ['id' => $candidate->id]);

        // and now delete woman too
        $this->signIn($this->admin)
            ->delete(action('App\Http\Controllers\MembersController@destroy', ['memberID' => $candidate->w_user_id]));

        $this->assertDatabaseMissing('users', ['id' => $candidate->m_user_id]);
        $this->assertDatabaseMissing('users', ['id' => $candidate->w_user_id]);
        $this->assertDatabaseMissing('candidates', ['m_user_id' => $candidate->m_user_id]);
        $this->assertDatabaseMissing('candidates', ['w_user_id' => $candidate->w_user_id]);
        $this->assertDatabaseMissing('candidates', ['id' => $candidate->id]);
    }
}
