{{-- Shared Referee form partial --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- First Name --}}
    <div>
        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
        <input type="text" id="first_name" name="first_name"
               value="{{ old('first_name', $referee->first_name ?? '') }}"
               placeholder="e.g. Pierluigi"
               class="w-full border {{ $errors->has('first_name') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('first_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Last Name --}}
    <div>
        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
        <input type="text" id="last_name" name="last_name"
               value="{{ old('last_name', $referee->last_name ?? '') }}"
               placeholder="e.g. Collina"
               class="w-full border {{ $errors->has('last_name') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('last_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Nationality --}}
    <div>
        <label for="nationality" class="block text-sm font-medium text-gray-700 mb-1">Nationality <span class="text-red-500">*</span></label>
        <input type="text" id="nationality" name="nationality"
               value="{{ old('nationality', $referee->nationality ?? '') }}"
               placeholder="e.g. Italian"
               class="w-full border {{ $errors->has('nationality') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('nationality')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- FIFA Badge Year --}}
    <div>
        <label for="fifa_badge_year" class="block text-sm font-medium text-gray-700 mb-1">FIFA Badge Year <span class="text-gray-400">(optional)</span></label>
        <input type="number" id="fifa_badge_year" name="fifa_badge_year"
               value="{{ old('fifa_badge_year', $referee->fifa_badge_year ?? '') }}"
               placeholder="e.g. 2010" min="1900" max="{{ date('Y') + 1 }}"
               class="w-full border {{ $errors->has('fifa_badge_year') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('fifa_badge_year')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

</div>
