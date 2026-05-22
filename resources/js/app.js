/**
 * app.js — Configuració de Laravel Echo i Notificacions Push en Temps Real (Sprint 7)
 *
 * Aquest fitxer inicialitza la connexió WebSocket entre el navegador i el servidor
 * de broadcasting Pusher, i gestiona la recepció i presentació de notificacions toast
 * quan es crea un nou vídeo a la plataforma.
 *
 * Dependències instal·lades (npm):
 *   - laravel-echo   → Client WebSocket de Laravel (abstracció sobre Pusher/Reverb/Socket.io).
 *   - pusher-js      → SDK oficial de Pusher per a navegadors web.
 *
 * Configuració .env necessària:
 *   BROADCAST_CONNECTION=pusher
 *   PUSHER_APP_KEY=<clau_real_o_mock>
 *   PUSHER_APP_CLUSTER=mt1 (o el cluster corresponent)
 *   VITE_PUSHER_APP_KEY=${PUSHER_APP_KEY}     ← Per exposar al frontend via Vite
 *   VITE_PUSHER_APP_CLUSTER=${PUSHER_APP_CLUSTER}
 *
 * Flux de notificació toast:
 *   1. El servidor dispara VideoCreated → Pusher → canal 'videos' → event '.VideoCreated'.
 *   2. Echo rep l'event al navegador.
 *   3. Es crea dinàmicament un contenidor de toasts (si no existeix).
 *   4. Es genera un toast amb: títol, descripció i link al vídeo nou.
 *   5. El toast apareix amb animació slide-up i desapareix automàticament als 10 segons.
 *   6. L'usuari pot tancar-lo manualment amb el botó "×".
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Exposem Pusher globalment perquè laravel-echo el pugui trobar internament.
// Laravel Echo busca window.Pusher quan el broadcaster és 'pusher'.
window.Pusher = Pusher;

/**
 * Instanciem Laravel Echo configurant-lo amb el driver Pusher.
 *
 * Les claus es llegeixen de les variables d'entorn de Vite (definides a .env
 * amb prefix VITE_) per evitar exposar secrets del servidor.
 * Si no estan definides (entorn de test), s'usen valors mock per defecte.
 */
window.Echo = new Echo({
    broadcaster: 'pusher',
    // La clau pública d'aplicació Pusher (no és un secret; és segura al client)
    key: import.meta.env.VITE_PUSHER_APP_KEY || 'mock-key',
    // Cluster de Pusher (determina el servidor al qual es connecta)
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
    // Forcem TLS (connexió segura) per protegir la comunicació WebSocket
    forceTLS: true
});

// ─────────────────────────────────────────────────────────────────────────────
// Escolta d'events en temps real al canal 'videos'
// ─────────────────────────────────────────────────────────────────────────────
// .channel('videos') → Canal públic; qualsevol client pot subscriure's sense autenticació.
// Per a canals privats s'usaria .private('videos') + autenticació de canal.
window.Echo.channel('videos')
    // .listen('.VideoCreated', ...) → L'event broadcastAs() retorna 'VideoCreated'.
    // El punt (.) inicial indica que és un event broadcasted (no un event de Pusher intern).
    .listen('.VideoCreated', (e) => {
        // e.video conté les propietats del model Video serialitzat:
        // id, title, description, url, published_at, user_id, series_id, etc.
        console.log('Video creat en temps real:', e.video);

        // ─── Crear o reutilitzar el contenidor de toasts ───────────────────
        // Usem un contenidor persistent per poder apilar múltiples notificacions.
        let container = document.getElementById('push-notifications-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'push-notifications-container';
            // Posicionem el contenidor a la cantonada inferior dreta de la pantalla
            container.style.position = 'fixed';
            container.style.bottom = '2rem';
            container.style.right = '2rem';
            container.style.zIndex = '9999'; // Per sobre de qualsevol element de la pàgina
            container.style.display = 'flex';
            container.style.flexDirection = 'column';
            container.style.gap = '1rem';
            document.body.appendChild(container);
        }

        // ─── Crear el toast de notificació ─────────────────────────────────
        const toast = document.createElement('div');

        // Estils del toast: disseny glassmorphic amb tema fosc consistent amb l'app
        toast.style.background = 'linear-gradient(135deg, #1e1e30 0%, #16213e 100%)';
        toast.style.border = '1px solid rgba(99, 102, 241, 0.4)';
        toast.style.borderRadius = '12px';
        toast.style.padding = '1.25rem';
        toast.style.color = '#fff';
        toast.style.boxShadow = '0 10px 25px rgba(0, 0, 0, 0.3)';
        toast.style.width = '350px';
        toast.style.fontFamily = "'Inter', sans-serif";
        toast.style.transition = 'all 0.3s ease';
        // Iniciem amb opacitat 0 i translació per a l'animació d'entrada
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(20px)';

        // Contingut HTML del toast amb: capçalera, títol, descripció i link d'acció
        toast.innerHTML = `
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.5rem;">
                <span style="font-weight:700; color:#6366f1; font-size:0.875rem;">NOU VÍDEO DISPONIBLE</span>
                <!-- Botó de tancament manual: elimina el toast del DOM immediatament -->
                <button onclick="this.parentElement.parentElement.remove()" style="background:none; border:none; color:#a1a1aa; cursor:pointer; font-size:1.25rem; line-height:1;">&times;</button>
            </div>
            <div style="font-weight:600; font-size:0.9375rem; margin-bottom:0.25rem;">${e.video.title}</div>
            <div style="font-size:0.8125rem; color:#a1a1aa; margin-bottom:0.75rem;">S'ha afegit una nova lliçó a la plataforma.</div>
            <!-- Link directe al vídeo nou per a navegació immediata -->
            <a href="/videos/${e.video.id}" style="display:inline-block; font-size:0.8125rem; color:#fff; background:#6366f1; padding:0.375rem 0.875rem; border-radius:6px; text-decoration:none; font-weight:600; transition:background 0.2s;">Veure Vídeo</a>
        `;

        // Afegim el toast al contenidor (apilat sobre els anteriors)
        container.appendChild(toast);

        // ─── Animació d'entrada (slide-up) ─────────────────────────────────
        // setTimeout de 50ms permet que el navegador processi l'estat inicial
        // (opacity: 0, translateY: 20px) abans d'aplicar la transició CSS.
        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        }, 50);

        // ─── Auto-eliminació als 10 segons ─────────────────────────────────
        // Apliquem l'animació de sortida (slide-down + fade-out) i després
        // eliminem l'element del DOM per alliberar memòria.
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(20px)';
            // Esperem que acabi la transició CSS (300ms) abans d'eliminar el node
            setTimeout(() => toast.remove(), 300);
        }, 10000); // 10.000 ms = 10 segons de visibilitat
    });
