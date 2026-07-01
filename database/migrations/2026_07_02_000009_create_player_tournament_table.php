<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_player_tournament_table
 *
 * Section 4.11 of the database design — junction table resolving PLAYER ↔ TOURNAMENT M:N
 * (through TEAM_TOURNAMENT).
 *
 * Oracle column mapping:
 *   player_tournament_id → NUMBER PK IDENTITY
 *   player_id            → NUMBER FK → players.player_id, NOT NULL
 *   team_tournament_id   → NUMBER FK → team_tournament.team_tournament_id, NOT NULL
 *   jersey_number        → NUMBER(3) NOT NULL
 *   is_captain           → CHAR(1) DEFAULT 'N', CHECK (Y/N)
 *
 * Composite UNIQUE: (player_id, team_tournament_id)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_tournament', function (Blueprint $table) {
            $table->id('player_tournament_id');
            $table->unsignedBigInteger('player_id');
            $table->unsignedBigInteger('team_tournament_id');
            $table->smallInteger('jersey_number');
            $table->char('is_captain', 1)->default('N');
            $table->timestamps();

            // Foreign keys
            $table->foreign('player_id')
                  ->references('player_id')
                  ->on('players')
                  ->cascadeOnDelete();

            $table->foreign('team_tournament_id')
                  ->references('team_tournament_id')
                  ->on('team_tournament')
                  ->cascadeOnDelete();

            // Composite UNIQUE: a player can only be in one squad per team-tournament entry
            $table->unique(['player_id', 'team_tournament_id'], 'uq_player_tournament');
        });

        // CHECK constraint: is_captain must be Y or N
        DB::statement("
            ALTER TABLE player_tournament
            ADD CONSTRAINT chk_pt_is_captain
            CHECK (is_captain IN ('Y','N'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('player_tournament');
    }
};
