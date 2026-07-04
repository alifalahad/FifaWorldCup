{{-- Shared form partial used by both create and edit --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Name --}}
    <div class="md:col-span-2">
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tournament Name <span class="text-red-500">*</span></label>
        <input type="text" id="name" name="name" value="{{ old('name', $tournament->name ?? '') }}"
               placeholder="e.g. FIFA World Cup 2026"
               class="w-full border {{ $errors->has('name') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Year --}}
    <div>
        <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Year <span class="text-red-500">*</span></label>
        <input type="number" id="year" name="year" value="{{ old('year', $tournament->year ?? '') }}"
               placeholder="e.g. 2026" min="1900" max="2100"
               class="w-full border {{ $errors->has('year') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('year')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Host Country --}}
    <div>
        <label for="host_country" class="block text-sm font-medium text-gray-700 mb-1">Host Country <span class="text-red-500">*</span></label>
        <input type="text" id="host_country" name="host_country" value="{{ old('host_country', $tournament->host_country ?? '') }}"
               placeholder="e.g. United States, Canada, Mexico"
               class="w-full border {{ $errors->has('host_country') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('host_country')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Start Date --}}
    <div>
        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date <span class="text-red-500">*</span></label>
        <input type="date" id="start_date" name="start_date"
               value="{{ old('start_date', isset($tournament->start_date) ? \Carbon\Carbon::parse($tournament->start_date)->format('Y-m-d') : '') }}"
               class="w-full border {{ $errors->has('start_date') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('start_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- End Date --}}
    <div>
        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date <span class="text-red-500">*</span></label>
        <input type="date" id="end_date" name="end_date"
               value="{{ old('end_date', isset($tournament->end_date) ? \Carbon\Carbon::parse($tournament->end_date)->format('Y-m-d') : '') }}"
               class="w-full border {{ $errors->has('end_date') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('end_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Total Teams --}}
    <div>
        <label for="total_teams" class="block text-sm font-medium text-gray-700 mb-1">Total Teams <span class="text-red-500">*</span></label>
        <input type="number" id="total_teams" name="total_teams" value="{{ old('total_teams', $tournament->total_teams ?? 32) }}"
               min="2" max="48"
               class="w-full border {{ $errors->has('total_teams') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('total_teams')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Status --}}
    <div>
        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
        <select id="status" name="status"
                class="w-full border {{ $errors->has('status') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            @foreach($statuses as $s)
            <option value="{{ $s }}" {{ old('status', $tournament->status ?? 'PLANNED') === $s ? 'selected' : '' }}>
                {{ $s }}
            </option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

</div>
