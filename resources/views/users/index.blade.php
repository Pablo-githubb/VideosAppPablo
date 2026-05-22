<x-videos-app-layout>
    <style>
        .users-container { max-width: 1280px; margin: 0 auto; padding: 2rem; }
        .users-header { display: flex; flex-direction: column; gap: 1.5rem; margin-bottom: 3rem; }
        .users-title h1 { font-size: 2rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem; background: linear-gradient(to right, #fff, #a1a1aa); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .users-title p { color: #a1a1aa; font-size: 0.9375rem; }
        
        /* Search form styling */
        .search-form { display: flex; gap: 0.75rem; max-width: 600px; width: 100%; position: relative; }
        .search-input { flex: 1; padding: 0.875rem 1.25rem; background: linear-gradient(135deg, #1e1e30, #161625); border: 1px solid rgba(99, 102, 241, 0.2); border-radius: 12px; color: #fff; font-size: 0.9375rem; transition: all 0.2s ease; }
        .search-input:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15); }
        .btn-search { display: inline-flex; align-items: center; justify-content: center; padding: 0.875rem 1.75rem; background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff; font-weight: 600; font-size: 0.9375rem; border: none; border-radius: 12px; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2); }
        .btn-search:hover { box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4); transform: translateY(-1px); }
        
        /* User list grid */
        .users-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; }
        .user-card { display: flex; align-items: center; gap: 1.25rem; padding: 1.5rem; background: linear-gradient(135deg, #1e1e30, #1a1a2e); border: 1px solid rgba(99, 102, 241, 0.1); border-radius: 16px; text-decoration: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .user-card:hover { border-color: rgba(99, 102, 241, 0.4); transform: translateY(-4px); box-shadow: 0 10px 25px rgba(99, 102, 241, 0.15); }
        
        /* Profile initials avatar */
        .avatar-initials { display: flex; align-items: center; justify-content: center; width: 56px; height: 56px; border-radius: 12px; background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff; font-weight: 700; font-size: 1.25rem; text-transform: uppercase; box-shadow: 0 4px 10px rgba(99, 102, 241, 0.25); flex-shrink: 0; }
        .user-info { display: flex; flex-direction: column; gap: 0.25rem; min-width: 0; }
        .user-name { font-size: 1.0625rem; font-weight: 700; color: #fff; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .user-email { font-size: 0.8125rem; color: #a1a1aa; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .user-role { font-size: 0.75rem; color: #6366f1; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; display: inline-flex; align-items: center; gap: 0.25rem; }
        
        .no-results { grid-column: 1 / -1; text-align: center; padding: 4rem 2rem; background: linear-gradient(135deg, #1e1e30, #1a1a2e); border: 1px dashed rgba(99, 102, 241, 0.2); border-radius: 16px; color: #71717a; }
        .no-results svg { width: 48px; height: 48px; color: #52525b; margin-bottom: 1rem; }
    </style>

    <div class="users-container">
        <div class="users-header">
            <div class="users-title">
                <h1>Comunitat de VideosApp</h1>
                <p>Descobreix altres creadors de contingut i consulta els seus vídeos educatius.</p>
            </div>
            
            <form method="GET" action="{{ route('users.index') }}" class="search-form">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cerca usuaris pel seu nom o correu..." class="search-input" data-qa="search-users-input">
                <button type="submit" class="btn-search">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px;margin-right:0.25rem">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.602 10.602Z" />
                    </svg>
                    Cercar
                </button>
            </form>
        </div>

        <div class="users-grid" data-qa="users-list-grid">
            @forelse($users as $user)
                <a href="{{ route('users.show', $user->id) }}" class="user-card" data-qa="user-card-{{ $user->id }}">
                    <div class="avatar-initials">
                        {{ $user->initials() }}
                    </div>
                    <div class="user-info">
                        <div class="user-name">{{ $user->name }}</div>
                        <div class="user-email">{{ $user->email }}</div>
                        @if($user->isSuperAdmin())
                            <div class="user-role">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:12px;height:12px">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                                </svg>
                                Admin
                            </div>
                        @else
                            <div class="user-role" style="color: #818cf8;">Creador</div>
                        @endif
                    </div>
                </a>
            @empty
                <div class="no-results">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                    <p style="font-size: 1.125rem; font-weight: 600; color: #fff; margin-bottom: 0.5rem;">No s'han trobat usuaris</p>
                    <p style="font-size: 0.875rem;">Proba a cercar un nom o correu electrònic diferent.</p>
                </div>
            @endforelse
        </div>
        
        @if($users->hasPages())
            <div style="margin-top: 2rem;">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-videos-app-layout>
