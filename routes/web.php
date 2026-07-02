<?php

use App\Models\GroupStanding;
use App\Models\TournamentGroup;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('home');
})->name('home');

// ── Stub auth routes (replaced by Breeze in Prompt 10) ──────────────────
Route::get('/login',    fn () => redirect('/'))->name('login');
Route::get('/register', fn () => redirect('/'))->name('register');
Route::post('/logout',  fn () => redirect('/'))->name('logout');

// ── Stub nav routes (replaced by real controllers in Prompts 12-21) ──────
Route::get('/tournaments', fn () => response('Tournaments page — coming soon!'))->name('tournaments.index');
Route::get('/teams',       fn () => response('Teams page — coming soon!'))->name('teams.index');
Route::get('/players',     fn () => response('Players page — coming soon!'))->name('players.index');
Route::get('/fixtures',    fn () => response('Fixtures page — coming soon!'))->name('fixtures.index');
Route::get('/standings',   fn () => response('Standings page — coming soon!'))->name('standings.index');

// ── Prompt 9: GroupStanding test route ───────────────────────────────────
// Verifies the view model + ranking scope return correct data.
// Hit /test/standings in the browser — remove after Prompt 18.
Route::get('/test/standings', function () {
    // Load all groups from DB so we can test with real IDs
    $groups = TournamentGroup::with('tournament')->get();

    $output = [];
    foreach ($groups as $group) {
        $standings = GroupStanding::rankedForGroup($group->group_id);

        $output[] = [
            'group'      => $group->group_name,
            'tournament' => $group->tournament->name ?? '—',
            'standings'  => $standings->map(fn ($s) => [
                'team'            => $s->team->country_name ?? "team_id:{$s->team_id}",
                'played'          => $s->played,
                'won'             => $s->won,
                'drawn'           => $s->drawn,
                'lost'            => $s->lost,
                'goals_for'       => $s->goals_for,
                'goals_against'   => $s->goals_against,
                'goal_difference' => $s->goal_difference,
                'points'          => $s->points,
            ])->values(),
        ];
    }

    return response()->json($output, 200, [], JSON_PRETTY_PRINT);
});
