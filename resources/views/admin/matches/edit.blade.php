@extends('layouts.admin')

@section('title', 'Edit Match')
@section('page-title', 'Edit Match')
@section('breadcrumb', 'Admin › Matches › Edit')

@section('content')
<div class="max-w-4xl space-y-8">
    @if(session('success'))
    <div class="p-3 bg-green-50 border border-green-200 text-green-800 rounded-md text-sm flex items-center gap-2">
        ✅ {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="p-4 bg-red-50 border border-red-200 rounded-md text-sm text-red-800">
        <p class="font-semibold mb-1">Please fix the following errors:</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- Match Edit Form --}}
    <form method="POST" action="{{ route('admin.matches.update', $match->match_id) }}"
          class="bg-white rounded-lg border border-gray-200 p-6">
        @csrf @method('PATCH')
        <h2 class="text-lg font-bold text-gray-800 mb-4">Match Details</h2>
        @include('admin.matches._form')
        
        <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-100">
            <a href="{{ route('admin.matches.index') }}"
               class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2 border border-gray-300 rounded-md transition">
                Back to Matches
            </a>
            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-md transition">
                Save Changes
            </button>
        </div>
    </form>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Goals Section --}}
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Goals</h2>
            
            {{-- List of existing goals --}}
            @if($match->goals->isNotEmpty())
            <div class="mb-6 border border-gray-100 rounded-md overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500">
                        <tr>
                            <th class="px-3 py-2 text-left font-medium">Min</th>
                            <th class="px-3 py-2 text-left font-medium">Scorer</th>
                            <th class="px-3 py-2 text-left font-medium">Team</th>
                            <th class="px-3 py-2 text-right font-medium"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($match->goals as $goal)
                        <tr>
                            <td class="px-3 py-2 text-gray-600">{{ $goal->goal_minute }}'</td>
                            <td class="px-3 py-2 text-gray-800 font-medium">
                                {{ $goal->scorer->first_name ?? '' }} {{ $goal->scorer->last_name ?? '' }}
                                @if($goal->goal_type !== 'OPEN_PLAY')
                                    <span class="text-xs text-gray-400">({{ str_replace('_', ' ', $goal->goal_type) }})</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-gray-600">{{ $goal->team->abbreviation ?? '' }}</td>
                            <td class="px-3 py-2 text-right">
                                <form method="POST" action="{{ route('admin.matches.goals.destroy', [$match->match_id, $goal->goal_id]) }}" class="inline-block" onsubmit="return confirm('Delete this goal?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Del</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-sm text-gray-500 mb-6 text-center py-4 bg-gray-50 rounded border border-dashed border-gray-200">No goals recorded yet.</p>
            @endif

            {{-- Add Goal Form --}}
            <form method="POST" action="{{ route('admin.matches.goals.store', $match->match_id) }}" class="space-y-4 bg-gray-50 p-4 rounded border border-gray-200">
                @csrf
                <p class="text-sm font-bold text-gray-700 mb-2">Add Goal</p>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Team *</label>
                        <select name="team_id" class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-indigo-400" required>
                            <option value="">Select...</option>
                            <option value="{{ $match->home_team_id }}">{{ $match->homeTeam->country_name }}</option>
                            <option value="{{ $match->away_team_id }}">{{ $match->awayTeam->country_name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Minute *</label>
                        <input type="number" name="goal_minute" min="1" max="150" class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-indigo-400" required placeholder="e.g. 45">
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Scorer *</label>
                        <select name="scorer_player_id" class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-indigo-400" required>
                            <option value="">Select...</option>
                            <optgroup label="{{ $match->homeTeam->country_name }}">
                                @foreach($homeRoster as $rt)
                                <option value="{{ $rt->player->player_id }}">{{ $rt->player->first_name }} {{ $rt->player->last_name }} ({{ $rt->jersey_number }})</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="{{ $match->awayTeam->country_name }}">
                                @foreach($awayRoster as $rt)
                                <option value="{{ $rt->player->player_id }}">{{ $rt->player->first_name }} {{ $rt->player->last_name }} ({{ $rt->jersey_number }})</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Assist (Optional)</label>
                        <select name="assist_player_id" class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-indigo-400">
                            <option value="">None</option>
                            <optgroup label="{{ $match->homeTeam->country_name }}">
                                @foreach($homeRoster as $rt)
                                <option value="{{ $rt->player->player_id }}">{{ $rt->player->first_name }} {{ $rt->player->last_name }} ({{ $rt->jersey_number }})</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="{{ $match->awayTeam->country_name }}">
                                @foreach($awayRoster as $rt)
                                <option value="{{ $rt->player->player_id }}">{{ $rt->player->first_name }} {{ $rt->player->last_name }} ({{ $rt->jersey_number }})</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Type *</label>
                        <select name="goal_type" class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-indigo-400" required>
                            <option value="OPEN_PLAY">Open Play</option>
                            <option value="PENALTY">Penalty</option>
                            <option value="FREE_KICK">Free Kick</option>
                            <option value="HEADER">Header</option>
                            <option value="OWN_GOAL">Own Goal</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Half *</label>
                        <select name="half" class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-indigo-400" required>
                            <option value="1ST">1st Half</option>
                            <option value="2ND">2nd Half</option>
                            <option value="ET1">Extra Time 1</option>
                            <option value="ET2">Extra Time 2</option>
                        </select>
                    </div>
                </div>
                
                <div class="text-right mt-2">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium px-4 py-1.5 rounded transition">Add Goal</button>
                </div>
            </form>
        </div>

        {{-- Cards Section --}}
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Cards</h2>
            
            {{-- List of existing cards --}}
            @if($match->cards->isNotEmpty())
            <div class="mb-6 border border-gray-100 rounded-md overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500">
                        <tr>
                            <th class="px-3 py-2 text-left font-medium">Min</th>
                            <th class="px-3 py-2 text-left font-medium">Player</th>
                            <th class="px-3 py-2 text-center font-medium">Card</th>
                            <th class="px-3 py-2 text-right font-medium"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($match->cards as $card)
                        <tr>
                            <td class="px-3 py-2 text-gray-600">{{ $card->card_minute }}'</td>
                            <td class="px-3 py-2 text-gray-800 font-medium">
                                {{ $card->player->first_name ?? '' }} {{ $card->player->last_name ?? '' }}
                            </td>
                            <td class="px-3 py-2 text-center">
                                @if($card->card_type === 'YELLOW')
                                    <span class="inline-block w-3 h-4 bg-yellow-400 rounded-sm" title="Yellow"></span>
                                @elseif($card->card_type === 'RED')
                                    <span class="inline-block w-3 h-4 bg-red-600 rounded-sm" title="Red"></span>
                                @else
                                    <span class="inline-block w-3 h-4 bg-yellow-400 rounded-sm border-2 border-red-600" title="Second Yellow"></span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-right">
                                <form method="POST" action="{{ route('admin.matches.cards.destroy', [$match->match_id, $card->card_id]) }}" class="inline-block" onsubmit="return confirm('Delete this card?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Del</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-sm text-gray-500 mb-6 text-center py-4 bg-gray-50 rounded border border-dashed border-gray-200">No cards recorded yet.</p>
            @endif

            {{-- Add Card Form --}}
            <form method="POST" action="{{ route('admin.matches.cards.store', $match->match_id) }}" class="space-y-4 bg-gray-50 p-4 rounded border border-gray-200">
                @csrf
                <p class="text-sm font-bold text-gray-700 mb-2">Add Card</p>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Team *</label>
                        <select name="team_id" class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-indigo-400" required>
                            <option value="">Select...</option>
                            <option value="{{ $match->home_team_id }}">{{ $match->homeTeam->country_name }}</option>
                            <option value="{{ $match->away_team_id }}">{{ $match->awayTeam->country_name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Minute *</label>
                        <input type="number" name="card_minute" min="1" max="150" class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-indigo-400" required placeholder="e.g. 75">
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Player *</label>
                        <select name="player_id" class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-indigo-400" required>
                            <option value="">Select...</option>
                            <optgroup label="{{ $match->homeTeam->country_name }}">
                                @foreach($homeRoster as $rt)
                                <option value="{{ $rt->player->player_id }}">{{ $rt->player->first_name }} {{ $rt->player->last_name }} ({{ $rt->jersey_number }})</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="{{ $match->awayTeam->country_name }}">
                                @foreach($awayRoster as $rt)
                                <option value="{{ $rt->player->player_id }}">{{ $rt->player->first_name }} {{ $rt->player->last_name }} ({{ $rt->jersey_number }})</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Card Type *</label>
                        <select name="card_type" class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-indigo-400" required>
                            <option value="YELLOW">Yellow</option>
                            <option value="RED">Red</option>
                            <option value="SECOND_YELLOW">Second Yellow</option>
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Reason (Optional)</label>
                        <input type="text" name="reason" maxlength="255" class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-indigo-400" placeholder="e.g. Foul">
                    </div>
                </div>
                
                <div class="text-right mt-2">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium px-4 py-1.5 rounded transition">Add Card</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Danger Zone: Match Deletion --}}
    <div class="mt-8 p-6 bg-red-50 border border-red-200 rounded-lg flex items-center justify-between">
        <div>
            <h3 class="text-red-800 font-bold text-sm">Danger Zone</h3>
            <p class="text-red-600 text-xs mt-1">Deleting this match will also delete all its goals and cards.</p>
        </div>
        <form method="POST" action="{{ route('admin.matches.destroy', $match->match_id) }}"
              onsubmit="return confirm('Are you sure you want to completely delete this match?')">
            @csrf @method('DELETE')
            <button type="submit" class="bg-white border border-red-300 text-sm text-red-600 hover:text-red-800 hover:bg-red-100 font-medium px-4 py-2 rounded-md transition">
                Delete Match
            </button>
        </form>
    </div>
</div>
@endsection
