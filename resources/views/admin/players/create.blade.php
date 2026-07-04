@extends('layouts.admin')

@section('title', 'New Player')
@section('page-title', 'New Player')
@section('breadcrumb', 'Admin › Players › Create')

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
    <form method="POST" action="{{ route('admin.players.store') }}" class="bg-white rounded-lg border border-gray-200 p-6">
        @csrf
        @include('admin.players._form')
        <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-100">
            <a href="{{ route('admin.players.index') }}"
               class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2 border border-gray-300 rounded-md transition">Cancel</a>
            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-md transition">
                Create Player
            </button>
        </div>
    </form>
</div>
@endsection
