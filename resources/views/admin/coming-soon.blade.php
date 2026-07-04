@extends('layouts.admin')

@section('title', $section)
@section('page-title', $section)
@section('breadcrumb', 'Admin › ' . $section)

@section('content')
<div class="flex flex-col items-center justify-center py-24 text-center">
    <div class="text-5xl mb-4">🚧</div>
    <h2 class="text-xl font-semibold text-gray-700 mb-2">{{ $section }} Management</h2>
    <p class="text-gray-500 text-sm mb-6">This section is being built. It will be ready in the next prompt!</p>
    <a href="{{ route('admin.dashboard') }}"
       class="text-sm bg-indigo-600 text-white px-5 py-2 rounded-md hover:bg-indigo-700 transition">
        ← Back to Dashboard
    </a>
</div>
@endsection
