<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\TournamentGroup;
use App\Models\TeamTournament;
use Illuminate\Http\JsonResponse;

class TournamentGroupsController extends Controller
{
    /**
     * Returns groups for a tournament as JSON, with team count per group.
     * Used by Alpine.js on the Register Team admin page.
     *
     * GET /api/tournaments/{tournament}/groups
     */
    public function __invoke(Tournament $tournament): JsonResponse
    {
        $groups = TournamentGroup::where('tournament_id', $tournament->tournament_id)
            ->orderBy('group_name')
            ->get(['group_id', 'group_name']);

        // Count how many teams are already assigned to each group
        $teamCounts = TeamTournament::where('tournament_id', $tournament->tournament_id)
            ->whereNotNull('group_id')
            ->selectRaw('"GROUP_ID", COUNT(*) as team_count')
            ->groupBy('group_id')
            ->pluck('team_count', 'group_id');

        $payload = $groups->map(fn ($g) => [
            'group_id'   => $g->group_id,
            'group_name' => $g->group_name,
            'team_count' => (int) ($teamCounts[$g->group_id] ?? 0),
            'label'      => 'Group ' . $g->group_name,
        ]);

        return response()->json([
            'tournament_id' => $tournament->tournament_id,
            'groups'        => $payload,
        ]);
    }
}
