<?php

/**
 * Fitxer de Rutes Web — routes/web.php
 *
 * Defineix totes les rutes HTTP de l'aplicació Laravel VideosApp.
 * Les rutes estan organitzades per recurs i per nivell d'accés:
 *
 * ┌──────────────────────────────────────────────────────────────────────┐
 * │  ESTRUCTURA DE RUTES                                                 │
 * ├──────────────┬───────────────────────────────────────────────────────┤
 * │  Prefix      │  Controlador             │  Accés                     │
 * ├──────────────┼──────────────────────────┼────────────────────────────┤
 * │  /           │  welcome view            │  Públic                    │
 * │  /videos     │  VideosController        │  Públic (index/show)       │
 * │              │                          │  Auth (create/store/edit…) │
 * │  /videos/manage │ VideosManageController│  Auth + permisos gestió    │
 * │  /users      │  UsersController         │  Auth                      │
 * │  /users/manage │ UsersManageController  │  Auth + permisos gestió    │
 * │  /series     │  SeriesController        │  Auth [Sprint 6]           │
 * │  /series/manage │ SeriesManageController│  Auth + permisos [Sprint 6]│
 * └──────────────┴──────────────────────────┴────────────────────────────┘
 *
 * Ordre de definició de rutes:
 *   Les rutes amb prefix específic (com /videos/manage) s'han de definir
 *   ABANS de les rutes amb wildcard (com /videos/{id}) per evitar conflictes
 *   on Laravel interpreta "manage" com un ID.
 *
 * Canvis Sprint 6:
 *   - Afegit grup de rutes CRUD per a VideosController (usuaris regulars).
 *   - Afegit grup de rutes per a SeriesManageController (/series/manage).
 *   - Afegit grup de rutes per a SeriesController (/series).
 *   - Importats SeriesController i SeriesManageController.
 */

use App\Http\Controllers\VideosController;
use App\Http\Controllers\VideosManageController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\UsersManageController;
use App\Http\Controllers\SeriesController;       // [Sprint 6] Portal públic de sèries
use App\Http\Controllers\SeriesManageController; // [Sprint 6] Gestió administrativa de sèries
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

// Pàgina principal (welcome) — accessible per tothom
Route::view('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

// Dashboard de l'equip (requereix auth + verificació + pertinença a equip)
Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');
    });

// Rutes d'invitació a equips (requereix auth)
Route::middleware(['auth'])->group(function () {
    Route::livewire('invitations/{invitation}/accept', 'pages::teams.accept-invitation')->name('invitations.accept');
});

// ─────────────────────────────────────────────────────────────────────────────
// VÍDEOS — Gestió Administrativa (requereix permisos específics de gestió)
// ─────────────────────────────────────────────────────────────────────────────
// IMPORTANT: Definit ABANS de /videos/{id} per evitar que Laravel interpreti
//            "manage" com un paràmetre wildcard {id}.
Route::middleware(['auth'])->prefix('videos/manage')->name('videos.manage.')->group(function () {
    Route::get('/',           [VideosManageController::class, 'index'])  ->name('index');
    Route::get('/create',     [VideosManageController::class, 'create']) ->name('create');
    Route::post('/',          [VideosManageController::class, 'store'])  ->name('store');
    Route::get('/{id}',       [VideosManageController::class, 'show'])   ->name('show');
    Route::get('/{id}/edit',  [VideosManageController::class, 'edit'])   ->name('edit');
    Route::put('/{id}',       [VideosManageController::class, 'update']) ->name('update');
    Route::get('/{id}/delete',[VideosManageController::class, 'delete']) ->name('delete');
    Route::delete('/{id}',    [VideosManageController::class, 'destroy'])->name('destroy');
});

// ─────────────────────────────────────────────────────────────────────────────
// VÍDEOS — Portal Públic i CRUD per a Usuaris Regulars [Sprint 6]
// ─────────────────────────────────────────────────────────────────────────────
// L'index és accessible sense autenticació (veure llista de vídeos).
Route::get('/videos', [VideosController::class, 'index'])->name('videos.index');

// Les accions de creació, edició i eliminació requereixen auth.
// La protecció d'ownership (editar/eliminar PROPIS vídeos) es gestiona dins del controlador.
Route::middleware(['auth'])->prefix('videos')->name('videos.')->group(function () {
    Route::get('/create',     [VideosController::class, 'create']) ->name('create');
    Route::post('/',          [VideosController::class, 'store'])  ->name('store');
    Route::get('/{id}/edit',  [VideosController::class, 'edit'])   ->name('edit');
    Route::put('/{id}',       [VideosController::class, 'update']) ->name('update');
    Route::get('/{id}/delete',[VideosController::class, 'delete']) ->name('delete');
    Route::delete('/{id}',    [VideosController::class, 'destroy'])->name('destroy');
});

// La ruta show s'ha de definir AQUÍ, FORA del grup auth, per ser accessible públicament.
Route::get('/videos/{id}', [VideosController::class, 'show'])->name('videos.show');

// ─────────────────────────────────────────────────────────────────────────────
// USUARIS — Gestió Administrativa (requereix permisos específics de gestió)
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth'])->prefix('users/manage')->name('users.manage.')->group(function () {
    Route::get('/',           [UsersManageController::class, 'index'])  ->name('index');
    Route::get('/create',     [UsersManageController::class, 'create']) ->name('create');
    Route::post('/',          [UsersManageController::class, 'store'])  ->name('store');
    Route::get('/{id}/edit',  [UsersManageController::class, 'edit'])   ->name('edit');
    Route::put('/{id}',       [UsersManageController::class, 'update']) ->name('update');
    Route::get('/{id}/delete',[UsersManageController::class, 'delete']) ->name('delete');
    Route::delete('/{id}',    [UsersManageController::class, 'destroy'])->name('destroy');
});

// USUARIS — Portal Autenticat (requereix auth)
Route::middleware(['auth'])->prefix('users')->name('users.')->group(function () {
    Route::get('/',     [UsersController::class, 'index'])->name('index');
    Route::get('/{id}', [UsersController::class, 'show']) ->name('show');
});

// ─────────────────────────────────────────────────────────────────────────────
// SÈRIES — Gestió Administrativa [Sprint 6] (requereix permisos específics)
// ─────────────────────────────────────────────────────────────────────────────
// Requereix auth + permís de sèries (comprovat amb Gate::authorize() al controlador).
Route::middleware(['auth'])->prefix('series/manage')->name('series.manage.')->group(function () {
    Route::get('/',           [SeriesManageController::class, 'index'])  ->name('index');
    Route::get('/create',     [SeriesManageController::class, 'create']) ->name('create');
    Route::post('/',          [SeriesManageController::class, 'store'])  ->name('store');
    Route::get('/{id}/edit',  [SeriesManageController::class, 'edit'])   ->name('edit');
    Route::put('/{id}',       [SeriesManageController::class, 'update']) ->name('update');
    Route::get('/{id}/delete',[SeriesManageController::class, 'delete']) ->name('delete');
    Route::delete('/{id}',    [SeriesManageController::class, 'destroy'])->name('destroy');
});

// SÈRIES — Portal Públic [Sprint 6] (requereix auth per veure les sèries)
Route::middleware(['auth'])->prefix('series')->name('series.')->group(function () {
    Route::get('/',     [SeriesController::class, 'index'])->name('index'); // Llistat amb cerca
    Route::get('/{id}', [SeriesController::class, 'show']) ->name('show');  // Detall + vídeos
});

// Carreguem les rutes de configuració de perfil d'usuari (fortify/jetstream)
require __DIR__.'/settings.php';
