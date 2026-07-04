<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — FIFA WC Manager Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">

<div class="flex h-screen overflow-hidden">

    {{-- ── Sidebar ─────────────────────────────────────────────────────── --}}
    <aside class="w-64 bg-gray-900 text-white flex flex-col flex-shrink-0">

        {{-- Logo / brand --}}
        <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-700">
            <span class="text-2xl">⚽</span>
            <div>
                <p class="text-sm font-bold text-white leading-tight">FIFA WC Manager</p>
                <p class="text-xs text-indigo-400 font-medium">Admin Panel</p>
            </div>
        </div>

        {{-- User info --}}
        <div class="px-6 py-4 border-b border-gray-700">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Signed in as</p>
            <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
            <span class="inline-block mt-1 text-xs bg-indigo-600 text-white px-2 py-0.5 rounded-full">
                {{ Auth::user()->role?->role_name ?? 'ADMIN' }}
            </span>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

            {{-- Overview --}}
            <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 mt-1">Overview</p>
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition
                      {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <span class="text-base">📊</span> Dashboard
            </a>

            {{-- Data Management --}}
            <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 mt-5">Manage Data</p>

            <a href="{{ route('admin.tournaments.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition
                      {{ request()->routeIs('admin.tournaments.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <span class="text-base">🏆</span> Tournaments
            </a>

            <a href="{{ route('admin.teams.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition
                      {{ request()->routeIs('admin.teams.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <span class="text-base">🛡️</span> Teams
            </a>

            <a href="{{ route('admin.players.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition
                      {{ request()->routeIs('admin.players.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <span class="text-base">🧑‍🤝‍🧑</span> Players
            </a>

            <a href="{{ route('admin.coaches.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition
                      {{ request()->routeIs('admin.coaches.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <span class="text-base">👨‍💼</span> Coaches
            </a>

            <a href="{{ route('admin.stadiums.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition
                      {{ request()->routeIs('admin.stadiums.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <span class="text-base">🏟️</span> Stadiums
            </a>

            <a href="{{ route('admin.referees.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition
                      {{ request()->routeIs('admin.referees.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <span class="text-base">🟨</span> Referees
            </a>

            <a href="{{ route('admin.matches.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition
                      {{ request()->routeIs('admin.matches.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <span class="text-base">📅</span> Matches
            </a>

        </nav>

        {{-- Bottom: back to site + logout --}}
        <div class="px-3 py-4 border-t border-gray-700 space-y-1">
            <a href="{{ route('home') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm text-gray-300 hover:bg-gray-800 hover:text-white transition">
                <span class="text-base">🌐</span> View Site
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2 rounded-md text-sm text-gray-300 hover:bg-red-800 hover:text-white transition text-left">
                    <span class="text-base">🚪</span> Log Out
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Main content area ───────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Top bar --}}
        <header class="bg-white shadow-sm px-6 py-4 flex items-center justify-between flex-shrink-0">
            <div>
                <h1 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Admin Dashboard')</h1>
                @hasSection('breadcrumb')
                <p class="text-xs text-gray-400 mt-0.5">@yield('breadcrumb')</p>
                @endif
            </div>
            <div class="text-sm text-gray-500">
                {{ now()->format('D, d M Y') }}
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="mx-6 mt-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-md text-sm flex items-center gap-2">
            <span>✅</span> {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mx-6 mt-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded-md text-sm flex items-center gap-2">
            <span>❌</span> {{ session('error') }}
        </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>

</div>

</body>
</html>
