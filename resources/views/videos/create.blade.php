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
            <h1>Afegir Vídeo</h1>
        </div>

        <form action="{{ route('videos.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="title">Títol del Vídeo</label>
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
                <label for="url">URL de Vídeo (YouTube)</label>
                <input type="url" name="url" id="url" class="form-control" value="{{ old('url') }}" required data-qa="url">
                @error('url')
                    <div class="text-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="series_id">Sèrie Associada (Opcional)</label>
                <select name="series_id" id="series_id" class="form-control" data-qa="series_id">
                    <option value="">Cap sèrie</option>
                    @foreach($series as $serie)
                        <option value="{{ $serie->id }}" {{ old('series_id') == $serie->id ? 'selected' : '' }}>
                            {{ $serie->title }}
                        </option>
                    @endforeach
                </select>
                @error('series_id')
                    <div class="text-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('videos.index') }}" class="btn btn-cancel">Cancel·lar</a>
                <button type="submit" class="btn btn-submit" data-qa="btn-store-video">Guardar Vídeo</button>
            </div>
        </form>
    </div>
</x-videos-app-layout>
