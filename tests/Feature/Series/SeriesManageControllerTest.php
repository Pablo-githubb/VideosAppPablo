<?php

/**
 * Test SeriesManageControllerTest — Tests de Feature per a la Gestió de Sèries (Sprint 6)
 *
 * Verifica que tots els endpoints CRUD de /series/manage funcionen correctament
 * respectant les regles de control d'accés basades en permisos.
 *
 * Cobertura de tests (15 tests):
 *   ┌─────────────────────────────────────────┬──────────────────────────────────┐
 *   │  Test                                   │  Verifica                        │
 *   ├─────────────────────────────────────────┼──────────────────────────────────┤
 *   │  test_user_with_permissions_can_see_add │  GET /series/manage/create → 200  │
 *   │  test_user_without_*_cannot_see_add     │  GET /series/manage/create → 403  │
 *   │  test_user_with_*_can_store             │  POST /series/manage → redirecció │
 *   │  test_user_without_*_cannot_store       │  POST /series/manage → 403        │
 *   │  test_user_with_*_can_destroy           │  DELETE /series/manage/{id} → 200 │
 *   │  test_user_without_*_cannot_destroy     │  DELETE → 403, BD intacta         │
 *   │  test_user_with_*_can_see_edit          │  GET /series/manage/{id}/edit →200 │
 *   │  test_user_without_*_cannot_see_edit    │  GET → 403                        │
 *   │  test_user_with_*_can_update            │  PUT → redirecció, BD actualitzada │
 *   │  test_user_without_*_cannot_update      │  PUT → 403, BD intacta             │
 *   │  test_user_with_*_can_manage            │  GET /series/manage → 200          │
 *   │  test_regular_users_cannot_manage       │  GET /series/manage → 403          │
 *   │  test_guest_users_cannot_manage         │  GET /series/manage → redirect login│
 *   │  test_videomanagers_can_manage          │  Gestors vídeos + permís → 200     │
 *   │  test_superadmins_can_manage            │  Superadmin → 200 sempre           │
 *   └─────────────────────────────────────────┴──────────────────────────────────┘
 *
 * Helpers privats:
 *   - loginAsVideoManager() → Usuari amb tots els permisos de gestió de vídeos.
 *   - loginAsSuperAdmin()   → Usuari amb super_admin = true (accés total).
 *   - loginAsRegularUser()  → Usuari sense cap permís especial.
 *
 * Usa RefreshDatabase per netejar la BD entre cada test (aïllament complet).
 */

namespace Tests\Feature\Series;

