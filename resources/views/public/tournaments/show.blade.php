@extends('layouts.app')

@section('title', $tournament->name . ' Overview')
@section('meta_description', 'Overview of the ' . $tournament->name . ' hosted in ' . $tournament->host_country)

@section('content')
@include('public.tournaments._subnav', ['tournament' => $tournament, 'active' => 'overview'])

<div class="relative bg-gray-900 border-b border-gray-800 overflow-hidden">
    <!-- Background patterns -->
    <div class="absolute inset-0 z-0 opacity-20">
        <div class="absolute inset-0 bg-gradient-to-r from-indigo-800 to-purple-900 mix-blend-multiply"></div>
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] mix-blend-overlay"></div>
    </div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-indigo-300 font-bold tracking-widest text-xs uppercase mb-3 flex items-center gap-2">
                    <span>{{ $tournament->year }}</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                    <span>{{ $tournament->host_country }}</span>
                </p>
                <h1 class="text-4xl font-extrabold text-white sm:text-5xl lg:text-6xl mb-6 drop-shadow-md">
                    {{ $tournament->name }}
                </h1>
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-300 font-medium">
                    <div class="flex items-center gap-2 bg-white/10 px-3 py-1.5 rounded-lg backdrop-blur-sm border border-white/10">
                        <span class="text-indigo-400">📅</span>
                        {{ $tournament->start_date->format('j M Y') }} – {{ $tournament->end_date->format('j M Y') }}
                    </div>
                    <div class="flex items-center gap-2 bg-white/10 px-3 py-1.5 rounded-lg backdrop-blur-sm border border-white/10">
                        <span class="text-emerald-400">🛡️</span>
                        {{ $tournament->total_teams }} Teams
                    </div>
                    <div class="flex items-center gap-2 bg-white/10 px-3 py-1.5 rounded-lg backdrop-blur-sm border border-white/10">
                        <span class="text-rose-400">🎯</span>
                        Status: <span class="text-white">{{ $tournament->status }}</span>
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
                                        // Cast both to int — Oracle returns pivot values as strings
                                        $groupTeams = $tournament->teams->filter(fn($t) => (int)$t->pivot->group_id === (int)$group->group_id)->sortBy('pivot.seed_position');
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
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm sticky top-32 overflow-hidden hover:shadow-md transition-shadow duration-300">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 backdrop-blur-sm">
                    <h3 class="font-bold text-gray-900 text-lg flex items-center gap-2">
                        <span class="w-2 h-6 bg-indigo-500 rounded-full"></span>
                        Participating Teams
                    </h3>
                </div>
                <div class="p-6">
                    @if($tournament->teams->isEmpty())
                        <div class="text-center py-6">
                            <span class="text-3xl mb-2 block opacity-50">🏟️</span>
                            <p class="text-sm text-gray-500">No teams registered yet.</p>
                        </div>
                    @else
                        <ul class="divide-y divide-gray-100 max-h-[600px] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-200">
                            @foreach($tournament->teams->sortBy('country_name') as $team)
                                <li class="py-3 flex items-center justify-between group">
                                    <a href="{{ route('teams.show', $team->team_id) }}" class="flex items-center gap-3 text-sm font-medium text-gray-700 group-hover:text-indigo-600 transition-colors">
                                        <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500 group-hover:bg-indigo-100 group-hover:text-indigo-600 transition-colors">
                                            {{ substr($team->country_name, 0, 1) }}
                                        </div>
                                        {{ $team->country_name }}
                                    </a>
                                    <span class="text-xs font-semibold text-gray-400 bg-gray-50 border border-gray-100 px-2 py-1 rounded-md">{{ $team->confederation }}</span>
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
