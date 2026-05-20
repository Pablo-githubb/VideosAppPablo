<x-videos-app-layout>
    <style>
        .form-container { max-width: 720px; margin: 0 auto; padding: 2rem; }
        .form-header { margin-bottom: 2rem; }
        .form-header h1 { font-size: 1.75rem; font-weight: 700; color: #f4f4f5; }
        .form-header p { color: #71717a; font-size: 0.875rem; margin-top: 0.5rem; }
        .form-card { background: linear-gradient(135deg, #1e1e30, #1a1a2e); border: 1px solid rgba(99, 102, 241, 0.1); border-radius: 16px; padding: 2rem; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; font-size: 0.8125rem; font-weight: 600; color: #a1a1aa; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em; }
        .form-group input, .form-group textarea { width: 100%; padding: 0.75rem 1rem; background: rgba(0,0,0,0.3); border: 1px solid rgba(99, 102, 241, 0.15); border-radius: 10px; color: #f4f4f5; font-size: 0.9375rem; font-family: inherit; transition: border-color 0.2s; outline: none; }
        .form-group input:focus, .form-group textarea:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15); }
        .form-group textarea { min-height: 120px; resize: vertical; }
        .form-actions { display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem; }
        .btn-submit { padding: 0.625rem 2rem; background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff; border: none; border-radius: 10px; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3); }
        .btn-submit:hover { box-shadow: 0 4px 16px rgba(99, 102, 241, 0.5); transform: translateY(-1px); }
        .btn-cancel { padding: 0.625rem 2rem; background: transparent; color: #a1a1aa; border: 1px solid rgba(161, 161, 170, 0.3); border-radius: 10px; font-size: 0.875rem; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; transition: all 0.2s; }
        .btn-cancel:hover { color: #fff; border-color: rgba(161, 161, 170, 0.5); }
        .error-msg { color: #f87171; font-size: 0.8125rem; margin-top: 0.375rem; }
    </style>

    <div class="form-container">
        <div class="form-header">
            <h1>Editar Vídeo</h1>
            <p>Modifica les dades del vídeo "{{ $video->title }}".</p>
        </div>

        <div class="form-card">
            <form method="POST" action="{{ route('videos.manage.update', $video->id) }}" data-qa="form-edit-video">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="title">Títol</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $video->title) }}" required data-qa="input-title">
                    @error('title')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Descripció</label>
                    <textarea id="description" name="description" required data-qa="input-description">{{ old('description', $video->description) }}</textarea>
                    @error('description')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="url">URL del Vídeo</label>
                    <input type="url" id="url" name="url" value="{{ old('url', $video->url) }}" required data-qa="input-url">
                    @error('url')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-actions">
                    <a href="{{ route('videos.manage.index') }}" class="btn-cancel">Cancel·lar</a>
                    <button type="submit" class="btn-submit" data-qa="btn-submit">Actualitzar Vídeo</button>
                </div>
            </form>
        </div>
    </div>
</x-videos-app-layout>
