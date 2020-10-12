<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AvatarUploadTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /** @ test */
    public function only_members_can_add_avatars()
    {
        $this->withoutExceptionHandling();
        $this->expectException(\Illuminate\Auth\AuthenticationException::class);

        $this->patch('/members/1111/updateavatar')
            ->assertRedirect();
    }

    /** @ test */
    public function a_valid_avatar_must_be_provided()
    {
        $this->withoutExceptionHandling();
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->signIn();

        $this->patch('/members/' . $this->user->id . '/updateavatar', [
            'avatar' => 'not_an_image',
        ])
            ->assertRedirect();
    }

    /** @ test */
    public function a_user_may_add_an_avatar_to_their_profile()
    {
        $this->withoutExceptionHandling()->signIn();
        $user = auth()->user();
        $user->avatar = null;
        $user->save();

        Storage::fake('local');

        $file = UploadedFile::fake()->image('avatar.png');

        $this->patch('/members/' . $user->id . '/updateavatar', [
            'avatar' => $file,
        ]);

        $this->assertEquals(url('avatars/' . $file->hashName()), auth()->user()->fresh()->avatar);

        Storage::disk('local')->assertExists('avatars/' . $file->hashName());
    }

    // @TODO - add test to catch exif_read_data(9yq27ctykSTPdSVx49xYCAsFzEOsySmXjEZFkRMw.jpeg): Incorrect APP1 Exif Identifier Code
}
