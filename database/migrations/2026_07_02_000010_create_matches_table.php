<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_matches_table
 *
 * Section 4.12 of the database design.
 *
 * Oracle column mapping:
 *   match_id       → NUMBER PK IDENTITY
 *   tournament_id  → NUMBER FK → tournaments, NOT NULL
 *   stadium_id     → NUMBER FK → stadiums, NOT NULL
 *   referee_id     → NUMBER FK → referees, nullable (assigned closer to match day)
 *   home_team_id   → NUMBER FK → teams, NOT NULL
 *   away_team_id   → NUMBER FK → teams, NOT NULL
 *   group_id       → NUMBER FK → tournament_groups, nullable (NULL = knockout match)
 *   match_date     → DATE NOT NULL
 *   stage          → VARCHAR2(30) NOT NULL, CHECK
 *   home_score     → NUMBER(3) nullable
 *   away_score     → NUMBER(3) nullable
 *   has_extra_time → CHAR(1) DEFAULT 'N'
 *   has_penalties  → CHAR(1) DEFAULT 'N'
 *   status         → VARCHAR2(20) DEFAULT 'SCHEDULED', CHECK
 *
 * Business rule: CHECK (home_team_id != away_team_id)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id('match_id');
            $table->unsignedBigInteger('tournament_id');
            $table->unsignedBigInteger('stadium_id');
            $table->unsignedBigInteger('referee_id')->nullable();
            $table->unsignedBigInteger('home_team_id');
            $table->unsignedBigInteger('away_team_id');
            $table->unsignedBigInteger('group_id')->nullable();
            $table->date('match_date');
            $table->string('stage', 30);
            $table->smallInteger('home_score')->nullable();
            $table->smallInteger('away_score')->nullable();
            $table->char('has_extra_time', 1)->default('N');
            $table->char('has_penalties', 1)->default('N');
            $table->string('status', 20)->default('SCHEDULED');
            $table->timestamps();

            // Foreign keys
            $table->foreign('tournament_id')
                  ->references('tournament_id')
                  ->on('tournaments')
                  ->cascadeOnDelete();

            $table->foreign('stadium_id')
                  ->references('stadium_id')
                  ->on('stadiums');

            $table->foreign('referee_id')
                  ->references('referee_id')
                  ->on('referees')
                  ->nullOnDelete();

            $table->foreign('home_team_id')
                  ->references('team_id')
                  ->on('teams');

            $table->foreign('away_team_id')
                  ->references('team_id')
                  ->on('teams');

            $table->foreign('group_id')
                  ->references('group_id')
                  ->on('tournament_groups')
                  ->nullOnDelete();
        });

        // CHECK: stage values
        try {
            DB::statement("
            ALTER TABLE matches
            ADD CONSTRAINT chk_match_stage
            CHECK (stage IN ('GROUP','ROUND_OF_16','QUARTER_FINAL','SEMI_FINAL','THIRD_PLACE','FINAL'))
        ");
        } catch (\Exception $e) {
            // Oracle-specific DDL — silently skipped on SQLite (test env)
        }

        // CHECK: status values
        try {
            DB::statement("
            ALTER TABLE matches
            ADD CONSTRAINT chk_match_status
            CHECK (status IN ('SCHEDULED','LIVE','COMPLETED','POSTPONED','CANCELLED'))
        ");
        } catch (\Exception $e) {
            // Oracle-specific DDL — silently skipped on SQLite (test env)
        }

        // CHECK: a team cannot play against itself
        try {
            DB::statement("
            ALTER TABLE matches
            ADD CONSTRAINT chk_match_diff_teams
            CHECK (home_team_id != away_team_id)
        ");
        } catch (\Exception $e) {
            // Oracle-specific DDL — silently skipped on SQLite (test env)
        }

        // CHECK: extra time / penalties flags
        try {
            DB::statement("
            ALTER TABLE matches
            ADD CONSTRAINT chk_match_extra_time
            CHECK (has_extra_time IN ('Y','N'))
        ");
        } catch (\Exception $e) {
            // Oracle-specific DDL — silently skipped on SQLite (test env)
        }

        try {
            DB::statement("
            ALTER TABLE matches
            ADD CONSTRAINT chk_match_penalties
            CHECK (has_penalties IN ('Y','N'))
        ");
        } catch (\Exception $e) {
            // Oracle-specific DDL — silently skipped on SQLite (test env)
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
