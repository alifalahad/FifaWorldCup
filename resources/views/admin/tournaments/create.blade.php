@extends('layouts.admin')

@section('title', 'New Tournament')
@section('page-title', 'New Tournament')
@section('breadcrumb', 'Admin › Tournaments › Create')

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

    <form method="POST" action="{{ route('admin.tournaments.store') }}" class="bg-white rounded-lg border border-gray-200 p-6">
        @csrf

        @include('admin.tournaments._form')

        <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-100">
            <a href="{{ route('admin.tournaments.index') }}"
               class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2 border border-gray-300 rounded-md transition">
                Cancel
            </a>
            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-md transition">
                Create Tournament
            </button>
        </div>
    </form>
</div>

@endsection
