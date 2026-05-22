<x-videos-app-layout>
    <style>
        .form-container { max-width: 600px; margin: 3rem auto; padding: 2.5rem; background: linear-gradient(135deg, #1e1e30, #1a1a2e); border: 1px solid rgba(99, 102, 241, 0.15); border-radius: 16px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3); }
        .form-header { margin-bottom: 2rem; }
        .form-header h1 { font-size: 1.5rem; font-weight: 700; color: #fff; margin-bottom: 0.5rem; }
        .form-header p { color: #a1a1aa; font-size: 0.875rem; }
        .form-group { margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 0.5rem; }
        .form-group label { font-size: 0.875rem; font-weight: 500; color: #d4d4d8; }
        .form-control { width: 100%; padding: 0.75rem 1rem; background: #0f0f17; border: 1px solid rgba(99, 102, 241, 0.2); border-radius: 8px; color: #fff; font-size: 0.875rem; transition: all 0.2s; }
        .form-control:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25); }
        .checkbox-group { display: flex; align-items: center; gap: 0.75rem; margin-top: 0.5rem; }
        .checkbox-group input { width: 1.2rem; height: 1.2rem; accent-color: #6366f1; cursor: pointer; }
        .checkbox-group label { color: #d4d4d8; font-size: 0.875rem; cursor: pointer; }
        .form-actions { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 0.75rem 1.5rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; text-decoration: none; transition: all 0.2s; cursor: pointer; border: none; }
        .btn-secondary { background: rgba(255, 255, 255, 0.05); color: #d4d4d8; border: 1px solid rgba(255, 255, 255, 0.1); }
        .btn-secondary:hover { background: rgba(255, 255, 255, 0.1); color: #fff; }
        .btn-primary { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2); }
        .btn-primary:hover { box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4); transform: translateY(-1px); }
        .error-msg { color: #f87171; font-size: 0.75rem; margin-top: 0.25rem; }
    </style>

    <div class="form-container">
        <div class="form-header">
            <h1>Afegir Nou Usuari</h1>
            <p>Crea un nou compte d'usuari omplint el següent formulari.</p>
        </div>

        <form method="POST" action="{{ route('users.manage.store') }}">
            @csrf

            <div class="form-group">
                <label for="name">Nom Complet</label>
                <input type="text" name="name" id="name" class="form-control" data-qa="user-name" value="{{ old('name') }}" required placeholder="Ex. Joan Garcia">
                @error('name')
                    <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Correu Electrònic</label>
                <input type="email" name="email" id="email" class="form-control" data-qa="user-email" value="{{ old('email') }}" required placeholder="Ex. joan@iesebre.com">
                @error('email')
                    <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Contrasenya</label>
                <input type="password" name="password" id="password" class="form-control" data-qa="user-password" required placeholder="Mínim 8 caràcters">
                @error('password')
                    <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group checkbox-group">
                <input type="checkbox" name="super_admin" id="super_admin" data-qa="user-super-admin" value="1" {{ old('super_admin') ? 'checked' : '' }}>
                <label for="super_admin">Administrador global (Super Admin)</label>
            </div>

            <div class="form-actions">
                <a href="{{ route('users.manage.index') }}" class="btn btn-secondary">Cancel·lar</a>
                <button type="submit" class="btn btn-primary" data-qa="btn-submit-user">Crear Usuari</button>
            </div>
        </form>
    </div>
</x-videos-app-layout>
