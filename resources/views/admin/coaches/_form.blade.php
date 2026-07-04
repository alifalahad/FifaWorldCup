{{-- Shared Coach form partial --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- First Name --}}
    <div>
        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
        <input type="text" id="first_name" name="first_name"
               value="{{ old('first_name', $coach->first_name ?? '') }}"
               placeholder="e.g. Didier"
               class="w-full border {{ $errors->has('first_name') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('first_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Last Name --}}
    <div>
        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
        <input type="text" id="last_name" name="last_name"
               value="{{ old('last_name', $coach->last_name ?? '') }}"
               placeholder="e.g. Deschamps"
               class="w-full border {{ $errors->has('last_name') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('last_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Nationality --}}
    <div>
        <label for="nationality" class="block text-sm font-medium text-gray-700 mb-1">Nationality <span class="text-red-500">*</span></label>
        <input type="text" id="nationality" name="nationality"
               value="{{ old('nationality', $coach->nationality ?? '') }}"
               placeholder="e.g. French"
               class="w-full border {{ $errors->has('nationality') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('nationality')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Coaching License --}}
    <div>
        <label for="coaching_license" class="block text-sm font-medium text-gray-700 mb-1">Coaching License <span class="text-gray-400">(optional)</span></label>
        <input type="text" id="coaching_license" name="coaching_license"
               value="{{ old('coaching_license', $coach->coaching_license ?? '') }}"
               placeholder="e.g. UEFA Pro License"
               class="w-full border {{ $errors->has('coaching_license') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('coaching_license')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

</div>
