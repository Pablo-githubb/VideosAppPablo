<?php

namespace Tests\Feature\Videos;

use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideosManageControllerTest extends TestCase
{
    use RefreshDatabase;

    private function loginAsVideoManager(): User
    {
        $user = User::factory()->create();
        $user->givePermission('videos_manage_index');
        $user->givePermission('videos_manage_create');
        $user->givePermission('videos_manage_store');
        $user->givePermission('videos_manage_edit');
        $user->givePermission('videos_manage_update');
        $user->givePermission('videos_manage_delete');
        $user->givePermission('videos_manage_destroy');
        $this->actingAs($user);

        return $user;
    }

    private function loginAsSuperAdmin(): User
    {
        $user = User::factory()->create(['super_admin' => true]);
        $this->actingAs($user);

        return $user;
    }

    private function loginAsRegularUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    private function createSampleVideo(): Video
    {
        return Video::create([
            'title' => 'Test Video',
            'description' => 'A test video description',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'published_at' => now(),
        ]);
    }

    // --- MANAGE INDEX ---

    public function test_user_with_permissions_can_manage_videos(): void
    {
        // Arrange
        $this->loginAsVideoManager();
        Video::create([
            'title' => 'Vídeo 1',
            'description' => 'Primer vídeo',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'published_at' => now(),
        ]);
        Video::create([
            'title' => 'Vídeo 2',
            'description' => 'Segon vídeo',
            'url' => 'https://www.youtube.com/watch?v=9bZkp7q19f0',
            'published_at' => now(),
        ]);
        Video::create([
            'title' => 'Vídeo 3',
            'description' => 'Tercer vídeo',
            'url' => 'https://www.youtube.com/watch?v=JGwWNGJdvx8',
            'published_at' => now(),
        ]);

        // Act
        $response = $this->get(route('videos.manage.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('Vídeo 1');
        $response->assertSee('Vídeo 2');
        $response->assertSee('Vídeo 3');
    }

    public function test_regular_users_cannot_manage_videos(): void
    {
        $this->loginAsRegularUser();

        $response = $this->get(route('videos.manage.index'));

        $response->assertStatus(403);
    }

    public function test_guest_users_cannot_manage_videos(): void
    {
        $response = $this->get(route('videos.manage.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_superadmins_can_manage_videos(): void
    {
        $this->loginAsSuperAdmin();
        $video = $this->createSampleVideo();

        $response = $this->get(route('videos.manage.index'));

        $response->assertStatus(200);
        $response->assertSee($video->title);
    }

    // --- CREATE / STORE ---

    public function test_user_with_permissions_can_see_add_videos(): void
    {
        $this->loginAsVideoManager();

        $response = $this->get(route('videos.manage.create'));

        $response->assertStatus(200);
        $response->assertSee('Afegir Vídeo');
    }

    public function test_user_without_videos_manage_create_cannot_see_add_videos(): void
    {
        $this->loginAsRegularUser();

        $response = $this->get(route('videos.manage.create'));

        $response->assertStatus(403);
    }

    public function test_user_with_permissions_can_store_videos(): void
    {
        $this->loginAsVideoManager();

        $response = $this->post(route('videos.manage.store'), [
            'title' => 'Nou Vídeo',
            'description' => 'Descripció del nou vídeo',
            'url' => 'https://www.youtube.com/watch?v=abc123',
        ]);

        $response->assertRedirect(route('videos.manage.index'));
        $this->assertDatabaseHas('videos', [
            'title' => 'Nou Vídeo',
            'description' => 'Descripció del nou vídeo',
        ]);
    }

    public function test_user_without_permissions_cannot_store_videos(): void
    {
        $this->loginAsRegularUser();

        $response = $this->post(route('videos.manage.store'), [
            'title' => 'Vídeo no permès',
            'description' => 'Descripció',
            'url' => 'https://www.youtube.com/watch?v=abc123',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('videos', ['title' => 'Vídeo no permès']);
    }

    // --- EDIT / UPDATE ---

    public function test_user_with_permissions_can_see_edit_videos(): void
    {
        $this->loginAsVideoManager();
        $video = $this->createSampleVideo();

        $response = $this->get(route('videos.manage.edit', $video->id));

        $response->assertStatus(200);
        $response->assertSee('Editar Vídeo');
        $response->assertSee($video->title);
    }

    public function test_user_without_permissions_cannot_see_edit_videos(): void
    {
        $this->loginAsRegularUser();
        $video = $this->createSampleVideo();

        $response = $this->get(route('videos.manage.edit', $video->id));

        $response->assertStatus(403);
    }

    public function test_user_with_permissions_can_update_videos(): void
    {
        $this->loginAsVideoManager();
        $video = $this->createSampleVideo();

        $response = $this->put(route('videos.manage.update', $video->id), [
            'title' => 'Títol Actualitzat',
            'description' => 'Descripció actualitzada',
            'url' => 'https://www.youtube.com/watch?v=updated',
        ]);

        $response->assertRedirect(route('videos.manage.index'));
        $this->assertDatabaseHas('videos', [
            'id' => $video->id,
            'title' => 'Títol Actualitzat',
        ]);
    }

    public function test_user_without_permissions_cannot_update_videos(): void
    {
        $this->loginAsRegularUser();
        $video = $this->createSampleVideo();

        $response = $this->put(route('videos.manage.update', $video->id), [
            'title' => 'Intent no permès',
            'description' => 'Descripció',
            'url' => 'https://www.youtube.com/watch?v=nope',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('videos', ['title' => 'Intent no permès']);
    }

    // --- DELETE / DESTROY ---

    public function test_user_with_permissions_can_destroy_videos(): void
    {
        $this->loginAsVideoManager();
        $video = $this->createSampleVideo();

        $response = $this->delete(route('videos.manage.destroy', $video->id));

        $response->assertRedirect(route('videos.manage.index'));
        $this->assertDatabaseMissing('videos', ['id' => $video->id]);
    }

    public function test_user_without_permissions_cannot_destroy_videos(): void
    {
        $this->loginAsRegularUser();
        $video = $this->createSampleVideo();

        $response = $this->delete(route('videos.manage.destroy', $video->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('videos', ['id' => $video->id]);
    }
}
