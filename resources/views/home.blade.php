@extends('layouts.app')

@section('title', 'Home')
@section('meta_description', 'Welcome to the FIFA World Cup Management System. Browse tournaments, teams, players, fixtures and standings.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Hero --}}
    <div class="text-center mb-16">
        <div class="text-6xl mb-4" aria-hidden="true">⚽</div>
        <h1 class="text-4xl font-bold text-gray-900 mb-3">FIFA World Cup Manager</h1>
        <p class="text-lg text-gray-500 max-w-xl mx-auto">
            A management system for tournaments, teams, players, fixtures and standings.
            Built with Laravel &amp; Oracle DB.
        </p>
    </div>

    {{-- Quick navigation cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-16">

        <a href="{{ url('/tournaments') }}"
           id="card-tournaments"
           class="group block bg-white rounded-lg border border-gray-200 p-6 hover:border-green-400 hover:shadow-md transition-all">
            <div class="text-3xl mb-3">🏆</div>
            <h2 class="text-base font-semibold text-gray-800 group-hover:text-green-700 mb-1">Tournaments</h2>
            <p class="text-sm text-gray-500">Browse all FIFA World Cup editions, host countries and schedules.</p>
        </a>

        <a href="{{ url('/teams') }}"
           id="card-teams"
           class="group block bg-white rounded-lg border border-gray-200 p-6 hover:border-green-400 hover:shadow-md transition-all">
            <div class="text-3xl mb-3">🛡️</div>
            <h2 class="text-base font-semibold text-gray-800 group-hover:text-green-700 mb-1">Teams</h2>
            <p class="text-sm text-gray-500">View national team profiles, confederations and FIFA rankings.</p>
        </a>

        <a href="{{ url('/players') }}"
           id="card-players"
           class="group block bg-white rounded-lg border border-gray-200 p-6 hover:border-green-400 hover:shadow-md transition-all">
            <div class="text-3xl mb-3">🧑‍🤝‍🧑</div>
            <h2 class="text-base font-semibold text-gray-800 group-hover:text-green-700 mb-1">Players</h2>
            <p class="text-sm text-gray-500">Explore player profiles, positions and tournament squads.</p>
        </a>

        <a href="{{ url('/fixtures') }}"
           id="card-fixtures"
           class="group block bg-white rounded-lg border border-gray-200 p-6 hover:border-green-400 hover:shadow-md transition-all">
            <div class="text-3xl mb-3">📅</div>
            <h2 class="text-base font-semibold text-gray-800 group-hover:text-green-700 mb-1">Fixtures</h2>
            <p class="text-sm text-gray-500">Check match schedules, results and live status by group or stage.</p>
        </a>

        <a href="{{ url('/standings') }}"
           id="card-standings"
           class="group block bg-white rounded-lg border border-gray-200 p-6 hover:border-green-400 hover:shadow-md transition-all">
            <div class="text-3xl mb-3">📊</div>
            <h2 class="text-base font-semibold text-gray-800 group-hover:text-green-700 mb-1">Standings</h2>
            <p class="text-sm text-gray-500">See live group standings — points, goal difference, and ranking.</p>
        </a>

        @auth
            @if(auth()->user()->role && auth()->user()->role->role_name === 'ADMIN')
                <a href="{{ url('/admin/dashboard') }}"
                   id="card-admin"
                   class="group block bg-white rounded-lg border border-indigo-200 p-6 hover:border-indigo-400 hover:shadow-md transition-all">
                    <div class="text-3xl mb-3">⚙️</div>
                    <h2 class="text-base font-semibold text-gray-800 group-hover:text-indigo-700 mb-1">Admin Dashboard</h2>
                    <p class="text-sm text-gray-500">Manage tournaments, teams, players, matches and results.</p>
                </a>
            @endif
        @else
            <a href="{{ route('login') }}"
               id="card-login"
               class="group block bg-white rounded-lg border border-gray-200 p-6 hover:border-green-400 hover:shadow-md transition-all">
                <div class="text-3xl mb-3">🔐</div>
                <h2 class="text-base font-semibold text-gray-800 group-hover:text-green-700 mb-1">Admin Login</h2>
                <p class="text-sm text-gray-500">Sign in to access the admin panel and manage data.</p>
            </a>
        @endauth

    </div>

    {{-- Tech stack note --}}
    <div class="border-t border-gray-100 pt-8 text-center">
        <p class="text-xs text-gray-400 tracking-wide uppercase">Tech Stack</p>
        <p class="text-sm text-gray-500 mt-1">
            Laravel 13 &bull; Oracle DB (yajra/laravel-oci8) &bull; Tailwind CSS v4 &bull; Vite
        </p>
    </div>

</div>
@endsection
