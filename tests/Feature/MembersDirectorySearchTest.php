<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class MembersDirectorySearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function membersdirectory_table_is_unreachable_if_not_a_member()
    {
        User::factory()->active()->create(['first' => 'foo', 'last' => 'bar']);
        User::factory()->active()->create(['first' => 'fizz', 'last' => 'buzz']);

        $this->expectException(HttpException::class);

        Livewire::test('members-directory')
            ->assertDontSee('fizz');
    }

    /** @test */
    function membersdirectory_table_is_searchable_by_members()
    {
        $this->seed();

        User::factory()->active()->create(['first' => 'foo', 'last' => 'bar']);
        User::factory()->active()->create(['first' => 'fizz', 'last' => 'buzz']);
        $user = User::factory()->active()
            ->create(['first' => 'valid', 'last' => 'communitymember']);
        $user->assignRole('Member');

        Livewire::actingAs($user)
            ->test('members-directory')
            ->assertSee('foo')
            ->assertSee('fizz')
            ->set('q', 'foo')
            ->assertSee('foo')
            ->assertDontSee('fizz');
    }



    /** @test */
    function membersaudit_table_is_unreachable_if_not_authorized()
    {
        $this->seed();

        User::factory()->active()->create(['first' => 'fizz', 'last' => 'buzz']);

        $this->expectException(HttpException::class);

        $user = User::factory()->active()
            ->create(['first' => 'valid', 'last' => 'communitymember']);
        $user->assignRole('Member');

        Livewire::actingAs($user)
            ->test('members-audit')
            ->assertDontSee('fizz');
    }

    /** @test */
    function membersaudit_table_is_reachable_if_authorized()
    {
        $this->seed();

        User::factory()->active()->create(['first' => 'foo', 'last' => 'bar']);
        User::factory()->active()->create(['first' => 'fizz', 'last' => 'buzz']);

        $user = User::factory()->active()
            ->create(['first' => 'valid', 'last' => 'communitymember']);
        $user->assignRole('Member');
        $user->givePermissionTo('edit members');

        Livewire::actingAs($user)
            ->test('members-audit')
            ->assertSee('foo')
            ->assertSee('fizz');
    }
}
