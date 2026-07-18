@extends('layouts.app')

@section('title', 'National Teams')
@section('meta_description', 'Browse national football teams, their confederations, and FIFA rankings.')

@section('content')
<div class="relative bg-gray-900 border-b border-gray-800 overflow-hidden">
    <div class="absolute inset-0 z-0 opacity-20">
        <div class="absolute inset-0 bg-gradient-to-r from-emerald-800 to-teal-900 mix-blend-multiply"></div>
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] mix-blend-overlay"></div>
    </div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight mb-4 drop-shadow-md">National Teams</h1>
        <p class="text-lg text-emerald-100 max-w-2xl mx-auto drop-shadow-sm">Explore all national teams, check their current FIFA world rankings, and view their tournament histories.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Filter Bar --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-10">
        <form method="GET" action="{{ route('teams.index') }}" class="flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="flex flex-wrap gap-4 w-full sm:w-auto flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search team or abbreviation..." class="flex-1 sm:min-w-[250px] border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <select name="continent" class="border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Confederations</option>
                    @foreach($continents as $c)
                        <option value="{{ $c }}" {{ request('continent') === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition">Search</button>
            </div>
            @if(request('search') || request('continent'))
                <a href="{{ route('teams.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium whitespace-nowrap">Clear Filters</a>
            @endif
        </form>
    </div>

    {{-- Teams Grid --}}
    @if($teams->isEmpty())
        <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
            <span class="text-4xl block mb-4">🛡️</span>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No Teams Found</h3>
            <p class="text-gray-500">We couldn't find any teams matching your search criteria.</p>
        </div>
    @else
        <div class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6 mb-10">
            @foreach($teams as $team)
                <a href="{{ route('teams.show', $team->team_id) }}" class="group bg-white border border-gray-200 rounded-2xl p-6 text-center hover:border-indigo-300 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col h-full justify-between">
                    <div>
                        <div class="w-16 h-16 mx-auto bg-gray-100 text-gray-600 font-black rounded-full flex items-center justify-center text-2xl mb-4 border border-gray-200 group-hover:scale-110 transition-transform">
                            {{ mb_substr($team->country_name, 0, 1) }}
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg mb-1 group-hover:text-indigo-600 transition-colors">{{ $team->country_name }}</h3>
                        <p class="text-sm text-gray-600 font-bold font-mono mb-4">{{ $team->abbreviation }}</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-2 border-t border-gray-200 pt-4 mt-2">
                        <div>
                            <p class="text-xs text-gray-600 font-bold uppercase tracking-wide">Rank</p>
                            <p class="font-semibold text-gray-900">{{ $team->fifa_ranking ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 font-bold uppercase tracking-wide">Confed</p>
                            <p class="font-semibold text-gray-900">{{ $team->continent ?? '—' }}</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        
        <div class="mt-8">
            {{ $teams->links() }}
        </div>
    @endif
</div>
@endsection
