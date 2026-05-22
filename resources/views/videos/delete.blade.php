<x-videos-app-layout>
    <style>
        .delete-container { max-width: 600px; margin: 4rem auto; padding: 2.5rem; background: linear-gradient(135deg, #2d1f2d, #1a1a2e); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 16px; text-align: center; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5); }
        .delete-icon { width: 64px; height: 64px; margin: 0 auto 1.5rem; color: #ef4444; }
        .delete-title { font-size: 1.5rem; font-weight: 700; color: #fff; margin-bottom: 1rem; }
        .delete-text { font-size: 0.9375rem; color: #a1a1aa; line-height: 1.6; margin-bottom: 2rem; }
        .delete-actions { display: flex; gap: 1rem; justify-content: center; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 0.75rem 2rem; border-radius: 10px; font-size: 0.875rem; font-weight: 600; text-decoration: none; transition: all 0.2s; cursor: pointer; border: none; }
        .btn-cancel { color: #a1a1aa; background: transparent; border: 1px solid rgba(161, 161, 170, 0.2); }
        .btn-cancel:hover { background: rgba(161, 161, 170, 0.05); color: #fff; }
        .btn-delete { color: #fff; background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); }
        .btn-delete:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(239, 68, 68, 0.5); }
    </style>

    <div class="delete-container">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="delete-icon">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
        </svg>

        <h1 class="delete-title">Confirmar Eliminació de Vídeo</h1>
        <p class="delete-text">
            Estàs completament segur que vols eliminar el vídeo <strong>"{{ $video->title }}"</strong>? Aquesta acció no es pot desfer.
        </p>

        <form action="{{ route('videos.destroy', $video->id) }}" method="POST">
            @csrf
            @method('DELETE')

            <div class="delete-actions">
                <a href="{{ route('videos.index') }}" class="btn btn-cancel">Cancel·lar</a>
                <button type="submit" class="btn btn-delete" data-qa="btn-confirm-delete-video">Eliminar Vídeo</button>
            </div>
        </form>
    </div>
</x-videos-app-layout>
