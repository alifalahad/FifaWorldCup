<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Laravel')) — {{ config('app.name') }}</title>
        @hasSection('meta_description')
        <meta name="description" content="@yield('meta_description')">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Flash Messages (public layout) -->
            @if(session('success') || session('error') || session('info'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4" x-data="{ show: true }" x-show="show">
                @if(session('success'))
                <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 mb-3 shadow-sm text-sm font-medium">
                    <span class="text-emerald-500 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    {{ session('success') }}
                    <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                @endif
                @if(session('error'))
                <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 mb-3 shadow-sm text-sm font-medium">
                    <span class="text-red-500 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    {{ session('error') }}
                    <button @click="show = false" class="ml-auto text-red-400 hover:text-red-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                @endif
                @if(session('info'))
                <div class="flex items-center gap-3 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl px-4 py-3 mb-3 shadow-sm text-sm font-medium">
                    <span class="text-blue-500 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    {{ session('info') }}
                    <button @click="show = false" class="ml-auto text-blue-400 hover:text-blue-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                @endif
            </div>
            @endif

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 mt-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-3">Navigate</h3>
                            <ul class="space-y-2 text-sm">
                                <li><a href="{{ route('home') }}" class="text-gray-600 hover:text-indigo-600 transition">Home</a></li>
                                <li><a href="{{ route('tournaments.index') }}" class="text-gray-600 hover:text-indigo-600 transition">Tournaments</a></li>
                                <li><a href="{{ route('teams.index') }}" class="text-gray-600 hover:text-indigo-600 transition">Teams</a></li>
                                <li><a href="{{ route('players.index') }}" class="text-gray-600 hover:text-indigo-600 transition">Players</a></li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-3">Search</h3>
                            <form method="GET" action="{{ route('search') }}" class="flex gap-2">
                                <input type="search" name="q" placeholder="Search…"
                                       class="flex-1 border border-gray-300 rounded-lg text-sm px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                <button type="submit" class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-indigo-700 transition">Go</button>
                            </form>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-3">{{ config('app.name') }}</h3>
                            <p class="text-xs text-gray-500 leading-relaxed">FIFA World Cup Database — tracking tournaments, teams, players, matches and statistics.</p>
                            @auth
                            <a href="{{ route('dashboard') }}" class="inline-block mt-3 text-xs text-indigo-600 hover:text-indigo-800 font-medium">Admin Dashboard →</a>
                            @endauth
                        </div>
                    </div>
                    <div class="mt-8 pt-6 border-t border-gray-100 text-center text-xs text-gray-400">
                        © {{ date('Y') }} {{ config('app.name') }}. Built with Laravel &amp; Oracle Database.
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
