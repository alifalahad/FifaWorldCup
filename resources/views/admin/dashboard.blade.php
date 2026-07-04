@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Admin › Overview')

@section('content')

{{-- ── Summary stat cards ──────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-5 gap-4 mb-8">

    {{-- Tournaments --}}
    <div class="bg-white rounded-lg border border-gray-200 p-5 flex items-center gap-4">
        <div class="text-3xl">🏆</div>
        <div>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['tournaments'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Tournaments</p>
        </div>
    </div>

    {{-- Teams --}}
    <div class="bg-white rounded-lg border border-gray-200 p-5 flex items-center gap-4">
        <div class="text-3xl">🛡️</div>
        <div>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['teams'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Teams</p>
        </div>
    </div>

    {{-- Players --}}
    <div class="bg-white rounded-lg border border-gray-200 p-5 flex items-center gap-4">
        <div class="text-3xl">🧑‍🤝‍🧑</div>
        <div>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['players'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Players</p>
        </div>
    </div>

    {{-- Matches played --}}
    <div class="bg-white rounded-lg border border-gray-200 p-5 flex items-center gap-4">
        <div class="text-3xl">📅</div>
        <div>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['matches_played'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Matches Played</p>
        </div>
    </div>

    {{-- Goals scored --}}
    <div class="bg-white rounded-lg border border-gray-200 p-5 flex items-center gap-4">
        <div class="text-3xl">⚽</div>
        <div>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['goals'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Goals Scored</p>
        </div>
    </div>

</div>

{{-- ── Secondary stats row ─────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-lg border border-gray-200 px-4 py-3 flex justify-between items-center">
        <span class="text-sm text-gray-600">Coaches</span>
        <span class="font-bold text-gray-800">{{ $stats['coaches'] }}</span>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 px-4 py-3 flex justify-between items-center">
        <span class="text-sm text-gray-600">Stadiums</span>
        <span class="font-bold text-gray-800">{{ $stats['stadiums'] }}</span>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 px-4 py-3 flex justify-between items-center">
        <span class="text-sm text-gray-600">Referees</span>
        <span class="font-bold text-gray-800">{{ $stats['referees'] }}</span>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 px-4 py-3 flex justify-between items-center">
        <span class="text-sm text-gray-600">Live Matches</span>
        <span class="font-bold {{ $stats['live_matches'] > 0 ? 'text-green-600' : 'text-gray-800' }}">
            {{ $stats['live_matches'] }}
        </span>
    </div>
</div>

{{-- ── Recent Matches table ─────────────────────────────────────────────── --}}
<div class="bg-white rounded-lg border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-700">Recent Matches</h2>
        <a href="{{ route('admin.matches.index') }}" class="text-xs text-indigo-600 hover:underline">View all →</a>
    </div>

    @if($recent_matches->isEmpty())
    <div class="px-6 py-10 text-center text-gray-400 text-sm">
        No matches recorded yet. <a href="{{ route('admin.matches.index') }}" class="text-indigo-600 hover:underline">Schedule the first match →</a>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-3 text-left">Date</th>
                    <th class="px-6 py-3 text-left">Tournament</th>
                    <th class="px-6 py-3 text-center">Home Team</th>
                    <th class="px-6 py-3 text-center">Score</th>
                    <th class="px-6 py-3 text-center">Away Team</th>
                    <th class="px-6 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($recent_matches as $match)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-3 text-gray-600 whitespace-nowrap">
                        {{ $match->match_date->format('d M Y') }}
                    </td>
                    <td class="px-6 py-3 text-gray-600 whitespace-nowrap">
                        {{ $match->tournament->name ?? '—' }}
                    </td>
                    <td class="px-6 py-3 text-right font-medium text-gray-800">
                        {{ $match->homeTeam->abbreviation ?? '—' }}
                    </td>
                    <td class="px-6 py-3 text-center font-bold text-gray-900">
                        @if($match->status === 'COMPLETED')
                            {{ $match->home_score }} – {{ $match->away_score }}
                        @else
                            vs
                        @endif
                    </td>
                    <td class="px-6 py-3 text-left font-medium text-gray-800">
                        {{ $match->awayTeam->abbreviation ?? '—' }}
                    </td>
                    <td class="px-6 py-3 text-center">
                        @php
                            $statusClasses = [
                                'COMPLETED' => 'bg-green-100 text-green-700',
                                'LIVE'      => 'bg-red-100 text-red-700 animate-pulse',
                                'SCHEDULED' => 'bg-blue-100 text-blue-700',
                                'POSTPONED' => 'bg-yellow-100 text-yellow-700',
                                'CANCELLED' => 'bg-gray-100 text-gray-500',
                            ];
                            $cls = $statusClasses[$match->status] ?? 'bg-gray-100 text-gray-500';
                        @endphp
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $cls }}">
                            {{ $match->status }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- ── Quick action buttons ─────────────────────────────────────────────── --}}
<div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3">
    <a href="{{ route('admin.tournaments.index') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-3 rounded-lg text-center transition">
        + New Tournament
    </a>
    <a href="{{ route('admin.teams.index') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-3 rounded-lg text-center transition">
        + New Team
    </a>
    <a href="{{ route('admin.players.index') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-3 rounded-lg text-center transition">
        + New Player
    </a>
    <a href="{{ route('admin.matches.index') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-3 rounded-lg text-center transition">
        + Schedule Match
    </a>
</div>

@endsection
