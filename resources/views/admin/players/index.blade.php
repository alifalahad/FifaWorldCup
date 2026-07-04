@extends('layouts.admin')

@section('title', 'Players')
@section('page-title', 'Players')
@section('breadcrumb', 'Admin › Players')

@section('content')

@if(session('success'))
<div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-md text-sm flex items-center gap-2">
    ✅ {{ session('success') }}
</div>
@endif

<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">{{ $players->total() }} player(s) found</p>
    <a href="{{ route('admin.players.create') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-md transition">
        + New Player
    </a>
</div>

{{-- Search + position filter --}}
<form method="GET" action="{{ route('admin.players.index') }}" class="flex gap-3 mb-6">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search by name or nationality…"
           class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
    <select name="position" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        <option value="">All positions</option>
        @foreach($positions as $pos)
        <option value="{{ $pos }}" {{ request('position') === $pos ? 'selected' : '' }}>{{ $pos }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white text-sm px-4 py-2 rounded-md transition">Filter</button>
    @if(request('search') || request('position'))
    <a href="{{ route('admin.players.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center">Clear</a>
    @endif
</form>

<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    @if($players->isEmpty())
    <div class="py-16 text-center text-gray-400 text-sm">
        No players found. <a href="{{ route('admin.players.create') }}" class="text-indigo-600 hover:underline">Add one →</a>
    </div>
    @else
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider border-b border-gray-100">
                <th class="px-6 py-3 text-left">Name</th>
                <th class="px-6 py-3 text-left">Nationality</th>
                <th class="px-6 py-3 text-center">Position</th>
                <th class="px-6 py-3 text-center">Age</th>
                <th class="px-6 py-3 text-center">Height</th>
                <th class="px-6 py-3 text-center">Weight</th>
                <th class="px-6 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($players as $player)
            @php
                $posColors = [
                    'GK' => 'bg-yellow-100 text-yellow-700',
                    'DF' => 'bg-blue-100 text-blue-700',
                    'MF' => 'bg-green-100 text-green-700',
                    'FW' => 'bg-red-100 text-red-700',
                ];
            @endphp
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-3 font-medium text-gray-800">
                    {{ $player->first_name }} {{ $player->last_name }}
                </td>
                <td class="px-6 py-3 text-gray-600">{{ $player->nationality }}</td>
                <td class="px-6 py-3 text-center">
                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold {{ $posColors[$player->position] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ $player->position }}
                    </span>
                </td>
                <td class="px-6 py-3 text-center text-gray-600">
                    {{ $player->date_of_birth ? \Carbon\Carbon::parse($player->date_of_birth)->age : '—' }}
                </td>
                <td class="px-6 py-3 text-center text-gray-500">{{ $player->height_cm ? $player->height_cm . ' cm' : '—' }}</td>
                <td class="px-6 py-3 text-center text-gray-500">{{ $player->weight_kg ? $player->weight_kg . ' kg' : '—' }}</td>
                <td class="px-6 py-3 text-center">
                    <div class="flex items-center justify-center gap-3">
                        <a href="{{ route('admin.players.edit', $player->player_id) }}"
                           class="text-gray-500 hover:text-indigo-600 text-xs font-medium">Edit</a>
                        <form method="POST" action="{{ route('admin.players.destroy', $player->player_id) }}"
                              onsubmit="return confirm('Delete {{ addslashes($player->first_name . ' ' . $player->last_name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($players->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $players->links() }}</div>
    @endif
    @endif
</div>

@endsection
