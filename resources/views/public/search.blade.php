@extends('layouts.app')

@section('title', $q ? 'Search: ' . $q : 'Search')
@section('meta_description', 'Search teams, players and coaches across all FIFA World Cup tournaments.')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Hero search form (large, centered) --}}
    <div class="text-center mb-10">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-6">
            @if($q)
                Results for <span class="text-indigo-600">"{{ $q }}"</span>
            @else
                Search
            @endif
        </h1>
        <form method="GET" action="{{ route('search') }}" class="max-w-lg mx-auto flex gap-2">
            <div class="relative flex-1">
                <input
                    id="search-main"
                    type="search"
                    name="q"
                    value="{{ $q }}"
                    placeholder="Search teams, players, coaches…"
                    autofocus
                    class="w-full rounded-xl border border-gray-300 bg-white text-gray-900 text-sm placeholder-gray-400 pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 shadow-sm"
                >
                <span class="absolute left-3 top-3.5 text-gray-400 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z" />
                    </svg>
                </span>
            </div>
            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-6 py-3 rounded-xl transition shadow-sm">
                Search
            </button>
        </form>
    </div>

    @if(!$q)
        {{-- Empty state: no query --}}
        <div class="text-center py-16 bg-white rounded-2xl border border-gray-100 shadow-sm">
            <span class="text-5xl block mb-4">🔍</span>
            <h2 class="text-lg font-bold text-gray-900 mb-2">Start searching</h2>
            <p class="text-gray-600">Enter a team name, player name, or coach name above.</p>
        </div>

    @elseif(isset($tooShort) && $tooShort)
        {{-- Query too short --}}
        <div class="text-center py-16 bg-white rounded-2xl border border-gray-100 shadow-sm">
            <span class="text-5xl block mb-4">✏️</span>
            <h2 class="text-lg font-bold text-gray-900 mb-2">Query too short</h2>
            <p class="text-gray-600">Please enter at least 2 characters to search.</p>
        </div>

    @elseif($teams->isEmpty() && $players->isEmpty() && $coaches->isEmpty())
        {{-- No results --}}
        <div class="text-center py-16 bg-white rounded-2xl border border-gray-100 shadow-sm">
            <span class="text-5xl block mb-4">😕</span>
            <h2 class="text-lg font-bold text-gray-900 mb-2">No results found</h2>
            <p class="text-gray-600">No teams, players or coaches matched <strong>"{{ $q }}"</strong>.</p>
        </div>

    @else
        {{-- Summary bar --}}
        @php
            $totalResults = $teams->count() + $players->count() + $coaches->count();
        @endphp
        <p class="text-sm text-gray-600 font-medium mb-8">
            Found <span class="font-bold text-gray-900">{{ $totalResults }}</span> result{{ $totalResults !== 1 ? 's' : '' }}
            across teams, players and coaches.
        </p>

        <div class="space-y-10">

            {{-- ── Teams ─────────────────────────────────────────────────── --}}
            @if($teams->isNotEmpty())
            <section>
                <h2 class="flex items-center gap-3 text-xl font-extrabold text-gray-900 mb-4">
                    <span class="bg-indigo-100 text-indigo-700 p-2 rounded-lg text-base">🛡️</span>
                    Teams
                    <span class="text-sm font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">{{ $teams->count() }}</span>
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($teams as $team)
                    <a href="{{ route('teams.show', $team->team_id) }}"
                       class="group flex items-center gap-4 bg-white border border-gray-200 rounded-xl p-4 hover:border-indigo-300 hover:shadow-md transition-all duration-200">
                        <div class="w-11 h-11 bg-gray-100 text-gray-700 font-black rounded-full flex items-center justify-center text-lg shrink-0 group-hover:bg-indigo-100 group-hover:text-indigo-700 transition-colors">
                            {{ mb_substr($team->country_name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-gray-900 group-hover:text-indigo-600 transition-colors truncate">{{ $team->country_name }}</p>
                            <p class="text-xs text-gray-600 font-semibold font-mono">{{ $team->abbreviation }}
                                @if($team->continent) · {{ $team->continent }}@endif
                            </p>
                        </div>
                        <div class="ml-auto text-gray-300 group-hover:text-indigo-400 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </a>
                    @endforeach
                </div>
            </section>
            @endif

            {{-- ── Players ────────────────────────────────────────────────── --}}
            @if($players->isNotEmpty())
            <section>
                <h2 class="flex items-center gap-3 text-xl font-extrabold text-gray-900 mb-4">
                    <span class="bg-emerald-100 text-emerald-700 p-2 rounded-lg text-base">⚽</span>
                    Players
                    <span class="text-sm font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">{{ $players->count() }}</span>
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($players as $player)
                    <div class="flex items-center gap-4 bg-white border border-gray-200 rounded-xl p-4">
                        <div class="w-11 h-11 bg-emerald-50 text-emerald-700 font-black rounded-full flex items-center justify-center text-base shrink-0 border border-emerald-100">
                            {{ mb_substr($player->first_name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-gray-900 truncate">{{ $player->first_name }} {{ $player->last_name }}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                @if($player->position)
                                @php
                                    $posColor = match($player->position) {
                                        'GK' => 'bg-yellow-100 text-yellow-800',
                                        'DF' => 'bg-blue-100 text-blue-800',
                                        'MF' => 'bg-green-100 text-green-800',
                                        'FW' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <span class="text-xs font-bold px-1.5 py-0.5 rounded {{ $posColor }}">{{ $player->position }}</span>
                                @endif
                                @if($player->nationality)
                                <span class="text-xs text-gray-600 font-semibold">{{ $player->nationality }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

            {{-- ── Coaches ────────────────────────────────────────────────── --}}
            @if($coaches->isNotEmpty())
            <section>
                <h2 class="flex items-center gap-3 text-xl font-extrabold text-gray-900 mb-4">
                    <span class="bg-amber-100 text-amber-700 p-2 rounded-lg text-base">📋</span>
                    Coaches
                    <span class="text-sm font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">{{ $coaches->count() }}</span>
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($coaches as $coach)
                    <div class="flex items-center gap-4 bg-white border border-gray-200 rounded-xl p-4">
                        <div class="w-11 h-11 bg-amber-50 text-amber-700 font-black rounded-full flex items-center justify-center text-base shrink-0 border border-amber-100">
                            {{ mb_substr($coach->first_name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-gray-900 truncate">{{ $coach->first_name }} {{ $coach->last_name }}</p>
                            @if($coach->nationality)
                            <p class="text-xs text-gray-600 font-semibold mt-0.5">{{ $coach->nationality }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

        </div>{{-- /space-y-10 --}}
    @endif

</div>
@endsection
