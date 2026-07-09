<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Page Not Found | {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 min-h-screen flex items-center justify-center px-4">
    <div class="text-center max-w-lg">
        {{-- Ball animation --}}
        <div class="mb-6 relative inline-block">
            <span class="text-8xl animate-bounce inline-block">⚽</span>
        </div>

        {{-- Code --}}
        <div class="mb-4">
            <span class="text-7xl font-black text-white tracking-tighter">404</span>
        </div>

        {{-- Message --}}
        <h1 class="text-2xl font-bold text-white mb-3">Page Not Found</h1>
        <p class="text-slate-400 text-base leading-relaxed mb-8">
            The page you're looking for has gone off-side.<br>
            It may have been moved, deleted, or never existed.
        </p>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url('/') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-8 py-3 rounded-xl transition-all duration-200 shadow-lg shadow-indigo-900/40">
                ← Back to Home
            </a>
            <a href="{{ route('tournaments.index') }}"
               class="bg-white/10 hover:bg-white/20 text-white font-semibold px-8 py-3 rounded-xl border border-white/20 transition-all duration-200">
                Browse Tournaments
            </a>
        </div>

        {{-- Quick links --}}
        <div class="mt-10 flex flex-wrap gap-4 justify-center text-sm">
            <a href="{{ route('teams.index') }}" class="text-slate-500 hover:text-indigo-400 transition">Teams</a>
            <span class="text-slate-700">&bull;</span>
            <a href="{{ route('tournaments.index') }}" class="text-slate-500 hover:text-indigo-400 transition">Tournaments</a>
            <span class="text-slate-700">&bull;</span>
            @auth
            <a href="{{ route('dashboard') }}" class="text-slate-500 hover:text-indigo-400 transition">Dashboard</a>
            @else
            <a href="{{ route('login') }}" class="text-slate-500 hover:text-indigo-400 transition">Login</a>
            @endauth
        </div>

        <p class="mt-12 text-slate-700 text-xs">{{ config('app.name') }} &bull; FIFA World Cup Database</p>
    </div>
</body>
</html>
