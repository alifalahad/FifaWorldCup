@extends('layouts.admin')

@section('title', 'Edit ' . $tournament->name)
@section('page-title', 'Edit Tournament')
@section('breadcrumb', 'Admin › Tournaments › ' . $tournament->name . ' › Edit')

@section('content')

<div class="max-w-3xl">

    {{-- Validation error summary --}}
    @if($errors->any())
    <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-md text-sm text-red-800">
        <p class="font-semibold mb-1">Please fix the following errors:</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="mb-5 p-3 bg-green-50 border border-green-200 text-green-800 rounded-md text-sm flex items-center gap-2">
        ✅ {{ session('success') }}
    </div>
    @endif

    {{-- ── Tournament Details Form ── --}}
    <form method="POST" action="{{ route('admin.tournaments.update', $tournament->tournament_id) }}" class="bg-white rounded-lg border border-gray-200 p-6">
        @csrf
        @method('PATCH')

        @include('admin.tournaments._form')

        <div class="flex items-center justify-between gap-3 mt-6 pt-6 border-t border-gray-100">
            {{-- Danger zone: delete --}}
            <form method="POST" action="{{ route('admin.tournaments.destroy', $tournament->tournament_id) }}"
                  onsubmit="return confirm('Permanently delete {{ addslashes($tournament->name) }}? All related groups and matches will also be deleted.')">
                @csrf @method('DELETE')
                <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium">
                    Delete this tournament
                </button>
            </form>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.tournaments.show', $tournament->tournament_id) }}"
                   class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2 border border-gray-300 rounded-md transition">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-md transition">
                    Save Changes
                </button>
            </div>
        </div>
    </form>

    {{-- ── Group Draw Management ── --}}
    {{-- Placed OUTSIDE the main form to avoid nested <form> issues --}}
    <div class="bg-white rounded-lg border border-gray-200 mt-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Group Draw
                <span class="ml-2 text-xs font-normal text-gray-400">({{ $tournament->groups->count() }} groups)</span>
            </h2>
            <p class="text-xs text-gray-400 mt-1">Add the groups for this tournament's draw (e.g. A, B, C…). Teams can then be assigned to a group when you register them.</p>
        </div>

        {{-- Existing groups with delete buttons --}}
        @if($tournament->groups->isEmpty())
        <div class="px-6 py-4 text-sm text-gray-400 italic">
            No groups created yet. Add one below.
        </div>
        @else
        <div class="flex flex-wrap gap-3 px-6 py-4">
            @foreach($tournament->groups->sortBy('group_name') as $group)
            <div class="flex items-center gap-2 bg-indigo-50 border border-indigo-200 rounded-lg px-3 py-2">
                <span class="text-base font-bold text-indigo-700">Group {{ $group->group_name }}</span>
                <form method="POST"
                      action="{{ route('admin.tournaments.groups.destroy', [$tournament->tournament_id, $group->group_id]) }}"
                      onsubmit="return confirm('Remove Group {{ $group->group_name }}? Teams assigned to this group will become unassigned.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="text-red-400 hover:text-red-600 text-xs font-bold leading-none"
                            title="Remove group">✕</button>
                </form>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Add new group form --}}
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-lg">
            <form method="POST"
                  action="{{ route('admin.tournaments.groups.store', $tournament->tournament_id) }}"
                  class="flex items-end gap-3">
                @csrf
                <div>
                    <label for="group_name" class="block text-xs font-medium text-gray-600 mb-1">
                        Group Name <span class="text-gray-400">(e.g. A, B, Group A)</span>
                    </label>
                    <input type="text"
                           id="group_name"
                           name="group_name"
                           placeholder="e.g. A"
                           maxlength="10"
                           value="{{ old('group_name') }}"
                           class="border {{ $errors->has('group_name') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm w-36 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    @error('group_name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-md transition">
                    + Add Group
                </button>
            </form>
        </div>
    </div>

</div>

@endsection

