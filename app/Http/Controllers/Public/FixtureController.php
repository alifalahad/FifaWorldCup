<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\GameMatch;
use App\Models\GroupStanding;
use App\Models\Tournament;

class FixtureController extends Controller
{
    /**
     * Public fixtures page: all matches for a tournament, grouped by stage/group.
     */
    public function index(Tournament $tournament)
    {
        // Eager-load everything needed for display
        $matches = $tournament->matches()
            ->with(['homeTeam', 'awayTeam', 'stadium', 'group'])
            ->orderBy('match_date')
            ->orderBy('match_id')
            ->get();

        // Stage display order
        $stageOrder = ['GROUP', 'ROUND_OF_16', 'QUARTER_FINAL', 'SEMI_FINAL', 'THIRD_PLACE', 'FINAL'];

        // Group-stage matches: sub-grouped by their group_id → group_name
        // Knockout matches: grouped by stage name
        $grouped = [];

        foreach ($stageOrder as $stage) {
            $stageMatches = $matches->where('stage', $stage);
            if ($stageMatches->isEmpty()) {
                continue;
            }

            if ($stage === 'GROUP') {
                // Sub-group by group
                $byGroup = $stageMatches->groupBy('group_id');
                foreach ($byGroup->sortKeys() as $groupId => $groupMatches) {
                    $groupName = $groupMatches->first()->group?->group_name ?? '?';
                    $grouped[] = [
                        'label'   => 'Group ' . $groupName,
                        'stage'   => $stage,
                        'matches' => $groupMatches->values(),
                    ];
                }
            } else {
                $label = str_replace('_', ' ', $stage);
                $label = ucwords(strtolower($label));
                $grouped[] = [
                    'label'   => $label,
                    'stage'   => $stage,
                    'matches' => $stageMatches->values(),
                ];
            }
        }

        return view('public.tournaments.fixtures', compact('tournament', 'grouped'));
    }

    /**
     * Public standings page: GROUP_STANDINGS view per group for a tournament.
     */
    public function standings(Tournament $tournament)
    {
        // Load all groups sorted by name
        $tournament->load(['groups' => function ($q) {
            $q->orderBy('group_name');
        }]);

        // Fetch standings from the view, grouped by group_id
        $standingsByGroup = GroupStanding::forTournament($tournament->tournament_id);

        // Map group_id → TournamentGroup model for display
        $groupsById = $tournament->groups->keyBy('group_id');

        return view('public.tournaments.standings', compact('tournament', 'standingsByGroup', 'groupsById'));
    }
}
