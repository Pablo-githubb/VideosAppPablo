<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verifica que els convidats són redirigits a la pàgina de login.
     */
    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $user = User::factory()->create();
        $team = $user->currentTeam;

        $response = $this->get(route('dashboard', ['current_team' => $team]));

        $response->assertRedirect(route('login'));
    }

    /**
     * Verifica que els usuaris autenticats poden visitar el dashboard.
     */
    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
        $user = User::factory()->create();
        $team = $user->currentTeam;

        $response = $this
            ->actingAs($user)
            ->get(route('dashboard', ['current_team' => $team]));

        $response->assertOk();
    }
}
