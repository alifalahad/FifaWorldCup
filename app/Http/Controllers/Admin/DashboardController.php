<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameMatch;
use App\Models\Goal;
use App\Models\Player;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\Coach;
use App\Models\Referee;
use App\Models\Stadium;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard with live summary stats.
     */
    public function index()
    {
        $stats = [
            'tournaments'   => Tournament::count(),
            'teams'         => Team::count(),
            'players'       => Player::count(),
            'coaches'       => Coach::count(),
            'stadiums'      => Stadium::count(),
            'referees'      => Referee::count(),
            'matches_total' => GameMatch::count(),
            'matches_played'=> GameMatch::where('status', 'COMPLETED')->count(),
            'goals'         => Goal::count(),
            'live_matches'  => GameMatch::where('status', 'LIVE')->count(),
        ];

        $recent_matches = GameMatch::with(['homeTeam', 'awayTeam', 'tournament'])
            ->orderByDesc('match_date')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_matches'));
    }
}
