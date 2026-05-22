<x-videos-app-layout>
    <style>
        .series-container { max-width: 1280px; margin: 0 auto; padding: 2rem; }
        .series-header { margin-bottom: 3rem; text-align: center; }
        .series-header h1 { font-size: 2.25rem; font-weight: 800; background: linear-gradient(135deg, #fff 0%, #a1a1aa 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 0.5rem; }
        .series-header p { color: #a1a1aa; font-size: 1rem; }
        
        .search-bar { max-width: 600px; margin: 0 auto 3rem; position: relative; }
        .search-input { width: 100%; padding: 0.875rem 1.5rem 0.875rem 3rem; background: rgba(30, 30, 48, 0.5); border: 1px solid rgba(99, 102, 241, 0.2); border-radius: 9999px; color: #fff; font-size: 0.9375rem; backdrop-filter: blur(10px); transition: all 0.3s; }
        .search-input:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 15px rgba(99, 102, 241, 0.25); }
        .search-icon { position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: #71717a; }
        
        /* Premium Cards Grid */
        .cards-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem; margin-bottom: 3rem; }
        .series-card { background: linear-gradient(135deg, #1e1e30, #131324); border: 1px solid rgba(99, 102, 241, 0.1); border-radius: 16px; overflow: hidden; transition: all 0.3s ease; display: flex; flex-direction: column; text-decoration: none; color: inherit; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); }
        .series-card:hover { transform: translateY(-5px); box-shadow: 0 12px 30px rgba(99, 102, 241, 0.15); border-color: rgba(99, 102, 241, 0.3); }
        
        .card-img-wrapper { position: relative; width: 100%; padding-top: 56.25%; background: #0c0c14; overflow: hidden; }
        .card-img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
        .series-card:hover .card-img { transform: scale(1.05); }
        
        .card-body { padding: 1.5rem; flex: 1; display: flex; flex-direction: column; }
        .card-title { font-size: 1.25rem; font-weight: 700; color: #fff; margin-bottom: 0.75rem; line-height: 1.3; }
        .card-desc { font-size: 0.875rem; color: #a1a1aa; line-height: 1.5; margin-bottom: 1.5rem; flex: 1; }
        
        .card-meta { display: flex; align-items: center; justify-content: space-between; border-top: 1px solid rgba(255, 255, 255, 0.05); padding-top: 1rem; margin-top: auto; }
        .creator-info { display: flex; align-items: center; gap: 0.5rem; }
        .creator-avatar { width: 28px; height: 28px; border-radius: 50%; object-fit: cover; border: 1px solid rgba(99, 102, 241, 0.3); }
        .creator-name { font-size: 0.8125rem; color: #d4d4d8; font-weight: 500; }
        .videos-count { font-size: 0.75rem; color: #6366f1; font-weight: 600; background: rgba(99, 102, 241, 0.1); padding: 0.25rem 0.75rem; border-radius: 9999px; }
        
        /* Empty state */
        .empty-state { text-align: center; padding: 5rem 2rem; background: linear-gradient(135deg, #181829, #11111f); border: 1px dashed rgba(99, 102, 241, 0.2); border-radius: 16px; max-width: 600px; margin: 0 auto; }
        .empty-icon { width: 48px; height: 48px; color: #52525b; margin: 0 auto 1rem; }
        .empty-title { font-size: 1.25rem; font-weight: 600; color: #fff; margin-bottom: 0.5rem; }
        .empty-desc { font-size: 0.875rem; color: #71717a; }
    </style>

    <div class="series-container">
        <div class="series-header">
            <h1>Explora les nostres Sèries</h1>
            <p>Rutes d'aprenentatge completes organitzades per temàtiques.</p>
        </div>

        <form action="{{ route('series.index') }}" method="GET" class="search-bar">
            <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.637 10.637Z" />
            </svg>
            <input type="text" name="search" class="search-input" placeholder="Cerca sèries..." value="{{ $search }}">
        </form>

        @if($series->count() > 0)
            <div class="cards-grid">
                @foreach($series as $serie)
                    <a href="{{ route('series.show', $serie->id) }}" class="series-card" data-qa="serie-card-{{ $serie->id }}">
                        <div class="card-img-wrapper">
                            @if($serie->image)
                                <img src="{{ $serie->image }}" class="card-img" alt="{{ $serie->title }}">
                            @else
                                <div style="position: absolute; top:0; left:0; width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#181829; color:#52525b">
                                    Sense Imatge
                                </div>
                            @endif
                        </div>
                        <div class="card-body">
                            <h2 class="card-title">{{ $serie->title }}</h2>
                            <p class="card-desc">{{ Str::limit($serie->description, 100) }}</p>
                            <div class="card-meta">
                                <div class="creator-info">
                                    @if($serie->user_photo_url)
                                        <img src="{{ $serie->user_photo_url }}" class="creator-avatar" alt="{{ $serie->user_name }}">
                                    @else
                                        <div class="creator-avatar" style="background:#6366f1; color:#fff; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:700">
                                            {{ strtoupper(substr($serie->user_name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="creator-name">{{ $serie->user_name }}</span>
                                </div>
                                <span class="videos-count">{{ $serie->videos->count() }} Vídeos</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            
            <div style="margin-top: 2rem;">
                {{ $series->links() }}
            </div>
        @else
            <div class="empty-state">
                <svg class="empty-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <div class="empty-title">No s'han trobat sèries</div>
                <div class="empty-desc">No hi ha cap sèrie que coincideixi amb la teva cerca. Torna a intentar-ho!</div>
            </div>
        @endif
    </div>
</x-videos-app-layout>
