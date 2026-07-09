<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index(Tournament $tournament)
    {
        $tid = $tournament->tournament_id;

        // ── 1. Top Scorers ────────────────────────────────────────────────
        // One aggregate query: group goals by scorer, count all + penalty subset.
        // Joins matches to filter by tournament, then joins players for names.
        $topScorers = DB::table('goals as g')
            ->join('matches as m', 'g.match_id', '=', 'm.match_id')
            ->join('players as p', 'g.scorer_player_id', '=', 'p.player_id')
            ->join('teams as t', 'g.team_id', '=', 't.team_id')
            ->where('m.tournament_id', $tid)
            ->where('g.goal_type', '!=', 'OWN_GOAL')   // own goals don't count for scorer
            ->select([
                'p.player_id',
                DB::raw("p.first_name || ' ' || p.last_name AS player_name"),
                't.country_name AS team_name',
                't.abbreviation AS team_abbr',
                DB::raw('COUNT(*) AS total_goals'),
                DB::raw("SUM(CASE WHEN g.goal_type = 'PENALTY' THEN 1 ELSE 0 END) AS penalty_goals"),
            ])
            ->groupBy([
                'p.player_id', 'p.first_name', 'p.last_name',
                't.country_name', 't.abbreviation',
            ])
            ->orderByDesc('total_goals')
            ->orderBy('player_name')
            ->limit(20)
            ->get();

        // ── 2. Assist Leaders ─────────────────────────────────────────────
        $assistLeaders = DB::table('goals as g')
            ->join('matches as m', 'g.match_id', '=', 'm.match_id')
            ->join('players as p', 'g.assist_player_id', '=', 'p.player_id')
            ->join('teams as t', 'g.team_id', '=', 't.team_id')
            ->where('m.tournament_id', $tid)
            ->whereNotNull('g.assist_player_id')
            ->select([
                'p.player_id',
                DB::raw("p.first_name || ' ' || p.last_name AS player_name"),
                't.country_name AS team_name',
                't.abbreviation AS team_abbr',
                DB::raw('COUNT(*) AS total_assists'),
            ])
            ->groupBy([
                'p.player_id', 'p.first_name', 'p.last_name',
                't.country_name', 't.abbreviation',
            ])
            ->orderByDesc('total_assists')
            ->orderBy('player_name')
            ->limit(20)
            ->get();

        // ── 3. Disciplinary Table ─────────────────────────────────────────
        // One query: pivot YELLOW / RED / SECOND_YELLOW counts per team.
        $disciplinary = DB::table('cards as c')
            ->join('matches as m', 'c.match_id', '=', 'm.match_id')
            ->join('teams as t', 'c.team_id', '=', 't.team_id')
            ->where('m.tournament_id', $tid)
            ->select([
                't.team_id',
                't.country_name AS team_name',
                't.abbreviation AS team_abbr',
                DB::raw("SUM(CASE WHEN c.card_type = 'YELLOW' THEN 1 ELSE 0 END) AS yellow_cards"),
                DB::raw("SUM(CASE WHEN c.card_type = 'RED' THEN 1 ELSE 0 END) AS red_cards"),
                DB::raw("SUM(CASE WHEN c.card_type = 'SECOND_YELLOW' THEN 1 ELSE 0 END) AS second_yellow_cards"),
                DB::raw('COUNT(*) AS total_cards'),
            ])
            ->groupBy(['t.team_id', 't.country_name', 't.abbreviation'])
            ->orderByDesc('total_cards')
            ->orderByDesc('red_cards')
            ->get();

        return view('public.tournaments.stats', compact(
            'tournament', 'topScorers', 'assistLeaders', 'disciplinary'
        ));
    }
}
