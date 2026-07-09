@extends('layouts.app')

@section('title', $tournament->name . ' Overview')
@section('meta_description', 'Overview of the ' . $tournament->name . ' hosted in ' . $tournament->host_country)

@section('content')
{{-- Sticky sub-nav --}}
<div class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14">
            <div class="flex items-baseline gap-3">
                <a href="{{ route('tournaments.index') }}" class="text-gray-500 hover:text-indigo-600 text-sm transition">Tournaments</a>
                <span class="text-gray-300">/</span>
                <span class="font-bold text-gray-900 text-sm">{{ $tournament->year }} {{ $tournament->host_country }}</span>
            </div>
            <div class="flex gap-4 text-sm font-medium">
                <a href="{{ route('tournaments.show', $tournament->tournament_id) }}"
                   class="border-b-2 border-indigo-600 text-indigo-600 pb-1">Overview</a>
                <a href="{{ route('tournaments.fixtures', $tournament->tournament_id) }}"
                   class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 pb-1 transition">Fixtures</a>
                <a href="{{ route('tournaments.standings', $tournament->tournament_id) }}"
                   class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 pb-1 transition">Standings</a>
                <a href="{{ route('tournaments.stats', $tournament->tournament_id) }}"
                   class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 pb-1 transition">Stats</a>
            </div>
        </div>
    </div>
</div>

<div class="bg-gray-900 border-b border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-indigo-400 font-semibold tracking-wide text-sm uppercase mb-1">
                    {{ $tournament->year }} · {{ $tournament->host_country }}
                </p>
                <h1 class="text-4xl font-extrabold text-white sm:text-5xl sm:tracking-tight lg:text-6xl mb-4">
                    {{ $tournament->name }}
                </h1>
                <div class="flex flex-wrap items-center gap-6 text-sm text-gray-300">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">📅</span>
                        {{ $tournament->start_date->format('j M Y') }} – {{ $tournament->end_date->format('j M Y') }}
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-lg">🛡️</span>
                        {{ $tournament->total_teams }} Teams
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-lg">🎯</span>
                        Status: <span class="font-medium text-white">{{ $tournament->status }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        
        {{-- Left Col: Groups --}}
        <div class="lg:col-span-2 space-y-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span>Groups</span>
                    <span class="bg-indigo-100 text-indigo-700 text-xs py-1 px-2.5 rounded-full font-medium">{{ $tournament->groups->count() }}</span>
                </h2>
                
                @if($tournament->groups->isEmpty())
                    <div class="bg-white border border-gray-200 rounded-xl p-8 text-center text-gray-500">
                        Group draw has not taken place yet.
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($tournament->groups->sortBy('group_name') as $group)
                            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition">
                                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                                    <h3 class="font-bold text-gray-900">Group {{ $group->group_name }}</h3>
                                </div>
                                <div class="p-4">
                                    @php
                                        // Get teams for this group
                                        $groupTeams = $tournament->teams->filter(fn($t) => $t->pivot->group_id === $group->group_id)->sortBy('pivot->seed_position');
                                    @endphp
                                    
                                    @if($groupTeams->isEmpty())
                                        <p class="text-sm text-gray-400 italic">No teams assigned yet.</p>
                                    @else
                                        <ul class="space-y-3">
                                            @foreach($groupTeams as $team)
                                                <li class="flex items-center justify-between text-sm">
                                                    <a href="{{ route('teams.show', $team->team_id) }}" class="flex items-center gap-3 font-medium text-gray-800 hover:text-indigo-600 transition">
                                                        <span class="w-6 text-center text-gray-400 text-xs">{{ $team->pivot->seed_position ?? '-' }}</span>
                                                        {{ $team->country_name }}
                                                    </a>
                                                    <span class="text-gray-400 text-xs font-mono">{{ $team->abbreviation }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Col: All Registered Teams List --}}
        <div>
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm sticky top-6">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="font-bold text-gray-900 text-lg">Participating Teams</h3>
                </div>
                <div class="p-6">
                    @if($tournament->teams->isEmpty())
                        <p class="text-sm text-gray-500">No teams have registered for this tournament yet.</p>
                    @else
                        <ul class="divide-y divide-gray-100 max-h-[600px] overflow-y-auto pr-2">
                            @foreach($tournament->teams->sortBy('country_name') as $team)
                                <li class="py-3 flex items-center justify-between group">
                                    <a href="{{ route('teams.show', $team->team_id) }}" class="text-sm font-medium text-gray-700 group-hover:text-indigo-600 transition">
                                        {{ $team->country_name }}
                                    </a>
                                    <span class="text-xs text-gray-400 bg-gray-50 px-2 py-1 rounded">{{ $team->confederation }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
