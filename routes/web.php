<?php

use App\Http\Controllers\ProfileController;
use App\Models\GroupStanding;
use App\Models\TournamentGroup;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ── Public home ──────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('home');
})->name('home');

// ── Dashboard (auth required — will be the admin landing) ────────────────
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ── Profile (Breeze — auth required) ─────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── Stub public nav routes (replaced by real controllers in Prompts 12-21) ─
Route::get('/tournaments', fn () => response('Tournaments page — coming soon!'))->name('tournaments.index');
Route::get('/teams',       fn () => response('Teams page — coming soon!'))->name('teams.index');
Route::get('/players',     fn () => response('Players page — coming soon!'))->name('players.index');
Route::get('/fixtures',    fn () => response('Fixtures page — coming soon!'))->name('fixtures.index');
Route::get('/standings',   fn () => response('Standings page — coming soon!'))->name('standings.index');

// ── Prompt 9: GroupStanding test route (remove after Prompt 18) ──────────
Route::get('/test/standings', function () {
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

// ── Breeze auth routes (login / register / logout / etc.) ────────────────
require __DIR__.'/auth.php';
