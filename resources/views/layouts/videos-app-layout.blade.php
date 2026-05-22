<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="VideosApp - Plataforma de vídeos educatius">

        <title>{{ config('app.name', 'Laravel Videos') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: 'Inter', sans-serif; background: #0f0f17; color: #e4e4e7; min-height: 100vh; display: flex; flex-direction: column; }
            .navbar { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border-bottom: 1px solid rgba(99, 102, 241, 0.2); padding: 0 2rem; position: sticky; top: 0; z-index: 50; backdrop-filter: blur(10px); }
            .navbar-inner { max-width: 1280px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; height: 64px; }
            .navbar-brand { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; color: #fff; font-weight: 700; font-size: 1.25rem; }
            .navbar-brand svg { width: 32px; height: 32px; color: #6366f1; }
            .navbar-links { display: flex; align-items: center; gap: 0.5rem; }
            .navbar-links a { text-decoration: none; color: #a1a1aa; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 500; transition: all 0.2s ease; }
            .navbar-links a:hover { color: #fff; background: rgba(99, 102, 241, 0.15); }
            .navbar-links a.active { color: #6366f1; background: rgba(99, 102, 241, 0.1); }
            .navbar-auth { display: flex; align-items: center; gap: 0.75rem; }
            .btn-nav { display: inline-flex; align-items: center; padding: 0.5rem 1.25rem; border-radius: 8px; font-size: 0.875rem; font-weight: 500; text-decoration: none; transition: all 0.2s ease; border: none; cursor: pointer; }
            .btn-nav-ghost { color: #a1a1aa; background: transparent; }
            .btn-nav-ghost:hover { color: #fff; }
            .btn-nav-primary { color: #fff; background: linear-gradient(135deg, #6366f1, #8b5cf6); box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3); }
            .btn-nav-primary:hover { box-shadow: 0 4px 16px rgba(99, 102, 241, 0.5); transform: translateY(-1px); }
            .btn-nav-danger { color: #fff; background: transparent; border: 1px solid rgba(239, 68, 68, 0.4); }
            .btn-nav-danger:hover { background: rgba(239, 68, 68, 0.1); }
            main { flex: 1; }
            .footer { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border-top: 1px solid rgba(99, 102, 241, 0.1); padding: 2rem; margin-top: auto; }
            .footer-inner { max-width: 1280px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; }
            .footer-text { color: #71717a; font-size: 0.8125rem; }
            .footer-links { display: flex; gap: 1.5rem; }
            .footer-links a { color: #71717a; text-decoration: none; font-size: 0.8125rem; transition: color 0.2s; }
            .footer-links a:hover { color: #a1a1aa; }
            @media (max-width: 768px) {
                .navbar { padding: 0 1rem; }
                .navbar-links { display: none; }
                .footer-inner { flex-direction: column; gap: 1rem; text-align: center; }
            }
        </style>
    </head>
    <body>
        <!-- Navbar -->
        <nav class="navbar">
            <div class="navbar-inner">
                <a href="{{ route('videos.index') }}" class="navbar-brand">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                    VideosApp
                </a>

                <div class="navbar-links">
                    <a href="{{ route('videos.index') }}">Vídeos</a>
                    @auth
                        <a href="{{ route('series.index') }}">Sèries</a>
                        <a href="{{ route('users.index') }}">Usuaris</a>
                        @can('videos_manage_index')
                            <a href="{{ route('videos.manage.index') }}">Gestió Vídeos</a>
                        @endcan
                        @can('series_manage_index')
                            <a href="{{ route('series.manage.index') }}">Gestió Sèries</a>
                        @endcan
                        @can('users_manage_index')
                            <a href="{{ route('users.manage.index') }}">Gestió Usuaris</a>
                        @endcan
                    @endauth
                </div>

                <div class="navbar-auth">
                    @auth
                        <span style="color: #a1a1aa; font-size: 0.875rem;">{{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-nav btn-nav-danger">Tancar sessió</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn-nav btn-nav-ghost">Iniciar sessió</a>
                        <a href="{{ route('register') }}" class="btn-nav btn-nav-primary">Registrar-se</a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Flash messages -->
        @if (session('success'))
            <div style="max-width: 1280px; margin: 1rem auto; padding: 1rem 1.5rem; background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); border-radius: 8px; color: #4ade80; font-size: 0.875rem;">
                {{ session('success') }}
            </div>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="footer">
            <div class="footer-inner">
                <p class="footer-text">&copy; {{ date('Y') }} VideosApp Pablo. Tots els drets reservats.</p>
                <div class="footer-links">
                    <a href="{{ route('videos.index') }}">Vídeos</a>
                    <a href="{{ route('home') }}">Inici</a>
                </div>
            </div>
        </footer>
    </body>
</html>
