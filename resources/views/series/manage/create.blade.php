<x-videos-app-layout>
    <style>
        .form-container { max-width: 600px; margin: 3rem auto; padding: 2rem; background: linear-gradient(135deg, #1e1e30, #1a1a2e); border: 1px solid rgba(99, 102, 241, 0.1); border-radius: 12px; }
        .form-header { margin-bottom: 2rem; }
        .form-header h1 { font-size: 1.5rem; font-weight: 700; color: #fff; }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; font-size: 0.875rem; font-weight: 500; color: #a1a1aa; margin-bottom: 0.5rem; }
        .form-control { width: 100%; padding: 0.75rem 1rem; background: #0f0f17; border: 1px solid rgba(99, 102, 241, 0.2); border-radius: 8px; color: #fff; font-size: 0.875rem; transition: border-color 0.2s; }
        .form-control:focus { outline: none; border-color: #6366f1; }
        .text-error { color: #f87171; font-size: 0.75rem; margin-top: 0.25rem; }
        .form-actions { display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 0.625rem 1.5rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; text-decoration: none; transition: all 0.2s; cursor: pointer; border: none; }
        .btn-cancel { color: #a1a1aa; background: transparent; border: 1px solid rgba(161, 161, 170, 0.2); }
        .btn-cancel:hover { background: rgba(161, 161, 170, 0.05); color: #fff; }
        .btn-submit { color: #fff; background: linear-gradient(135deg, #6366f1, #8b5cf6); }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3); }
    </style>

    <div class="form-container">
        <div class="form-header">
            <h1>Crear Nova Sèrie</h1>
        </div>

        <form action="{{ route('series.manage.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="title">Títol de la Sèrie</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required data-qa="title">
                @error('title')
                    <div class="text-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Descripció</label>
                <textarea name="description" id="description" class="form-control" rows="4" required data-qa="description">{{ old('description') }}</textarea>
                @error('description')
                    <div class="text-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="image">URL de la Imatge (Opcional)</label>
                <input type="text" name="image" id="image" class="form-control" value="{{ old('image') }}" data-qa="image">
                @error('image')
                    <div class="text-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('series.manage.index') }}" class="btn btn-cancel">Cancel·lar</a>
                <button type="submit" class="btn btn-submit" data-qa="btn-store-serie">Guardar Sèrie</button>
            </div>
        </form>
    </div>
</x-videos-app-layout>
