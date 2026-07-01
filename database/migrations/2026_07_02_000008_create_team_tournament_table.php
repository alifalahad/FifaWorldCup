<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_team_tournament_table
 *
 * Section 4.10 of the database design — junction table resolving TEAM ↔ TOURNAMENT M:N.
 *
 * Oracle column mapping:
 *   team_tournament_id → NUMBER PK IDENTITY
 *   team_id            → NUMBER FK → teams.team_id, NOT NULL
 *   tournament_id      → NUMBER FK → tournaments.tournament_id, NOT NULL
 *   group_id           → NUMBER FK → tournament_groups.group_id, nullable (unset until group draw)
 *   coach_id           → NUMBER FK → coaches.coach_id, nullable
 *   seed_position      → NUMBER(2) nullable
 *   elimination_stage  → VARCHAR2(30) nullable, CHECK
 *
 * Composite UNIQUE: (team_id, tournament_id)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_tournament', function (Blueprint $table) {
            $table->id('team_tournament_id');
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('tournament_id');
            $table->unsignedBigInteger('group_id')->nullable();
            $table->unsignedBigInteger('coach_id')->nullable();
            $table->smallInteger('seed_position')->nullable();
            $table->string('elimination_stage', 30)->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('team_id')
                  ->references('team_id')
                  ->on('teams')
                  ->cascadeOnDelete();

            $table->foreign('tournament_id')
                  ->references('tournament_id')
                  ->on('tournaments')
                  ->cascadeOnDelete();

            $table->foreign('group_id')
                  ->references('group_id')
                  ->on('tournament_groups')
                  ->nullOnDelete();

            $table->foreign('coach_id')
                  ->references('coach_id')
                  ->on('coaches')
                  ->nullOnDelete();

            // Composite UNIQUE: a team can only register once per tournament
            $table->unique(['team_id', 'tournament_id'], 'uq_team_tournament');
        });

        // CHECK constraint: elimination_stage values (nullable — set when team is eliminated)
        DB::statement("
            ALTER TABLE team_tournament
            ADD CONSTRAINT chk_tt_elim_stage
            CHECK (elimination_stage IS NULL OR elimination_stage IN (
                'GROUP','ROUND_OF_16','QUARTER_FINAL','SEMI_FINAL','THIRD_PLACE','FINAL','CHAMPION'
            ))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('team_tournament');
    }
};
