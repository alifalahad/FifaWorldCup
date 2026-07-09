@extends('layouts.app')

@section('title', 'Players')
@section('meta_description', 'Browse football players, their positions, and tournament histories.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-10 text-center">
        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight mb-3">Players</h1>
        <p class="text-lg text-gray-500 max-w-2xl mx-auto">Explore players from around the world, check their stats, and view their tournament history.</p>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-10">
        <form method="GET" action="{{ route('players.index') }}" class="flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="flex flex-wrap gap-4 w-full sm:w-auto flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search player name..." class="flex-1 sm:min-w-[250px] border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <select name="position" class="border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Positions</option>
                    @foreach($positions as $p)
                        <option value="{{ $p }}" {{ request('position') === $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition">Search</button>
            </div>
            @if(request('search') || request('position'))
                <a href="{{ route('players.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium whitespace-nowrap">Clear Filters</a>
            @endif
        </form>
    </div>

    {{-- Players Grid --}}
    @if($players->isEmpty())
        <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
            <span class="text-4xl block mb-4">👟</span>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No Players Found</h3>
            <p class="text-gray-500">We couldn't find any players matching your search criteria.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
            @foreach($players as $player)
                <a href="{{ route('players.show', $player->player_id) }}" class="group bg-white border border-gray-200 rounded-2xl p-6 hover:border-indigo-300 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex items-start gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-100 to-white border border-indigo-100 text-indigo-600 font-black rounded-full flex items-center justify-center text-2xl group-hover:scale-110 transition-transform flex-shrink-0 shadow-sm">
                        {{ mb_substr($player->first_name, 0, 1) }}{{ mb_substr($player->last_name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-gray-900 text-lg mb-1 group-hover:text-indigo-600 transition-colors truncate">
                            {{ $player->first_name }} {{ $player->last_name }}
                        </h3>
                        <p class="text-sm text-gray-500 mb-2 truncate">{{ $player->nationality }}</p>
                        
                        <div class="flex gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold 
                                {{ match($player->position) {
                                    'GK' => 'bg-amber-100 text-amber-800',
                                    'DF' => 'bg-blue-100 text-blue-800',
                                    'MF' => 'bg-emerald-100 text-emerald-800',
                                    'FW' => 'bg-rose-100 text-rose-800',
                                    default => 'bg-gray-100 text-gray-800'
                                } }}">
                                {{ $player->position }}
                            </span>
                            @if($player->date_of_birth)
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 text-gray-600 text-xs font-medium">
                                    {{ \Carbon\Carbon::parse($player->date_of_birth)->age }} yrs
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $players->links() }}
        </div>
    @endif
</div>
@endsection
