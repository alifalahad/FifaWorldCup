{{--
    Shared tournament sub-navigation partial.
    Usage: @include('public.tournaments._subnav', ['tournament' => $tournament, 'active' => 'fixtures'])
    $active: 'overview' | 'fixtures' | 'standings' | 'stats'
--}}
<div class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Tournament title row (desktop: inline with tabs; mobile: stacked) --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between sm:h-14 py-2 sm:py-0 gap-1 sm:gap-0">
            {{-- Tournament name breadcrumb --}}
            <a href="{{ route('tournaments.show', $tournament->tournament_id) }}"
               class="text-base font-bold text-gray-900 hover:text-indigo-600 transition truncate shrink-0 mr-4">
                {{ $tournament->name }}
            </a>

            {{-- Tab links — scrollable on mobile --}}
            <div class="flex gap-1 overflow-x-auto scrollbar-hide pb-1 sm:pb-0 -mb-px sm:mb-0">
                @php
                    $tabs = [
                        ['route' => 'tournaments.show',      'key' => 'overview',  'label' => 'Overview'],
                        ['route' => 'tournaments.fixtures',  'key' => 'fixtures',  'label' => 'Fixtures'],
                        ['route' => 'tournaments.standings', 'key' => 'standings', 'label' => 'Standings'],
                        ['route' => 'tournaments.stats',     'key' => 'stats',     'label' => 'Stats'],
                    ];
                @endphp
                @foreach($tabs as $tab)
                    <a href="{{ route($tab['route'], $tournament->tournament_id) }}"
                       class="shrink-0 text-sm font-medium px-3 py-2 sm:py-0 sm:pb-1 rounded-t sm:rounded-none whitespace-nowrap transition
                              {{ $active === $tab['key']
                                 ? 'text-indigo-600 border-b-2 border-indigo-600 bg-indigo-50 sm:bg-transparent'
                                 : 'text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300' }}">
                        {{ $tab['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
