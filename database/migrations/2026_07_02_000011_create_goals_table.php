<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_goals_table
 *
 * Section 4.13 of the database design.
 *
 * Oracle column mapping:
 *   goal_id           → NUMBER PK IDENTITY
 *   match_id          → NUMBER FK → matches, NOT NULL
 *   scorer_player_id  → NUMBER FK → players, NOT NULL
 *   assist_player_id  → NUMBER FK → players, nullable
 *   team_id           → NUMBER FK → teams, NOT NULL (credited team; opponent for own goals)
 *   goal_minute       → NUMBER(3) NOT NULL
 *   goal_type         → VARCHAR2(20) DEFAULT 'OPEN_PLAY', CHECK
 *   half              → VARCHAR2(5) CHECK (1ST/2ND/ET1/ET2)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->id('goal_id');
            $table->unsignedBigInteger('match_id');
            $table->unsignedBigInteger('scorer_player_id');
            $table->unsignedBigInteger('assist_player_id')->nullable();
            $table->unsignedBigInteger('team_id');
            $table->smallInteger('goal_minute');
            $table->string('goal_type', 20)->default('OPEN_PLAY');
            $table->string('half', 5)->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('match_id')
                  ->references('match_id')
                  ->on('matches')
                  ->cascadeOnDelete();

            $table->foreign('scorer_player_id')
                  ->references('player_id')
                  ->on('players');

            $table->foreign('assist_player_id')
                  ->references('player_id')
                  ->on('players')
                  ->nullOnDelete();

            $table->foreign('team_id')
                  ->references('team_id')
                  ->on('teams');
        });

        // CHECK: goal_type values
        try {
            DB::statement("
            ALTER TABLE goals
            ADD CONSTRAINT chk_goal_type
            CHECK (goal_type IN ('OPEN_PLAY','PENALTY','FREE_KICK','HEADER','OWN_GOAL'))
        ");
        } catch (\Exception $e) {
            // Oracle-specific DDL — silently skipped on SQLite (test env)
        }

        // CHECK: half values
        try {
            DB::statement("
            ALTER TABLE goals
            ADD CONSTRAINT chk_goal_half
            CHECK (half IS NULL OR half IN ('1ST','2ND','ET1','ET2'))
        ");
        } catch (\Exception $e) {
            // Oracle-specific DDL — silently skipped on SQLite (test env)
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};
