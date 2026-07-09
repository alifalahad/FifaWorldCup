@extends('layouts.admin')

@section('title', 'Referees')
@section('page-title', 'Referees')
@section('breadcrumb', 'Admin › Referees')

@section('content')

@if(session('success'))
<div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-md text-sm flex items-center gap-2">
    ✅ {{ session('success') }}
</div>
@endif

<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">{{ $referees->total() }} referee(s) found</p>
    <a href="{{ route('admin.referees.create') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-md transition">
        + New Referee
    </a>
</div>

<form method="GET" action="{{ route('admin.referees.index') }}" class="flex gap-3 mb-6">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search by name or nationality…"
           class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
    <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white text-sm px-4 py-2 rounded-md transition">Search</button>
    @if(request('search'))
    <a href="{{ route('admin.referees.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center">Clear</a>
    @endif
</form>

<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    @if($referees->isEmpty())
    <div class="py-16 text-center text-gray-400 text-sm">
        No referees found. <a href="{{ route('admin.referees.create') }}" class="text-indigo-600 hover:underline">Add one →</a>
    </div>
    @else
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider border-b border-gray-100">
                <th class="px-6 py-3 text-left">Name</th>
                <th class="px-6 py-3 text-left">Nationality</th>
                <th class="px-6 py-3 text-center">FIFA Badge Year</th>
                <th class="px-6 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($referees as $referee)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-3 font-medium text-gray-800">
                    {{ $referee->first_name }} {{ $referee->last_name }}
                </td>
                <td class="px-6 py-3 text-gray-600">{{ $referee->nationality }}</td>
                <td class="px-6 py-3 text-center text-gray-500">
                    {{ $referee->fifa_badge_year ?? '—' }}
                </td>
                <td class="px-6 py-3 text-center">
                    <div class="flex items-center justify-center gap-3">
                        <a href="{{ route('admin.referees.edit', $referee->referee_id) }}"
                           class="text-gray-500 hover:text-indigo-600 text-xs font-medium">Edit</a>
                        <form method="POST" action="{{ route('admin.referees.destroy', $referee->referee_id) }}"
                              onsubmit="return confirm('Delete {{ addslashes($referee->first_name . ' ' . $referee->last_name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($referees->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $referees->links() }}</div>
    @endif
    @endif
</div>

@endsection
