<x-videos-app-layout>
    <style>
        .delete-container { max-width: 560px; margin: 0 auto; padding: 3rem 2rem; }
        .delete-card { background: linear-gradient(135deg, #1e1e30, #1a1a2e); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 16px; padding: 2.5rem; text-align: center; }
        .delete-icon { width: 64px; height: 64px; margin: 0 auto 1.5rem; background: rgba(239, 68, 68, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .delete-icon svg { width: 32px; height: 32px; color: #f87171; }
        .delete-card h1 { font-size: 1.5rem; font-weight: 700; color: #f4f4f5; margin-bottom: 0.5rem; }
        .delete-card p { color: #71717a; font-size: 0.9375rem; margin-bottom: 0.25rem; }
        .video-title { color: #f87171; font-weight: 600; font-size: 1.125rem; margin: 1rem 0 2rem; padding: 1rem; background: rgba(239, 68, 68, 0.06); border: 1px solid rgba(239, 68, 68, 0.12); border-radius: 10px; }
        .delete-actions { display: flex; gap: 1rem; justify-content: center; }
        .btn-cancel { padding: 0.625rem 2rem; background: transparent; color: #a1a1aa; border: 1px solid rgba(161, 161, 170, 0.3); border-radius: 10px; font-size: 0.875rem; font-weight: 500; text-decoration: none; transition: all 0.2s; }
        .btn-cancel:hover { color: #fff; border-color: rgba(161, 161, 170, 0.5); }
        .btn-destroy { padding: 0.625rem 2rem; background: linear-gradient(135deg, #dc2626, #ef4444); color: #fff; border: none; border-radius: 10px; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3); }
        .btn-destroy:hover { box-shadow: 0 4px 16px rgba(239, 68, 68, 0.5); transform: translateY(-1px); }
    </style>

    <div class="delete-container">
        <div class="delete-card">
            <div class="delete-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                </svg>
            </div>

            <h1>Eliminar Vídeo</h1>
            <p>Estàs segur que vols eliminar aquest vídeo?</p>
            <p style="font-size: 0.8125rem; color: #52525b;">Aquesta acció no es pot desfer.</p>

            <div class="video-title">
                {{ $video->title }}
            </div>

            <div class="delete-actions">
                <a href="{{ route('videos.manage.index') }}" class="btn-cancel">Cancel·lar</a>
                <form method="POST" action="{{ route('videos.manage.destroy', $video->id) }}" style="display:inline" data-qa="form-delete-video">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-destroy" data-qa="btn-confirm-delete">Sí, eliminar</button>
                </form>
            </div>
        </div>
    </div>
</x-videos-app-layout>
