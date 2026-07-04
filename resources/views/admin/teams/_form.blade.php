{{-- Shared Team form partial --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Country Name --}}
    <div class="md:col-span-2">
        <label for="country_name" class="block text-sm font-medium text-gray-700 mb-1">Country Name <span class="text-red-500">*</span></label>
        <input type="text" id="country_name" name="country_name"
               value="{{ old('country_name', $team->country_name ?? '') }}"
               placeholder="e.g. France"
               class="w-full border {{ $errors->has('country_name') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('country_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Abbreviation --}}
    <div>
        <label for="abbreviation" class="block text-sm font-medium text-gray-700 mb-1">3-Letter Abbreviation <span class="text-red-500">*</span></label>
        <input type="text" id="abbreviation" name="abbreviation"
               value="{{ old('abbreviation', $team->abbreviation ?? '') }}"
               placeholder="e.g. FRA" maxlength="3" style="text-transform:uppercase"
               class="w-full border {{ $errors->has('abbreviation') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('abbreviation')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Continent --}}
    <div>
        <label for="continent" class="block text-sm font-medium text-gray-700 mb-1">Confederation <span class="text-red-500">*</span></label>
        <select id="continent" name="continent"
                class="w-full border {{ $errors->has('continent') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <option value="">Select confederation…</option>
            @foreach($continents as $c)
            <option value="{{ $c }}" {{ old('continent', $team->continent ?? '') === $c ? 'selected' : '' }}>{{ $c }}</option>
            @endforeach
        </select>
        @error('continent')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- FIFA Ranking --}}
    <div>
        <label for="fifa_ranking" class="block text-sm font-medium text-gray-700 mb-1">FIFA Ranking <span class="text-gray-400">(optional)</span></label>
        <input type="number" id="fifa_ranking" name="fifa_ranking"
               value="{{ old('fifa_ranking', $team->fifa_ranking ?? '') }}"
               placeholder="e.g. 2" min="1" max="999"
               class="w-full border {{ $errors->has('fifa_ranking') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('fifa_ranking')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

</div>
