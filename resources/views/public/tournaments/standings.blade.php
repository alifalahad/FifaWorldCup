@extends('layouts.app')

@section('title', $tournament->name . ' Standings')
@section('meta_description', 'Group stage standings for ' . $tournament->name . ' — points, goal difference, and rankings.')

@section('content')
{{-- Tournament sub-nav --}}
<div class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14">
            <div class="flex items-baseline gap-3">
                <a href="{{ route('tournaments.show', $tournament->tournament_id) }}" class="text-lg font-bold text-gray-900 hover:text-indigo-600 transition">
                    {{ $tournament->name }}
                </a>
                <span class="text-gray-300">/</span>
                <span class="text-indigo-600 font-semibold text-sm">Standings</span>
            </div>
            <div class="flex gap-4 text-sm font-medium">
                <a href="{{ route('tournaments.fixtures', $tournament->tournament_id) }}"
                   class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 pb-1 transition">Fixtures</a>
                <a href="{{ route('tournaments.standings', $tournament->tournament_id) }}"
                   class="border-b-2 border-indigo-600 text-indigo-600 pb-1">Standings</a>
                <a href="{{ route('tournaments.stats', $tournament->tournament_id) }}"
                   class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 pb-1 transition">Stats</a>
                <a href="{{ route('tournaments.show', $tournament->tournament_id) }}"
                   class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 pb-1 transition">Overview</a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Group Stage Standings</h1>
    <p class="text-gray-500 mb-8">{{ $tournament->name }} · {{ $tournament->year }}</p>

    @if($standingsByGroup->isEmpty())
        <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
            <span class="text-4xl block mb-4">📊</span>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No Standings Available</h3>
            <p class="text-gray-500">Standings will appear once group-stage matches have been completed.</p>
        </div>
    @else
        <div class="space-y-10">
            @foreach($standingsByGroup as $groupId => $rows)
                @php
                    $groupModel = $groupsById->get($groupId);
                    $groupLabel = $groupModel ? 'Group ' . $groupModel->group_name : 'Group ' . $groupId;
                @endphp

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    {{-- Group header --}}
                    <div class="bg-gradient-to-r from-indigo-700 to-indigo-600 px-6 py-4">
                        <h2 class="text-lg font-extrabold text-white tracking-wide">{{ $groupLabel }}</h2>
                    </div>

                    {{-- FIFA-style standing table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                                    <th class="px-4 py-3 text-center w-8">#</th>
                                    <th class="px-4 py-3 text-left">Team</th>
                                    <th class="px-4 py-3 text-center w-10" title="Played">P</th>
                                    <th class="px-4 py-3 text-center w-10" title="Won">W</th>
                                    <th class="px-4 py-3 text-center w-10" title="Drawn">D</th>
                                    <th class="px-4 py-3 text-center w-10" title="Lost">L</th>
                                    <th class="px-4 py-3 text-center w-16" title="Goals For">GF</th>
                                    <th class="px-4 py-3 text-center w-16" title="Goals Against">GA</th>
                                    <th class="px-4 py-3 text-center w-16" title="Goal Difference">GD</th>
                                    <th class="px-4 py-3 text-center w-12" title="Points">Pts</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($rows as $i => $standing)
                                @php
                                    $pos = $i + 1;
                                    $qualified = $pos <= 2; // top 2 qualify
                                    $rowBg = $qualified
                                        ? ($pos === 1 ? 'bg-emerald-50 hover:bg-emerald-100/70' : 'bg-blue-50 hover:bg-blue-100/70')
                                        : 'bg-white hover:bg-gray-50';
                                @endphp
                                <tr class="{{ $rowBg }} transition-colors">
                                    {{-- Position --}}
                                    <td class="px-4 py-3.5 text-center">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold
                                            {{ $pos === 1 ? 'bg-emerald-600 text-white' : ($pos === 2 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600') }}">
                                            {{ $pos }}
                                        </span>
                                    </td>

                                    {{-- Team --}}
                                    <td class="px-4 py-3.5">
                                        <a href="{{ route('teams.show', $standing->team_id) }}" class="flex items-center gap-2.5 group">
                                            <div class="w-7 h-7 bg-gray-100 rounded-full flex items-center justify-center text-xs font-bold text-gray-600 group-hover:bg-indigo-100 group-hover:text-indigo-700 transition-colors flex-shrink-0">
                                                {{ mb_substr($standing->team->country_name ?? '?', 0, 1) }}
                                            </div>
                                            <div>
                                                <span class="font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                                    {{ $standing->team->country_name ?? '—' }}
                                                </span>
                                                <span class="text-xs text-gray-400 ml-1.5 font-mono">
                                                    {{ $standing->team->abbreviation ?? '' }}
                                                </span>
                                            </div>
                                        </a>
                                    </td>

                                    {{-- Stats --}}
                                    <td class="px-4 py-3.5 text-center text-gray-700">{{ $standing->played }}</td>
                                    <td class="px-4 py-3.5 text-center font-semibold text-emerald-700">{{ $standing->won }}</td>
                                    <td class="px-4 py-3.5 text-center text-gray-500">{{ $standing->drawn }}</td>
                                    <td class="px-4 py-3.5 text-center text-red-500">{{ $standing->lost }}</td>
                                    <td class="px-4 py-3.5 text-center text-gray-700">{{ $standing->goals_for }}</td>
                                    <td class="px-4 py-3.5 text-center text-gray-700">{{ $standing->goals_against }}</td>
                                    <td class="px-4 py-3.5 text-center font-mono text-gray-600">
                                        {{ $standing->goal_difference >= 0 ? '+' . $standing->goal_difference : $standing->goal_difference }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm font-extrabold bg-indigo-600 text-white shadow-sm">
                                            {{ $standing->points }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Legend --}}
                    <div class="px-6 py-3 border-t border-gray-100 bg-gray-50 flex items-center gap-6 text-xs text-gray-500">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                            Advances (1st)
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                            Advances (2nd)
                        </div>
                        <div class="ml-auto text-gray-400">
                            Sorted by: Pts → GD → GF
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
