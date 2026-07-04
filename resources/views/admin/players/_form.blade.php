{{-- Shared Player form partial --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- First Name --}}
    <div>
        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
        <input type="text" id="first_name" name="first_name"
               value="{{ old('first_name', $player->first_name ?? '') }}"
               placeholder="e.g. Kylian"
               class="w-full border {{ $errors->has('first_name') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('first_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Last Name --}}
    <div>
        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
        <input type="text" id="last_name" name="last_name"
               value="{{ old('last_name', $player->last_name ?? '') }}"
               placeholder="e.g. Mbappé"
               class="w-full border {{ $errors->has('last_name') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('last_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Nationality --}}
    <div>
        <label for="nationality" class="block text-sm font-medium text-gray-700 mb-1">Nationality <span class="text-red-500">*</span></label>
        <input type="text" id="nationality" name="nationality"
               value="{{ old('nationality', $player->nationality ?? '') }}"
               placeholder="e.g. French"
               class="w-full border {{ $errors->has('nationality') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('nationality')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Date of Birth --}}
    <div>
        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth <span class="text-red-500">*</span></label>
        <input type="date" id="date_of_birth" name="date_of_birth"
               value="{{ old('date_of_birth', isset($player->date_of_birth) ? \Carbon\Carbon::parse($player->date_of_birth)->format('Y-m-d') : '') }}"
               class="w-full border {{ $errors->has('date_of_birth') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('date_of_birth')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Position --}}
    <div>
        <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Position <span class="text-red-500">*</span></label>
        <select id="position" name="position"
                class="w-full border {{ $errors->has('position') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <option value="">Select position…</option>
            @foreach($positions as $pos)
            @php
                $labels = ['GK' => 'GK — Goalkeeper', 'DF' => 'DF — Defender', 'MF' => 'MF — Midfielder', 'FW' => 'FW — Forward'];
            @endphp
            <option value="{{ $pos }}" {{ old('position', $player->position ?? '') === $pos ? 'selected' : '' }}>
                {{ $labels[$pos] ?? $pos }}
            </option>
            @endforeach
        </select>
        @error('position')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Height --}}
    <div>
        <label for="height_cm" class="block text-sm font-medium text-gray-700 mb-1">Height (cm) <span class="text-gray-400">(optional)</span></label>
        <input type="number" id="height_cm" name="height_cm"
               value="{{ old('height_cm', $player->height_cm ?? '') }}"
               placeholder="e.g. 178" min="140" max="220" step="0.1"
               class="w-full border {{ $errors->has('height_cm') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('height_cm')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Weight --}}
    <div>
        <label for="weight_kg" class="block text-sm font-medium text-gray-700 mb-1">Weight (kg) <span class="text-gray-400">(optional)</span></label>
        <input type="number" id="weight_kg" name="weight_kg"
               value="{{ old('weight_kg', $player->weight_kg ?? '') }}"
               placeholder="e.g. 73" min="40" max="150" step="0.1"
               class="w-full border {{ $errors->has('weight_kg') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('weight_kg')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

</div>
