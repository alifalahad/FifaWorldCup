@extends('layouts.app')

@section('title', $tournament->name . ' Statistics')
@section('meta_description', 'Top scorers, assist leaders, and disciplinary table for ' . $tournament->name)

@section('content')
{{-- Sticky sub-nav --}}
<div class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14">
            <div class="flex items-baseline gap-3">
                <a href="{{ route('tournaments.show', $tournament->tournament_id) }}" class="text-lg font-bold text-gray-900 hover:text-indigo-600 transition">
                    {{ $tournament->name }}
                </a>
                <span class="text-gray-300">/</span>
                <span class="text-indigo-600 font-semibold text-sm">Stats</span>
            </div>
            <div class="flex gap-4 text-sm font-medium">
                <a href="{{ route('tournaments.fixtures', $tournament->tournament_id) }}"
                   class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 pb-1 transition">Fixtures</a>
                <a href="{{ route('tournaments.standings', $tournament->tournament_id) }}"
                   class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 pb-1 transition">Standings</a>
                <a href="{{ route('tournaments.stats', $tournament->tournament_id) }}"
                   class="border-b-2 border-indigo-600 text-indigo-600 pb-1">Stats</a>
                <a href="{{ route('tournaments.show', $tournament->tournament_id) }}"
                   class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 pb-1 transition">Overview</a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Tournament Statistics</h1>
    <p class="text-gray-600 font-medium mb-10">{{ $tournament->name }} · {{ $tournament->year }}</p>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

        {{-- ══ 1. Top Scorers ══════════════════════════════════════════════ --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-700 to-emerald-600 px-6 py-4 flex items-center gap-3">
                <span class="text-2xl">⚽</span>
                <div>
                    <h2 class="text-lg font-extrabold text-white">Top Scorers</h2>
                    <p class="text-emerald-200 text-xs">Excluding own goals</p>
                </div>
            </div>

            @if($topScorers->isEmpty())
                <div class="p-8 text-center text-gray-600 font-medium">
                    No goals recorded yet for this tournament.
                </div>
            @else
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-600 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3 text-center w-10">#</th>
                            <th class="px-4 py-3 text-left">Player</th>
                            <th class="px-4 py-3 text-center w-12" title="Penalties">P</th>
                            <th class="px-4 py-3 text-center w-14">Goals</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($topScorers as $i => $row)
                        <tr class="{{ $i < 3 ? 'bg-emerald-50/40' : 'bg-white' }} hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-center">
                                @if($i === 0)
                                    <span class="text-lg">🥇</span>
                                @elseif($i === 1)
                                    <span class="text-lg">🥈</span>
                                @elseif($i === 2)
                                    <span class="text-lg">🥉</span>
                                @else
                                    <span class="text-gray-500 font-bold text-xs">{{ $i + 1 }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-bold text-gray-900">{{ $row->player_name }}</p>
                                <p class="text-xs font-semibold text-gray-600">{{ $row->team_name }} <span class="font-mono text-gray-500">({{ $row->team_abbr }})</span></p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($row->penalty_goals > 0)
                                    <span class="inline-block bg-amber-100 text-amber-800 text-xs font-bold px-1.5 py-0.5 rounded" title="Penalties">
                                        {{ $row->penalty_goals }}P
                                    </span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-base font-extrabold
                                    {{ $i === 0 ? 'bg-emerald-600 text-white shadow' : 'bg-gray-100 text-gray-900' }}">
                                    {{ $row->total_goals }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- ══ 2. Assist Leaders ═══════════════════════════════════════════ --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-blue-700 to-blue-600 px-6 py-4 flex items-center gap-3">
                <span class="text-2xl">🎯</span>
                <div>
                    <h2 class="text-lg font-extrabold text-white">Assist Leaders</h2>
                    <p class="text-blue-200 text-xs">Recorded goal assists</p>
                </div>
            </div>

            @if($assistLeaders->isEmpty())
                <div class="p-8 text-center text-gray-600 font-medium">
                    No assists recorded yet for this tournament.
                </div>
            @else
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-600 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3 text-center w-10">#</th>
                            <th class="px-4 py-3 text-left">Player</th>
                            <th class="px-4 py-3 text-center w-14">Ast</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($assistLeaders as $i => $row)
                        <tr class="{{ $i < 3 ? 'bg-blue-50/40' : 'bg-white' }} hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-center">
                                @if($i === 0)
                                    <span class="text-lg">🥇</span>
                                @elseif($i === 1)
                                    <span class="text-lg">🥈</span>
                                @elseif($i === 2)
                                    <span class="text-lg">🥉</span>
                                @else
                                    <span class="text-gray-500 font-bold text-xs">{{ $i + 1 }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-bold text-gray-900">{{ $row->player_name }}</p>
                                <p class="text-xs font-semibold text-gray-600">{{ $row->team_name }} <span class="font-mono text-gray-500">({{ $row->team_abbr }})</span></p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-base font-extrabold
                                    {{ $i === 0 ? 'bg-blue-600 text-white shadow' : 'bg-gray-100 text-gray-900' }}">
                                    {{ $row->total_assists }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- ══ 3. Disciplinary Table ═══════════════════════════════════════ --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-red-700 to-red-600 px-6 py-4 flex items-center gap-3">
                <span class="text-2xl">🟥</span>
                <div>
                    <h2 class="text-lg font-extrabold text-white">Disciplinary</h2>
                    <p class="text-red-200 text-xs">Cards by team</p>
                </div>
            </div>

            @if($disciplinary->isEmpty())
                <div class="p-8 text-center text-gray-600 font-medium">
                    No cards recorded yet for this tournament.
                </div>
            @else
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-600 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3 text-left">Team</th>
                            <th class="px-4 py-3 text-center w-10" title="Yellow Cards">
                                <span class="inline-block w-3 h-4 bg-yellow-400 rounded-sm align-middle"></span>
                            </th>
                            <th class="px-4 py-3 text-center w-10" title="Second Yellow → Red">
                                <span class="inline-block w-3 h-4 bg-yellow-400 rounded-sm border-2 border-red-600 align-middle"></span>
                            </th>
                            <th class="px-4 py-3 text-center w-10" title="Red Cards">
                                <span class="inline-block w-3 h-4 bg-red-600 rounded-sm align-middle"></span>
                            </th>
                            <th class="px-4 py-3 text-center w-12">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($disciplinary as $i => $row)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3">
                                <a href="{{ route('teams.show', $row->team_id) }}" class="font-bold text-gray-900 hover:text-indigo-600 transition">
                                    {{ $row->team_name }}
                                </a>
                                <span class="text-xs text-gray-500 font-mono ml-1">{{ $row->team_abbr }}</span>
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-yellow-700">
                                {{ $row->yellow_cards ?: '—' }}
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-orange-600">
                                {{ $row->second_yellow_cards ?: '—' }}
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-red-700">
                                {{ $row->red_cards ?: '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-sm font-extrabold
                                    {{ $i === 0 ? 'bg-red-600 text-white shadow' : 'bg-gray-100 text-gray-900' }}">
                                    {{ $row->total_cards }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Card legend --}}
                <div class="px-5 py-3 border-t border-gray-100 bg-gray-50 flex items-center gap-6 text-xs text-gray-600 font-medium">
                    <div class="flex items-center gap-1.5">
                        <span class="inline-block w-3 h-4 bg-yellow-400 rounded-sm"></span> Yellow
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="inline-block w-3 h-4 bg-yellow-400 rounded-sm border-2 border-red-600"></span> 2nd Yellow
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="inline-block w-3 h-4 bg-red-600 rounded-sm"></span> Red
                    </div>
                </div>
            @endif
        </div>

    </div>{{-- /grid --}}
</div>
@endsection
