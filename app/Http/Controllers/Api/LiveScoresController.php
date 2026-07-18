<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GameMatch;
use App\Models\Tournament;
use Illuminate\Http\JsonResponse;

class LiveScoresController extends Controller
{
    /**
     * Returns live scores for all ONGOING matches in a given tournament.
     * Polled by Alpine.js every 30 seconds from the public fixtures page.
     *
     * GET /api/tournaments/{tournament}/live-scores
     */
    public function __invoke(Tournament $tournament): JsonResponse
    {
        $matches = GameMatch::with(['homeTeam:team_id,country_name,abbreviation', 'awayTeam:team_id,country_name,abbreviation'])
            ->where('tournament_id', $tournament->tournament_id)
            ->where('status', 'ONGOING')
            ->get();

        $payload = $matches->map(fn ($m) => [
            'match_id'        => $m->match_id,
            'status'          => $m->status,
            'home_score'      => (int) $m->home_score,
            'away_score'      => (int) $m->away_score,
            'has_penalties'   => $m->has_penalties,
            'has_extra_time'  => $m->has_extra_time,
            'home_team_label' => $m->homeTeam->country_name,
            'away_team_label' => $m->awayTeam->country_name,
        ]);

        return response()->json([
            'tournament_id' => $tournament->tournament_id,
            'live_count'    => $matches->count(),
            'matches'       => $payload,
        ]);
    }
}
