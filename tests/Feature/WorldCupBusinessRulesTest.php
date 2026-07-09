<?php

namespace Tests\Feature;

use Tests\FifaTestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Tournament;
use App\Models\Team;
use App\Models\Stadium;
use App\Models\TournamentGroup;
use App\Models\TeamTournament;
use App\Models\GroupStanding;
use Illuminate\Support\Facades\DB;

/**
 * Feature tests for core FIFA World Cup business rules.
 *
 * Test strategy:
 * - SQLite in-memory (phpunit.xml default)
 * - RefreshDatabase via FifaTestCase (fresh schema per class)
 * - Oracle-specific DDL (ALTER TABLE ADD CONSTRAINT, CREATE OR REPLACE VIEW)
 *   is swallowed by the overridden runDatabaseMigrations()
 * - The group_standings VIEW is recreated with SQLite-compatible CTE syntax
 */
class WorldCupBusinessRulesTest extends FifaTestCase
{
    // ──────────────────────────────────────────────────────────────────────
    // Test 1 — Unauthenticated user cannot access /admin routes
    // ──────────────────────────────────────────────────────────────────────

    /**
     * An unauthenticated visitor hitting any /admin URL should be redirected
     * to /login — never served the admin page.
     */
    public function test_unauthenticated_user_is_redirected_from_admin(): void
    {
        $response = $this->get(route('admin.tournaments.index'));

        // Must redirect (302) to login, not 200/403
        $response->assertRedirectToRoute('login');
    }

    /**
     * POST to an admin endpoint while unauthenticated also redirects to login.
     */
    public function test_unauthenticated_user_cannot_post_to_admin(): void
    {
        $response = $this->post(route('admin.tournaments.store'), $this->tournamentAttrs());

        $response->assertRedirectToRoute('login');
    }

    // ──────────────────────────────────────────────────────────────────────
    // Test 2 — VIEWER cannot access /admin; ADMIN can
    // ──────────────────────────────────────────────────────────────────────

    /**
     * A logged-in VIEWER-role user must be rejected from admin routes
     * with a redirect back to home (not 403 abort — the middleware redirects
     * with an error flash message instead).
     */
    public function test_viewer_role_cannot_access_admin_routes(): void
    {
        $this->seedRoles();
        $viewer = $this->makeViewer();

        $response = $this->actingAs($viewer)->get(route('admin.tournaments.index'));

        // Middleware redirects to home with an error message — not a 200
        $response->assertRedirect(route('home'));
        // Ensure it wasn't allowed through
        $response->assertSessionHas('error');
    }

    /**
     * A logged-in ADMIN-role user must be able to reach the admin dashboard.
     */
    public function test_admin_role_can_access_admin_routes(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get(route('admin.tournaments.index'));

        $response->assertOk();
    }

    // ──────────────────────────────────────────────────────────────────────
    // Test 3 — Duplicate tournament year fails validation
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Creating a tournament with a year that already exists should fail
     * Laravel validation and redirect back with a 'year' error.
     * This mirrors the unique constraint on tournaments.year.
     */
    public function test_duplicate_tournament_year_fails_validation(): void
    {
        $admin = $this->makeAdmin();

        // Create the first tournament (should succeed)
        Tournament::create($this->tournamentAttrs(['year' => 2026]));

        // Try to create a second tournament with the same year
        $response = $this->actingAs($admin)
            ->post(route('admin.tournaments.store'), $this->tournamentAttrs(['year' => 2026]));

        $response->assertSessionHasErrors('year');
        $this->assertSame(
            'A tournament for that year already exists.',
            session('errors')->first('year')
        );
        $this->assertDatabaseCount('tournaments', 1); // Only the first one was saved
    }

    /**
     * A tournament with a unique year succeeds and appears in the database.
     */
    public function test_tournament_with_unique_year_is_created(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)
            ->post(route('admin.tournaments.store'), $this->tournamentAttrs(['year' => 2034]));

