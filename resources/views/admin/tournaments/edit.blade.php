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
</div>

@endsection
