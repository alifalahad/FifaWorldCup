<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\PlayerTournament;
use App\Models\TeamTournament;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RosterController extends Controller
{
    private const MAX_SQUAD_SIZE = 26;

    /**
     * Show the roster for a specific team-tournament registration.
     * URL: GET /admin/team-tournament/{teamTournament}/roster
     */
    public function index(TeamTournament $teamTournament)
    {
        $teamTournament->load([
            'team',
            'tournament',
            'group',
            'playerTournaments.player',
        ]);

        $roster = $teamTournament->playerTournaments
            ->sortBy('jersey_number');

        $players = Player::orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        // Players already in THIS squad
        $rosteredPlayerIds = $teamTournament->playerTournaments
            ->pluck('player_id')
            ->toArray();

        $rosterCount = count($rosteredPlayerIds);
        $isFull      = $rosterCount >= self::MAX_SQUAD_SIZE;

        return view('admin.roster.index', compact(
            'teamTournament', 'roster', 'players',
            'rosteredPlayerIds', 'rosterCount', 'isFull'
        ));
    }

    /**
     * Add a player to the squad roster.
     * URL: POST /admin/team-tournament/{teamTournament}/roster
     */
    public function store(Request $request, TeamTournament $teamTournament)
    {
        // Enforce max squad size
        $currentCount = PlayerTournament::where('team_tournament_id', $teamTournament->team_tournament_id)->count();
        if ($currentCount >= self::MAX_SQUAD_SIZE) {
            return redirect()
                ->route('admin.roster.index', $teamTournament->team_tournament_id)
                ->with('error', 'Squad is full. Maximum ' . self::MAX_SQUAD_SIZE . ' players allowed.');
        }

        $validated = $request->validate([
            'player_id'    => [
                'required', 'integer', 'exists:players,player_id',
                // (player_id, team_tournament_id) unique
                Rule::unique('player_tournament', 'player_id')
                    ->where('team_tournament_id', $teamTournament->team_tournament_id),
            ],
            'jersey_number'=> [
                'required', 'integer', 'min:1', 'max:99',
                // jersey_number must be unique within THIS squad (application-level check)
                Rule::unique('player_tournament', 'jersey_number')
                    ->where('team_tournament_id', $teamTournament->team_tournament_id),
            ],
            'is_captain'   => ['nullable', 'boolean'],
        ], [
            'player_id.unique'     => 'That player is already in this squad.',
            'jersey_number.unique' => 'Jersey number ' . $request->jersey_number . ' is already taken in this squad.',
        ]);

        // If setting as captain, clear existing captain first
        if (! empty($validated['is_captain'])) {
            PlayerTournament::where('team_tournament_id', $teamTournament->team_tournament_id)
                ->where('is_captain', 'Y')
                ->update(['is_captain' => 'N']);
        }

        PlayerTournament::create([
            'player_id'         => $validated['player_id'],
            'team_tournament_id'=> $teamTournament->team_tournament_id,
            'jersey_number'     => $validated['jersey_number'],
            'is_captain'        => ! empty($validated['is_captain']) ? 'Y' : 'N',
        ]);

        return redirect()
            ->route('admin.roster.index', $teamTournament->team_tournament_id)
            ->with('success', 'Player added to squad.');
    }

    /**
     * Remove a player from the squad.
     * URL: DELETE /admin/team-tournament/{teamTournament}/roster/{playerTournament}
     */
    public function destroy(TeamTournament $teamTournament, PlayerTournament $playerTournament)
    {
        // Safety check: the entry must belong to this teamTournament
        if ($playerTournament->team_tournament_id !== $teamTournament->team_tournament_id) {
            abort(404);
        }

        $playerTournament->delete();

        return redirect()
            ->route('admin.roster.index', $teamTournament->team_tournament_id)
            ->with('success', 'Player removed from squad.');
    }
}
