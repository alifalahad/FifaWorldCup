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

        {{-- Group assignment --}}
        <div>
            <label for="group_id" class="block text-sm font-medium text-gray-700 mb-1">
                Group <span class="text-gray-400">(optional — assign to a group)</span>
            </label>
            @if($groups->isEmpty())
            <p class="text-xs text-gray-400 italic mt-1">No groups exist for this tournament yet.</p>
            <input type="hidden" name="group_id" value="">
            @else
            <select id="group_id" name="group_id"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Unassigned</option>
                @foreach($groups as $group)
                <option value="{{ $group->group_id }}" {{ old('group_id') == $group->group_id ? 'selected' : '' }}>
                    Group {{ $group->group_name }}
                </option>
                @endforeach
            </select>
            @endif
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
