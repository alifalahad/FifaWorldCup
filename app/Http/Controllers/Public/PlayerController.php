<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlayerController extends Controller
{
    public function index(Request $request)
    {
        $query = Player::query()->orderBy('last_name')->orderBy('first_name');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%');
            });
        }

        if ($position = $request->input('position')) {
            $query->where('position', $position);
        }

        $players = $query->paginate(15)->withQueryString();

        $positions = Player::select('position')->whereNotNull('position')->distinct()->orderBy('position')->pluck('position');

        return view('public.players.index', compact('players', 'positions'));
    }

    public function show(Player $player)
    {
        // Load player's tournament history including the team they played for
        $player->load([
            'playerTournaments.teamTournament.tournament' => function($q) {
                $q->orderByDesc('year');
            },
            'playerTournaments.teamTournament.team'
        ]);

        // Calculate goals across all tournaments
        $totalGoals = DB::table('goals')
            ->where('scorer_player_id', $player->player_id)
            ->count();
            
        // Calculate assists
        $totalAssists = DB::table('goals')
            ->where('assist_player_id', $player->player_id)
            ->count();
            
        // Calculate cards
        $cards = DB::table('cards')
            ->where('player_id', $player->player_id)
            ->select('card_type', DB::raw('count(*) as count'))
            ->groupBy('card_type')
            ->get();
            
        $yellowCards = $cards->where('card_type', 'YELLOW')->first()->count ?? 0;
        $redCards = $cards->whereIn('card_type', ['RED', 'SECOND_YELLOW'])->sum('count');

        return view('public.players.show', compact('player', 'totalGoals', 'totalAssists', 'yellowCards', 'redCards'));
    }
}
