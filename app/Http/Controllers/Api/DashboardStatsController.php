<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\GameMatch;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\Card;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardStatsController extends Controller
{
    /**
     * Returns aggregate statistics for the Admin Dashboard charts.
     *
     * GET /api/dashboard/stats
     *
     * Payload:
     *  - goals_per_tournament  → bar chart (one bar per tournament)
     *  - top_scoring_teams     → horizontal bar chart (top 8 teams by goals)
     *  - goals_by_type         → doughnut chart (NORMAL / OWN_GOAL / PENALTY)
     *  - match_status_summary  → doughnut chart (SCHEDULED / ONGOING / COMPLETED)
     *  - cards_per_tournament  → bar chart (yellow vs red cards per tournament)
     */
    public function __invoke(): JsonResponse
    {
        // ── 1. Goals per Tournament ──────────────────────────────────────────
        $tournaments = Tournament::orderBy('year')->get(['tournament_id', 'name', 'year']);

        $goalsByTournament = Goal::select('matches.tournament_id', DB::raw('COUNT(*) as goal_count'))
            ->join('matches', 'goals.match_id', '=', 'matches.match_id')
            ->groupBy('matches.tournament_id')
            ->pluck('goal_count', 'tournament_id');

        $goalsPerTournament = $tournaments->map(fn ($t) => [
            'label' => $t->name . ' (' . $t->year . ')',
            'value' => (int) ($goalsByTournament[$t->tournament_id] ?? 0),
        ]);

        // ── 2. Top Scoring Teams (all-time, top 8) ───────────────────────────
        $topTeams = Goal::select('goals.team_id', DB::raw('COUNT(*) as goal_count'))
            ->groupBy('goals.team_id')
            ->orderByDesc('goal_count')
            ->limit(8)
            ->get();

        $teamIds   = $topTeams->pluck('team_id');
        $teamNames = Team::whereIn('team_id', $teamIds)->pluck('abbreviation', 'team_id');

        $topScoringTeams = $topTeams->map(fn ($row) => [
            'label' => $teamNames[$row->team_id] ?? 'Unknown',
            'value' => (int) $row->goal_count,
        ]);

        // ── 3. Goals by Type ─────────────────────────────────────────────────
        $goalTypes = Goal::select('goal_type', DB::raw('COUNT(*) as cnt'))
            ->groupBy('goal_type')
            ->pluck('cnt', 'goal_type');

        $goalsByType = [
            ['label' => 'Normal',    'value' => (int) ($goalTypes['NORMAL']    ?? 0)],
            ['label' => 'Own Goal',  'value' => (int) ($goalTypes['OWN_GOAL']  ?? 0)],
            ['label' => 'Penalty',   'value' => (int) ($goalTypes['PENALTY']   ?? 0)],
        ];

        // ── 4. Match Status Summary ───────────────────────────────────────────
        $matchStatuses = GameMatch::select('status', DB::raw('COUNT(*) as cnt'))
            ->groupBy('status')
            ->pluck('cnt', 'status');

        $matchStatusSummary = [
            ['label' => 'Scheduled',  'value' => (int) ($matchStatuses['SCHEDULED']  ?? 0)],
            ['label' => 'Ongoing',    'value' => (int) ($matchStatuses['ONGOING']    ?? 0)],
            ['label' => 'Completed',  'value' => (int) ($matchStatuses['COMPLETED']  ?? 0)],
        ];

        // ── 5. Cards per Tournament (yellow vs red) ───────────────────────────
        $yellowCards = Card::select('matches.tournament_id', DB::raw('COUNT(*) as cnt'))
            ->join('matches', 'cards.match_id', '=', 'matches.match_id')
            ->where('card_type', 'YELLOW')
            ->groupBy('matches.tournament_id')
            ->pluck('cnt', 'tournament_id');

        $redCards = Card::select('matches.tournament_id', DB::raw('COUNT(*) as cnt'))
            ->join('matches', 'cards.match_id', '=', 'matches.match_id')
            ->where('card_type', 'RED')
            ->groupBy('matches.tournament_id')
            ->pluck('cnt', 'tournament_id');

        $cardsPerTournament = $tournaments->map(fn ($t) => [
            'label'  => $t->year,
            'yellow' => (int) ($yellowCards[$t->tournament_id] ?? 0),
            'red'    => (int) ($redCards[$t->tournament_id]   ?? 0),
        ]);

        return response()->json([
            'goals_per_tournament'  => $goalsPerTournament,
            'top_scoring_teams'     => $topScoringTeams,
            'goals_by_type'         => $goalsByType,
            'match_status_summary'  => $matchStatusSummary,
            'cards_per_tournament'  => $cardsPerTournament,
        ]);
    }
}
