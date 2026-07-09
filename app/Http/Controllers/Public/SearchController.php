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
                'q'       => $q,
                'teams'   => collect(),
                'players' => collect(),
                'coaches' => collect(),
                'tooShort' => true,
            ]);
        }

        $like = '%' . $q . '%';

        // Teams — search country_name
        $teams = Team::where('country_name', 'like', $like)
            ->orWhere('abbreviation', 'like', $like)
            ->orderBy('country_name')
            ->limit(15)
            ->get();

        // Players — search first_name + last_name (both individually and concat)
        $players = Player::where('first_name', 'like', $like)
            ->orWhere('last_name', 'like', $like)
            ->orWhereRaw("first_name || ' ' || last_name LIKE ?", [$like])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->limit(15)
            ->get();

        // Coaches — search first_name + last_name
        $coaches = Coach::where('first_name', 'like', $like)
            ->orWhere('last_name', 'like', $like)
            ->orWhereRaw("first_name || ' ' || last_name LIKE ?", [$like])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->limit(15)
            ->get();

        return view('public.search', compact('q', 'teams', 'players', 'coaches') + ['tooShort' => false]);
    }
}