use App\Models\User;
use App\Models\Serie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeriesManageControllerTest extends TestCase
{
    // RefreshDatabase executa les migracions i neteja la BD per a cada test,
    // garantint que els tests siguin independents i reproduïbles.
    use RefreshDatabase;

    // ─── Helpers d'autenticació ───────────────────────────────────────────────

    /**
     * Crea i autentica un usuari amb tots els permisos de gestió de vídeos.
     * S'usa per verificar que gestors de vídeos poden accedir a sèries
     * si se'ls atorga el permís addicional.
     */
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

    /**
     * Crea i autentica un usuari superadministrador.
     * Els superadmins passen tots els Gates sense necessitar permisos explícits.
     */
    private function loginAsSuperAdmin(): User
    {
        $user = User::factory()->create(['super_admin' => true]);
        $this->actingAs($user);

        return $user;
    }

    /**
     * Crea i autentica un usuari regular sense cap permís especial.
     * S'usa per verificar que els usuaris sense permisos reben HTTP 403.
     */
    private function loginAsRegularUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    // ─── Tests de CREATE ─────────────────────────────────────────────────────

    /**
     * Un usuari amb el permís 'series_manage_create' ha de poder accedir
     * al formulari de creació de sèries (GET /series/manage/create → 200).
     */
    public function test_user_with_permissions_can_see_add_series(): void
    {
        $user = User::factory()->create();
        $user->givePermission('series_manage_create');
        $this->actingAs($user);

        $response = $this->get(route('series.manage.create'));

        $response->assertStatus(200);
    }

    /**
     * Un usuari sense el permís 'series_manage_create' ha de rebre HTTP 403
     * en intentar accedir al formulari de creació.
     */
    public function test_user_without_series_manage_create_cannot_see_add_series(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('series.manage.create'));

        $response->assertStatus(403);
    }

    // ─── Tests de STORE ──────────────────────────────────────────────────────

    /**
     * Un usuari amb el permís 'series_manage_store' ha de poder crear una sèrie
     * nova. El test verifica:
     *   1. La redirecció a l'índex (comportament esperat en cas d'èxit).
     *   2. La persistència a la BD (assertDatabaseHas).
     */
    public function test_user_with_permissions_can_store_series(): void
    {
        $user = User::factory()->create();
        $user->givePermission('series_manage_store');
        $this->actingAs($user);

        $response = $this->post(route('series.manage.store'), [
            'title'       => 'Nova Serie',
            'description' => 'Descripció de la nova sèrie.',
            'image'       => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=400&q=80',
        ]);

        $response->assertRedirect(route('series.manage.index'));
        $this->assertDatabaseHas('series', [
            'title'       => 'Nova Serie',
            'description' => 'Descripció de la nova sèrie.',
        ]);
    }

    /**
     * Un usuari sense el permís 'series_manage_store' ha de rebre HTTP 403
     * i la sèrie NO ha de ser creada a la BD.
     */
    public function test_user_without_permissions_cannot_store_series(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('series.manage.store'), [
            'title'       => 'Nova Serie 2',
            'description' => 'Descripció 2',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('series', ['title' => 'Nova Serie 2']);
    }

    // ─── Tests de DESTROY ────────────────────────────────────────────────────

    /**
     * Un usuari amb el permís 'series_manage_destroy' ha de poder eliminar
     * una sèrie. El test verifica la redirecció i que la sèrie ja no existeix a la BD.
     */
    public function test_user_with_permissions_can_destroy_series(): void
    {
        $user = User::factory()->create();
        $user->givePermission('series_manage_destroy');
        $this->actingAs($user);

        $serie = Serie::create([
            'title'     => 'Serie to delete',
            'description'=> 'Delete me',
            'user_name' => 'Pablo',
        ]);

        $response = $this->delete(route('series.manage.destroy', $serie->id));

        $response->assertRedirect(route('series.manage.index'));
        $this->assertDatabaseMissing('series', ['id' => $serie->id]);
    }

    /**
     * Un usuari sense el permís 'series_manage_destroy' ha de rebre HTTP 403
     * i la sèrie ha de continuar existint a la BD.
     */
    public function test_user_without_permissions_cannot_destroy_series(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $serie = Serie::create([
            'title'      => 'Serie to protect',
            'description'=> 'Do not delete me',
            'user_name'  => 'Pablo',
        ]);

        $response = $this->delete(route('series.manage.destroy', $serie->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('series', ['id' => $serie->id]);
    }

    // ─── Tests d'EDIT ────────────────────────────────────────────────────────

    /** Permís 'series_manage_edit': accés al formulari d'edició → 200. */
    public function test_user_with_permissions_can_see_edit_series(): void
    {
        $user = User::factory()->create();
        $user->givePermission('series_manage_edit');
        $this->actingAs($user);

        $serie = Serie::create(['title' => 'Edit me', 'description' => 'Description', 'user_name' => 'Pablo']);

        $response = $this->get(route('series.manage.edit', $serie->id));

        $response->assertStatus(200);
    }

    /** Sense permís 'series_manage_edit': accés al formulari → 403. */
    public function test_user_without_permissions_cannot_see_edit_series(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $serie = Serie::create(['title' => 'Do not edit me', 'description' => 'Description', 'user_name' => 'Pablo']);

        $response = $this->get(route('series.manage.edit', $serie->id));

        $response->assertStatus(403);
    }

    // ─── Tests d'UPDATE ──────────────────────────────────────────────────────

    /**
     * Permís 'series_manage_update': actualització de la sèrie → redirecció + BD actualitzada.
     */
    public function test_user_with_permissions_can_update_series(): void
    {
        $user = User::factory()->create();
        $user->givePermission('series_manage_update');
        $this->actingAs($user);

        $serie = Serie::create(['title' => 'Original title', 'description' => 'Original description', 'user_name' => 'Pablo']);

        $response = $this->put(route('series.manage.update', $serie->id), [
            'title'       => 'Updated title',
            'description' => 'Updated description',
        ]);

        $response->assertRedirect(route('series.manage.index'));
        $this->assertDatabaseHas('series', ['id' => $serie->id, 'title' => 'Updated title', 'description' => 'Updated description']);
    }

    /**
     * Sense permís 'series_manage_update': HTTP 403 + BD sense canvis.
     */
    public function test_user_without_permissions_cannot_update_series(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $serie = Serie::create(['title' => 'Original title', 'description' => 'Original description', 'user_name' => 'Pablo']);

        $response = $this->put(route('series.manage.update', $serie->id), [
            'title'       => 'Updated title',
            'description' => 'Updated description',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('series', ['id' => $serie->id, 'title' => 'Original title']);
    }

    // ─── Tests d'INDEX i Rols ────────────────────────────────────────────────

    /** Permís 'series_manage_index': accés a la llista → 200. */
    public function test_user_with_permissions_can_manage_series(): void
    {
        $user = User::factory()->create();
        $user->givePermission('series_manage_index');
        $this->actingAs($user);

        $response = $this->get(route('series.manage.index'));

        $response->assertStatus(200);
    }

    /** Usuari regular sense permisos → 403. */
    public function test_regular_users_cannot_manage_series(): void
    {
        $this->loginAsRegularUser();

        $response = $this->get(route('series.manage.index'));

        $response->assertStatus(403);
    }

    /** Usuari no autenticat → redirecció al login. */
    public function test_guest_users_cannot_manage_series(): void
    {
        $response = $this->get(route('series.manage.index'));

        $response->assertRedirect(route('login'));
    }

    /** Gestor de vídeos amb permís extra de sèries → accés a gestió → 200. */
    public function test_videomanagers_can_manage_series(): void
    {
        $user = $this->loginAsVideoManager();
        // El gestor de vídeos també necessita el permís específic de sèries
        $user->givePermission('series_manage_index');

        $response = $this->get(route('series.manage.index'));

        $response->assertStatus(200);
    }

    /** Superadmin → accés a tota la gestió de sèries sense necessitar permisos explícits → 200. */
    public function test_superadmins_can_manage_series(): void
    {
        $this->loginAsSuperAdmin();

        $response = $this->get(route('series.manage.index'));

        $response->assertStatus(200);
    }
}
