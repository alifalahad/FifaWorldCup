<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\Player;
use App\Models\Coach;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SearchSuggestController extends Controller
{
    /**
     * Returns a JSON autocomplete payload for the live search dropdown.
     * Called by Alpine.js as the user types in the global search bar.
     * Reuses the same multi-word, case-insensitive Oracle search algorithm
     * as the full search results page.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));

        if (strlen($q) < 2) {
            return response()->json(['teams' => [], 'players' => [], 'coaches' => []]);
        }

        $terms      = collect(preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY));
        $fullPhrase = '%' . strtoupper($q) . '%';

        // ── Teams ────────────────────────────────────────────────────────────
        $teamsQuery = Team::query();
        foreach ($terms as $term) {
            $t = '%' . strtoupper($term) . '%';
            $teamsQuery->orWhereRaw('UPPER("COUNTRY_NAME") LIKE ?', [$t])
                       ->orWhereRaw('UPPER("ABBREVIATION") LIKE ?', [$t]);
        }
        $teams = $teamsQuery->orderBy('country_name')->limit(5)->get(['team_id', 'country_name', 'abbreviation', 'continent']);

        // ── Players ──────────────────────────────────────────────────────────
        $playersQuery = Player::query();
        $playersQuery->whereRaw('UPPER("FIRST_NAME" || \' \' || "LAST_NAME") LIKE ?', [$fullPhrase]);
        foreach ($terms as $term) {
            $t = '%' . strtoupper($term) . '%';
            $playersQuery->orWhereRaw('UPPER("FIRST_NAME") LIKE ?', [$t])
                         ->orWhereRaw('UPPER("LAST_NAME") LIKE ?', [$t]);
        }
        $players = $playersQuery->orderBy('last_name')->orderBy('first_name')->limit(5)->get(['player_id', 'first_name', 'last_name', 'position', 'nationality']);

        // ── Coaches ──────────────────────────────────────────────────────────
        $coachesQuery = Coach::query();
        $coachesQuery->whereRaw('UPPER("FIRST_NAME" || \' \' || "LAST_NAME") LIKE ?', [$fullPhrase]);
        foreach ($terms as $term) {
            $t = '%' . strtoupper($term) . '%';
            $coachesQuery->orWhereRaw('UPPER("FIRST_NAME") LIKE ?', [$t])
                         ->orWhereRaw('UPPER("LAST_NAME") LIKE ?', [$t]);
        }
        $coaches = $coachesQuery->orderBy('last_name')->orderBy('first_name')->limit(3)->get(['coach_id', 'first_name', 'last_name', 'nationality']);

        return response()->json([
            'teams'   => $teams->map(fn ($t) => [
                'id'           => $t->team_id,
                'label'        => $t->country_name,
                'meta'         => $t->abbreviation . ' · ' . $t->continent,
                'url'          => route('teams.show', $t->team_id),
                'type'         => 'team',
                'icon'         => '🛡️',
            ]),
            'players' => $players->map(fn ($p) => [
                'id'    => $p->player_id,
                'label' => $p->first_name . ' ' . $p->last_name,
                'meta'  => $p->position . ' · ' . $p->nationality,
                'url'   => route('players.show', $p->player_id),
                'type'  => 'player',
                'icon'  => '⚽',
            ]),
            'coaches' => $coaches->map(fn ($c) => [
                'id'    => $c->coach_id,
                'label' => $c->first_name . ' ' . $c->last_name,
                'meta'  => 'Coach · ' . $c->nationality,
                'url'   => route('search') . '?q=' . urlencode($c->first_name . ' ' . $c->last_name),
                'type'  => 'coach',
                'icon'  => '🏋️',
            ]),
        ]);
    }
}
