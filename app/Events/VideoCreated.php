<?php

/**
 * Classe VideoCreated — Event de Difusió en Temps Real (Sprint 7)
 *
 * Aquest event s'emet automàticament cada vegada que un usuari (regular o gestor)
 * crea un nou vídeo a la plataforma. Implementa ShouldBroadcast perquè Laravel
 * el transmeti en temps real via Pusher cap als clients web connectats.
 *
 * Flux d'execució:
 *   1. L'usuari envia el formulari de creació de vídeo.
 *   2. El controlador (VideosController o VideosManageController) crea el Video a la BD.
 *   3. Es crida event(new VideoCreated($video)).
 *   4. Laravel envia l'event al canal públic 'videos' via Pusher.
 *   5. El frontend (app.js) escolta el canal i mostra una notificació toast.
 *   6. El listener SendVideoCreatedNotification envia correus als superadmins.
 */

namespace App\Events;

use App\Models\Video;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VideoCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * El model Video recentment creat.
     * Es serialitza automàticament gràcies al trait SerializesModels,
     * permetent recuperar-lo correctament si l'event es processa en cua.
     */
    public Video $video;

    /**
     * Constructor de l'event.
     *
     * Rep el vídeo acabat de crear i l'emmagatzema com a propietat pública
     * perquè estigui disponible tant per al listener com per al broadcaster.
     *
     * @param Video $video El vídeo recentment persistit a la base de dades.
     */
    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    /**
     * Defineix els canals de difusió de l'event.
     *
     * Utilitza un canal públic anomenat 'videos' perquè qualsevol client
     * web (autenticat o no) pugui rebre la notificació en temps real.
     * Si cal restringir, es pot canviar per PrivateChannel.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            // Canal públic: tots els clients web que estiguin escoltant
            // el canal 'videos' rebran aquest event via Pusher.
            new Channel('videos'),
        ];
    }

    /**
     * Nom personalitzat de l'event per al broadcaster.
     *
     * Per defecte Laravel usaria 'App\Events\VideoCreated' (amb backslash),
     * cosa que dificulta l'escolta al frontend. Amb broadcastAs() definim
     * un nom net 'VideoCreated' que s'escolta al JS amb .listen('.VideoCreated', ...).
     */
    public function broadcastAs(): string
    {
        return 'VideoCreated';
    }
}
