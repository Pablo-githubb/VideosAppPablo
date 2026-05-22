<?php

/**
 * Controlador SeriesManageController — CRUD Administratiu de Sèries (Sprint 6)
 *
 * Gestiona totes les operacions CRUD de sèries per a usuaris autenticats amb permisos.
 * Cada acció està protegida per un Gate específic que comprova si l'usuari té
 * el permís corresponent (emmagatzemat a la taula user_permissions o super_admin = true).
 *
 * Gates necessaris (definits a helpers.php → defineSeriesPermissionGates()):
 *   - series_manage_index    → Llistar totes les sèries.
 *   - series_manage_create   → Veure formulari de creació.
 *   - series_manage_store    → Persistir nova sèrie.
 *   - series_manage_edit     → Veure formulari d'edició.
 *   - series_manage_update   → Persistir canvis a una sèrie existent.
 *   - series_manage_delete   → Veure confirmació d'eliminació.
 *   - series_manage_destroy  → Executar l'eliminació definitiva.
 *
 * Rutes (definides a routes/web.php):
 *   GET    /series/manage          → index
 *   GET    /series/manage/create   → create
 *   POST   /series/manage          → store
 *   GET    /series/manage/{id}/edit   → edit
 *   PUT    /series/manage/{id}        → update
 *   GET    /series/manage/{id}/delete → delete
 *   DELETE /series/manage/{id}        → destroy
 *
 * Test associat: Tests\Feature\Series\SeriesManageControllerTest (15 tests)
 */

namespace App\Http\Controllers;

use App\Models\Serie;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SeriesManageController extends Controller
{
    /**
     * Referència al test de feature associat.
     * Facilita la traçabilitat entre controlador i test.
     */
    public function testedBy(): string
    {
        return \Tests\Feature\Series\SeriesManageControllerTest::class;
    }

    /**
     * Llista totes les sèries en ordre descendent per ID.
     *
     * Requereix: permís 'series_manage_index'.
     * Vista: resources/views/series/manage/index.blade.php
     *
     * La vista mostra una taula responsive que en dispositius mòbils
     * es converteix automàticament en una llista de targetes (media query CSS).
     */
    public function index(): View
    {
        Gate::authorize('series_manage_index');

        // Obtenim totes les sèries ordenades de més nova a més antiga
        $series = Serie::orderBy('id', 'desc')->get();

        return view('series.manage.index', compact('series'));
    }

    /**
     * Mostra el formulari de creació d'una nova sèrie.
     *
     * Requereix: permís 'series_manage_create'.
     * Vista: resources/views/series/manage/create.blade.php
     */
    public function create(): View
    {
        Gate::authorize('series_manage_create');

        return view('series.manage.create');
    }

    /**
     * Valida i persisteix una nova sèrie a la base de dades.
     *
     * Requereix: permís 'series_manage_store'.
     * Redirecciona a: series.manage.index amb missatge d'èxit.
     *
     * Camps rebuts del formulari:
     *   - title       (obligatori, màx 255 caràcters)
     *   - description (obligatori)
     *   - image       (opcional, URL de la imatge de portada)
     *
     * El user_name i user_photo_url es capturen de l'usuari autenticat
     * en el moment de creació (desnormalitzat per evitar JOINs freqüents).
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('series_manage_store');

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'image'       => 'nullable|string|max:255',
        ]);

        Serie::create([
            'title'         => $validated['title'],
            'description'   => $validated['description'],
            // Si no s'ha proporcionat imatge, usem una imatge per defecte d'Unsplash
            'image'         => $validated['image'] ?? 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=400&q=80',
            // Desnormalitzem el nom i foto del creador per a rendiment en les vistes
            'user_name'     => auth()->user()->name,
            'user_photo_url'=> auth()->user()->profile_photo_url ?? null,
            'published_at'  => now(),
        ]);

        return redirect()->route('series.manage.index')
            ->with('success', 'Sèrie creada correctament.');
    }

    /**
     * Mostra el formulari d'edició prefilled amb les dades actuals de la sèrie.
     *
     * Requereix: permís 'series_manage_edit'.
     * Vista: resources/views/series/manage/edit.blade.php
     * Llança 404 si la sèrie no existeix.
     */
    public function edit(string $id): View
    {
        Gate::authorize('series_manage_edit');

        // findOrFail llança ModelNotFoundException (→ HTTP 404) si no es troba
        $serie = Serie::findOrFail($id);

        return view('series.manage.edit', compact('serie'));
    }

    /**
     * Valida i persisteix els canvis d'una sèrie existent.
     *
     * Requereix: permís 'series_manage_update'.
     * Redirecciona a: series.manage.index amb missatge d'èxit.
     *
     * Si no s'envia una nova imatge, es conserva la imatge actual de la sèrie.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        Gate::authorize('series_manage_update');

        $serie = Serie::findOrFail($id);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'image'       => 'nullable|string|max:255',
        ]);

        $serie->update([
            'title'       => $validated['title'],
            'description' => $validated['description'],
            // Conservem la imatge existent si no s'ha proporcionat una de nova
            'image'       => $validated['image'] ?? $serie->image,
        ]);

        return redirect()->route('series.manage.index')
            ->with('success', 'Sèrie actualitzada correctament.');
    }

    /**
     * Mostra la pàgina de confirmació d'eliminació de la sèrie.
     *
     * Requereix: permís 'series_manage_delete'.
     * Vista: resources/views/series/manage/delete.blade.php
     *
     * La vista mostra la llista de vídeos associats que es desassignaran
     * (series_id → null) com a advertència visual a l'administrador.
     */
    public function delete(string $id): View
    {
        Gate::authorize('series_manage_delete');

        $serie = Serie::findOrFail($id);

        return view('series.manage.delete', compact('serie'));
    }

    /**
     * Elimina definitivament la sèrie de la base de dades.
     *
     * Requereix: permís 'series_manage_destroy'.
     * Redirecciona a: series.manage.index amb missatge d'èxit.
     *
     * Estratègia d'eliminació:
     *   - NO eliminem els vídeos associats (evitem pèrdua de dades accidental).
     *   - En canvi, posem series_id = null a tots els vídeos associats (desassignació).
     *   - Posteriorment s'elimina la sèrie.
     *   - Si es volgués eliminació en cascada, s'hauria de configurar al nivell de la migració
     *     o afegir $serie->videos()->delete() abans de $serie->delete().
     */
    public function destroy(string $id): RedirectResponse
    {
        Gate::authorize('series_manage_destroy');

        $serie = Serie::findOrFail($id);

        // Desassignem els vídeos de la sèrie (series_id → null) per preservar-los
        $serie->videos()->update(['series_id' => null]);

        // Eliminem la sèrie un cop els vídeos estan desassignats
        $serie->delete();

        return redirect()->route('series.manage.index')
            ->with('success', 'Sèrie eliminada correctament.');
    }
}
