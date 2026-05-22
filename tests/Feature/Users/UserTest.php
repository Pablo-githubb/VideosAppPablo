<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_permissions_can_see_default_users_page(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('users.index'));

        $response->assertStatus(200);
    }

    public function test_user_with_permissions_can_see_default_users_page(): void
    {
        $user = User::factory()->create(['super_admin' => true]);
        $this->actingAs($user);

        $response = $this->get(route('users.index'));

        $response->assertStatus(200);
    }

    public function test_not_logged_users_cannot_see_default_users_page(): void
    {
        $response = $this->get(route('users.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_without_permissions_can_see_user_show_page(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('users.show', $otherUser->id));

        $response->assertStatus(200);
    }

    public function test_user_with_permissions_can_see_user_show_page(): void
    {
        $user = User::factory()->create(['super_admin' => true]);
        $otherUser = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('users.show', $otherUser->id));

        $response->assertStatus(200);
    }

    public function test_not_logged_users_cannot_see_user_show_page(): void
    {
        $otherUser = User::factory()->create();

        $response = $this->get(route('users.show', $otherUser->id));

        $response->assertRedirect(route('login'));
    }
}
