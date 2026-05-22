<x-videos-app-layout>
    <style>
        .manage-container { max-width: 1280px; margin: 0 auto; padding: 2rem; }
        .manage-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; }
        .manage-header h1 { font-size: 1.75rem; font-weight: 700; color: #f4f4f5; }
        .btn-create { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.5rem; background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff; border-radius: 10px; font-size: 0.875rem; font-weight: 600; text-decoration: none; transition: all 0.2s ease; box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3); }
        .btn-create:hover { box-shadow: 0 4px 16px rgba(99, 102, 241, 0.5); transform: translateY(-1px); }
        
        /* Table Styles (Desktop) */
        .desktop-table { display: table; width: 100%; border-collapse: separate; border-spacing: 0; background: linear-gradient(135deg, #1e1e30, #1a1a2e); border: 1px solid rgba(99, 102, 241, 0.1); border-radius: 12px; overflow: hidden; }
        .desktop-table thead { background: rgba(99, 102, 241, 0.08); }
        .desktop-table th { padding: 1rem 1.25rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #a1a1aa; text-transform: uppercase; letter-spacing: 0.05em; }
        .desktop-table td { padding: 1rem 1.25rem; font-size: 0.875rem; color: #d4d4d8; border-top: 1px solid rgba(99, 102, 241, 0.06); }
        .desktop-table tr:hover td { background: rgba(99, 102, 241, 0.04); }
        
        .actions { display: flex; gap: 0.5rem; }
        .btn-action { display: inline-flex; align-items: center; padding: 0.375rem 0.875rem; border-radius: 6px; font-size: 0.8125rem; font-weight: 500; text-decoration: none; transition: all 0.2s ease; }
        .btn-edit { color: #818cf8; border: 1px solid rgba(129, 140, 248, 0.3); }
        .btn-edit:hover { background: rgba(129, 140, 248, 0.1); }
        .btn-delete { color: #f87171; border: 1px solid rgba(248, 113, 113, 0.3); }
        .btn-delete:hover { background: rgba(248, 113, 113, 0.1); }

        /* Mobile list Styles */
        .mobile-list { display: none; flex-direction: column; gap: 1rem; }
        .mobile-item { background: linear-gradient(135deg, #1e1e30, #1a1a2e); border: 1px solid rgba(99, 102, 241, 0.1); border-radius: 12px; padding: 1.25rem; }
        .mobile-title { font-size: 1rem; font-weight: 600; color: #fff; margin-bottom: 0.5rem; }
        .mobile-desc { font-size: 0.875rem; color: #a1a1aa; margin-bottom: 1rem; }
        .mobile-info { font-size: 0.8125rem; color: #71717a; margin-bottom: 1rem; display: flex; flex-direction: column; gap: 0.25rem; }
        
        @media (max-width: 768px) {
            .desktop-table { display: none; }
            .mobile-list { display: flex; }
        }
    </style>

    <div class="manage-container">
        <div class="manage-header">
            <h1>Gestió de Sèries</h1>
            <a href="{{ route('series.manage.create') }}" class="btn-create" data-qa="btn-create-serie">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Crear Sèrie
            </a>
        </div>

        <!-- Desktop Table View -->
        <table class="desktop-table" data-qa="series-manage-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imatge</th>
                    <th>Títol</th>
                    <th>Descripció</th>
                    <th>Creador</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($series as $serie)
                    <tr>
                        <td>{{ $serie->id }}</td>
                        <td>
                            @if($serie->image)
                                <img src="{{ $serie->image }}" alt="{{ $serie->title }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                            @else
                                <span style="color:#52525b">Sense imatge</span>
                            @endif
                        </td>
                        <td>{{ $serie->title }}</td>
                        <td>{{ Str::limit($serie->description, 60) }}</td>
                        <td>{{ $serie->user_name }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('series.manage.edit', $serie->id) }}" class="btn-action btn-edit" data-qa="btn-edit-{{ $serie->id }}">Editar</a>
                                <a href="{{ route('series.manage.delete', $serie->id) }}" class="btn-action btn-delete" data-qa="btn-delete-{{ $serie->id }}">Eliminar</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;color:#52525b;padding:2rem">No hi ha sèries creades.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Mobile List View -->
        <div class="mobile-list">
            @forelse($series as $serie)
                <div class="mobile-item">
                    <div class="mobile-title">{{ $serie->title }}</div>
                    <div class="mobile-desc">{{ Str::limit($serie->description, 100) }}</div>
                    <div class="mobile-info">
                        <span><strong>ID:</strong> {{ $serie->id }}</span>
                        <span><strong>Creador:</strong> {{ $serie->user_name }}</span>
                    </div>
                    <div class="actions">
                        <a href="{{ route('series.manage.edit', $serie->id) }}" class="btn-action btn-edit" data-qa="btn-edit-mobile-{{ $serie->id }}">Editar</a>
                        <a href="{{ route('series.manage.delete', $serie->id) }}" class="btn-action btn-delete" data-qa="btn-delete-mobile-{{ $serie->id }}">Eliminar</a>
                    </div>
                </div>
            @empty
                <div style="text-align:center;color:#52525b;padding:2rem">No hi ha sèries creades.</div>
            @endforelse
        </div>
    </div>
</x-videos-app-layout>
