@extends('layouts.admin')

@section('title', 'Stadiums')
@section('page-title', 'Stadiums')
@section('breadcrumb', 'Admin › Stadiums')

@section('content')

@if(session('success'))
<div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-md text-sm flex items-center gap-2">
    ✅ {{ session('success') }}
</div>
@endif

<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">{{ $stadiums->total() }} stadium(s) found</p>
    <a href="{{ route('admin.stadiums.create') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-md transition">
        + New Stadium
    </a>
</div>

<form method="GET" action="{{ route('admin.stadiums.index') }}" class="flex gap-3 mb-6">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search by name, city or country…"
           class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
    <select name="surface_type" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        <option value="">All surfaces</option>
        @foreach($surfaceTypes as $s)
        <option value="{{ $s }}" {{ request('surface_type') === $s ? 'selected' : '' }}>{{ $s }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white text-sm px-4 py-2 rounded-md transition">Filter</button>
    @if(request('search') || request('surface_type'))
    <a href="{{ route('admin.stadiums.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center">Clear</a>
    @endif
</form>

<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    @if($stadiums->isEmpty())
    <div class="py-16 text-center text-gray-400 text-sm">
        No stadiums found. <a href="{{ route('admin.stadiums.create') }}" class="text-indigo-600 hover:underline">Add one →</a>
    </div>
    @else
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider border-b border-gray-100">
                <th class="px-6 py-3 text-left">Name</th>
                <th class="px-6 py-3 text-left">City</th>
                <th class="px-6 py-3 text-left">Country</th>
                <th class="px-6 py-3 text-right">Capacity</th>
                <th class="px-6 py-3 text-center">Surface</th>
                <th class="px-6 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($stadiums as $stadium)
            @php
                $surfColors = [
                    'GRASS'      => 'bg-green-100 text-green-700',
                    'ARTIFICIAL' => 'bg-blue-100 text-blue-700',
                    'HYBRID'     => 'bg-purple-100 text-purple-700',
                ];
            @endphp
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-3 font-medium text-gray-800">{{ $stadium->name }}</td>
                <td class="px-6 py-3 text-gray-600">{{ $stadium->city }}</td>
                <td class="px-6 py-3 text-gray-600">{{ $stadium->country }}</td>
                <td class="px-6 py-3 text-right text-gray-600">{{ number_format($stadium->capacity) }}</td>
                <td class="px-6 py-3 text-center">
                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $surfColors[$stadium->surface_type] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ $stadium->surface_type }}
                    </span>
                </td>
                <td class="px-6 py-3 text-center">
                    <div class="flex items-center justify-center gap-3">
                        <a href="{{ route('admin.stadiums.edit', $stadium->stadium_id) }}"
                           class="text-gray-500 hover:text-indigo-600 text-xs font-medium">Edit</a>
                        <form method="POST" action="{{ route('admin.stadiums.destroy', $stadium->stadium_id) }}"
                              onsubmit="return confirm('Delete {{ addslashes($stadium->name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($stadiums->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $stadiums->links() }}</div>
    @endif
    @endif
</div>

@endsection
