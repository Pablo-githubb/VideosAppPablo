<x-videos-app-layout>
    <style>
        .videos-hero { padding: 3rem 2rem 1.5rem; text-align: center; }
        .videos-hero h1 { font-size: 2.25rem; font-weight: 800; background: linear-gradient(135deg, #fff 0%, #a1a1aa 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 0.5rem; }
        .videos-hero p { color: #a1a1aa; font-size: 1rem; margin-bottom: 1.5rem; }
        
        .btn-create-video { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.5rem; background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; text-decoration: none; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3); }
        .btn-create-video:hover { box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5); transform: translateY(-1px); }
        
        .videos-grid { max-width: 1280px; margin: 0 auto; padding: 1.5rem 2rem 3rem; display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem; }
        .video-card { background: linear-gradient(135deg, #1e1e30, #1a1a2e); border: 1px solid rgba(99, 102, 241, 0.1); border-radius: 16px; overflow: hidden; transition: all 0.3s ease; color: inherit; display: flex; flex-direction: column; box-shadow: 0 4px 20px rgba(0,0,0,0.25); }
        .video-card:hover { border-color: rgba(99, 102, 241, 0.3); transform: translateY(-4px); box-shadow: 0 12px 40px rgba(99, 102, 241, 0.15); }
        
        .video-thumb { position: relative; width: 100%; padding-top: 56.25%; background: #0a0a14; overflow: hidden; }
        .video-thumb iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none; pointer-events: none; }
        .video-thumb-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease; }
        .video-card:hover .video-thumb-overlay { opacity: 1; }
        
        .play-icon { width: 56px; height: 56px; background: rgba(99, 102, 241, 0.9); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 20px rgba(99, 102, 241, 0.5); }
        .play-icon svg { width: 24px; height: 24px; color: #fff; margin-left: 2px; }
        
        .video-info { padding: 1.25rem; flex: 1; display: flex; flex-direction: column; }
        .video-title-link { text-decoration: none; color: #f4f4f5; }
        .video-title-link:hover { color: #6366f1; }
        .video-info h3 { font-size: 1.0625rem; font-weight: 700; margin-bottom: 0.5rem; line-height: 1.4; }
        .video-info p { font-size: 0.875rem; color: #a1a1aa; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; flex: 1; }
        
        .video-meta { display: flex; align-items: center; justify-content: space-between; padding: 0 1.25rem 1.25rem; font-size: 0.75rem; color: #71717a; border-top: 1px solid rgba(255, 255, 255, 0.05); padding-top: 0.75rem; }
        
        .card-actions { display: flex; gap: 0.5rem; padding: 0 1.25rem 1.25rem; }
        .btn-card { display: inline-flex; align-items: center; justify-content: center; padding: 0.4rem 0.875rem; border-radius: 8px; font-size: 0.75rem; font-weight: 600; text-decoration: none; transition: all 0.2s; border: none; cursor: pointer; }
        .btn-card-edit { color: #818cf8; border: 1px solid rgba(129, 140, 248, 0.3); background: transparent; }
        .btn-card-edit:hover { background: rgba(129, 140, 248, 0.1); }
        .btn-card-delete { color: #f87171; border: 1px solid rgba(248, 113, 113, 0.3); background: transparent; }
        .btn-card-delete:hover { background: rgba(248, 113, 113, 0.1); }

        .empty-state { text-align: center; padding: 4rem 2rem; color: #71717a; }
        .empty-state svg { width: 64px; height: 64px; margin: 0 auto 1rem; opacity: 0.3; }
    </style>

    <div class="videos-hero">
        <h1>Vídeos</h1>
        <p>Explora tots els vídeos disponibles a la plataforma</p>
        @auth
            <a href="{{ route('videos.create') }}" class="btn-create-video" data-qa="btn-create-video">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Afegir Vídeo
            </a>
        @endauth
    </div>

    @if($videos->count() > 0)
        <div class="videos-grid">
            @foreach($videos as $video)
                <div class="video-card" data-qa="video-card-{{ $video->id }}">
                    <a href="{{ route('videos.show', $video->id) }}" style="display: block; position: relative;">
                        <div class="video-thumb">
                            @php
                                preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video->url, $match);
                                $youtube_id = $match[1] ?? null;
                            @endphp
                            @if($youtube_id)
                                <img src="https://img.youtube.com/vi/{{ $youtube_id }}/mqdefault.jpg" style="position: absolute; top:0; left:0; width:100%; height:100%; object-fit:cover;" alt="{{ $video->title }}">
                            @else
                                <iframe src="{{ str_replace('watch?v=', 'embed/', $video->url) }}" loading="lazy"></iframe>
                            @endif
                            <div class="video-thumb-overlay">
                                <div class="play-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </a>
                    <div class="video-info">
                        <h3>
                            <a href="{{ route('videos.show', $video->id) }}" class="video-title-link">
                                {{ $video->title }}
                            </a>
                        </h3>
                        <p>{{ $video->description }}</p>
                    </div>
                    <div class="video-meta">
                        <span>{{ $video->formatted_published_at }}</span>
                        @if($video->serie)
                            <span style="color:#6366f1;font-weight:600">{{ $video->serie->title }}</span>
                        @endif
                    </div>
                    
                    @auth
                        @if($video->user_id === auth()->id() || auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('videos_manage_edit'))
                            <div class="card-actions">
                                <a href="{{ route('videos.edit', $video->id) }}" class="btn-card btn-card-edit" data-qa="btn-edit-video-{{ $video->id }}">Editar</a>
                                <a href="{{ route('videos.delete', $video->id) }}" class="btn-card btn-card-delete" data-qa="btn-delete-video-{{ $video->id }}">Eliminar</a>
                            </div>
                        @endif
                    @endauth
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
            </svg>
            <p>No hi ha vídeos disponibles.</p>
        </div>
    @endif
</x-videos-app-layout>
