<?php

namespace Tests\Feature\Videos;

use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideosTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_view_videos(): void
    {
        // Arrange
        $video = Video::create([
            'title' => 'Test Video',
            'description' => 'A test video',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'published_at' => now(),
        ]);

        // Act
        $response = $this->get(route('videos.show', $video));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('Test Video');
        $response->assertSee('A test video');
    }

    public function test_users_cannot_view_not_existing_videos(): void
    {
        // Act
        $response = $this->get(route('videos.show', 999));

        // Assert
        $response->assertStatus(404);
    }

    public function test_user_without_permissions_can_see_default_videos_page(): void
    {
        // Arrange
        $user = User::factory()->create();
        Video::create([
            'title' => 'Vídeo públic',
            'description' => 'Descripció del vídeo',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'published_at' => now(),
        ]);

        // Act
        $response = $this->actingAs($user)->get(route('videos.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('Vídeo públic');
    }

    public function test_user_with_permissions_can_see_default_videos_page(): void
    {
        // Arrange
        $user = User::factory()->create();
        $user->givePermission('videos_manage_index');

        Video::create([
            'title' => 'Vídeo visible',
            'description' => 'Descripció del vídeo',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'published_at' => now(),
        ]);

        // Act
        $response = $this->actingAs($user)->get(route('videos.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('Vídeo visible');
    }

    public function test_not_logged_users_can_see_default_videos_page(): void
    {
        // Arrange
        Video::create([
            'title' => 'Vídeo sense login',
            'description' => 'Descripció del vídeo',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'published_at' => now(),
        ]);

        // Act
        $response = $this->get(route('videos.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('Vídeo sense login');
    }
}
