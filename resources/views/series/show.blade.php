<x-videos-app-layout>
    <style>
        .serie-hero { background: linear-gradient(135deg, #1c1c30 0%, #0d0d16 100%); padding: 4rem 2rem; border-bottom: 1px solid rgba(99, 102, 241, 0.15); margin-bottom: 3rem; }
        .hero-inner { max-width: 1280px; margin: 0 auto; display: flex; gap: 3rem; align-items: center; }
        .hero-img-wrapper { width: 320px; height: 180px; flex-shrink: 0; border-radius: 12px; overflow: hidden; border: 1px solid rgba(99, 102, 241, 0.2); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4); }
        .hero-img { width: 100%; height: 100%; object-fit: cover; }
        .hero-content { flex: 1; }
        .hero-title { font-size: 2.25rem; font-weight: 800; color: #fff; margin-bottom: 1rem; }
        .hero-desc { font-size: 1rem; color: #a1a1aa; line-height: 1.6; margin-bottom: 2rem; }
        
        .creator-info { display: flex; align-items: center; gap: 0.75rem; }
        .creator-avatar { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid #6366f1; }
        .creator-details { display: flex; flex-direction: column; }
        .creator-name { font-size: 0.875rem; color: #fff; font-weight: 600; }
        .creator-role { font-size: 0.75rem; color: #71717a; }

        .videos-section { max-width: 1280px; margin: 0 auto; padding: 0 2rem 4rem; }
        .section-title { font-size: 1.5rem; font-weight: 700; color: #fff; margin-bottom: 2rem; border-left: 4px solid #6366f1; padding-left: 0.75rem; }

        /* Video cards grid */
        .video-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem; }
        .video-card { background: linear-gradient(135deg, #1e1e30, #131324); border: 1px solid rgba(99, 102, 241, 0.08); border-radius: 12px; overflow: hidden; transition: all 0.3s ease; display: flex; flex-direction: column; text-decoration: none; color: inherit; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15); }
        .video-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(99, 102, 241, 0.12); border-color: rgba(99, 102, 241, 0.25); }
        
        .video-thumbnail-wrapper { position: relative; width: 100%; padding-top: 56.25%; background: #000; }
        .video-thumbnail { position: absolute; top:0; left:0; width:100%; height:100%; object-fit: cover; }
        .play-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.4); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s; }
        .video-card:hover .play-overlay { opacity: 1; }
        .play-icon { width: 44px; height: 44px; color: #fff; background: #6366f1; border-radius: 50%; padding: 0.625rem; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4); }

        .video-body { padding: 1.25rem; flex: 1; display: flex; flex-direction: column; }
        .video-title { font-size: 1.0625rem; font-weight: 600; color: #fff; margin-bottom: 0.5rem; line-height: 1.4; }
        .video-desc { font-size: 0.8125rem; color: #8e8e9f; line-height: 1.5; margin-bottom: 1rem; flex: 1; }
        .video-meta { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(255, 255, 255, 0.05); padding-top: 0.75rem; margin-top: auto; font-size: 0.75rem; color: #71717a; }

        @media (max-width: 768px) {
            .hero-inner { flex-direction: column; text-align: center; gap: 1.5rem; }
            .hero-img-wrapper { width: 100%; max-width: 320px; }
            .creator-info { justify-content: center; }
        }
    </style>

    <div class="serie-hero">
        <div class="hero-inner">
            <div class="hero-img-wrapper">
                @if($serie->image)
                    <img src="{{ $serie->image }}" class="hero-img" alt="{{ $serie->title }}">
                @else
                    <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#181829; color:#52525b">
                        Sense Imatge
                    </div>
                @endif
            </div>
            <div class="hero-content">
                <h1 class="hero-title">{{ $serie->title }}</h1>
                <p class="hero-desc">{{ $serie->description }}</p>
                <div class="creator-info">
                    @if($serie->user_photo_url)
                        <img src="{{ $serie->user_photo_url }}" class="creator-avatar" alt="{{ $serie->user_name }}">
                    @else
                        <div class="creator-avatar" style="background:#6366f1; color:#fff; display:flex; align-items:center; justify-content:center; font-size:0.875rem; font-weight:700">
                            {{ strtoupper(substr($serie->user_name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="creator-details">
                        <span class="creator-name">{{ $serie->user_name }}</span>
                        <span class="creator-role">Creador de la Sèrie</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="videos-section">
        <h2 class="section-title">Contingut de la Sèrie ({{ $videos->count() }} vídeos)</h2>

        @if($videos->count() > 0)
            <div class="video-grid">
                @foreach($videos as $video)
                    <a href="{{ route('videos.show', $video->id) }}" class="video-card">
                        <div class="video-thumbnail-wrapper">
                            @php
                                // Extraure ID de youtube si és possible
                                preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video->url, $match);
                                $youtube_id = $match[1] ?? null;
                            @endphp
                            @if($youtube_id)
                                <img src="https://img.youtube.com/vi/{{ $youtube_id }}/mqdefault.jpg" class="video-thumbnail" alt="{{ $video->title }}">
                            @else
                                <div style="position: absolute; top:0; left:0; width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#1e1e30; color:#52525b">
                                    Format Vídeo
                                </div>
                            @endif
                            <div class="play-overlay">
                                <svg class="play-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                                </svg>
                            </div>
                        </div>
                        <div class="video-body">
                            <h3 class="video-title">{{ $video->title }}</h3>
                            <p class="video-desc">{{ Str::limit($video->description, 80) }}</p>
                            <div class="video-meta">
                                <span>{{ $video->formatted_published_at }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div style="text-align:center; padding:3rem; color:#52525b">
                Aquesta sèrie encara no té cap vídeo associat.
            </div>
        @endif
    </div>
</x-videos-app-layout>
