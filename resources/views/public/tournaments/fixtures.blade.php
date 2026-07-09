@extends('layouts.app')

@section('title', $tournament->name . ' Fixtures')
@section('meta_description', 'All match fixtures and results for ' . $tournament->name)

@section('content')
@include('public.tournaments._subnav', ['tournament' => $tournament, 'active' => 'fixtures'])

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-3xl font-extrabold text-gray-900 mb-8 sr-only">Fixtures — {{ $tournament->name }}</h1>

    @if(empty($grouped))
        <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
            <span class="text-4xl block mb-4">📅</span>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No Matches Scheduled</h3>
            <p class="text-gray-500">Fixtures have not been scheduled for this tournament yet.</p>
        </div>
    @else
        <div class="space-y-10">
        @foreach($grouped as $section)
            @php
                $stageColor = match($section['stage']) {
                    'GROUP'        => 'bg-indigo-600',
                    'ROUND_OF_16'  => 'bg-violet-600',
                    'QUARTER_FINAL'=> 'bg-purple-600',
                    'SEMI_FINAL'   => 'bg-fuchsia-600',
                    'THIRD_PLACE'  => 'bg-rose-500',
                    'FINAL'        => 'bg-amber-500',
                    default        => 'bg-gray-600'
                };
            @endphp
            <div>
                {{-- Stage header --}}
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-px flex-1 bg-gray-200"></div>
                    <span class="text-xs font-bold uppercase tracking-widest text-white px-3 py-1 rounded-full {{ $stageColor }}">
                        {{ $section['label'] }}
                    </span>
                    <div class="h-px flex-1 bg-gray-200"></div>
                </div>

                {{-- Matches --}}
                <div class="space-y-3">
                    @foreach($section['matches'] as $match)
                    @php
                        $isCompleted = $match->status === 'COMPLETED';
                        $isLive      = $match->status === 'ONGOING';
                        $isScheduled = $match->status === 'SCHEDULED';
                    @endphp
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden hover:shadow-md hover:border-indigo-200 transition-all duration-200">
                        <div class="px-4 py-3">
                            {{-- Status badge + date row --}}
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                                <div class="flex items-center gap-2">
                                    <span>{{ $match->match_date->format('D, d M Y') }}</span>
                                    @if($match->stadium)
                                        <span class="text-gray-300">·</span>
                                        <span>{{ $match->stadium->stadium_name }}</span>
                                    @endif
                                </div>
                                @if($isLive)
                                    <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-700 font-bold text-xs px-2.5 py-0.5 rounded-full ring-1 ring-red-300">
                                        <span class="w-1.5 h-1.5 bg-red-600 rounded-full animate-pulse"></span>
                                        LIVE
                                    </span>
                                @elseif($isCompleted)
                                    <span class="bg-gray-100 text-gray-600 text-xs px-2.5 py-0.5 rounded-full font-medium">FT</span>
                                @elseif($isScheduled)
                                    <span class="bg-blue-50 text-blue-600 text-xs px-2.5 py-0.5 rounded-full font-medium">Upcoming</span>
                                @else
                                    <span class="bg-gray-50 text-gray-500 text-xs px-2.5 py-0.5 rounded-full font-medium">{{ $match->status }}</span>
                                @endif
                            </div>

                            {{-- Main score row --}}
                            <div class="flex items-center justify-between">
                                {{-- Home team --}}
                                <div class="flex-1 text-right">
                                    <a href="{{ route('teams.show', $match->homeTeam->team_id) }}"
                                       class="text-base font-bold text-gray-900 hover:text-indigo-600 transition">
                                        {{ $match->homeTeam->country_name }}
                                    </a>
                                    <div class="text-xs text-gray-400 font-mono">{{ $match->homeTeam->abbreviation }}</div>
                                </div>

                                {{-- Score / VS --}}
                                <div class="mx-6 text-center min-w-[80px]">
                                    @if($isCompleted || $isLive)
                                        <div class="flex items-center justify-center gap-2">
                                            <span class="text-3xl font-black {{ $match->home_score > $match->away_score ? 'text-gray-900' : 'text-gray-400' }}">
                                                {{ $match->home_score }}
                                            </span>
                                            <span class="text-gray-300 font-light text-xl">–</span>
                                            <span class="text-3xl font-black {{ $match->away_score > $match->home_score ? 'text-gray-900' : 'text-gray-400' }}">
                                                {{ $match->away_score }}
                                            </span>
                                        </div>
                                        {{-- Extra time / Penalties indicator --}}
                                        @if($match->has_penalties === 'Y')
                                            <div class="text-xs text-gray-400 mt-1">After Penalties</div>
                                        @elseif($match->has_extra_time === 'Y')
                                            <div class="text-xs text-gray-400 mt-1">After Extra Time</div>
                                        @endif
                                    @else
                                        <span class="text-2xl font-semibold text-gray-300">vs</span>
                                    @endif
                                </div>

                                {{-- Away team --}}
                                <div class="flex-1 text-left">
                                    <a href="{{ route('teams.show', $match->awayTeam->team_id) }}"
                                       class="text-base font-bold text-gray-900 hover:text-indigo-600 transition">
                                        {{ $match->awayTeam->country_name }}
                                    </a>
                                    <div class="text-xs text-gray-400 font-mono">{{ $match->awayTeam->abbreviation }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        @endforeach
        </div>
    @endif
</div>
@endsection
