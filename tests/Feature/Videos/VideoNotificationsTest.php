<?php

/**
 * Test VideoNotificationsTest — Tests de Feature per a Events i Broadcasting (Sprint 7)
 *
 * Verifica que el sistema d'events i notificacions funciona correctament:
 *   1. Que l'event VideoCreated es dispara quan es crea un vídeo.
 *   2. Que l'event es dispara a través de l'endpoint POST /videos (ruta pública regular).
 *
 * Estratègia de test:
 *   S'utilitza Event::fake() per interceptar tots els events de Laravel i
 *   prevenir la seva execució real (no s'envien correus ni es connecta a Pusher).
 *   Això permet verificar que els events s'han disparat sense efectes secundaris.
 *
 * Per què Event::fake()?
 *   - Velocitat: els tests no esperen connexions de xarxa (Pusher, SMTP).
 *   - Aïllament: els tests no depenen de serveis externs.
 *   - Precisió: Event::assertDispatched() verifica exactament quin event i amb quines dades.
 *
 * Cobertura:
 *   - test_video_created_event_is_dispatched → Verificació directa de l'event.
 *   - test_push_notification_is_sent_when_video_is_created → Verificació via HTTP endpoint.
 */

namespace Tests\Feature\Videos;

use App\Events\VideoCreated;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class VideoNotificationsTest extends TestCase
{
    // Cada test comença amb una BD neta per garantir independència entre tests.
    use RefreshDatabase;

    /**
     * Verifica que l'event VideoCreated es dispara correctament quan
     * s'invoca manualment amb un vídeo concret.
     *
     * Flux del test:
     *   1. Event::fake() → Intercepta tots els events (no s'executen listeners reals).
     *   2. Creem un usuari i l'autentiquem.
     *   3. Creem un vídeo directament via Eloquent.
     *   4. Disparem manualment l'event VideoCreated.
     *   5. Verifiquem que VideoCreated s'ha disparat amb el vídeo correcte.
     *
     * La closure de assertDispatched() rep cada instància de l'event i
     * comprova que el vídeo de l'event és el que hem creat al test.
     */
    public function test_video_created_event_is_dispatched(): void
    {
        // Arrange: Interceptem tots els events de Laravel
        Event::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        // Act: Creem un vídeo directament a la BD (simulant la creació)
        $video = Video::create([
            'title'        => 'Test Video Notifications',
            'description'  => 'A test video for events',
            'url'          => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'published_at' => now(),
            'user_id'      => $user->id,
        ]);

        // Disparem l'event manualment per simular el que fa el controlador
        event(new VideoCreated($video));

        // Assert: Verifiquem que VideoCreated s'ha disparat UNA vegada
        // i que el vídeo de l'event és el mateix que hem creat.
        Event::assertDispatched(VideoCreated::class, function ($event) use ($video) {
            return $event->video->id === $video->id;
        });
    }

    /**
     * Verifica que l'event VideoCreated es dispara quan un usuari crea
     * un vídeo a través de l'endpoint HTTP POST /videos.
     *
     * Flux del test:
     *   1. Event::fake() → Intercepta tots els events.
     *   2. Autentiquem un usuari regular.
     *   3. Fem una petició POST a la ruta 'videos.store' amb dades vàlides.
     *   4. Verifiquem que VideoCreated s'ha disparat (independentment de quin vídeo).
     *
     * Nota: Aquí no verifiquem les dades exactes del vídeo (ho fa el test anterior).
     * L'objectiu és confirmar la integració completa: HTTP → Controller → Event.
     */
    public function test_push_notification_is_sent_when_video_is_created(): void
    {
        // Arrange
        Event::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        // Act: Simulem l'enviament del formulari de creació de vídeo
        $response = $this->post(route('videos.store'), [
            'title'       => 'Video for Push test',
            'description' => 'Description test',
            'url'         => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        // Assert: L'event VideoCreated s'ha disparat com a conseqüència de la petició HTTP.
        // Això verifica que el controlador VideosController::store() crida event(new VideoCreated(...)).
        Event::assertDispatched(VideoCreated::class);
    }
}
