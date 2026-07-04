@extends('layouts.admin')

@section('title', 'Edit ' . $team->country_name)
@section('page-title', 'Edit Team')
@section('breadcrumb', 'Admin › Teams › ' . $team->country_name)

@section('content')
<div class="max-w-2xl">
    @if($errors->any())
    <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-md text-sm text-red-800">
        <p class="font-semibold mb-1">Please fix the following errors:</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.teams.update', $team->team_id) }}" class="bg-white rounded-lg border border-gray-200 p-6">
        @csrf @method('PATCH')
        @include('admin.teams._form')
        <div class="flex items-center justify-between gap-3 mt-6 pt-6 border-t border-gray-100">
            <form method="POST" action="{{ route('admin.teams.destroy', $team->team_id) }}"
                  onsubmit="return confirm('Delete {{ addslashes($team->country_name) }}? This will also remove all tournament registrations.')">
                @csrf @method('DELETE')
                <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium">Delete this team</button>
            </form>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.teams.index') }}"
                   class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2 border border-gray-300 rounded-md transition">Cancel</a>
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-md transition">
                    Save Changes
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
