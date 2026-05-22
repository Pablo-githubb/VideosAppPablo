<?php

/**
 * Classe EventServiceProvider — Proveïdor de Serveis d'Events (Sprint 7)
 *
 * Aquest proveïdor registra la relació entre events i listeners a l'aplicació.
 * Laravel consulta el mapa $listen per saber quin listener(s) han d'executar-se
 * automàticament quan es dispara un event concret.
 *
 * Registre: Afegit a bootstrap/providers.php perquè s'inicialitzi amb l'aplicació.
 *
 * Events registrats:
 *   - VideoCreated → SendVideoCreatedNotification
 *     Es dispara quan qualsevol usuari (regular o gestor) crea un vídeo nou.
 *     El listener envia correus als superadmins i registra l'activitat als logs.
 */

namespace App\Providers;

use App\Events\VideoCreated;
use App\Listeners\SendVideoCreatedNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Mapa d'events → listeners de l'aplicació.
     *
     * Cada clau és la classe de l'event, i el valor és un array amb tots
     * els listeners que han d'executar-se quan l'event es dispari.
     * Es pot afegir múltiples listeners per a un mateix event.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Quan es crea un vídeo nou, notifiquem els superadmins per correu.
        VideoCreated::class => [
            SendVideoCreatedNotification::class,
        ],
    ];

    /**
     * Inicialitza el proveïdor d'events.
     *
     * Crida al mètode boot del pare per registrar automàticament tots
     * els listeners definits a $listen i descobrir events per event discovery.
     */
    public function boot(): void
    {
        parent::boot();
    }
}
