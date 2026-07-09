<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TournamentController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\CoachController;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Admin\RosterController;
use App\Http\Controllers\Admin\StadiumController;
use App\Http\Controllers\Admin\RefereeController;
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

        // Prompt 12: Tournaments (real CRUD)
        Route::resource('tournaments', TournamentController::class)
            ->parameters(['tournaments' => 'tournament:tournament_id']);

        // Register Team nested routes on Tournament
        Route::get('/tournaments/{tournament:tournament_id}/register-team',
            [TournamentController::class, 'registerTeam'])
            ->name('tournaments.register-team');
        Route::post('/tournaments/{tournament:tournament_id}/register-team',
            [TournamentController::class, 'storeRegisteredTeam'])
            ->name('tournaments.register-team.store');

        // Prompt 13: Teams (real CRUD)
        Route::resource('teams', TeamController::class)
            ->parameters(['teams' => 'team:team_id'])
            ->except(['show']);

        // Prompt 13: Coaches (real CRUD)
        Route::resource('coaches', CoachController::class)
            ->parameters(['coaches' => 'coach:coach_id'])
            ->except(['show']);

        // Prompt 14: Players (real CRUD)
        Route::resource('players', PlayerController::class)
            ->parameters(['players' => 'player:player_id'])
            ->except(['show']);

        // Prompt 14: Roster management (team-tournament squad)
        Route::get('/team-tournament/{teamTournament}/roster',
            [RosterController::class, 'index'])->name('roster.index');
        Route::post('/team-tournament/{teamTournament}/roster',
            [RosterController::class, 'store'])->name('roster.store');
        Route::delete('/team-tournament/{teamTournament}/roster/{playerTournament}',
            [RosterController::class, 'destroy'])->name('roster.destroy');

        // Prompt 15: Stadiums (real CRUD)
        Route::resource('stadiums', StadiumController::class)
            ->parameters(['stadiums' => 'stadium:stadium_id'])
            ->except(['show']);

        // Prompt 15: Referees (real CRUD)
        Route::resource('referees', RefereeController::class)
            ->parameters(['referees' => 'referee:referee_id'])
            ->except(['show']);

        // Stub for Prompt 16 (Matches)
        Route::get('/matches',        fn () => $comingSoon('Matches'))->name('matches.index');
        Route::get('/matches/create', fn () => $comingSoon('Matches'))->name('matches.create');
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
