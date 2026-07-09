@extends('layouts.app')

@section('title', 'Tournaments')
@section('meta_description', 'Browse all FIFA World Cup tournaments, histories, and host countries.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-10 text-center">
        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight mb-3">FIFA World Cup Editions</h1>
        <p class="text-lg text-gray-500 max-w-2xl mx-auto">Explore the rich history of the world's greatest football tournament, from the classic editions to upcoming global spectacles.</p>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-10">
        <form method="GET" action="{{ route('tournaments.index') }}" class="flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="flex flex-wrap gap-4 w-full sm:w-auto">
                <select name="year" class="border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Years</option>
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <select name="status" class="border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(strtolower($s)) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition">Filter</button>
            </div>
            @if(request('year') || request('status'))
                <a href="{{ route('tournaments.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Clear Filters</a>
            @endif
        </form>
    </div>

    {{-- Tournament Grid --}}
    @if($tournaments->isEmpty())
        <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
            <span class="text-4xl block mb-4">🏆</span>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No Tournaments Found</h3>
            <p class="text-gray-500">Try adjusting your filters to see more results.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-10">
            @foreach($tournaments as $t)
                @php
                    $statusColor = match($t->status) {
                        'ONGOING' => 'bg-red-50 text-red-700 ring-red-600/20',
                        'UPCOMING' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                        'COMPLETED' => 'bg-green-50 text-green-700 ring-green-600/20',
                        default => 'bg-gray-50 text-gray-600 ring-gray-500/10'
                    };
                @endphp
                <a href="{{ route('tournaments.show', $t->tournament_id) }}" class="group bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl hover:border-indigo-200 hover:-translate-y-1 transition-all duration-300">
                    <div class="h-40 bg-gradient-to-br from-indigo-900 to-gray-900 relative p-6 flex flex-col justify-between overflow-hidden">
                        {{-- Subtle background pattern --}}
                        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-white to-transparent"></div>
                        
                        <div class="relative z-10 flex justify-between items-start">
                            <span class="text-3xl font-black text-white/90 tracking-tighter">{{ $t->year }}</span>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $statusColor }}">
                                {{ $t->status }}
                            </span>
                        </div>
                        <div class="relative z-10">
                            <h3 class="text-xl font-bold text-white">{{ $t->name }}</h3>
                            <p class="text-indigo-200 text-sm mt-1 flex items-center gap-1">
                                📍 {{ $t->host_country }}
                            </p>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                            <div class="flex items-center gap-2">
                                <span class="bg-gray-100 p-1.5 rounded-md">🗓️</span>
                                <span>{{ $t->start_date->format('M Y') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span>{{ $t->total_teams }} Teams</span>
                                <span class="bg-gray-100 p-1.5 rounded-md">🛡️</span>
                            </div>
                        </div>
                        <div class="text-indigo-600 font-medium text-sm flex items-center gap-1 group-hover:gap-2 transition-all">
                            View Details <span aria-hidden="true">&rarr;</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        
        <div class="mt-8">
            {{ $tournaments->links() }}
        </div>
    @endif
</div>
@endsection
