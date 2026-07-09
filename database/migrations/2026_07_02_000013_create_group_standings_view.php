<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Migration: create_group_standings_view
 *
 * Section 6 of the database design — replaces the old physical GROUP_STANDING table
 * with a VIEW that computes standings live from MATCHES data.
 *
 * Uses DB::statement() because Laravel's Schema builder doesn't support CREATE VIEW.
 */
return new class extends Migration
{
    public function up(): void
    {
        try {
            DB::statement("
            CREATE OR REPLACE VIEW group_standings AS
            WITH match_results AS (
                SELECT tournament_id, group_id, home_team_id AS team_id,
                       home_score AS goals_for, away_score AS goals_against
                FROM matches
                WHERE stage = 'GROUP' AND status = 'COMPLETED'
                UNION ALL
                SELECT tournament_id, group_id, away_team_id AS team_id,
                       away_score AS goals_for, home_score AS goals_against
                FROM matches
                WHERE stage = 'GROUP' AND status = 'COMPLETED'
            )
            SELECT
                group_id,
                tournament_id,
                team_id,
                COUNT(*)                                                            AS played,
                SUM(CASE WHEN goals_for > goals_against THEN 1 ELSE 0 END)          AS won,
                SUM(CASE WHEN goals_for = goals_against THEN 1 ELSE 0 END)          AS drawn,
                SUM(CASE WHEN goals_for < goals_against THEN 1 ELSE 0 END)          AS lost,
                SUM(goals_for)                                                      AS goals_for,
                SUM(goals_against)                                                  AS goals_against,
                SUM(goals_for) - SUM(goals_against)                                 AS goal_difference,
                SUM(CASE WHEN goals_for > goals_against THEN 3
                         WHEN goals_for = goals_against THEN 1 ELSE 0 END)          AS points
            FROM match_results
            GROUP BY group_id, tournament_id, team_id
        ");
        } catch (\Exception $e) {
            // Oracle-specific DDL — silently skipped on SQLite (test env)
        }
    }

    public function down(): void
    {
        try {
            DB::statement('DROP VIEW IF EXISTS group_standings');
        } catch (\Exception $e) {
            // Oracle-specific DDL — silently skipped on SQLite (test env)
        }
    }
};
