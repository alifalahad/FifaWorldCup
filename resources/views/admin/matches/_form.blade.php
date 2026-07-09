{{--
    Shared match form partial.
    Expects: $tournaments, $stadiums, $referees, $tournamentTeams, $tournamentGroups,
             $stages, $statuses, and optionally $match (for edit mode).
--}}
@php
    $isEdit          = isset($match);
    $selectedTournId = old('tournament_id', $isEdit ? $match->tournament_id : '');
    $selectedStage   = old('stage',         $isEdit ? $match->stage         : 'GROUP');
    $selectedStatus  = old('status',        $isEdit ? $match->status        : 'SCHEDULED');
    $selectedHome    = old('home_team_id',  $isEdit ? $match->home_team_id  : '');
    $selectedAway    = old('away_team_id',  $isEdit ? $match->away_team_id  : '');
    $selectedGroup   = old('group_id',      $isEdit ? $match->group_id      : '');

    $stageLabels = [
        'GROUP'         => 'Group Stage',
        'ROUND_OF_16'   => 'Round of 16',
        'QUARTER_FINAL' => 'Quarter Final',
        'SEMI_FINAL'    => 'Semi Final',
        'THIRD_PLACE'   => 'Third Place',
        'FINAL'         => 'Final',
    ];
@endphp

