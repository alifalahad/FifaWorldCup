<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\User;

/**
 * FifaTestCase — base class for all FIFA World Cup feature tests.
 *
 * Database strategy:
 *   - phpunit.xml → DB_CONNECTION=sqlite, DB_DATABASE=:memory:
 *   - RefreshDatabase trait: fresh schema per test class via migrate
 *   - All Oracle-specific DB::statement() calls in migrations are wrapped
 *     in try/catch and silently skipped on SQLite
 *   - group_standings VIEW is recreated with SQLite-compatible CTE syntax
 *     in setUp(), since the Oracle CREATE OR REPLACE VIEW is skipped
 */
abstract class FifaTestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The Oracle CREATE OR REPLACE VIEW is silently skipped on SQLite.
        // Recreate it using portable CTE syntax so test 5 can use GroupStanding.
        try {
            DB::statement('DROP VIEW IF EXISTS group_standings');
            DB::statement("
                CREATE VIEW group_standings AS
                WITH match_results AS (
                    SELECT tournament_id, group_id, home_team_id AS team_id,
                           home_score AS goals_for, away_score AS goals_against
                    FROM matches WHERE stage = 'GROUP' AND status = 'COMPLETED'
                    UNION ALL
                    SELECT tournament_id, group_id, away_team_id AS team_id,
                           away_score AS goals_for, home_score AS goals_against
                    FROM matches WHERE stage = 'GROUP' AND status = 'COMPLETED'
                )
                SELECT group_id, tournament_id, team_id,
                    COUNT(*) AS played,
                    SUM(CASE WHEN goals_for > goals_against THEN 1 ELSE 0 END) AS won,
                    SUM(CASE WHEN goals_for = goals_against THEN 1 ELSE 0 END) AS drawn,
                    SUM(CASE WHEN goals_for < goals_against THEN 1 ELSE 0 END) AS lost,
                    SUM(goals_for) AS goals_for,
                    SUM(goals_against) AS goals_against,
                    SUM(goals_for) - SUM(goals_against) AS goal_difference,
                    SUM(CASE WHEN goals_for > goals_against THEN 3
                             WHEN goals_for = goals_against THEN 1
                             ELSE 0 END) AS points
                FROM match_results
                GROUP BY group_id, tournament_id, team_id
            ");
        } catch (\Exception $e) {
            // View already created for this test run — no-op
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    /**
     * Seed the roles table (ADMIN + VIEWER). Returns an array of both models.
     */
    protected function seedRoles(): array
    {
        $admin  = Role::create(['role_name' => 'ADMIN',  'description' => 'Administrator']);
        $viewer = Role::create(['role_name' => 'VIEWER', 'description' => 'Read-only viewer']);
        return compact('admin', 'viewer');
    }

    /**
     * Create an active ADMIN user (also seeds roles).
     */
    protected function makeAdmin(): User
    {
        ['admin' => $adminRole] = $this->seedRoles();
        return User::factory()->create([
            'role_id'   => $adminRole->role_id,
            'is_active' => 'Y',
        ]);
    }

    /**
     * Create an active VIEWER user. Assumes roles are already seeded.
     */
    protected function makeViewer(): User
    {
        $viewerRole = Role::where('role_name', 'VIEWER')->firstOrFail();
        return User::factory()->create([
            'role_id'   => $viewerRole->role_id,
            'is_active' => 'Y',
        ]);
    }

    /**
     * Minimal valid tournament attribute array.
     */
    protected function tournamentAttrs(array $overrides = []): array
    {
        return array_merge([
            'name'         => 'FIFA World Cup Test',
            'year'         => 2030,
            'host_country' => 'Testland',
            'start_date'   => '2030-06-01',
            'end_date'     => '2030-07-15',
            'total_teams'  => 32,
            'status'       => 'PLANNED',
        ], $overrides);
    }
}
