<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_cards_table
 *
 * Section 4.14 (Tier 2) of the database design.
 *
 * Oracle column mapping:
 *   card_id     → NUMBER PK IDENTITY
 *   match_id    → NUMBER FK → matches, NOT NULL
 *   player_id   → NUMBER FK → players, NOT NULL
 *   team_id     → NUMBER FK → teams, NOT NULL
 *   card_type   → VARCHAR2(20) NOT NULL, CHECK (YELLOW/RED/SECOND_YELLOW)
 *   card_minute → NUMBER(3) NOT NULL
 *   reason      → VARCHAR2(255) nullable
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id('card_id');
            $table->unsignedBigInteger('match_id');
            $table->unsignedBigInteger('player_id');
            $table->unsignedBigInteger('team_id');
            $table->string('card_type', 20);
            $table->smallInteger('card_minute');
            $table->string('reason', 255)->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('match_id')
                  ->references('match_id')
                  ->on('matches')
                  ->cascadeOnDelete();

            $table->foreign('player_id')
                  ->references('player_id')
                  ->on('players');

            $table->foreign('team_id')
                  ->references('team_id')
                  ->on('teams');
        });

        // CHECK: card_type values
        try {
            DB::statement("
            ALTER TABLE cards
            ADD CONSTRAINT chk_card_type
            CHECK (card_type IN ('YELLOW','RED','SECOND_YELLOW'))
        ");
        } catch (\Exception $e) {
            // Oracle-specific DDL — silently skipped on SQLite (test env)
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
