<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'FIFA World Cup') — FIFA WC Manager</title>
    <meta name="description" content="@yield('meta_description', 'FIFA World Cup Management System — tournaments, teams, players, fixtures and standings.')">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 antialiased min-h-screen flex flex-col">

    {{-- =====================================================================
         TOP NAVIGATION BAR
         ===================================================================== --}}
    <nav class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                {{-- Brand / Logo --}}
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-2xl" aria-hidden="true">⚽</span>
                    <a href="{{ url('/') }}"
                       class="text-lg font-semibold text-gray-800 hover:text-green-600 transition-colors">
                        FIFA WC Manager
                    </a>
                </div>

                {{-- Primary Navigation Links --}}
                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ url('/') }}"
                       id="nav-home"
                       class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors
                              {{ request()->is('/') ? 'bg-green-50 text-green-700 font-semibold' : '' }}">
                        Home
                    </a>
                    <a href="{{ url('/tournaments') }}"
                       id="nav-tournaments"
                       class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors
                              {{ request()->is('tournaments*') ? 'bg-green-50 text-green-700 font-semibold' : '' }}">
                        Tournaments
                    </a>
                    <a href="{{ url('/teams') }}"
                       id="nav-teams"
                       class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors
                              {{ request()->is('teams*') ? 'bg-green-50 text-green-700 font-semibold' : '' }}">
                        Teams
                    </a>
                    <a href="{{ url('/players') }}"
                       id="nav-players"
                       class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors
                              {{ request()->is('players*') ? 'bg-green-50 text-green-700 font-semibold' : '' }}">
                        Players
                    </a>
                    <a href="{{ url('/fixtures') }}"
                       id="nav-fixtures"
                       class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors
                              {{ request()->is('fixtures*') ? 'bg-green-50 text-green-700 font-semibold' : '' }}">
                        Fixtures
                    </a>
                    <a href="{{ url('/standings') }}"
                       id="nav-standings"
                       class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors
                              {{ request()->is('standings*') ? 'bg-green-50 text-green-700 font-semibold' : '' }}">
                        Standings
                    </a>
                </div>

                {{-- Auth Area (right side) --}}
                <div class="hidden md:flex items-center gap-2">
                    @auth
                        {{-- Admin link — only visible to ADMIN role (wired up in Prompt 10) --}}
                        @if(auth()->user()->role && auth()->user()->role->role_name === 'ADMIN')
                            <a href="{{ url('/admin/dashboard') }}"
                               id="nav-admin-dashboard"
                               class="px-3 py-2 rounded-md text-sm font-medium text-indigo-600 hover:bg-indigo-50 transition-colors">
                                Admin
                            </a>
                        @endif

                        <span class="text-sm text-gray-500">{{ auth()->user()->username ?? auth()->user()->name }}</span>

                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                    id="btn-logout"
                                    class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-red-600 transition-colors">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                           id="nav-login"
                           class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                            Login
                        </a>
                        <a href="{{ route('register') }}"
                           id="nav-register"
                           class="px-4 py-2 rounded-md text-sm font-medium bg-green-600 text-white hover:bg-green-700 transition-colors">
                            Register
                        </a>
                    @endauth
                </div>

                {{-- Mobile hamburger button --}}
                <button id="mobile-menu-btn"
                        type="button"
                        class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:bg-gray-100 focus:outline-none"
                        aria-controls="mobile-menu"
                        aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    {{-- Hamburger icon --}}
                    <svg id="icon-hamburger" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    {{-- Close icon (hidden by default) --}}
                    <svg id="icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

            </div>
        </div>

        {{-- Mobile Menu (collapsed by default) --}}
        <div id="mobile-menu" class="md:hidden hidden border-t border-gray-100">
            <div class="px-3 pt-2 pb-3 space-y-1">
                <a href="{{ url('/') }}"
                   class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100">Home</a>
                <a href="{{ url('/tournaments') }}"
                   class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100">Tournaments</a>
                <a href="{{ url('/teams') }}"
                   class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100">Teams</a>
                <a href="{{ url('/players') }}"
                   class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100">Players</a>
                <a href="{{ url('/fixtures') }}"
                   class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100">Fixtures</a>
                <a href="{{ url('/standings') }}"
                   class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100">Standings</a>

                <div class="border-t border-gray-100 pt-2 mt-2">
                    @auth
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="block w-full text-left px-3 py-2 rounded-md text-sm font-medium text-red-600 hover:bg-gray-100">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                           class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100">Login</a>
                        <a href="{{ route('register') }}"
                           class="block px-3 py-2 rounded-md text-sm font-medium text-green-600 hover:bg-gray-100">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- =====================================================================
         FLASH MESSAGES
         ===================================================================== --}}
    @if(session('success'))
        <div id="flash-success"
             class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4"
             role="alert">
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-md px-4 py-3 text-sm flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-green-600 hover:text-green-800 ml-4">&times;</button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div id="flash-error"
             class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4"
             role="alert">
            <div class="bg-red-50 border border-red-200 text-red-800 rounded-md px-4 py-3 text-sm flex justify-between items-center">
                <span>{{ session('error') }}</span>
                <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800 ml-4">&times;</button>
            </div>
        </div>
    @endif

    @if(session('warning'))
        <div id="flash-warning"
             class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4"
             role="alert">
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-md px-4 py-3 text-sm flex justify-between items-center">
                <span>{{ session('warning') }}</span>
                <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-yellow-600 hover:text-yellow-800 ml-4">&times;</button>
            </div>
        </div>
    @endif

    {{-- =====================================================================
         MAIN CONTENT
         ===================================================================== --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- =====================================================================
         FOOTER
         ===================================================================== --}}
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row justify-between items-center gap-2">
            <p class="text-sm text-gray-500">
                ⚽ FIFA World Cup Manager &mdash; Lab Project
            </p>
            <p class="text-xs text-gray-400">
                Built with Laravel &amp; Oracle DB
            </p>
        </div>
    </footer>

    {{-- Mobile menu toggle script --}}
    <script>
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');
        const iconOpen = document.getElementById('icon-hamburger');
        const iconClose = document.getElementById('icon-close');

        btn.addEventListener('click', () => {
            const isOpen = !menu.classList.contains('hidden');
            menu.classList.toggle('hidden', isOpen);
            iconOpen.classList.toggle('hidden', !isOpen);
            iconClose.classList.toggle('hidden', isOpen);
            btn.setAttribute('aria-expanded', String(!isOpen));
        });
    </script>

    {{-- Page-specific scripts slot --}}
    @stack('scripts')

</body>
</html>
