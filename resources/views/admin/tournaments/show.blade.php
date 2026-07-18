@extends('layouts.admin')

@section('title', $tournament->name)
@section('page-title', $tournament->name)
@section('breadcrumb', 'Admin › Tournaments › ' . $tournament->name)

@section('content')

{{-- Flash messages --}}
@if(session('success'))
<div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-md text-sm flex items-center gap-2">
    ✅ {{ session('success') }}
</div>
@endif

{{-- Action buttons --}}
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.tournaments.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700">← All Tournaments</a>
    <span class="text-gray-300">|</span>
    <a href="{{ route('admin.tournaments.edit', $tournament->tournament_id) }}"
       class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md transition">
        Edit Tournament
    </a>
    <a href="{{ route('admin.tournaments.register-team', $tournament->tournament_id) }}"
       class="text-sm bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition">
        + Register Team
    </a>
</div>

{{-- Tournament details card --}}
<div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">

        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Year</p>
            <p class="text-2xl font-bold text-gray-800">{{ $tournament->year }}</p>
        </div>

        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Host Country</p>
            <p class="text-lg font-medium text-gray-800">{{ $tournament->host_country }}</p>
        </div>

        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Status</p>
            @php
                $cls = match($tournament->status) {
                    'PLANNED'   => 'bg-blue-100 text-blue-700',
                    'ONGOING'   => 'bg-green-100 text-green-700',
                    'COMPLETED' => 'bg-gray-100 text-gray-600',
                    'CANCELLED' => 'bg-red-100 text-red-600',
                    default     => 'bg-gray-100 text-gray-500',
                };
            @endphp
            <span class="inline-block px-3 py-1 rounded-full text-sm font-medium {{ $cls }}">
                {{ $tournament->status }}
            </span>
        </div>

        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Start Date</p>
            <p class="text-sm text-gray-700">{{ \Carbon\Carbon::parse($tournament->start_date)->format('d F Y') }}</p>
        </div>

        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">End Date</p>
            <p class="text-sm text-gray-700">{{ \Carbon\Carbon::parse($tournament->end_date)->format('d F Y') }}</p>
        </div>

        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Total Teams</p>
            <p class="text-sm text-gray-700">{{ $tournament->total_teams }}</p>
        </div>

    </div>
</div>

{{-- Groups --}}
<div class="bg-white rounded-lg border border-gray-200 mb-6">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-700">Groups
            <span class="ml-2 text-xs font-normal text-gray-400">({{ $tournament->groups->count() }} created)</span>
        </h2>
        <a href="{{ route('admin.tournaments.edit', $tournament->tournament_id) }}"
           class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
            Manage Groups →
        </a>
    </div>
    @if($tournament->groups->isEmpty())
    <div class="px-6 py-4 text-sm text-gray-400 italic">
        No groups yet.
        <a href="{{ route('admin.tournaments.edit', $tournament->tournament_id) }}" class="text-indigo-600 hover:underline">Go to Edit Tournament to add groups.</a>
    </div>
    @else
    <div class="flex flex-wrap gap-3 px-6 py-4">
        @foreach($tournament->groups->sortBy('group_name') as $group)
        <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-indigo-50 border border-indigo-200">
            <span class="text-lg font-bold text-indigo-700">{{ $group->group_name }}</span>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- Registered Teams --}}
<div class="bg-white rounded-lg border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-700">
            Registered Teams
            <span class="ml-2 text-xs text-gray-400">({{ $tournament->teamTournaments->count() }} / {{ $tournament->total_teams }})</span>
        </h2>
    </div>

    @if($tournament->teamTournaments->isEmpty())
    <div class="px-6 py-8 text-center text-sm text-gray-400">
        No teams registered yet. Teams will be added in the Teams management section.
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-3 text-left">Team</th>
                    <th class="px-6 py-3 text-left">Abbreviation</th>
                    <th class="px-6 py-3 text-left">Group</th>
                    <th class="px-6 py-3 text-left">Coach</th>
                    <th class="px-6 py-3 text-left">Seed</th>
                    <th class="px-6 py-3 text-left">Stage</th>
                    <th class="px-6 py-3 text-center">Roster</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($tournament->teamTournaments->sortBy('team.country_name') as $tt)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-3 font-medium text-gray-800">
                        {{ $tt->team->country_name ?? '—' }}
                    </td>
                    <td class="px-6 py-3 text-gray-500 font-mono">
                        {{ $tt->team->abbreviation ?? '—' }}
                    </td>
                    <td class="px-6 py-3 text-gray-600">
                        {{ $tt->group?->group_name ?? '—' }}
                    </td>
                    <td class="px-6 py-3 text-gray-600">
                        {{ $tt->coach ? $tt->coach->first_name . ' ' . $tt->coach->last_name : '—' }}
                    </td>
                    <td class="px-6 py-3 text-gray-600">
                        {{ $tt->seed_position ?? '—' }}
                    </td>
                    <td class="px-6 py-3 text-gray-600 text-xs">
                        {{ $tt->elimination_stage ?? 'Active' }}
                    </td>
                    <td class="px-6 py-3 text-center">
                        <a href="{{ route('admin.roster.index', $tt->team_tournament_id) }}"
                           class="inline-block text-xs font-medium text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-2 py-1 rounded transition">
                            Manage Roster
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@endsection
