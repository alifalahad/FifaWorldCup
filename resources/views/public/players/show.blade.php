@extends('layouts.app')

@section('title', $player->first_name . ' ' . $player->last_name . ' — Player Profile')
@section('meta_description', 'Player profile, statistics, and tournament history for ' . $player->first_name . ' ' . $player->last_name)

@section('content')
<div class="bg-gray-900 border-b border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
            {{-- Avatar placeholder --}}
            <div class="w-32 h-32 md:w-40 md:h-40 bg-gradient-to-br from-gray-700 to-gray-800 rounded-full border-4 border-gray-800 shadow-xl flex items-center justify-center shrink-0">
                <span class="text-5xl font-black text-gray-500 tracking-tighter">
                    {{ mb_substr($player->first_name, 0, 1) }}{{ mb_substr($player->last_name, 0, 1) }}
                </span>
            </div>

            {{-- Player info --}}
            <div class="text-center md:text-left flex-1">
                <div class="inline-flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 bg-gray-800 text-gray-300 rounded-full text-xs font-bold tracking-widest uppercase">
                        {{ $player->position }}
                    </span>
                    <span class="text-gray-400 text-sm font-medium">{{ $player->nationality }}</span>
                </div>
                
                <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight mb-4">
                    {{ $player->first_name }} {{ $player->last_name }}
                </h1>
                
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-6 text-sm text-gray-400">
                    @if($player->date_of_birth)
                    <div class="flex items-center gap-2">
                        <span class="text-gray-500">Born</span>
                        <strong class="text-gray-300">{{ \Carbon\Carbon::parse($player->date_of_birth)->format('j M Y') }}</strong>
                        <span class="bg-gray-800 text-gray-400 px-2 py-0.5 rounded text-xs ml-1">{{ \Carbon\Carbon::parse($player->date_of_birth)->age }} yrs</span>
                    </div>
                    @endif
                    
                    @if($player->height_cm)
                    <div class="flex items-center gap-2">
                        <span class="text-gray-500">Height</span>
                        <strong class="text-gray-300">{{ $player->height_cm }} cm</strong>
                    </div>
                    @endif
                    
                    @if($player->weight_kg)
                    <div class="flex items-center gap-2">
                        <span class="text-gray-500">Weight</span>
                        <strong class="text-gray-300">{{ $player->weight_kg }} kg</strong>
                    </div>
                    @endif
                </div>
            </div>
            
            {{-- Career Stats Summary Block --}}
            <div class="flex gap-4 md:gap-6 mt-8 md:mt-0">
                <div class="text-center">
                    <div class="text-3xl font-black text-white mb-1">{{ $totalGoals }}</div>
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-widest">Goals</div>
                </div>
                <div class="w-px bg-gray-800"></div>
                <div class="text-center">
                    <div class="text-3xl font-black text-white mb-1">{{ $totalAssists }}</div>
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-widest">Assists</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Left column: Tournament History --}}
        <div class="lg:col-span-2 space-y-8">
            <h2 class="text-2xl font-bold text-gray-900">Tournament History</h2>
            
            @if($player->playerTournaments->isEmpty())
                <div class="bg-gray-50 rounded-xl p-8 text-center border border-gray-100">
                    <p class="text-gray-500">This player hasn't participated in any tournaments yet.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($player->playerTournaments as $pt)
                        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600 font-bold">
                                    {{ $pt->teamTournament->tournament->year }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900">
                                        <a href="{{ route('tournaments.show', $pt->teamTournament->tournament->tournament_id) }}" class="hover:text-indigo-600 transition">
                                            {{ $pt->teamTournament->tournament->name }}
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-500 flex items-center gap-2 mt-1">
                                        <a href="{{ route('teams.show', $pt->teamTournament->team->team_id) }}" class="font-medium text-gray-700 hover:text-indigo-600 transition">
                                            {{ $pt->teamTournament->team->country_name }}
                                        </a>
                                        @if($pt->is_captain === 'Y')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 uppercase tracking-wide">
                                                Captain
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 px-4 py-2 rounded-lg border border-gray-100 text-center sm:text-right shrink-0">
                                <div class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Squad Number</div>
                                <div class="text-xl font-black text-gray-900 font-mono">#{{ $pt->jersey_number }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Right column: Disciplinary Record --}}
        <div>
            <h2 class="text-xl font-bold text-gray-900 mb-6">Disciplinary Record</h2>
            
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-6 bg-yellow-400 rounded-sm shadow-sm border border-yellow-500"></div>
                        <span class="font-medium text-gray-700">Yellow Cards</span>
                    </div>
                    <span class="text-2xl font-black text-gray-900">{{ $yellowCards }}</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-6 bg-red-600 rounded-sm shadow-sm border border-red-700"></div>
                        <span class="font-medium text-gray-700">Red Cards</span>
                    </div>
                    <span class="text-2xl font-black text-gray-900">{{ $redCards }}</span>
                </div>
                
                @if($yellowCards == 0 && $redCards == 0)
                    <div class="mt-6 pt-4 border-t border-gray-100 text-sm text-emerald-600 font-medium flex items-center gap-2">
                        <span>✨</span> Clean disciplinary record
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
