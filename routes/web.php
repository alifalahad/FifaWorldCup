<?php

use App\Http\Controllers\Admin\DashboardController;
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

// ── Dashboard — admins auto-redirect to admin panel ───────────────────────
Route::get('/dashboard', function () {
    if (auth()->check() && auth()->user()->role?->role_name === 'ADMIN') {
        return redirect()->route('admin.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ── Profile (Breeze — auth required) ─────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── Admin routes — protected by auth + role:ADMIN ────────────────────────
Route::prefix('admin')
    ->middleware(['auth', 'role:ADMIN'])
    ->name('admin.')
    ->group(function () {

        // Dashboard (Prompt 11)
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Stub sections — replaced by real controllers in Prompts 12-17
        $comingSoon = fn (string $section) => response()->view('admin.coming-soon', ['section' => $section]);
        Route::get('/tournaments',        fn () => $comingSoon('Tournaments'))->name('tournaments.index');
        Route::get('/tournaments/create', fn () => $comingSoon('Tournaments'))->name('tournaments.create');
        Route::get('/teams',              fn () => $comingSoon('Teams'))->name('teams.index');
        Route::get('/teams/create',       fn () => $comingSoon('Teams'))->name('teams.create');
        Route::get('/players',            fn () => $comingSoon('Players'))->name('players.index');
        Route::get('/players/create',     fn () => $comingSoon('Players'))->name('players.create');
        Route::get('/coaches',            fn () => $comingSoon('Coaches'))->name('coaches.index');
        Route::get('/coaches/create',     fn () => $comingSoon('Coaches'))->name('coaches.create');
        Route::get('/stadiums',           fn () => $comingSoon('Stadiums'))->name('stadiums.index');
        Route::get('/stadiums/create',    fn () => $comingSoon('Stadiums'))->name('stadiums.create');
        Route::get('/referees',           fn () => $comingSoon('Referees'))->name('referees.index');
        Route::get('/referees/create',    fn () => $comingSoon('Referees'))->name('referees.create');
        Route::get('/matches',            fn () => $comingSoon('Matches'))->name('matches.index');
        Route::get('/matches/create',     fn () => $comingSoon('Matches'))->name('matches.create');
    });

// ── Stub public nav routes (replaced by real controllers in Prompts 18-21) ─
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
