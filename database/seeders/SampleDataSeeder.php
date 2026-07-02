<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds realistic sample data for development and testing:
 *   - 2 tournaments (2022 Qatar, 2026 USA/Mexico/Canada)
 *   - 8 teams (diverse confederations)
 *   - 2 stadiums
 *   - 1 referee
 *   - 1 coach
 *   - Tournament groups for 2022
 *   - Team registrations for 2022
 *   - 1 ADMIN test user, 1 VIEWER test user
 *
 * Uses DB::table()->insertGetId() for tables without Eloquent models yet (Prompt 8 creates them).
 * All IDs are captured so FK references are correct.
 */
class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── TOURNAMENTS ──────────────────────────────────────────────────
        $t2022 = DB::table('tournaments')->insertGetId([
            'name'         => 'FIFA World Cup 2022',
            'year'         => 2022,
            'host_country' => 'Qatar',
            'start_date'   => '2022-11-20',
            'end_date'     => '2022-12-18',
            'total_teams'  => 32,
            'status'       => 'COMPLETED',
            'created_at'   => now(),
            'updated_at'   => now(),
        ], 'tournament_id');

        $t2026 = DB::table('tournaments')->insertGetId([
            'name'         => 'FIFA World Cup 2026',
            'year'         => 2026,
            'host_country' => 'USA / Mexico / Canada',
            'start_date'   => '2026-06-11',
            'end_date'     => '2026-07-19',
            'total_teams'  => 48,
            'status'       => 'PLANNED',
            'created_at'   => now(),
            'updated_at'   => now(),
        ], 'tournament_id');

        // ── TEAMS (8 — from diverse confederations) ──────────────────────
        $teams = [];
        $teamData = [
            ['country_name' => 'Argentina',    'abbreviation' => 'ARG', 'continent' => 'CONMEBOL', 'fifa_ranking' => 1],
            ['country_name' => 'France',       'abbreviation' => 'FRA', 'continent' => 'UEFA',     'fifa_ranking' => 2],
            ['country_name' => 'Brazil',       'abbreviation' => 'BRA', 'continent' => 'CONMEBOL', 'fifa_ranking' => 3],
            ['country_name' => 'England',      'abbreviation' => 'ENG', 'continent' => 'UEFA',     'fifa_ranking' => 4],
            ['country_name' => 'Germany',      'abbreviation' => 'GER', 'continent' => 'UEFA',     'fifa_ranking' => 15],
            ['country_name' => 'Japan',        'abbreviation' => 'JPN', 'continent' => 'AFC',      'fifa_ranking' => 20],
            ['country_name' => 'Morocco',      'abbreviation' => 'MAR', 'continent' => 'CAF',      'fifa_ranking' => 11],
            ['country_name' => 'USA',          'abbreviation' => 'USA', 'continent' => 'CONCACAF', 'fifa_ranking' => 13],
        ];

        foreach ($teamData as $t) {
            $teams[$t['abbreviation']] = DB::table('teams')->insertGetId(array_merge($t, [
                'created_at' => now(),
                'updated_at' => now(),
            ]), 'team_id');
        }

        // ── STADIUMS (2) ─────────────────────────────────────────────────
        $stadium1 = DB::table('stadiums')->insertGetId([
            'name'         => 'Lusail Iconic Stadium',
            'city'         => 'Lusail',
            'country'      => 'Qatar',
            'capacity'     => 80000,
            'surface_type' => 'NATURAL GRASS',
            'created_at'   => now(),
            'updated_at'   => now(),
        ], 'stadium_id');

        $stadium2 = DB::table('stadiums')->insertGetId([
            'name'         => 'Al Bayt Stadium',
            'city'         => 'Al Khor',
            'country'      => 'Qatar',
            'capacity'     => 60000,
            'surface_type' => 'NATURAL GRASS',
            'created_at'   => now(),
            'updated_at'   => now(),
        ], 'stadium_id');

        // ── REFEREES (1) ─────────────────────────────────────────────────
        $referee1 = DB::table('referees')->insertGetId([
            'first_name'      => 'Szymon',
            'last_name'       => 'Marciniak',
            'nationality'     => 'Poland',
            'fifa_badge_year' => 2011,
            'created_at'      => now(),
            'updated_at'      => now(),
        ], 'referee_id');

        // ── COACHES (1) ──────────────────────────────────────────────────
        $coach1 = DB::table('coaches')->insertGetId([
            'first_name'       => 'Lionel',
            'last_name'        => 'Scaloni',
            'nationality'      => 'Argentina',
            'coaching_license' => 'UEFA Pro',
            'created_at'       => now(),
            'updated_at'       => now(),
        ], 'coach_id');

        // ── TOURNAMENT GROUPS (4 groups for 2022: A, B, C, D) ────────────
        $groups = [];
        foreach (['A', 'B', 'C', 'D'] as $letter) {
            $groups[$letter] = DB::table('tournament_groups')->insertGetId([
                'tournament_id' => $t2022,
                'group_name'    => $letter,
                'created_at'    => now(),
                'updated_at'    => now(),
            ], 'group_id');
        }

        // ── TEAM_TOURNAMENT (register 8 teams into 2022, 2 per group) ────
        //  Group A: Argentina, USA
        //  Group B: France, Morocco
        //  Group C: Brazil, Japan
        //  Group D: England, Germany
        $groupAssignments = [
            'A' => ['ARG', 'USA'],
            'B' => ['FRA', 'MAR'],
            'C' => ['BRA', 'JPN'],
            'D' => ['ENG', 'GER'],
        ];

        $teamTournaments = [];
        $seed = 1;
        foreach ($groupAssignments as $groupLetter => $abbrevs) {
            foreach ($abbrevs as $abbr) {
                $ttId = DB::table('team_tournament')->insertGetId([
                    'team_id'           => $teams[$abbr],
                    'tournament_id'     => $t2022,
                    'group_id'          => $groups[$groupLetter],
                    'coach_id'          => $abbr === 'ARG' ? $coach1 : null,
                    'seed_position'     => $seed++,
                    'elimination_stage' => null,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ], 'team_tournament_id');
                $teamTournaments[$abbr] = $ttId;
            }
        }

        // ── SAMPLE MATCHES (2 group matches so the standings view has data) ──
        // Group A, Matchday 1: Argentina 2 – 0 USA
        $match1 = DB::table('matches')->insertGetId([
            'tournament_id' => $t2022,
            'stadium_id'    => $stadium1,
            'referee_id'    => $referee1,
            'home_team_id'  => $teams['ARG'],
            'away_team_id'  => $teams['USA'],
            'group_id'      => $groups['A'],
            'match_date'    => '2022-11-22',
            'stage'         => 'GROUP',
            'home_score'    => 2,
            'away_score'    => 0,
            'has_extra_time' => 'N',
            'has_penalties'  => 'N',
            'status'        => 'COMPLETED',
            'created_at'    => now(),
            'updated_at'    => now(),
        ], 'match_id');

        // Group B, Matchday 1: France 2 – 1 Morocco
        $match2 = DB::table('matches')->insertGetId([
            'tournament_id' => $t2022,
            'stadium_id'    => $stadium2,
            'referee_id'    => $referee1,
            'home_team_id'  => $teams['FRA'],
            'away_team_id'  => $teams['MAR'],
            'group_id'      => $groups['B'],
            'match_date'    => '2022-11-22',
            'stage'         => 'GROUP',
            'home_score'    => 2,
            'away_score'    => 1,
            'has_extra_time' => 'N',
            'has_penalties'  => 'N',
            'status'        => 'COMPLETED',
            'created_at'    => now(),
            'updated_at'    => now(),
        ], 'match_id');

        // ── TEST USERS (1 ADMIN, 1 VIEWER) ───────────────────────────────
        $adminRole  = Role::where('role_name', 'ADMIN')->first();
        $viewerRole = Role::where('role_name', 'VIEWER')->first();

        User::updateOrCreate(
            ['email' => 'admin@fifawc.test'],
            [
                'name'     => 'Admin User',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'role_id'  => $adminRole->role_id,
                'is_active' => 'Y',
            ]
        );

        User::updateOrCreate(
            ['email' => 'viewer@fifawc.test'],
            [
                'name'     => 'Viewer User',
                'username' => 'viewer',
                'password' => Hash::make('password'),
                'role_id'  => $viewerRole->role_id,
                'is_active' => 'Y',
            ]
        );
    }
}
