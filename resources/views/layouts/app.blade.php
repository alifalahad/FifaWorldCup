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
            <footer class="bg-gray-900 border-t border-gray-800 mt-16 text-gray-300">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-10">
                        <div>
                            <div class="flex items-center gap-2 mb-4 group">
                                <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center text-white font-bold shadow-md">
                                    WC
                                </div>
                                <span class="font-extrabold text-xl tracking-tight text-white">
                                    FIFA Manager
                                </span>
                            </div>
                            <p class="text-sm text-gray-400 leading-relaxed mb-6">
                                The ultimate database for tracking tournaments, teams, players, matches, and statistics. Built with modern web technologies and Oracle Database.
                            </p>
                            @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-lg transition-colors">
                                Admin Dashboard
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                            </a>
                            @endauth
                        </div>
                        
                        <div class="sm:ml-auto">
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Quick Links</h3>
                            <ul class="space-y-3 text-sm">
                                <li><a href="{{ route('home') }}" class="hover:text-indigo-400 transition-colors flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-gray-600"></span> Home</a></li>
                                <li><a href="{{ route('tournaments.index') }}" class="hover:text-indigo-400 transition-colors flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-gray-600"></span> Tournaments</a></li>
                                <li><a href="{{ route('teams.index') }}" class="hover:text-indigo-400 transition-colors flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-gray-600"></span> Teams</a></li>
                                <li><a href="{{ route('players.index') }}" class="hover:text-indigo-400 transition-colors flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-gray-600"></span> Players</a></li>
                            </ul>
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Search</h3>
                            <form method="GET" action="{{ route('search') }}" class="flex gap-2">
                                <input type="search" name="q" placeholder="Search database…"
                                       class="flex-1 bg-gray-800 border-gray-700 text-white rounded-lg text-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent placeholder-gray-500">
                                <button type="submit" class="bg-indigo-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-500 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z" /></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="mt-12 pt-8 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="text-sm text-gray-500">
                            &copy; {{ date('Y') }} FIFA WC Manager. All rights reserved.
                        </div>
                        <div class="text-xs text-gray-600 flex items-center gap-1">
                            Built with Laravel 
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-red-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" /></svg>
                            & Oracle
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
