<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\Player;
use App\Models\Coach;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $q = trim($request->input('q', ''));

        if (strlen($q) < 2) {
            return view('public.search', [
                'q'        => $q,
                'teams'    => collect(),
                'players'  => collect(),
                'coaches'  => collect(),
                'tooShort' => true,
            ]);
        }

        // ── Improved search algorithm ────────────────────────────────────────
        // Strategy: use UPPER() on both sides for case-insensitive search on Oracle.
        // For multi-word queries (e.g. "Fernando Santos"), we search for EACH word
        // individually AND the full phrase. This way "Fernando Santos" finds the coach
        // even if first_name="Fernando" and last_name="Santos" are separate columns.
        //
        // We also strip extra spaces in the query.

        $terms = collect(preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY));
        $fullPhrase = '%' . strtoupper($q) . '%';

        // ── Teams: search country_name and abbreviation (case-insensitive) ───
        $teamsQuery = Team::query();
        foreach ($terms as $term) {
            $t = '%' . strtoupper($term) . '%';
            $teamsQuery->orWhereRaw('UPPER("COUNTRY_NAME") LIKE ?', [$t])
                       ->orWhereRaw('UPPER("ABBREVIATION") LIKE ?', [$t]);
        }
        $teams = $teamsQuery->orderBy('country_name')->limit(15)->get();

        // ── Players: each word + full-name concatenation ─────────────────────
        $playersQuery = Player::query();
        // Full phrase against concatenated name
        $playersQuery->whereRaw('UPPER("FIRST_NAME" || \' \' || "LAST_NAME") LIKE ?', [$fullPhrase]);
        // OR each individual term against first/last name separately
        foreach ($terms as $term) {
            $t = '%' . strtoupper($term) . '%';
            $playersQuery->orWhereRaw('UPPER("FIRST_NAME") LIKE ?', [$t])
                         ->orWhereRaw('UPPER("LAST_NAME") LIKE ?', [$t]);
        }
        $players = $playersQuery->orderBy('last_name')->orderBy('first_name')->limit(15)->get();

        // ── Coaches: same multi-word strategy as players ──────────────────────
        $coachesQuery = Coach::query();
        // Full phrase against concatenated name
        $coachesQuery->whereRaw('UPPER("FIRST_NAME" || \' \' || "LAST_NAME") LIKE ?', [$fullPhrase]);
        // OR each individual term against first/last name separately
        foreach ($terms as $term) {
            $t = '%' . strtoupper($term) . '%';
            $coachesQuery->orWhereRaw('UPPER("FIRST_NAME") LIKE ?', [$t])
                         ->orWhereRaw('UPPER("LAST_NAME") LIKE ?', [$t]);
        }
        $coaches = $coachesQuery->orderBy('last_name')->orderBy('first_name')->limit(15)->get();

        return view('public.search', compact('q', 'teams', 'players', 'coaches') + ['tooShort' => false]);
    }
}
