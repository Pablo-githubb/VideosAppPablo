<x-videos-app-layout>
    <style>
        .profile-container { max-width: 1280px; margin: 0 auto; padding: 2rem; }
        
        /* Profile Header */
        .profile-header { display: flex; align-items: center; gap: 2rem; padding: 2.5rem; background: linear-gradient(135deg, #1e1e30, #161625); border: 1px solid rgba(99, 102, 241, 0.15); border-radius: 20px; margin-bottom: 3rem; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2); }
        .profile-avatar { display: flex; align-items: center; justify-content: center; width: 96px; height: 96px; border-radius: 18px; background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff; font-weight: 800; font-size: 2.25rem; text-transform: uppercase; box-shadow: 0 6px 20px rgba(99, 102, 241, 0.3); flex-shrink: 0; }
        .profile-meta { display: flex; flex-direction: column; gap: 0.5rem; }
        .profile-name { font-size: 1.875rem; font-weight: 800; color: #fff; }
        .profile-email { font-size: 0.9375rem; color: #a1a1aa; display: flex; align-items: center; gap: 0.5rem; }
        .profile-role-badge { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 1rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; width: fit-content; margin-top: 0.25rem; }
        .badge-admin { background: rgba(236, 72, 153, 0.15); color: #f472b6; border: 1px solid rgba(236, 72, 153, 0.2); }
        .badge-creator { background: rgba(99, 102, 241, 0.15); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.2); }
        
        /* Videos list */
        .section-title { font-size: 1.5rem; font-weight: 700; color: #fff; margin-bottom: 1.5rem; border-left: 4px solid #6366f1; padding-left: 0.75rem; }
        .videos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; }
        .video-card { display: flex; flex-direction: column; background: linear-gradient(135deg, #1e1e30, #1a1a2e); border: 1px solid rgba(99, 102, 241, 0.1); border-radius: 16px; overflow: hidden; text-decoration: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); height: 100%; }
        .video-card:hover { border-color: rgba(99, 102, 241, 0.4); transform: translateY(-4px); box-shadow: 0 10px 25px rgba(99, 102, 241, 0.15); }
        
        /* Video Card elements */
        .video-thumbnail-sim { position: relative; width: 100%; aspect-ratio: 16/9; background: #0f0f17; display: flex; align-items: center; justify-content: center; }
        .video-thumbnail-sim::after { content: ''; position: absolute; inset: 0; background: linear-gradient(to bottom, transparent, rgba(0, 0, 0, 0.6)); }
        .play-button-sim { display: flex; align-items: center; justify-content: center; width: 48px; height: 48px; background: rgba(99, 102, 241, 0.9); border-radius: 50%; color: #fff; z-index: 10; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); transition: all 0.2s ease; }
        .video-card:hover .play-button-sim { background: #8b5cf6; transform: scale(1.1); }
        
        .video-content { padding: 1.25rem; display: flex; flex-direction: column; gap: 0.75rem; flex-grow: 1; }
        .video-title { font-size: 1.0625rem; font-weight: 700; color: #fff; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .video-desc { font-size: 0.875rem; color: #a1a1aa; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .video-date { font-size: 0.8125rem; color: #71717a; margin-top: auto; display: flex; align-items: center; gap: 0.375rem; }
        
        .no-videos { grid-column: 1 / -1; text-align: center; padding: 4rem 2rem; background: linear-gradient(135deg, #1e1e30, #1a1a2e); border: 1px dashed rgba(99, 102, 241, 0.2); border-radius: 16px; color: #71717a; }
        .no-videos svg { width: 48px; height: 48px; color: #52525b; margin-bottom: 1rem; }
        
        @media (max-width: 768px) {
            .profile-header { flex-direction: column; text-align: center; gap: 1.25rem; padding: 1.75rem; }
            .profile-role-badge { margin: 0.25rem auto 0; }
        }
    </style>

    <div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header" data-qa="profile-header">
            <div class="profile-avatar">
                {{ $user->initials() }}
            </div>
            <div class="profile-meta">
                <h1 class="profile-name" data-qa="profile-name">{{ $user->name }}</h1>
                <div class="profile-email" data-qa="profile-email">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:16px;height:16px">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                    {{ $user->email }}
                </div>
                @if($user->isSuperAdmin())
                    <span class="profile-role-badge badge-admin">Super Admin</span>
                @else
                    <span class="profile-role-badge badge-creator">Creador</span>
                @endif
            </div>
        </div>

        <!-- User's Videos -->
        <h2 class="section-title">Vídeos de {{ $user->name }}</h2>
        <div class="videos-grid" data-qa="user-videos-grid">
            @forelse($videos as $video)
                <a href="{{ route('videos.show', $video->id) }}" class="video-card" data-qa="video-card-{{ $video->id }}">
                    <div class="video-thumbnail-sim">
                        <div class="play-button-sim">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:20px;height:20px;margin-left:2px">
                                <path fill-rule="evenodd" d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div class="video-content">
                        <div class="video-title">{{ $video->title }}</div>
                        <div class="video-desc">{{ Str::limit($video->description, 100) }}</div>
                        <div class="video-date">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:14px;height:14px">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                            Publicat el {{ $video->formatted_published_at }}
                        </div>
                    </div>
                </a>
            @empty
                <div class="no-videos">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                    <p style="font-size: 1.125rem; font-weight: 600; color: #fff; margin-bottom: 0.5rem;">Encara no hi ha vídeos</p>
                    <p style="font-size: 0.875rem;">Aquest usuari no ha publicat cap vídeo fins ara.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-videos-app-layout>