<div class="space-y-6">

    {{-- Row 1: Tournament + Date --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label for="tournament_id" class="block text-sm font-medium text-gray-700 mb-1">
                Tournament <span class="text-red-500">*</span>
            </label>
            <select id="tournament_id" name="tournament_id"
                    class="w-full border {{ $errors->has('tournament_id') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Select tournament…</option>
                @foreach($tournaments as $t)
                <option value="{{ $t->tournament_id }}" {{ $selectedTournId == $t->tournament_id ? 'selected' : '' }}>
                    {{ $t->name }} ({{ $t->year }})
                </option>
                @endforeach
            </select>
            @error('tournament_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            <p class="mt-1 text-xs text-gray-400">Changing tournament will reload team & group dropdowns.</p>
        </div>
        <div>
            <label for="match_date" class="block text-sm font-medium text-gray-700 mb-1">
                Match Date <span class="text-red-500">*</span>
            </label>
            <input type="date" id="match_date" name="match_date"
                   value="{{ old('match_date', $isEdit ? $match->match_date->format('Y-m-d') : '') }}"
                   class="w-full border {{ $errors->has('match_date') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            @error('match_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    {{-- Row 2: Stage + Status --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label for="stage" class="block text-sm font-medium text-gray-700 mb-1">
                Stage <span class="text-red-500">*</span>
            </label>
            <select id="stage" name="stage"
                    class="w-full border {{ $errors->has('stage') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                @foreach($stages as $s)
                <option value="{{ $s }}" {{ $selectedStage === $s ? 'selected' : '' }}>
                    {{ $stageLabels[$s] ?? $s }}
                </option>
                @endforeach
            </select>
            @error('stage')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                Status <span class="text-red-500">*</span>
            </label>
            <select id="status" name="status"
                    class="w-full border {{ $errors->has('status') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                @foreach($statuses as $s)
                <option value="{{ $s }}" {{ $selectedStatus === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
            @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    {{-- Row 3: Home Team vs Away Team --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label for="home_team_id" class="block text-sm font-medium text-gray-700 mb-1">
                Home Team <span class="text-red-500">*</span>
            </label>
            <select id="home_team_id" name="home_team_id"
                    class="w-full border {{ $errors->has('home_team_id') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Select home team…</option>
                @foreach($tournamentTeams as $tt)
                <option value="{{ $tt->team->team_id }}" {{ $selectedHome == $tt->team->team_id ? 'selected' : '' }}>
                    {{ $tt->team->country_name }} ({{ $tt->team->abbreviation }})
                </option>
                @endforeach
            </select>
            @error('home_team_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="away_team_id" class="block text-sm font-medium text-gray-700 mb-1">
                Away Team <span class="text-red-500">*</span>
            </label>
            <select id="away_team_id" name="away_team_id"
                    class="w-full border {{ $errors->has('away_team_id') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Select away team…</option>
                @foreach($tournamentTeams as $tt)
                <option value="{{ $tt->team->team_id }}" {{ $selectedAway == $tt->team->team_id ? 'selected' : '' }}>
                    {{ $tt->team->country_name }} ({{ $tt->team->abbreviation }})
                </option>
                @endforeach
            </select>
            @error('away_team_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    {{-- Row 4: Group (only for GROUP stage) --}}
    <div id="group-row" class="{{ $selectedStage === 'GROUP' ? '' : 'hidden' }}">
        <label for="group_id" class="block text-sm font-medium text-gray-700 mb-1">
            Group <span class="text-gray-400">(only for group stage)</span>
        </label>
        <select id="group_id" name="group_id"
                class="w-full md:w-1/2 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <option value="">No group</option>
            @foreach($tournamentGroups as $g)
            <option value="{{ $g->group_id }}" {{ $selectedGroup == $g->group_id ? 'selected' : '' }}>
                Group {{ $g->group_name }}
            </option>
            @endforeach
        </select>
        @error('group_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Row 5: Stadium + Referee --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label for="stadium_id" class="block text-sm font-medium text-gray-700 mb-1">
                Stadium <span class="text-red-500">*</span>
            </label>
            <select id="stadium_id" name="stadium_id"
                    class="w-full border {{ $errors->has('stadium_id') ? 'border-red-400' : 'border-gray-300' }} rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Select stadium…</option>
                @foreach($stadiums as $s)
                <option value="{{ $s->stadium_id }}" {{ old('stadium_id', $isEdit ? $match->stadium_id : '') == $s->stadium_id ? 'selected' : '' }}>
                    {{ $s->name }} (cap. {{ number_format($s->capacity) }})
                </option>
                @endforeach
            </select>
            @error('stadium_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="referee_id" class="block text-sm font-medium text-gray-700 mb-1">
                Referee <span class="text-gray-400">(optional)</span>
            </label>
            <select id="referee_id" name="referee_id"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">No referee assigned</option>
                @foreach($referees as $r)
                <option value="{{ $r->referee_id }}" {{ old('referee_id', $isEdit ? $match->referee_id : '') == $r->referee_id ? 'selected' : '' }}>
                    {{ $r->first_name }} {{ $r->last_name }} ({{ $r->nationality }})
                </option>
                @endforeach
            </select>
            @error('referee_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

</div>

{{-- ── JavaScript: Dynamic team/group loading when tournament changes ─────────────── --}}
<script>
(function () {
    const tournamentSel = document.getElementById('tournament_id');
    const homeSel       = document.getElementById('home_team_id');
    const awaySel       = document.getElementById('away_team_id');
    const groupSel      = document.getElementById('group_id');
    const stageSel      = document.getElementById('stage');
    const groupRow      = document.getElementById('group-row');

    // Toggle group row based on stage
    function toggleGroupRow() {
        if (stageSel.value === 'GROUP') {
            groupRow.classList.remove('hidden');
        } else {
            groupRow.classList.add('hidden');
            groupSel.value = '';
        }
    }

    // Rebuild a <select> from an array of { value, label } objects, keeping selected value if present
    function rebuildSelect(sel, items, placeholder) {
        const current = sel.value;
        sel.innerHTML = `<option value="">${placeholder}</option>`;
        items.forEach(({ value, label }) => {
            const opt = document.createElement('option');
            opt.value = value;
            opt.textContent = label;
            if (String(value) === String(current)) opt.selected = true;
            sel.appendChild(opt);
        });
    }

    // Fetch teams + groups for chosen tournament
    function loadTournamentData(tournamentId, keepSelected) {
        if (!tournamentId) {
            rebuildSelect(homeSel, [], 'Select home team…');
            rebuildSelect(awaySel, [], 'Select away team…');
            rebuildSelect(groupSel, [], 'No group');
            return;
        }

        fetch(`/admin/api/tournaments/${tournamentId}/teams`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            const teamItems  = data.teams.map(t => ({ value: t.team_id, label: `${t.country_name} (${t.abbreviation})` }));
            const groupItems = data.groups.map(g => ({ value: g.group_id, label: `Group ${g.group_name}` }));

            rebuildSelect(homeSel,  teamItems,  'Select home team…');
            rebuildSelect(awaySel,  teamItems,  'Select away team…');
            rebuildSelect(groupSel, groupItems, 'No group');

            // Restore pre-selected values (edit mode / old() after validation failure)
            if (keepSelected) {
                homeSel.value  = '{{ $selectedHome }}';
                awaySel.value  = '{{ $selectedAway }}';
                groupSel.value = '{{ $selectedGroup }}';
            }
        })
        .catch(() => console.warn('Could not load teams for tournament.'));
    }

    stageSel.addEventListener('change', toggleGroupRow);

    tournamentSel.addEventListener('change', function () {
        loadTournamentData(this.value, false);
    });

    // On page load: if tournament is already selected (edit/old), reload data and restore selections
    if (tournamentSel.value) {
        loadTournamentData(tournamentSel.value, true);
    }
})();
</script>
