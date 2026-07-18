{{--
    Shared tournament sub-navigation partial.
    Usage: @include('public.tournaments._subnav', ['tournament' => $tournament, 'active' => 'fixtures'])
    $active: 'overview' | 'fixtures' | 'standings' | 'stats'
--}}
<div class="bg-white/80 backdrop-blur-md border-b border-gray-100 shadow-sm sticky top-[65px] z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-3 gap-3 sm:gap-0">
            {{-- Tournament name breadcrumb --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center border border-indigo-100 shrink-0">
                    <span class="text-lg">🏆</span>
                </div>
                <a href="{{ route('tournaments.show', $tournament->tournament_id) }}"
                   class="text-lg font-bold text-gray-900 hover:text-indigo-600 transition truncate shrink-0">
                    {{ $tournament->name }}
                </a>
            </div>

            {{-- Pill tab links — scrollable on mobile --}}
            <div class="flex gap-2 overflow-x-auto scrollbar-hide pb-1 sm:pb-0">
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
                       class="shrink-0 text-sm font-semibold px-4 py-2 rounded-full whitespace-nowrap transition-all duration-300
                              {{ $active === $tab['key']
                                 ? 'bg-gray-900 text-white shadow-md'
                                 : 'bg-gray-50 text-gray-600 hover:bg-gray-100 hover:text-gray-900 border border-transparent hover:border-gray-200' }}">
                        {{ $tab['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
