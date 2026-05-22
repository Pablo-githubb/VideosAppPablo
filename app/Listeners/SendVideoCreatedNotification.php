<?php

/**
 * Classe SendVideoCreatedNotification — Listener d'Events (Sprint 7)
 *
 * Aquest listener s'activa automàticament quan es dispara l'event VideoCreated.
 * La seva responsabilitat és notificar per correu electrònic a tots els usuaris
 * superadministradors que s'ha afegit un nou vídeo a la plataforma.
 *
 * Registre: Aquest listener està registrat a EventServiceProvider::$listen.
 *
 * Configuració de correu:
 *   - Driver configurat a .env: MAIL_MAILER=log (entorn local registra als logs).
 *   - En producció canviar a smtp, mailgun, ses, etc.
 *   - Adreça d'enviament: MAIL_FROM_ADDRESS="pablomaso@iesebre.com"
 */

namespace App\Listeners;

use App\Events\VideoCreated;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendVideoCreatedNotification
{
    /**
     * Gestiona l'event VideoCreated.
     *
     * Per a cada superadministrador del sistema (camp super_admin = true),
     * intenta enviar un correu raw amb les dades del vídeo creat.
     * Si l'enviament falla per algun motiu (connexió SMTP, etc.), captura
     * l'excepció i la registra als logs sense aturar el procés per als
     * altres administradors.
     *
     * @param VideoCreated $event L'event que conté el vídeo recentment creat.
     */
    public function handle(VideoCreated $event): void
    {
        // Recuperem el vídeo des del payload de l'event
        $video = $event->video;

        // Obtenim tots els usuaris amb privilegis de superadministrador.
        // Aquests son els únics que reben la notificació per correu.
        $admins = User::where('super_admin', true)->get();

        foreach ($admins as $admin) {
            try {
                // Enviem un correu de text pla (raw) amb el títol i URL del vídeo.
                // Mail::raw és suficient per a notificacions simples; per a
                // dissenys HTML rics es podria usar Mail::send amb una vista Blade.
                Mail::raw(
                    "S'ha creat un nou vídeo a la plataforma: {$video->title}. Pots veure'l a: {$video->url}",
                    function ($message) use ($admin) {
                        $message->to($admin->email)
                                ->subject("Nou Vídeo Creat a la Plataforma");
                    }
                );
            } catch (\Exception $e) {
                // Si hi ha un error d'enviament, el registrem als logs d'aplicació
                // però continuem amb el bucle per notificar la resta d'admins.
                Log::error("Error enviant correu a {$admin->email}: " . $e->getMessage());
            }
        }

        // Registrem un missatge informatiu per confirmar que el procés ha acabat.
        Log::info("Notificació processada correctament per al vídeo: {$video->title}");
    }
}
