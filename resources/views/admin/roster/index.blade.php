@extends('layouts.admin')

@section('title', 'Squad Roster')
@section('page-title', ($teamTournament->team->country_name ?? 'Team') . ' — Squad Roster')
@section('breadcrumb', 'Admin › Tournaments › ' . ($teamTournament->tournament->name ?? '') . ' › ' . ($teamTournament->team->abbreviation ?? '') . ' Roster')

@section('content')

{{-- Flash messages --}}
@if(session('success'))
<div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-md text-sm flex items-center gap-2">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded-md text-sm flex items-center gap-2">❌ {{ session('error') }}</div>
@endif

{{-- Back link --}}
<div class="mb-5">
    <a href="{{ route('admin.tournaments.show', $teamTournament->tournament_id) }}"
       class="text-sm text-gray-500 hover:text-gray-700">← Back to {{ $teamTournament->tournament->name ?? 'Tournament' }}</a>
</div>

{{-- Squad info banner --}}
<div class="bg-white rounded-lg border border-gray-200 p-5 mb-6 flex items-center justify-between">
    <div class="flex items-center gap-5">
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">Team</p>
            <p class="font-bold text-gray-800 text-lg">{{ $teamTournament->team->country_name ?? '—' }}</p>
        </div>
        <div class="w-px h-10 bg-gray-200"></div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">Tournament</p>
            <p class="font-medium text-gray-700">{{ $teamTournament->tournament->name ?? '—' }}</p>
        </div>
        @if($teamTournament->group)
        <div class="w-px h-10 bg-gray-200"></div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">Group</p>
            <p class="font-bold text-indigo-600 text-lg">{{ $teamTournament->group->group_name }}</p>
        </div>
        @endif
    </div>

    {{-- Squad size meter --}}
    <div class="text-right">
        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Squad Size</p>
        <div class="flex items-center gap-2">
            <div class="w-32 h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full {{ $rosterCount >= 26 ? 'bg-red-500' : ($rosterCount >= 20 ? 'bg-yellow-400' : 'bg-green-500') }} transition-all"
                     style="width: {{ min(100, ($rosterCount / 26) * 100) }}%"></div>
            </div>
            <span class="font-bold text-sm {{ $rosterCount >= 26 ? 'text-red-600' : 'text-gray-700' }}">
                {{ $rosterCount }} / 26
            </span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── Left: Add Player form ────────────────────────────────────── --}}
    <div class="lg:col-span-1">
        @if($isFull)
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-700">
            <p class="font-semibold mb-1">🚫 Squad Full</p>
            <p>Maximum of 26 players has been reached. Remove a player to add another.</p>
        </div>
        @else
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Add Player to Squad</h2>

            @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md text-xs text-red-800">
                @foreach($errors->all() as $err)<p>• {{ $err }}</p>@endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('admin.roster.store', $teamTournament->team_tournament_id) }}" class="space-y-4">
                @csrf

                {{-- Player picker --}}
                <div>
                    <label for="player_id" class="block text-xs font-medium text-gray-700 mb-1">Player <span class="text-red-500">*</span></label>
                    <select id="player_id" name="player_id"
                            class="w-full border {{ $errors->has('player_id') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">Select a player…</option>
                        @foreach($players as $player)
                        @php $alreadyIn = in_array($player->player_id, $rosteredPlayerIds); @endphp
                        <option value="{{ $player->player_id }}"
                                {{ old('player_id') == $player->player_id ? 'selected' : '' }}
                                {{ $alreadyIn ? 'disabled' : '' }}>
                            [{{ $player->position }}] {{ $player->first_name }} {{ $player->last_name }}
                            {{ $alreadyIn ? '— in squad' : '' }}
                        </option>
                        @endforeach
                    </select>
                    @error('player_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Jersey Number --}}
                <div>
                    <label for="jersey_number" class="block text-xs font-medium text-gray-700 mb-1">Jersey Number <span class="text-red-500">*</span></label>
                    <input type="number" id="jersey_number" name="jersey_number"
                           value="{{ old('jersey_number') }}"
                           min="1" max="99" placeholder="1–99"
                           class="w-full border {{ $errors->has('jersey_number') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    @error('jersey_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Captain --}}
                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" id="is_captain" name="is_captain" value="1"
                           {{ old('is_captain') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                    <label for="is_captain" class="text-sm text-gray-700">
                        Set as Captain <span class="text-gray-400 text-xs">(replaces existing captain)</span>
                    </label>
                </div>

                <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-md transition">
                    + Add to Squad
                </button>
            </form>
        </div>
        @endif
    </div>

    {{-- ── Right: Current roster table ─────────────────────────────── --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-700">Current Squad</h2>
                <span class="text-xs text-gray-400">{{ $rosterCount }} player(s)</span>
            </div>

            @if($roster->isEmpty())
            <div class="py-12 text-center text-sm text-gray-400">
                No players added yet. Use the form on the left to build the squad.
            </div>
            @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                        <th class="px-4 py-3 text-center w-12">#</th>
                        <th class="px-4 py-3 text-left">Player</th>
                        <th class="px-4 py-3 text-center">Pos</th>
                        <th class="px-4 py-3 text-center">Captain</th>
                        <th class="px-4 py-3 text-center">Remove</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($roster as $entry)
                    @php
                        $posColors = [
                            'GK' => 'bg-yellow-100 text-yellow-700',
                            'DF' => 'bg-blue-100 text-blue-700',
                            'MF' => 'bg-green-100 text-green-700',
                            'FW' => 'bg-red-100 text-red-700',
                        ];
                    @endphp
                    <tr class="hover:bg-gray-50 transition {{ $entry->is_captain === 'Y' ? 'bg-amber-50' : '' }}">
                        <td class="px-4 py-3 text-center font-mono font-bold text-gray-700">
                            {{ $entry->jersey_number }}
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800">
                                {{ $entry->player->first_name ?? '' }} {{ $entry->player->last_name ?? '—' }}
                            </p>
                            <p class="text-xs text-gray-400">{{ $entry->player->nationality ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold {{ $posColors[$entry->player->position ?? ''] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $entry->player->position ?? '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($entry->is_captain === 'Y')
                            <span class="text-amber-500 text-base" title="Captain">⭐</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <form method="POST"
                                  action="{{ route('admin.roster.destroy', [$teamTournament->team_tournament_id, $entry->player_tournament_id]) }}"
                                  onsubmit="return confirm('Remove {{ addslashes(($entry->player->first_name ?? '') . ' ' . ($entry->player->last_name ?? '')) }} from squad?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

</div>

@endsection
