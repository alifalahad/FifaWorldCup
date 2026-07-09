<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Team;

class TeamController extends Controller
{
    public function index()
    {
        $query = Team::query()->orderBy('country_name');

        if ($search = request('search')) {
            $query->where('country_name', 'like', '%' . $search . '%')
                  ->orWhere('abbreviation', 'like', '%' . $search . '%');
        }

        if ($continent = request('continent')) {
            $query->where('continent', $continent);
        }

        $teams = $query->paginate(24)->withQueryString();

        $continents = Team::select('continent')->whereNotNull('continent')->distinct()->orderBy('continent')->pluck('continent');

        return view('public.teams.index', compact('teams', 'continents'));
    }

    public function show(Team $team)
    {
        // Load tournament history for this team, sorted by year descending
        $team->load(['teamTournaments.tournament' => function($q) {
            $q->orderByDesc('year');
        }]);

        // Find the most recent active squad if registered for an ongoing/upcoming tournament
        $currentSquad = collect();
        $activeTournament = $team->teamTournaments
            ->filter(fn ($tt) => in_array($tt->tournament->status, ['ONGOING', 'UPCOMING']))
            ->sortByDesc(fn ($tt) => $tt->tournament->year)
            ->first();

        if ($activeTournament) {
            $activeTournament->load(['playerTournaments.player']);
            $currentSquad = $activeTournament->playerTournaments;
        }

        return view('public.teams.show', compact('team', 'currentSquad', 'activeTournament'));
    }
}
