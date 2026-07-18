@extends('layouts.admin')

@section('title', 'Register Team — ' . $tournament->name)
@section('page-title', 'Register Team')
@section('breadcrumb', 'Admin › Tournaments › ' . $tournament->name . ' › Register Team')

@section('content')
<div class="max-w-2xl">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.tournaments.show', $tournament->tournament_id) }}"
           class="text-sm text-gray-500 hover:text-gray-700">← Back to {{ $tournament->name }}</a>
    </div>

    {{-- Info banner --}}
    <div class="mb-5 p-4 bg-blue-50 border border-blue-200 rounded-md text-sm text-blue-800">
        <strong>{{ $tournament->name }}</strong> ·
        {{ $registeredTeamIds ? count($registeredTeamIds) : 0 }} of {{ $tournament->total_teams }} teams registered
    </div>

    {{-- Validation errors --}}
    @if($errors->any())
    <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-md text-sm text-red-800">
        <p class="font-semibold mb-1">Please fix the following errors:</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST"
          action="{{ route('admin.tournaments.register-team.store', $tournament->tournament_id) }}"
          class="bg-white rounded-lg border border-gray-200 p-6 space-y-5">
        @csrf

        {{-- Team picker --}}
        <div>
            <label for="team_id" class="block text-sm font-medium text-gray-700 mb-1">
                Team <span class="text-red-500">*</span>
            </label>
            <select id="team_id" name="team_id"
                    class="w-full border {{ $errors->has('team_id') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Select a team…</option>
                @foreach($teams as $team)
                @php $alreadyIn = in_array($team->team_id, $registeredTeamIds); @endphp
                <option value="{{ $team->team_id }}"
                        {{ old('team_id') == $team->team_id ? 'selected' : '' }}
                        {{ $alreadyIn ? 'disabled' : '' }}>
                    {{ $team->country_name }} ({{ $team->abbreviation }})
                    {{ $alreadyIn ? '— already registered' : '' }}
                </option>
                @endforeach
            </select>
            @error('team_id')
            <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        {{-- ── Dynamic Group Assignment (Alpine.js + API) ──────────────────── --}}
        <div
            x-data="{
                groups: [],
                selectedGroup: '{{ old('group_id') }}',
                loading: false,
                loaded: false,

                async loadGroups() {
                    this.loading = true;
                    try {
                        const res  = await fetch('/api/tournaments/{{ $tournament->tournament_id }}/groups');
                        const data = await res.json();
                        this.groups = data.groups;
                        this.loaded = true;
                    } catch(e) {
                        this.groups = [];
                    } finally {
                        this.loading = false;
                    }
                }
            }"
            x-init="loadGroups()"
        >
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Group <span class="text-gray-400">(optional — assign to a group)</span>
            </label>

            {{-- Loading spinner --}}
            <div x-show="loading" class="flex items-center gap-2 text-sm text-gray-400 py-2">
                <svg class="h-4 w-4 animate-spin text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Loading groups…
            </div>

            {{-- No groups state --}}
            <template x-if="loaded && groups.length === 0">
                <p class="text-xs text-gray-400 italic mt-1">No groups exist for this tournament yet.</p>
            </template>

            {{-- Group card-picker grid --}}
            <template x-if="loaded && groups.length > 0">
                <div class="space-y-3">
                    {{-- Hidden input that carries the actual submitted value --}}
                    <input type="hidden" name="group_id" :value="selectedGroup">

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">

                        {{-- "Unassigned" card --}}
                        <button
                            type="button"
                            @click="selectedGroup = ''"
                            :class="selectedGroup === ''
                                ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-200'
                                : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50'"
                            class="relative flex flex-col items-center justify-center px-3 py-3 rounded-lg border text-sm font-medium transition-all duration-150 cursor-pointer"
                        >
                            <span class="text-lg mb-0.5">🚫</span>
                            <span :class="selectedGroup === '' ? 'text-indigo-700' : 'text-gray-600'"
                                  class="font-semibold text-xs">Unassigned</span>
                        </button>

                        {{-- Dynamic group cards --}}
                        <template x-for="group in groups" :key="group.group_id">
                            <button
                                type="button"
                                @click="selectedGroup = group.group_id"
                                :class="String(selectedGroup) === String(group.group_id)
                                    ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-200'
                                    : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50'"
                                class="relative flex flex-col items-center justify-center px-3 py-3 rounded-lg border transition-all duration-150 cursor-pointer"
                            >
                                {{-- Team count badge (top-right corner) --}}
                                <span
                                    class="absolute top-1.5 right-1.5 inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold rounded-full"
                                    :class="group.team_count > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-400'"
                                    x-text="group.team_count"
                                ></span>
                                <span class="text-lg mb-0.5">🏟️</span>
                                <span
                                    :class="String(selectedGroup) === String(group.group_id) ? 'text-indigo-700' : 'text-gray-700'"
                                    class="font-bold text-xs"
                                    x-text="'Group ' + group.group_name"
                                ></span>
                                <span class="text-[10px] text-gray-400 mt-0.5" x-text="group.team_count + ' team(s)'"></span>
                            </button>
                        </template>
                    </div>

                    <p class="text-xs text-gray-400">
                        The badge number shows how many teams are already in each group.
                    </p>
                </div>
            </template>

            @error('group_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Coach assignment --}}
        <div>
            <label for="coach_id" class="block text-sm font-medium text-gray-700 mb-1">
                Coach <span class="text-gray-400">(optional)</span>
            </label>
            <select id="coach_id" name="coach_id"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">No coach assigned</option>
                @foreach($coaches as $coach)
                <option value="{{ $coach->coach_id }}" {{ old('coach_id') == $coach->coach_id ? 'selected' : '' }}>
                    {{ $coach->first_name }} {{ $coach->last_name }} ({{ $coach->nationality }})
                </option>
                @endforeach
            </select>
            @error('coach_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Seed position --}}
        <div>
            <label for="seed_position" class="block text-sm font-medium text-gray-700 mb-1">
                Seed Position <span class="text-gray-400">(optional, 1–99)</span>
            </label>
            <input type="number" id="seed_position" name="seed_position"
                   value="{{ old('seed_position') }}"
                   min="1" max="99" placeholder="e.g. 1"
                   class="w-full border {{ $errors->has('seed_position') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            @error('seed_position')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.tournaments.show', $tournament->tournament_id) }}"
               class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2 border border-gray-300 rounded-md transition">
                Cancel
            </a>
            <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-5 py-2 rounded-md transition">
                Register Team
            </button>
        </div>
    </form>
</div>
@endsection
