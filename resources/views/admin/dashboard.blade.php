@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Admin › Overview')

@section('content')

{{-- ── Summary stat cards ──────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-5 gap-4 mb-8">

    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-2xl">🏆</div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['tournaments'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5 font-medium uppercase tracking-wide">Tournaments</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-2xl">🛡️</div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['teams'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5 font-medium uppercase tracking-wide">Teams</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-2xl">🧑‍🤝‍🧑</div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['players'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5 font-medium uppercase tracking-wide">Players</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-xl bg-violet-50 flex items-center justify-center text-2xl">📅</div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['matches_played'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5 font-medium uppercase tracking-wide">Matches</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center text-2xl">⚽</div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['goals'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5 font-medium uppercase tracking-wide">Goals</p>
        </div>
    </div>

</div>

{{-- ── Secondary stats row ──────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 flex justify-between items-center shadow-sm">
        <span class="text-sm text-gray-600">Coaches</span>
        <span class="font-bold text-gray-900">{{ $stats['coaches'] }}</span>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 flex justify-between items-center shadow-sm">
        <span class="text-sm text-gray-600">Stadiums</span>
        <span class="font-bold text-gray-900">{{ $stats['stadiums'] }}</span>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 flex justify-between items-center shadow-sm">
        <span class="text-sm text-gray-600">Referees</span>
        <span class="font-bold text-gray-900">{{ $stats['referees'] }}</span>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 flex justify-between items-center shadow-sm">
        <span class="text-sm text-gray-600">Live Now</span>
        <span class="font-bold {{ $stats['live_matches'] > 0 ? 'text-green-600' : 'text-gray-900' }}">
            {{ $stats['live_matches'] }}
            @if($stats['live_matches'] > 0)
                <span class="inline-block w-2 h-2 bg-green-500 rounded-full animate-pulse ml-1"></span>
            @endif
        </span>
    </div>
</div>

{{-- ── Chart Section ────────────────────────────────────────────────────── --}}
<div
    id="charts-section"
    x-data="{
        loading: true,
        error: false,
        charts: {},

        async init() {
            try {
                const res  = await fetch('/api/dashboard/stats');
                if (!res.ok) throw new Error('API error');
                const data = await res.json();
                this.loading = false;
                this.$nextTick(() => this.renderCharts(data));
            } catch(e) {
                this.loading = false;
                this.error = true;
            }
        },

        renderCharts(data) {
            Chart.defaults.font.family = 'Inter, system-ui, sans-serif';

            // ── Chart 1: Goals per Tournament (Bar) ─────────────────────────
            const ctx1 = document.getElementById('chart-goals-tournament');
            if (ctx1 && data.goals_per_tournament.length) {
                new Chart(ctx1, {
                    type: 'bar',
                    data: {
                        labels: data.goals_per_tournament.map(d => d.label),
                        datasets: [{
                            label: 'Goals',
                            data: data.goals_per_tournament.map(d => d.value),
                            backgroundColor: 'rgba(99, 102, 241, 0.8)',
                            borderColor: 'rgb(99, 102, 241)',
                            borderWidth: 1,
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { precision: 0 } },
                            x: { grid: { display: false }, ticks: { maxRotation: 30 } }
                        }
                    }
                });
            }

            // ── Chart 2: Top Scoring Teams (Horizontal Bar) ──────────────────
            const ctx2 = document.getElementById('chart-top-teams');
            if (ctx2 && data.top_scoring_teams.length) {
                new Chart(ctx2, {
                    type: 'bar',
                    data: {
                        labels: data.top_scoring_teams.map(d => d.label),
                        datasets: [{
                            label: 'Goals',
                            data: data.top_scoring_teams.map(d => d.value),
                            backgroundColor: [
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(5, 150, 105, 0.8)',
                                'rgba(4, 120, 87, 0.8)',
                                'rgba(6, 95, 70, 0.8)',
                                'rgba(209, 250, 229, 0.9)',
                                'rgba(167, 243, 208, 0.9)',
                                'rgba(110, 231, 183, 0.9)',
                                'rgba(52, 211, 153, 0.9)',
                            ],
                            borderRadius: 5,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { precision: 0 } },
                            y: { grid: { display: false } }
                        }
                    }
                });
            }

            // ── Chart 3: Goals by Type (Doughnut) ────────────────────────────
            const ctx3 = document.getElementById('chart-goal-types');
            if (ctx3) {
                new Chart(ctx3, {
                    type: 'doughnut',
                    data: {
                        labels: data.goals_by_type.map(d => d.label),
                        datasets: [{
                            data: data.goals_by_type.map(d => d.value),
                            backgroundColor: ['rgba(99,102,241,0.85)', 'rgba(239,68,68,0.85)', 'rgba(245,158,11,0.85)'],
                            borderWidth: 2,
                            borderColor: '#fff',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: { position: 'bottom', labels: { padding: 14, font: { size: 12 } } }
                        }
                    }
                });
            }

            // ── Chart 4: Match Status (Doughnut) ─────────────────────────────
            const ctx4 = document.getElementById('chart-match-status');
            if (ctx4) {
                new Chart(ctx4, {
                    type: 'doughnut',
                    data: {
                        labels: data.match_status_summary.map(d => d.label),
                        datasets: [{
                            data: data.match_status_summary.map(d => d.value),
                            backgroundColor: ['rgba(59,130,246,0.85)', 'rgba(239,68,68,0.85)', 'rgba(16,185,129,0.85)'],
                            borderWidth: 2,
                            borderColor: '#fff',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: { position: 'bottom', labels: { padding: 14, font: { size: 12 } } }
                        }
                    }
                });
            }

            // ── Chart 5: Cards per Tournament (Grouped Bar) ───────────────────
            const ctx5 = document.getElementById('chart-cards');
            if (ctx5 && data.cards_per_tournament.length) {
                new Chart(ctx5, {
                    type: 'bar',
                    data: {
                        labels: data.cards_per_tournament.map(d => d.label),
                        datasets: [
                            {
                                label: 'Yellow Cards',
                                data: data.cards_per_tournament.map(d => d.yellow),
                                backgroundColor: 'rgba(245, 158, 11, 0.8)',
                                borderRadius: 5,
                            },
                            {
                                label: 'Red Cards',
                                data: data.cards_per_tournament.map(d => d.red),
                                backgroundColor: 'rgba(239, 68, 68, 0.8)',
                                borderRadius: 5,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'top' } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { precision: 0 } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }
        }
    }"
    x-init="init()"
    class="mb-8"
>
    {{-- Loading skeleton --}}
    <template x-if="loading">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @for($i = 0; $i < 4; $i++)
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="h-4 w-40 bg-gray-100 rounded animate-pulse mb-4"></div>
                <div class="h-52 bg-gray-50 rounded-lg animate-pulse"></div>
            </div>
            @endfor
        </div>
    </template>

    {{-- Error state --}}
    <template x-if="error">
        <div class="text-center py-12 text-gray-400">
            <span class="text-3xl block mb-2">⚠️</span>
            <p class="text-sm">Could not load chart data. Ensure the server is running.</p>
        </div>
    </template>

    {{-- Charts grid --}}
    <template x-if="!loading && !error">
        <div class="space-y-6">

            {{-- Row 1: Goals per Tournament + Top Scoring Teams --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Chart 1: Goals per Tournament --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="text-sm font-bold text-gray-800">Goals per Tournament</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Total goals scored across each edition</p>
                        </div>
                        <span class="text-xl">⚽</span>
                    </div>
                    <div class="h-56">
                        <canvas id="chart-goals-tournament"></canvas>
                    </div>
                </div>

                {{-- Chart 2: Top Scoring Teams --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="text-sm font-bold text-gray-800">Top Scoring Teams</h3>
                            <p class="text-xs text-gray-400 mt-0.5">All-time top 8 teams by goals scored</p>
                        </div>
                        <span class="text-xl">🏅</span>
                    </div>
                    <div class="h-56">
                        <canvas id="chart-top-teams"></canvas>
                    </div>
                </div>

            </div>

            {{-- Row 2: Goal Types + Match Status + Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Chart 3: Goals by Type --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="text-sm font-bold text-gray-800">Goal Types</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Normal · Own Goal · Penalty</p>
                        </div>
                        <span class="text-xl">🥅</span>
                    </div>
                    <div class="h-52">
                        <canvas id="chart-goal-types"></canvas>
                    </div>
                </div>

                {{-- Chart 4: Match Status --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="text-sm font-bold text-gray-800">Match Status</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Scheduled · Live · Completed</p>
                        </div>
                        <span class="text-xl">📊</span>
                    </div>
                    <div class="h-52">
                        <canvas id="chart-match-status"></canvas>
                    </div>
                </div>

                {{-- Chart 5: Cards per Tournament --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="text-sm font-bold text-gray-800">Disciplinary Cards</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Yellow vs Red per edition</p>
                        </div>
                        <span class="text-xl">🟨</span>
                    </div>
                    <div class="h-52">
                        <canvas id="chart-cards"></canvas>
                    </div>
                </div>

            </div>
        </div>
    </template>
</div>

{{-- ── Recent Matches table ─────────────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-sm font-bold text-gray-800">Recent Matches</h2>
        <a href="{{ route('admin.matches.index') }}" class="text-xs text-indigo-600 hover:underline font-medium">View all →</a>
    </div>

    @if($recent_matches->isEmpty())
    <div class="px-6 py-10 text-center text-gray-400 text-sm">
        No matches recorded yet. <a href="{{ route('admin.matches.index') }}" class="text-indigo-600 hover:underline">Schedule the first match →</a>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-3 text-left">Date</th>
                    <th class="px-6 py-3 text-left">Tournament</th>
                    <th class="px-6 py-3 text-center">Home</th>
                    <th class="px-6 py-3 text-center">Score</th>
                    <th class="px-6 py-3 text-center">Away</th>
                    <th class="px-6 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($recent_matches as $match)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-3 text-gray-600 whitespace-nowrap text-xs">
                        {{ $match->match_date->format('d M Y') }}
                    </td>
                    <td class="px-6 py-3 text-gray-600 whitespace-nowrap text-xs">
                        {{ $match->tournament->name ?? '—' }}
                    </td>
                    <td class="px-6 py-3 text-right font-semibold text-gray-800">
                        {{ $match->homeTeam->abbreviation ?? '—' }}
                    </td>
                    <td class="px-6 py-3 text-center font-bold text-gray-900">
                        @if($match->status === 'COMPLETED')
                            {{ $match->home_score }} – {{ $match->away_score }}
                        @else
                            <span class="text-gray-400 font-normal">vs</span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-left font-semibold text-gray-800">
                        {{ $match->awayTeam->abbreviation ?? '—' }}
                    </td>
                    <td class="px-6 py-3 text-center">
                        @php
                            $cls = match($match->status) {
                                'COMPLETED' => 'bg-green-100 text-green-700',
                                'ONGOING'   => 'bg-red-100 text-red-700',
                                'SCHEDULED' => 'bg-blue-100 text-blue-700',
                                'POSTPONED' => 'bg-yellow-100 text-yellow-700',
                                default     => 'bg-gray-100 text-gray-500',
                            };
                        @endphp
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $cls }}">
                            {{ $match->status }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- ── Quick action buttons ─────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
    <a href="{{ route('admin.tournaments.index') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-3 rounded-xl text-center transition shadow-sm">
        + New Tournament
    </a>
    <a href="{{ route('admin.teams.index') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-3 rounded-xl text-center transition shadow-sm">
        + New Team
    </a>
    <a href="{{ route('admin.players.index') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-3 rounded-xl text-center transition shadow-sm">
        + New Player
    </a>
    <a href="{{ route('admin.matches.index') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-3 rounded-xl text-center transition shadow-sm">
        + Schedule Match
    </a>
</div>

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

@endsection
