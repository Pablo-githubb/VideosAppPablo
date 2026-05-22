<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersManageControllerTest extends TestCase
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

    public function test_user_with_permissions_can_see_add_users(): void
    {
        $user = User::factory()->create();
        $user->givePermission('users_manage_create');
        $this->actingAs($user);

        $response = $this->get(route('users.manage.create'));

        $response->assertStatus(200);
    }

    public function test_user_without_users_manage_create_cannot_see_add_users(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('users.manage.create'));

        $response->assertStatus(403);
    }

    public function test_user_with_permissions_can_store_users(): void
    {
        $user = User::factory()->create();
        $user->givePermission('users_manage_store');
        $this->actingAs($user);

        $response = $this->post(route('users.manage.store'), [
            'name' => 'Nou Usuari',
            'email' => 'nouusuari@iesebre.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('users.manage.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'Nou Usuari',
            'email' => 'nouusuari@iesebre.com',
        ]);
    }

    public function test_user_without_permissions_cannot_store_users(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('users.manage.store'), [
            'name' => 'Nou Usuari 2',
            'email' => 'nouusuari2@iesebre.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('users', [
            'email' => 'nouusuari2@iesebre.com',
        ]);
    }

    public function test_user_with_permissions_can_destroy_users(): void
    {
        $user = User::factory()->create();
        $user->givePermission('users_manage_destroy');
        $this->actingAs($user);

        $targetUser = User::factory()->create();

        $response = $this->delete(route('users.manage.destroy', $targetUser->id));

        $response->assertRedirect(route('users.manage.index'));
        $this->assertDatabaseMissing('users', [
            'id' => $targetUser->id,
        ]);
    }

    public function test_user_without_permissions_cannot_destroy_users(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $targetUser = User::factory()->create();

        $response = $this->delete(route('users.manage.destroy', $targetUser->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
        ]);
    }

    public function test_user_with_permissions_can_see_edit_users(): void
    {
        $user = User::factory()->create();
        $user->givePermission('users_manage_edit');
        $this->actingAs($user);

        $targetUser = User::factory()->create();

        $response = $this->get(route('users.manage.edit', $targetUser->id));

        $response->assertStatus(200);
    }

    public function test_user_without_permissions_cannot_see_edit_users(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $targetUser = User::factory()->create();

        $response = $this->get(route('users.manage.edit', $targetUser->id));

        $response->assertStatus(403);
    }

    public function test_user_with_permissions_can_update_users(): void
    {
        $user = User::factory()->create();
        $user->givePermission('users_manage_update');
        $this->actingAs($user);

        $targetUser = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@iesebre.com',
        ]);

        $response = $this->put(route('users.manage.update', $targetUser->id), [
            'name' => 'Updated Name',
            'email' => 'updated@iesebre.com',
        ]);

        $response->assertRedirect(route('users.manage.index'));
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Updated Name',
            'email' => 'updated@iesebre.com',
        ]);
    }

    public function test_user_without_permissions_cannot_update_users(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $targetUser = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@iesebre.com',
        ]);

        $response = $this->put(route('users.manage.update', $targetUser->id), [
            'name' => 'Updated Name',
            'email' => 'updated@iesebre.com',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Original Name',
            'email' => 'original@iesebre.com',
        ]);
    }

    public function test_user_with_permissions_can_manage_users(): void
    {
        $user = User::factory()->create();
        $user->givePermission('users_manage_index');
        $this->actingAs($user);

        $response = $this->get(route('users.manage.index'));

        $response->assertStatus(200);
    }

    public function test_regular_users_cannot_manage_users(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('users.manage.index'));

        $response->assertStatus(403);
    }

    public function test_guest_users_cannot_manage_users(): void
    {
        $response = $this->get(route('users.manage.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_superadmins_can_manage_users(): void
    {
        $user = User::factory()->create(['super_admin' => true]);
        $this->actingAs($user);

        $response = $this->get(route('users.manage.index'));

        $response->assertStatus(200);
    }
}
