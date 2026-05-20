<?php

use App\Http\Controllers\VideosController;
use App\Http\Controllers\VideosManageController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::view('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');
    });

Route::middleware(['auth'])->group(function () {
    Route::livewire('invitations/{invitation}/accept', 'pages::teams.accept-invitation')->name('invitations.accept');
});

// Videos manage (només per usuaris autenticats amb permisos)
// IMPORTANT: Definit ABANS de /videos/{id} per evitar conflicte de rutes
Route::middleware(['auth'])->prefix('videos/manage')->name('videos.manage.')->group(function () {
    Route::get('/', [VideosManageController::class, 'index'])->name('index');
    Route::get('/create', [VideosManageController::class, 'create'])->name('create');
    Route::post('/', [VideosManageController::class, 'store'])->name('store');
    Route::get('/{id}', [VideosManageController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [VideosManageController::class, 'edit'])->name('edit');
    Route::put('/{id}', [VideosManageController::class, 'update'])->name('update');
    Route::get('/{id}/delete', [VideosManageController::class, 'delete'])->name('delete');
    Route::delete('/{id}', [VideosManageController::class, 'destroy'])->name('destroy');
});

// Videos públics (accessibles per tothom)
Route::get('/videos', [VideosController::class, 'index'])->name('videos.index');
Route::get('/videos/{id}', [VideosController::class, 'show'])->name('videos.show');

require __DIR__.'/settings.php';