        $response->assertRedirect(route('admin.tournaments.index'));
        $this->assertDatabaseHas('tournaments', ['year' => 2034]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // Test 4 — Same team cannot be registered twice for one tournament
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Registering the same team a second time for the same tournament must
     * fail at the application validation layer with a friendly error message,
     * before Oracle's unique constraint ever fires.
     */
    public function test_registering_same_team_twice_for_tournament_fails(): void
    {
        $admin      = $this->makeAdmin();
        $tournament = Tournament::create($this->tournamentAttrs());
        $team       = Team::create([
            'country_name' => 'Brazil',
            'abbreviation' => 'BRA',
            'continent'    => 'CONMEBOL',
            'fifa_ranking' => 1,
        ]);

        // First registration — must succeed
        $first = $this->actingAs($admin)->post(
            route('admin.tournaments.register-team.store', $tournament->tournament_id),
            ['team_id' => $team->team_id]
        );
        $first->assertRedirect();
        $this->assertDatabaseCount('team_tournament', 1);

        // Second registration — must fail validation
        $second = $this->actingAs($admin)->post(
            route('admin.tournaments.register-team.store', $tournament->tournament_id),
            ['team_id' => $team->team_id]
        );

        $second->assertSessionHasErrors('team_id');
        $this->assertSame(
            'That team is already registered for this tournament.',
            session('errors')->first('team_id')
        );
        $this->assertDatabaseCount('team_tournament', 1); // Still only one row
    }

    // ──────────────────────────────────────────────────────────────────────
    // Test 5 — Completed match correctly appears in group_standings view
    // ──────────────────────────────────────────────────────────────────────

    /**
     * When a GROUP-stage match is set to COMPLETED with scores, the
     * group_standings VIEW must reflect the result correctly:
     * - The winning team should have 3 pts, 1W, 1 played
     * - The losing team should have 0 pts, 1L, 1 played
     * - GF/GA/GD must be accurate
     */
    public function test_completed_group_match_appears_correctly_in_standings(): void
    {
        // ── Arrange ─────────────────────────────────────────────────────

        // We need a tournament, group, two teams, and a stadium for the match
        $tournament = Tournament::create($this->tournamentAttrs(['year' => 2026]));

        $group = TournamentGroup::create([
            'tournament_id' => $tournament->tournament_id,
            'group_name'    => 'A',
        ]);

        $teamA = Team::create([
            'country_name' => 'Argentina',
            'abbreviation' => 'ARG',
            'continent'    => 'CONMEBOL',
        ]);
        $teamB = Team::create([
            'country_name' => 'England',
            'abbreviation' => 'ENG',
            'continent'    => 'UEFA',
        ]);

        $stadium = Stadium::create([
            'name'         => 'Test Arena',
            'city'         => 'Testville',
            'country'      => 'Testland',
            'capacity'     => 50000,
            'surface_type' => 'GRASS',
        ]);

        // Insert a completed match directly — bypassing controller to test the VIEW
        DB::table('matches')->insert([
            'tournament_id' => $tournament->tournament_id,
            'stadium_id'    => $stadium->stadium_id,
            'home_team_id'  => $teamA->team_id,
            'away_team_id'  => $teamB->team_id,
            'group_id'      => $group->group_id,
            'match_date'    => '2026-06-10',
            'stage'         => 'GROUP',
            'home_score'    => 2,
            'away_score'    => 0,
            'has_extra_time'=> 'N',
            'has_penalties' => 'N',
            'status'        => 'COMPLETED',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // ── Assert ───────────────────────────────────────────────────────

        // forTournament() returns Collection keyed by group_id, each value is a
        // nested Collection of rows. Flatten one level to get all rows.
        $allRows = GroupStanding::forTournament($tournament->tournament_id)->flatten(1);

        $this->assertCount(2, $allRows, 'Both teams should appear in the standings');

        $rowA = $allRows->firstWhere('team_id', $teamA->team_id);
        $rowB = $allRows->firstWhere('team_id', $teamB->team_id);

        $this->assertNotNull($rowA, 'Argentina should be in the standings');
        $this->assertNotNull($rowB, 'England should be in the standings');

        // Argentina (home, 2-0 win)
        $this->assertEquals(1, $rowA->played,          'ARG: played = 1');
        $this->assertEquals(1, $rowA->won,             'ARG: won = 1');
        $this->assertEquals(0, $rowA->drawn,           'ARG: drawn = 0');
        $this->assertEquals(0, $rowA->lost,            'ARG: lost = 0');
        $this->assertEquals(2, $rowA->goals_for,       'ARG: GF = 2');
        $this->assertEquals(0, $rowA->goals_against,   'ARG: GA = 0');
        $this->assertEquals(2, $rowA->goal_difference, 'ARG: GD = +2');
        $this->assertEquals(3, $rowA->points,          'ARG: points = 3');

        // England (away, 0-2 loss)
        $this->assertEquals(1, $rowB->played,          'ENG: played = 1');
        $this->assertEquals(0, $rowB->won,             'ENG: won = 0');
        $this->assertEquals(0, $rowB->drawn,           'ENG: drawn = 0');
        $this->assertEquals(1, $rowB->lost,            'ENG: lost = 1');
        $this->assertEquals(0, $rowB->goals_for,       'ENG: GF = 0');
        $this->assertEquals(2, $rowB->goals_against,   'ENG: GA = 2');
        $this->assertEquals(-2, $rowB->goal_difference,'ENG: GD = -2');
        $this->assertEquals(0, $rowB->points,          'ENG: points = 0');
    }

    /**
     * A match with status SCHEDULED (not COMPLETED) must NOT appear in standings.
     */
    public function test_scheduled_match_does_not_appear_in_standings(): void
    {
        $tournament = Tournament::create($this->tournamentAttrs());
        $group      = TournamentGroup::create([
            'tournament_id' => $tournament->tournament_id,
            'group_name'    => 'B',
        ]);
        $teamA = Team::create(['country_name' => 'France',  'abbreviation' => 'FRA', 'continent' => 'UEFA']);
        $teamB = Team::create(['country_name' => 'Germany', 'abbreviation' => 'GER', 'continent' => 'UEFA']);
        $stadium = Stadium::create(['name' => 'Arena B', 'city' => 'Paris', 'country' => 'France', 'capacity' => 80000, 'surface_type' => 'GRASS']);

        DB::table('matches')->insert([
            'tournament_id' => $tournament->tournament_id,
            'stadium_id'    => $stadium->stadium_id,
            'home_team_id'  => $teamA->team_id,
            'away_team_id'  => $teamB->team_id,
            'group_id'      => $group->group_id,
            'match_date'    => '2026-06-11',
            'stage'         => 'GROUP',
            'home_score'    => null,
            'away_score'    => null,
            'has_extra_time'=> 'N',
            'has_penalties' => 'N',
            'status'        => 'SCHEDULED',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $standings = GroupStanding::forTournament($tournament->tournament_id);

        $this->assertEmpty($standings, 'Scheduled matches must not appear in standings');
    }
}
