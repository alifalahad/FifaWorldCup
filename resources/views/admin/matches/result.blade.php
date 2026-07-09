@extends('layouts.admin')

@section('title', 'Enter Result')
@section('page-title', 'Enter Match Result')
@section('breadcrumb', 'Admin › Matches › Result')

@section('content')
<div class="max-w-xl">

    <div class="mb-5">
        <a href="{{ route('admin.matches.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700">← Back to Matches</a>
    </div>

    {{-- Match header card --}}
    <div class="bg-white border border-gray-200 rounded-lg p-5 mb-6">
        <p class="text-xs text-gray-400 uppercase tracking-wider mb-2">
            {{ $match->tournament->name ?? '' }} · {{ str_replace('_', ' ', $match->stage) }}
            @if($match->group) · Group {{ $match->group?->group_name }} @endif
        </p>
        <div class="flex items-center justify-center gap-6">
            <div class="text-right flex-1">
                <p class="text-xl font-bold text-gray-800">{{ $match->homeTeam->country_name ?? '—' }}</p>
                <p class="text-xs text-gray-400">{{ $match->homeTeam->abbreviation ?? '' }}</p>
            </div>
            <div class="text-2xl font-bold text-gray-400">vs</div>
            <div class="text-left flex-1">
                <p class="text-xl font-bold text-gray-800">{{ $match->awayTeam->country_name ?? '—' }}</p>
                <p class="text-xs text-gray-400">{{ $match->awayTeam->abbreviation ?? '' }}</p>
            </div>
        </div>
        <p class="text-xs text-center text-gray-400 mt-2">
            {{ $match->match_date->format('d F Y') }}
        </p>
    </div>

    {{-- Errors --}}
    @if($errors->any())
    <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-md text-sm text-red-800">
        @foreach($errors->all() as $err)<p>• {{ $err }}</p>@endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('admin.matches.result.store', $match->match_id) }}"
          class="bg-white rounded-lg border border-gray-200 p-6 space-y-5">
        @csrf

        {{-- Score row --}}
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label for="home_score" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ $match->homeTeam->abbreviation ?? 'Home' }} Score <span class="text-red-500">*</span>
                </label>
                <input type="number" id="home_score" name="home_score"
                       value="{{ old('home_score', $match->home_score ?? '') }}"
                       min="0" max="99"
                       class="w-full border {{ $errors->has('home_score') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-2xl font-bold text-center focus:outline-none focus:ring-2 focus:ring-indigo-400">
                @error('home_score')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="away_score" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ $match->awayTeam->abbreviation ?? 'Away' }} Score <span class="text-red-500">*</span>
                </label>
                <input type="number" id="away_score" name="away_score"
                       value="{{ old('away_score', $match->away_score ?? '') }}"
                       min="0" max="99"
                       class="w-full border {{ $errors->has('away_score') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-2xl font-bold text-center focus:outline-none focus:ring-2 focus:ring-indigo-400">
                @error('away_score')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Extra time / Penalties --}}
        @if($match->stage !== 'GROUP')
        <div class="flex items-center gap-8 pt-1">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="has_extra_time" value="1" id="has_extra_time"
                       {{ old('has_extra_time', $match->has_extra_time === 'Y') ? 'checked' : '' }}
                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                <span class="text-sm text-gray-700">Extra Time played</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="has_penalties" value="1" id="has_penalties"
                       {{ old('has_penalties', $match->has_penalties === 'Y') ? 'checked' : '' }}
                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                <span class="text-sm text-gray-700">Decided by Penalties</span>
            </label>
        </div>
        @else
        {{-- Group stage: extra time/penalties not applicable --}}
        <input type="hidden" name="has_extra_time" value="0">
        <input type="hidden" name="has_penalties"  value="0">
        @endif

        {{-- Status --}}
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Match Status <span class="text-red-500">*</span></label>
            <select id="status" name="status"
                    class="w-full border {{ $errors->has('status') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                @foreach($statuses as $s)
                <option value="{{ $s }}" {{ old('status', $match->status) === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
            @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="p-3 bg-blue-50 border border-blue-200 rounded-md text-xs text-blue-700">
            💡 Setting status to <strong>COMPLETED</strong> will cause this result to appear in the group standings view automatically.
        </div>

        <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
            <a href="{{ route('admin.matches.index') }}"
               class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2 border border-gray-300 rounded-md transition">
                Cancel
            </a>
            <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-5 py-2 rounded-md transition">
                Save Result
            </button>
        </div>
    </form>
</div>
@endsection
