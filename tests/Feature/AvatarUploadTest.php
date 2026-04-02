<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
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
        $this->seed();
    }

    #[Test]
    public function only_members_can_add_avatars()
    {
        $this->withoutExceptionHandling();
        $this->expectException(\Illuminate\Auth\AuthenticationException::class);

        $this->patch('/members/1111/updateavatar')
            ->assertRedirect();
    }

    #[Test]
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

    #[Test]
    public function a_user_may_add_an_avatar_to_their_profile()
    {
        // bypass the queue for this test to ensure the conversions are generated immediately, otherwise we can't assert against them.
        config()->set('media-library.queue_conversions_by_default', false);

        $this->withoutExceptionHandling()->signIn();
        $user = auth()->user();
        $user->avatar = null;
        $user->save();

        Storage::fake('public');

        $file = UploadedFile::fake()->image('avatar.png');

        $this->patch('/members/' . $user->id . '/updateavatar', [
            'avatar' => $file,
        ]);

        $avatarUrl = (string) auth()->user()->fresh()->avatar;

        $avatarPath = (string) (parse_url($avatarUrl, PHP_URL_PATH) ?? $avatarUrl);

        $this->assertStringStartsWith('/storage/', $avatarPath);
        $this->assertStringEndsWith('/conversions/avatar-avatar.jpg', $avatarPath);

        Storage::disk('public')->assertExists(ltrim(str_replace('/storage/', '', $avatarPath), '/'));

        // ensure the media records were created too.
        $user = $user->fresh();
        $media = $user->getFirstMedia('avatar');

        $this->assertNotNull($media);
        $this->assertTrue($media->hasGeneratedConversion('avatar'));

        $this->assertDatabaseHas('media', [
            'model_type' => get_class($user),
            'model_id' => $user->id,
            'collection_name' => 'avatar',
        ]);

        Storage::disk($media->disk)->assertExists(
            $media->getPathRelativeToRoot('avatar')
        );
    }

    // @TODO - add test to catch exif_read_data(9yq27ctykSTPdSVx49xYCAsFzEOsySmXjEZFkRMw.jpeg): Incorrect APP1 Exif Identifier Code
}
