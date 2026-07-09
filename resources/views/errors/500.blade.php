<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 — Server Error | {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-slate-900 via-red-950 to-slate-900 min-h-screen flex items-center justify-center px-4">
    <div class="text-center max-w-lg">
        <div class="mb-6">
            <span class="text-8xl">⚠️</span>
        </div>
        <div class="mb-4">
            <span class="text-7xl font-black text-white tracking-tighter">500</span>
        </div>
        <h1 class="text-2xl font-bold text-white mb-3">Server Error</h1>
        <p class="text-slate-400 text-base leading-relaxed mb-8">
            Something went wrong on our end. Our team has been notified.<br>
            Please try again in a moment.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url('/') }}"
               class="bg-red-600 hover:bg-red-700 text-white font-semibold px-8 py-3 rounded-xl transition-all duration-200 shadow-lg shadow-red-900/40">
                ← Back to Home
            </a>
            <button onclick="window.location.reload()"
               class="bg-white/10 hover:bg-white/20 text-white font-semibold px-8 py-3 rounded-xl border border-white/20 transition-all duration-200 cursor-pointer">
                Try Again
            </button>
        </div>
        <p class="mt-12 text-slate-700 text-xs">{{ config('app.name') }} &bull; FIFA World Cup Database</p>
    </div>
</body>
</html>
