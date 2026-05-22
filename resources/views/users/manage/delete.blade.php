<x-videos-app-layout>
    <style>
        .delete-container { max-width: 600px; margin: 4rem auto; padding: 2.5rem; background: linear-gradient(135deg, #2a1b1b, #1a1515); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 16px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4); text-align: center; }
        .warning-icon { display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 50%; color: #ef4444; margin-bottom: 1.5rem; }
        .delete-header h1 { font-size: 1.5rem; font-weight: 700; color: #fff; margin-bottom: 0.75rem; }
        .delete-header p { color: #fca5a5; font-size: 0.9375rem; margin-bottom: 2rem; line-height: 1.5; }
        .user-card { background: rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.05); padding: 1.25rem; border-radius: 10px; margin-bottom: 2rem; text-align: left; }
        .user-card-row { display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem; }
        .user-card-row:last-child { margin-bottom: 0; }
        .user-card-label { color: #a1a1aa; font-weight: 500; }
        .user-card-value { color: #e4e4e7; font-weight: 600; }
        .form-actions { display: flex; justify-content: center; gap: 1rem; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 0.75rem 1.75rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; text-decoration: none; transition: all 0.2s; cursor: pointer; border: none; }
        .btn-secondary { background: rgba(255, 255, 255, 0.05); color: #d4d4d8; border: 1px solid rgba(255, 255, 255, 0.1); }
        .btn-secondary:hover { background: rgba(255, 255, 255, 0.1); color: #fff; }
        .btn-danger { background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); }
        .btn-danger:hover { box-shadow: 0 6px 20px rgba(239, 68, 68, 0.5); transform: translateY(-1px); }
    </style>

    <div class="delete-container">
        <div class="warning-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:32px;height:32px">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
        </div>

        <div class="delete-header">
            <h1>Confirmar Eliminació d'Usuari</h1>
            <p>Estàs segur que vols eliminar permanentment aquest usuari? Aquesta acció no es pot desfer i es perdran totes les dades i associacions relacionades.</p>
        </div>

        <div class="user-card">
            <div class="user-card-row">
                <span class="user-card-label">Nom:</span>
                <span class="user-card-value">{{ $user->name }}</span>
            </div>
            <div class="user-card-row">
                <span class="user-card-label">Correu electrònic:</span>
                <span class="user-card-value">{{ $user->email }}</span>
            </div>
            <div class="user-card-row">
                <span class="user-card-label">Rol:</span>
                <span class="user-card-value">{{ $user->isSuperAdmin() ? 'Super Admin' : 'Usuari' }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('users.manage.destroy', $user->id) }}">
            @csrf
            @method('DELETE')

            <div class="form-actions">
                <a href="{{ route('users.manage.index') }}" class="btn btn-secondary">Cancel·lar</a>
                <button type="submit" class="btn btn-danger" data-qa="btn-confirm-delete">Eliminar Usuari</button>
            </div>
        </form>
    </div>
</x-videos-app-layout>
