<?php

/**
 * Controlador VideosController — CRUD Públic de Vídeos per a Usuaris Regulars (Sprint 6)
 *
 * Gestiona les vistes i accions de vídeos per a qualsevol usuari autenticat.
 * A diferència de VideosManageController (que requereix permisos de gestió),
 * aquest controlador permet a qualsevol usuari registrat:
 *   - Visualitzar el llistat de tots els vídeos i el detall d'un vídeo.
 *   - Crear els seus propis vídeos (assignant-los opcionalment a una sèrie).
 *   - Editar i eliminar ÚNICAMENT els vídeos que ell ha creat.
 *
 * Protecció d'ownership:
 *   Les accions edit, update, delete i destroy comproven si $video->user_id
 *   coincideix amb auth()->id(). Si no coincideix, l'usuari ha de tenir el
 *   permís de gestió corresponent o ser superadmin per continuar.
 *   En cas contrari, s'atura l'execució amb HTTP 403 Forbidden.
 *
 * Integració amb Sèries (Sprint 6):
 *   Els formularis de creació i edició mostren un dropdown amb totes les sèries
 *   disponibles, permetent associar el vídeo a una sèrie temàtica.
 *
 * Integració amb Events (Sprint 7):
 *   En crear un vídeo, es dispara l'event VideoCreated que activa el listener
 *   SendVideoCreatedNotification i el broadcaster Pusher per a notificacions.
 *
 * Rutes (definides a routes/web.php):
 *   GET    /videos                → index   (públic, sense auth)
 *   GET    /videos/{id}           → show    (públic, sense auth)
 *   GET    /videos/create         → create  (requereix auth)
 *   POST   /videos                → store   (requereix auth)
 *   GET    /videos/{id}/edit      → edit    (requereix auth + ownership o permís)
 *   PUT    /videos/{id}           → update  (requereix auth + ownership o permís)
 *   GET    /videos/{id}/delete    → delete  (requereix auth + ownership o permís)
 *   DELETE /videos/{id}           → destroy (requereix auth + ownership o permís)
 *
 * Test associat: Tests\Feature\Videos\VideosTest
 */

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Serie;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VideosController extends Controller
{
    /**
     * Referència al test de feature associat.
     */
    public function testedBy(): string
    {
        return \Tests\Feature\Videos\VideosTest::class;
    }

    /**
     * Llistat públic de tots els vídeos.
     *
     * Accessible per qualsevol visitant (autenticat o no). Retorna tots
     * els vídeos ordenats per data de publicació descendent (els més nous primer).
     *
     * La vista mostra:
     *   - Miniatura de YouTube extreta dinàmicament de la URL.
     *   - Botons "Editar" i "Eliminar" visibles NOMÉS al propietari del vídeo o gestors.
     *   - Botó "Afegir Vídeo" visible a tots els usuaris autenticats.
     *
     * Vista: resources/views/videos/index.blade.php
     */
    public function index(): View
    {
        $videos = Video::orderBy('published_at', 'desc')->get();

        return view('videos.index', compact('videos'));
    }

    /**
     * Mostra el detall d'un vídeo concret.
     *
     * Accessible públicament. Retorna HTTP 404 si el vídeo no existeix.
     *
     * Vista: resources/views/videos/show.blade.php
     *
     * @param string $id Identificador del vídeo.
     */
    public function show(string $id): View
    {
        $video = Video::findOrFail($id);

        return view('videos.show', compact('video'));
    }

    /**
     * Mostra el formulari de creació d'un nou vídeo.
     *
     * Requereix: auth (middleware de la ruta).
     * Carrega totes les sèries disponibles per al dropdown de selecció.
     *
     * Vista: resources/views/videos/create.blade.php
     */
    public function create(): View
    {
        // Carreguem totes les sèries per mostrar-les al selector del formulari
        $series = Serie::all();

        return view('videos.create', compact('series'));
    }

    /**
     * Valida i persisteix un nou vídeo a la base de dades.
     *
     * Requereix: auth.
     *
     * Camps validats:
     *   - title       (obligatori, màx 255 chars)
     *   - description (obligatori)
     *   - url         (obligatori, format URL vàlid, màx 255 chars)
     *   - series_id   (opcional, ha d'existir a la taula series)
     *
     * Assignació automàtica:
     *   - published_at s'estableix a now() per fer-lo visible immediatament.
     *   - user_id s'estableix a auth()->id() per vincular-lo al creador.
     *
     * Event dispatching (Sprint 7):
     *   Es dispara VideoCreated per notificar als admins i al frontend via Pusher.
     */
    public function store(Request $request): RedirectResponse
    {
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
            // Si no s'ha seleccionat cap sèrie, el vídeo queda sense assignar (null)
            'series_id'   => $validated['series_id'] ?? null,
            'published_at'=> now(),
            // Vinculem el vídeo a l'usuari autenticat que l'ha creat
            'user_id'     => auth()->id(),
        ]);

        // Sprint 7: Disparem l'event de creació per activar:
        //   1. El listener que envia correus als superadmins.
        //   2. El broadcaster Pusher que notifica el frontend en temps real.
        event(new \App\Events\VideoCreated($video));

        return redirect()->route('videos.index')
            ->with('success', 'Vídeo afegit correctament a la plataforma.');
    }

    /**
     * Mostra el formulari d'edició d'un vídeo existent.
     *
     * Requereix: auth + ownership o permís 'videos_manage_edit' o ser superadmin.
     *
     * Protecció d'ownership:
     *   Si l'usuari NO és el propietari del vídeo I NO té el permís de gestió
     *   I NO és superadmin, l'acció s'atura amb HTTP 403 Forbidden.
     *
     * @param string $id Identificador del vídeo a editar.
     */
    public function edit(string $id): View
    {
        $video = Video::findOrFail($id);

        // Comprovem en tres nivells: propietari, permís de gestió, o superadmin
        if ($video->user_id !== auth()->id()
            && !auth()->user()->isSuperAdmin()
            && !auth()->user()->hasPermission('videos_manage_edit')
        ) {
            abort(403, 'No tens permís per editar aquest vídeo.');
        }

        $series = Serie::all();

        return view('videos.edit', compact('video', 'series'));
    }

    /**
     * Valida i persisteix els canvis d'un vídeo existent.
     *
     * Requereix: auth + ownership o permís 'videos_manage_update' o ser superadmin.
     *
     * Nota: No actualitzem user_id ni published_at per preservar el creador original
     * i la data de publicació inicial.
     *
     * @param string $id Identificador del vídeo a actualitzar.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $video = Video::findOrFail($id);

        // Reutilitzem la mateixa comprovació d'ownership que a edit()
        if ($video->user_id !== auth()->id()
            && !auth()->user()->isSuperAdmin()
            && !auth()->user()->hasPermission('videos_manage_update')
        ) {
            abort(403, 'No tens permís per actualitzar aquest vídeo.');
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'url'         => 'required|url|max:255',
            'series_id'   => 'nullable|exists:series,id',
        ]);

        $video->update([
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'url'         => $validated['url'],
            'series_id'   => $validated['series_id'] ?? null,
        ]);

        return redirect()->route('videos.index')
            ->with('success', 'Vídeo actualitzat correctament.');
    }

    /**
     * Mostra la pàgina de confirmació d'eliminació d'un vídeo.
     *
     * Requereix: auth + ownership o permís 'videos_manage_delete' o ser superadmin.
     *
     * Vista: resources/views/videos/delete.blade.php
     *
     * @param string $id Identificador del vídeo a eliminar.
     */
    public function delete(string $id): View
    {
        $video = Video::findOrFail($id);

        if ($video->user_id !== auth()->id()
            && !auth()->user()->isSuperAdmin()
            && !auth()->user()->hasPermission('videos_manage_delete')
        ) {
            abort(403, 'No tens permís per eliminar aquest vídeo.');
        }

        return view('videos.delete', compact('video'));
    }

    /**
     * Elimina definitivament un vídeo de la base de dades.
     *
     * Requereix: auth + ownership o permís 'videos_manage_destroy' o ser superadmin.
     *
     * Nota: L'eliminació és permanent i no es pot desfer. La confirmació prèvia
     * la gestiona la pàgina de delete() anterior.
     *
     * @param string $id Identificador del vídeo a eliminar.
     */
    public function destroy(string $id): RedirectResponse
    {
        $video = Video::findOrFail($id);

        if ($video->user_id !== auth()->id()
            && !auth()->user()->isSuperAdmin()
            && !auth()->user()->hasPermission('videos_manage_destroy')
        ) {
            abort(403, 'No tens permís per eliminar aquest vídeo.');
        }

        $video->delete();

        return redirect()->route('videos.index')
            ->with('success', 'Vídeo eliminat correctament de la plataforma.');
    }
}
