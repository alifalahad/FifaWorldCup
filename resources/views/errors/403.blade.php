<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — Access Denied | {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 min-h-screen flex items-center justify-center px-4">
    <div class="text-center max-w-lg">
        {{-- Icon --}}
        <div class="mb-6">
            <span class="text-8xl">🔒</span>
        </div>

        {{-- Code --}}
        <div class="inline-flex items-center gap-3 mb-4">
            <span class="text-7xl font-black text-white tracking-tighter">403</span>
        </div>

        {{-- Message --}}
        <h1 class="text-2xl font-bold text-white mb-3">Access Denied</h1>
        <p class="text-slate-400 mb-2 text-base leading-relaxed">
            You don't have permission to view this page.
        </p>
        @if(session('error'))
            <p class="text-amber-400 text-sm mb-6 bg-amber-400/10 border border-amber-400/20 rounded-lg px-4 py-3">
                {{ session('error') }}
            </p>
        @else
            <p class="text-slate-500 text-sm mb-8">
                If you believe this is a mistake, please contact an administrator.
            </p>
        @endif

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center mt-6">
            <a href="{{ url('/') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-8 py-3 rounded-xl transition-all duration-200 shadow-lg shadow-indigo-900/40">
                ← Back to Home
            </a>
            @guest
            <a href="{{ route('login') }}"
               class="bg-white/10 hover:bg-white/20 text-white font-semibold px-8 py-3 rounded-xl border border-white/20 transition-all duration-200">
                Login
            </a>
            @endguest
        </div>

        {{-- Subtle footer --}}
        <p class="mt-12 text-slate-700 text-xs">{{ config('app.name') }} &bull; FIFA World Cup Database</p>
    </div>
</body>
</html>
