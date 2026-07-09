{{-- Shared Stadium form partial --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Name --}}
    <div class="md:col-span-2">
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Stadium Name <span class="text-red-500">*</span></label>
        <input type="text" id="name" name="name"
               value="{{ old('name', $stadium->name ?? '') }}"
               placeholder="e.g. Lusail Iconic Stadium"
               class="w-full border {{ $errors->has('name') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- City --}}
    <div>
        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City <span class="text-red-500">*</span></label>
        <input type="text" id="city" name="city"
               value="{{ old('city', $stadium->city ?? '') }}"
               placeholder="e.g. Lusail"
               class="w-full border {{ $errors->has('city') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('city')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Country --}}
    <div>
        <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country <span class="text-red-500">*</span></label>
        <input type="text" id="country" name="country"
               value="{{ old('country', $stadium->country ?? '') }}"
               placeholder="e.g. Qatar"
               class="w-full border {{ $errors->has('country') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('country')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Capacity --}}
    <div>
        <label for="capacity" class="block text-sm font-medium text-gray-700 mb-1">Capacity <span class="text-red-500">*</span></label>
        <input type="number" id="capacity" name="capacity"
               value="{{ old('capacity', $stadium->capacity ?? '') }}"
               placeholder="e.g. 80000" min="1000" max="200000"
               class="w-full border {{ $errors->has('capacity') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('capacity')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Surface Type --}}
    <div>
        <label for="surface_type" class="block text-sm font-medium text-gray-700 mb-1">Surface Type <span class="text-red-500">*</span></label>
        <select id="surface_type" name="surface_type"
                class="w-full border {{ $errors->has('surface_type') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <option value="">Select surface…</option>
            @foreach($surfaceTypes as $s)
            <option value="{{ $s }}" {{ old('surface_type', $stadium->surface_type ?? '') === $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>
        @error('surface_type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

</div>
