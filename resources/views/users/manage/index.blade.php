<x-videos-app-layout>
    <style>
        .manage-container { max-width: 1280px; margin: 0 auto; padding: 2rem; }
        .manage-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; }
        .manage-header h1 { font-size: 1.75rem; font-weight: 700; color: #f4f4f5; }
        .btn-create { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.5rem; background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff; border-radius: 10px; font-size: 0.875rem; font-weight: 600; text-decoration: none; transition: all 0.2s ease; box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3); }
        .btn-create:hover { box-shadow: 0 4px 16px rgba(99, 102, 241, 0.5); transform: translateY(-1px); }
        .users-table { width: 100%; border-collapse: separate; border-spacing: 0; background: linear-gradient(135deg, #1e1e30, #1a1a2e); border: 1px solid rgba(99, 102, 241, 0.1); border-radius: 12px; overflow: hidden; }
        .users-table thead { background: rgba(99, 102, 241, 0.08); }
        .users-table th { padding: 1rem 1.25rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #a1a1aa; text-transform: uppercase; letter-spacing: 0.05em; }
        .users-table td { padding: 1rem 1.25rem; font-size: 0.875rem; color: #d4d4d8; border-top: 1px solid rgba(99, 102, 241, 0.06); }
        .users-table tr:hover td { background: rgba(99, 102, 241, 0.04); }
        .role-badge { display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; }
        .role-admin { background: rgba(236, 72, 153, 0.15); color: #f472b6; border: 1px solid rgba(236, 72, 153, 0.2); }
        .role-user { background: rgba(99, 102, 241, 0.15); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.2); }
        .actions { display: flex; gap: 0.5rem; }
        .btn-action { display: inline-flex; align-items: center; padding: 0.375rem 0.875rem; border-radius: 6px; font-size: 0.8125rem; font-weight: 500; text-decoration: none; transition: all 0.2s ease; }
        .btn-edit { color: #818cf8; border: 1px solid rgba(129, 140, 248, 0.3); }
        .btn-edit:hover { background: rgba(129, 140, 248, 0.1); }
        .btn-delete { color: #f87171; border: 1px solid rgba(248, 113, 113, 0.3); }
        .btn-delete:hover { background: rgba(248, 113, 113, 0.1); }
    </style>

    <div class="manage-container">
        <div class="manage-header">
            <h1>Gestió d'Usuaris</h1>
            <a href="{{ route('users.manage.create') }}" class="btn-create" data-qa="btn-create-user">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Afegir Usuari
            </a>
        </div>

        <table class="users-table" data-qa="users-manage-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Correu Electrònic</th>
                    <th>Rol</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->isSuperAdmin())
                                <span class="role-badge role-admin">Super Admin</span>
                            @else
                                <span class="role-badge role-user">Usuari</span>
                            @endif
                        </td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('users.manage.edit', $user->id) }}" class="btn-action btn-edit" data-qa="btn-edit-{{ $user->id }}">Editar</a>
                                <a href="{{ route('users.manage.delete', $user->id) }}" class="btn-action btn-delete" data-qa="btn-delete-{{ $user->id }}">Eliminar</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;color:#52525b;padding:2rem">No hi ha usuaris.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-videos-app-layout>
