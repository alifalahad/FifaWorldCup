@extends('layouts.app')

@section('title', $team->country_name . ' Profile')
@section('meta_description', 'Profile, current squad, and World Cup history for ' . $team->country_name)

@section('content')
<div class="bg-gray-900 border-b border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex items-center gap-6">
                <div class="w-24 h-24 bg-white/10 border border-white/20 rounded-2xl flex items-center justify-center text-4xl text-white font-bold backdrop-blur-sm">
                    {{ mb_substr($team->country_name, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-4xl font-extrabold text-white sm:text-5xl sm:tracking-tight lg:text-6xl mb-2">
                        {{ $team->country_name }}
                    </h1>
                    <div class="flex items-center gap-4 text-sm text-indigo-300 font-medium">
                        <span class="bg-indigo-900/50 px-3 py-1 rounded-full border border-indigo-700/50">{{ $team->abbreviation }}</span>
                        <span>{{ $team->continent }}</span>
                        @if($team->fifa_ranking)
                        <span class="flex items-center gap-1">
                            <span>FIFA Rank:</span>
                            <span class="text-white font-bold">#{{ $team->fifa_ranking }}</span>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        
        {{-- Left Col: Current/Latest Squad --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">
                            {{ $activeTournament ? 'Current Squad' : 'Latest Squad' }}
                        </h2>
                        @if($activeTournament)
                        <p class="text-sm text-gray-500 mt-1">For {{ $activeTournament->tournament->name }}</p>
                        @endif
                    </div>
                    @if($activeTournament && $activeTournament->coach)
                    <div class="text-right text-sm">
                        <p class="text-gray-500 uppercase text-xs tracking-wide">Coach</p>
                        <p class="font-medium text-gray-800">{{ $activeTournament->coach->first_name }} {{ $activeTournament->coach->last_name }}</p>
                    </div>
                    @endif
                </div>

                <div class="p-0">
                    @if($currentSquad->isEmpty())
                        <div class="p-8 text-center text-gray-700 font-medium">
                            No active squad roster registered currently.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                                    <tr>
                                        <th class="px-6 py-3 text-center w-16">#</th>
                                        <th class="px-6 py-3 text-left">Player</th>
                                        <th class="px-6 py-3 text-center">Pos</th>
                                        <th class="px-6 py-3 text-right">Age</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($currentSquad->sortBy('jersey_number') as $pt)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-3 text-center font-mono font-medium text-gray-500">
                                            {{ $pt->jersey_number }}
                                        </td>
                                        <td class="px-6 py-3 font-medium text-gray-900 flex items-center gap-2">
                                            {{ $pt->player->first_name }} {{ $pt->player->last_name }}
                                            @if($pt->is_captain === 'Y')
                                                <span class="inline-flex items-center justify-center w-5 h-5 bg-yellow-100 text-yellow-800 rounded-full text-xs font-bold" title="Captain">C</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3 text-center">
                                            @php
                                                $posColor = match($pt->player->position) {
                                                    'GK' => 'bg-yellow-100 text-yellow-800',
                                                    'DF' => 'bg-blue-100 text-blue-800',
                                                    'MF' => 'bg-green-100 text-green-800',
                                                    'FW' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                };
                                            @endphp
                                            <span class="inline-block px-2 py-0.5 rounded text-xs font-bold {{ $posColor }}">
                                                {{ $pt->player->position }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-right text-gray-600">
                                            {{ \Carbon\Carbon::parse($pt->player->date_of_birth)->age }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Col: Tournament History --}}
        <div>
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm sticky top-6">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-900 text-lg">World Cup History</h3>
                </div>
                <div class="p-0">
                    @if($team->teamTournaments->isEmpty())
                        <div class="p-6 text-sm text-gray-700 font-medium text-center">
                            No tournament history found.
                        </div>
                    @else
                        <ul class="divide-y divide-gray-100 max-h-[600px] overflow-y-auto">
                            @foreach($team->teamTournaments as $history)
                                <li class="p-4 hover:bg-gray-50 transition">
                                    <a href="{{ route('tournaments.show', $history->tournament_id) }}" class="block">
                                        <div class="flex justify-between items-start mb-1">
                                            <span class="font-bold text-gray-900 group-hover:text-indigo-600">{{ $history->tournament->year }}</span>
                                            <span class="text-xs text-gray-700 font-medium">{{ $history->elimination_stage ?? 'Active' }}</span>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-800">{{ $history->tournament->name }}</p>
                                        @if($history->group)
                                            <p class="text-xs font-semibold text-gray-600 mt-2">Group {{ $history->group->group_name }}</p>
                                        @endif
                                    </a>
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
