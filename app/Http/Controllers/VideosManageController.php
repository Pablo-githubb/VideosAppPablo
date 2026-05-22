<?php

/**
 * Controlador VideosManageController — CRUD Administratiu de Vídeos (Sprint 4 + 6 + 7)
 *
 * Gestiona totes les operacions CRUD de vídeos per a usuaris amb permisos de gestió.
 * Cada acció requereix un permís específic comprovat via Gate::authorize().
 *
 * Diferència respecte a VideosController:
 *   - VideosManageController → Per a gestors/admins, accés via /videos/manage/*.
 *   - VideosController       → Per a usuaris regulars, accés via /videos/* amb ownership.
 *
 * Canvis introduïts als Sprints 6 i 7:
 *   [Sprint 6] Afegit 'series_id' als formularis de creació i edició per poder
 *              associar vídeos a una sèrie temàtica.
 *   [Sprint 6] Afegit 'user_id' per registrar qui ha creat el vídeo.
 *   [Sprint 7] Afegit event(new VideoCreated($video)) al mètode store() per
 *              disparar l'event de notificació i broadcasting en temps real.
 *
 * Gates necessaris (definits a helpers.php → defineVideoPermissionGates()):
 *   - videos_manage_index   → Llistar tots els vídeos.
 *   - videos_manage_create  → Veure formulari de creació.
 *   - videos_manage_store   → Persistir nou vídeo.
 *   - videos_manage_edit    → Veure formulari d'edició.
 *   - videos_manage_update  → Persistir canvis.
 *   - videos_manage_delete  → Veure confirmació d'eliminació.
 *   - videos_manage_destroy → Executar eliminació definitiva.
 *
 * Test associat: Tests\Feature\Videos\VideosManageControllerTest
 */

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class VideosManageController extends Controller
{
    /**
     * Referència al test de feature associat.
     */
    public function testedBy(): string
    {
        return \Tests\Feature\Videos\VideosManageControllerTest::class;
    }

    /**
     * Llistat de tots els vídeos de la plataforma (vista d'administrador).
     *
     * Requereix: permís 'videos_manage_index'.
     * Vista: resources/views/videos/manage/index.blade.php
     *
     * Mostra tots els vídeos en una taula amb columnes ID, Títol, Descripció,
     * URL, Data de publicació i Accions (editar/eliminar).
     */
    public function index(): View
    {
        Gate::authorize('videos_manage_index');

        // Obtenim tots els vídeos de la BD, ordenats del més nou al més antic
        $videos = Video::orderBy('published_at', 'desc')->get();

        return view('videos.manage.index', compact('videos'));
    }

    /**
     * Mostra el formulari de creació d'un nou vídeo (gestió).
     *
     * Requereix: permís 'videos_manage_create'.
     * Vista: resources/views/videos/manage/create.blade.php
     *
     * [Sprint 6] Carrega totes les sèries disponibles per al dropdown de selecció.
     */
    public function create(): View
    {
        Gate::authorize('videos_manage_create');

        // [Sprint 6] Carreguem les sèries per al selector del formulari
        $series = \App\Models\Serie::all();

        return view('videos.manage.create', compact('series'));
    }

    /**
     * Valida i persisteix un nou vídeo creat per un gestor.
     *
     * Requereix: permís 'videos_manage_store'.
     *
     * Camps validats:
     *   - title       (obligatori, màx 255 chars)
     *   - description (obligatori)
     *   - url         (obligatori, format URL, màx 255 chars)
     *   - series_id   (opcional, ha d'existir a la taula series) [Sprint 6]
     *
     * [Sprint 6] Assigna 'user_id' a l'id de l'usuari autenticat.
     * [Sprint 7] Dispara l'event VideoCreated per notificació i broadcasting.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('videos_manage_store');

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'url'         => 'required|url|max:255',
            'series_id'   => 'nullable|exists:series,id',
        ]);

        $video = Video::create([
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'url'         => $validated['url'],
            // [Sprint 6] Associació opcional a una sèrie temàtica
            'series_id'   => $validated['series_id'] ?? null,
            'published_at'=> now(),
            // [Sprint 5] Registrem l'usuari gestor que ha creat el vídeo
            'user_id'     => auth()->id(),
        ]);

        // [Sprint 7] Disparem l'event per activar:
        //   - Listener: envia correu als superadmins (SendVideoCreatedNotification).
        //   - Broadcaster: notifica el frontend via Pusher (canal 'videos').
        event(new \App\Events\VideoCreated($video));

        return redirect()->route('videos.manage.index')
            ->with('success', 'Vídeo creat correctament.');
    }

    /**
     * Mostra el detall d'un vídeo des del panell de gestió.
     *
     * Requereix: permís 'videos_manage_index' (reutilitzem el permís de llistat).
     * Vista: resources/views/videos/show.blade.php (compartida amb la vista pública).
     *
     * @param string $id Identificador del vídeo.
     */
    public function show(string $id): View
    {
        Gate::authorize('videos_manage_index');

        $video = Video::findOrFail($id);

        return view('videos.show', compact('video'));
    }

    /**
     * Mostra el formulari d'edició d'un vídeo (gestió).
     *
     * Requereix: permís 'videos_manage_edit'.
     * Vista: resources/views/videos/manage/edit.blade.php
     *
     * [Sprint 6] Carrega les sèries per al dropdown de selecció de la sèrie.
     *
     * @param string $id Identificador del vídeo a editar.
     */
    public function edit(string $id): View
    {
        Gate::authorize('videos_manage_edit');

        $video = Video::findOrFail($id);
        // [Sprint 6] Necessitem les sèries per mostrar el selector al formulari
        $series = \App\Models\Serie::all();

        return view('videos.manage.edit', compact('video', 'series'));
    }

    /**
     * Valida i persisteix els canvis d'un vídeo existent (gestió).
     *
     * Requereix: permís 'videos_manage_update'.
     *
     * [Sprint 6] Inclou la validació i actualització del camp series_id.
     *
     * @param string $id Identificador del vídeo a actualitzar.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        Gate::authorize('videos_manage_update');

        $video = Video::findOrFail($id);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'url'         => 'required|url|max:255',
            // [Sprint 6] La sèrie és opcional; null desassigna el vídeo de qualsevol sèrie
            'series_id'   => 'nullable|exists:series,id',
        ]);

        $video->update([
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'url'         => $validated['url'],
            'series_id'   => $validated['series_id'] ?? null,
        ]);

        return redirect()->route('videos.manage.index')
            ->with('success', 'Vídeo actualitzat correctament.');
    }

    /**
     * Mostra la pàgina de confirmació d'eliminació d'un vídeo (gestió).
     *
     * Requereix: permís 'videos_manage_delete'.
     * Vista: resources/views/videos/manage/delete.blade.php
     *
     * @param string $id Identificador del vídeo a eliminar.
     */
    public function delete(string $id): View
    {
        Gate::authorize('videos_manage_delete');

        $video = Video::findOrFail($id);

        return view('videos.manage.delete', compact('video'));
    }

    /**
     * Elimina definitivament un vídeo de la base de dades (gestió).
     *
     * Requereix: permís 'videos_manage_destroy'.
     * L'eliminació és permanent; la confirmació prèvia la fa delete().
     *
     * @param string $id Identificador del vídeo a eliminar.
     */
    public function destroy(string $id): RedirectResponse
    {
        Gate::authorize('videos_manage_destroy');

        $video = Video::findOrFail($id);
        $video->delete();

        return redirect()->route('videos.manage.index')
            ->with('success', 'Vídeo eliminat correctament.');
    }
}
