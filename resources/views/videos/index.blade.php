<x-videos-app-layout>
    <style>
        .videos-hero { padding: 3rem 2rem 1.5rem; text-align: center; }
        .videos-hero h1 { font-size: 2rem; font-weight: 700; background: linear-gradient(135deg, #6366f1, #a78bfa, #f472b6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 0.5rem; }
        .videos-hero p { color: #71717a; font-size: 1rem; }
        .videos-grid { max-width: 1280px; margin: 0 auto; padding: 1.5rem 2rem 3rem; display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; }
        .video-card { background: linear-gradient(135deg, #1e1e30, #1a1a2e); border: 1px solid rgba(99, 102, 241, 0.1); border-radius: 16px; overflow: hidden; transition: all 0.3s ease; text-decoration: none; color: inherit; display: block; }
        .video-card:hover { border-color: rgba(99, 102, 241, 0.4); transform: translateY(-4px); box-shadow: 0 12px 40px rgba(99, 102, 241, 0.15); }
        .video-thumb { position: relative; width: 100%; padding-top: 56.25%; background: #0a0a14; overflow: hidden; }
        .video-thumb iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none; pointer-events: none; }
        .video-thumb-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease; }
        .video-card:hover .video-thumb-overlay { opacity: 1; }
        .play-icon { width: 56px; height: 56px; background: rgba(99, 102, 241, 0.9); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 20px rgba(99, 102, 241, 0.5); }
        .play-icon svg { width: 24px; height: 24px; color: #fff; margin-left: 2px; }
        .video-info { padding: 1.25rem; }
        .video-info h3 { font-size: 1rem; font-weight: 600; color: #f4f4f5; margin-bottom: 0.5rem; line-height: 1.4; }
        .video-info p { font-size: 0.8125rem; color: #71717a; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .video-meta { padding: 0 1.25rem 1.25rem; font-size: 0.75rem; color: #52525b; }
        .empty-state { text-align: center; padding: 4rem 2rem; color: #52525b; }
        .empty-state svg { width: 64px; height: 64px; margin: 0 auto 1rem; opacity: 0.3; }
    </style>

    <div class="videos-hero">
        <h1>Vídeos</h1>
        <p>Explora tots els vídeos disponibles a la plataforma</p>
    </div>

    @if($videos->count() > 0)
        <div class="videos-grid">
            @foreach($videos as $video)
                <a href="{{ route('videos.show', $video->id) }}" class="video-card" data-qa="video-card-{{ $video->id }}">
                    <div class="video-thumb">
                        <iframe src="{{ str_replace('watch?v=', 'embed/', $video->url) }}" loading="lazy"></iframe>
                        <div class="video-thumb-overlay">
                            <div class="play-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="video-info">
                        <h3>{{ $video->title }}</h3>
                        <p>{{ $video->description }}</p>
                    </div>
                    <div class="video-meta">
                        {{ $video->formatted_published_at }}
                    </div>
                </a>
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
