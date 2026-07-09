@extends('layouts.admin')

@section('title', 'Matches')
@section('page-title', 'Matches')
@section('breadcrumb', 'Admin › Matches')

@section('content')

@if(session('success'))
<div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-md text-sm flex items-center gap-2">
    ✅ {{ session('success') }}
</div>
@endif

<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">{{ $matches->total() }} match(es) found</p>
    <a href="{{ route('admin.matches.create') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-md transition">
        + Schedule Match
    </a>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.matches.index') }}" class="flex flex-wrap gap-3 mb-6">
    <select name="tournament_id" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        <option value="">All tournaments</option>
        @foreach($tournaments as $t)
        <option value="{{ $t->tournament_id }}" {{ request('tournament_id') == $t->tournament_id ? 'selected' : '' }}>
            {{ $t->name }}
        </option>
        @endforeach
    </select>
    <select name="stage" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        <option value="">All stages</option>
        @foreach($stages as $s)
        <option value="{{ $s }}" {{ request('stage') === $s ? 'selected' : '' }}>{{ str_replace('_', ' ', $s) }}</option>
        @endforeach
    </select>
    <select name="status" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        <option value="">All statuses</option>
        @foreach($statuses as $s)
        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white text-sm px-4 py-2 rounded-md transition">Filter</button>
    @if(request('tournament_id') || request('stage') || request('status'))
    <a href="{{ route('admin.matches.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center">Clear</a>
    @endif
</form>

<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    @if($matches->isEmpty())
    <div class="py-16 text-center text-gray-400 text-sm">
        No matches found. <a href="{{ route('admin.matches.create') }}" class="text-indigo-600 hover:underline">Schedule one →</a>
    </div>
    @else
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider border-b border-gray-100">
                <th class="px-4 py-3 text-left">Date</th>
                <th class="px-4 py-3 text-left">Tournament</th>
                <th class="px-4 py-3 text-center">Stage</th>
                <th class="px-4 py-3 text-center">Group</th>
                <th class="px-4 py-3 text-right">Home</th>
                <th class="px-4 py-3 text-center">Score</th>
                <th class="px-4 py-3 text-left">Away</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($matches as $match)
            @php
                $statusCls = match($match->status) {
                    'COMPLETED' => 'bg-green-100 text-green-700',
                    'LIVE'      => 'bg-red-100 text-red-700',
                    'SCHEDULED' => 'bg-blue-100 text-blue-700',
                    'POSTPONED' => 'bg-yellow-100 text-yellow-700',
                    'CANCELLED' => 'bg-gray-100 text-gray-500',
                    default     => 'bg-gray-100 text-gray-500',
                };
            @endphp
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 text-gray-600 whitespace-nowrap text-xs">
                    {{ $match->match_date->format('d M Y') }}
                </td>
                <td class="px-4 py-3 text-gray-600 text-xs">
                    {{ $match->tournament->name ?? '—' }}
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="text-xs text-gray-500">{{ str_replace('_', ' ', $match->stage) }}</span>
                </td>
                <td class="px-4 py-3 text-center text-xs text-gray-500">
                    {{ $match->group?->group_name ?? '—' }}
                </td>
                <td class="px-4 py-3 text-right font-semibold text-gray-800">
                    {{ $match->homeTeam->abbreviation ?? '—' }}
                </td>
                <td class="px-4 py-3 text-center font-bold text-gray-900">
                    @if($match->status === 'COMPLETED')
                        {{ $match->home_score }} – {{ $match->away_score }}
                        @if($match->has_extra_time === 'Y')
                        <span class="text-xs text-gray-400 ml-1">(ET{{ $match->has_penalties === 'Y' ? '+P' : '' }})</span>
                        @endif
                    @else
                        vs
                    @endif
                </td>
                <td class="px-4 py-3 text-left font-semibold text-gray-800">
                    {{ $match->awayTeam->abbreviation ?? '—' }}
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $statusCls }}">
                        {{ $match->status }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-2">
                        @if(in_array($match->status, ['SCHEDULED', 'LIVE']))
                        <a href="{{ route('admin.matches.result', $match->match_id) }}"
                           class="text-xs font-medium text-green-600 hover:text-green-800 bg-green-50 hover:bg-green-100 px-2 py-0.5 rounded transition">
                            Result
                        </a>
                        @endif
                        <a href="{{ route('admin.matches.edit', $match->match_id) }}"
                           class="text-xs font-medium text-gray-500 hover:text-indigo-600">Edit</a>
                        <form method="POST" action="{{ route('admin.matches.destroy', $match->match_id) }}"
                              onsubmit="return confirm('Delete this match?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs font-medium text-red-500 hover:text-red-700">Del</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    @if($matches->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $matches->links() }}</div>
    @endif
    @endif
</div>

@endsection
