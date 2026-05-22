<?php

/**
 * Controlador SeriesController — Portal Públic de Sèries (Sprint 6)
 *
 * Gestiona les vistes públiques de sèries accessibles a tots els usuaris autenticats.
 * A diferència de SeriesManageController, aquest controlador NO requereix permisos
 * especials de gestió, però sí que les rutes estan protegides per middleware 'auth'.
 *
 * Funcionalitats:
 *   - Llistar sèries amb cerca en temps real per títol o descripció.
 *   - Mostrar el detall d'una sèrie amb tots els seus vídeos ordenats per data.
 *
 * Rutes (definides a routes/web.php, grup auth):
 *   GET /series         → index (llistat amb cerca i paginació)
 *   GET /series/{id}    → show (detall de la sèrie + vídeos)
 */

namespace App\Http\Controllers;

use App\Models\Serie;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SeriesController extends Controller
{
    /**
     * Mostra el llistat paginat de totes les sèries, amb filtre de cerca opcional.
     *
     * La cerca és un filtre dinàmic que consulta per títol i descripció simultàniament
     * usant el mètode when() d'Eloquent per evitar consultes condicionals complexes.
     *
     * Paràmetres GET:
     *   - search (opcional): Text a cercar. Filtra per títol o descripció.
     *
     * Paginació: 12 sèries per pàgina (adequat per a una graella de 3 o 4 columnes).
     *
     * Vista: resources/views/series/index.blade.php
     *   Mostra targetes premium amb imatge, títol, descripció, creador i comptador de vídeos.
     *   Si no hi ha resultats, mostra un estat buit estilitzat.
     */
    public function index(Request $request): View
    {
        // Capturem el paràmetre de cerca de la URL (pot ser null si no s'ha cercat)
        $search = $request->input('search');

        $series = Serie::query()
            // when() aplica la condició de cerca NOMÉS si $search no és null/buit.
            // Sense when(), hauríem de fer un if/else extern menys llegible.
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            // Ordenem de més nova a més antiga per a millor UX
            ->orderBy('created_at', 'desc')
            // paginate() genera automàticament la paginació de Laravel
            // i és compatible amb $series->links() a la vista Blade
            ->paginate(12);

        // Passem $search a la vista per mantenir el valor al camp de cerca
        return view('series.index', compact('series', 'search'));
    }

    /**
     * Mostra el detall complet d'una sèrie i els seus vídeos associats.
     *
     * Carrega la sèrie per ID i recupera tots els seus vídeos ordenats
     * cronològicament (els més antics primer, seguint l'ordre d'aprenentatge).
     *
     * Llança 404 si la sèrie no existeix (findOrFail).
     *
     * Vista: resources/views/series/show.blade.php
     *   Mostra una capçalera "hero" amb la imatge i informació del creador,
     *   seguida d'una graella de targetes de vídeo amb miniatures de YouTube.
     *
     * @param string $id L'identificador de la sèrie a mostrar.
     */
    public function show(string $id): View
    {
        // Recuperem la sèrie o retornem HTTP 404 si no existeix
        $serie = Serie::findOrFail($id);

        // Obtenim els vídeos de la sèrie ordenats per data de publicació ascendent
        // (del primer al més recent), seguint l'ordre lògic d'un curs o sèrie.
        $videos = $serie->videos()->orderBy('published_at', 'asc')->get();

        return view('series.show', compact('serie', 'videos'));
    }
}
