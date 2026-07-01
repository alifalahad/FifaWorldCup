<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_players_table
 *
 * Section 4.6 of the database design.
 *
 * Oracle column mapping:
 *   player_id      → NUMBER PK IDENTITY
 *   first_name     → VARCHAR2(50) NOT NULL
 *   last_name      → VARCHAR2(50) NOT NULL
 *   date_of_birth  → DATE NOT NULL
 *   nationality    → VARCHAR2(100) NOT NULL
 *   position       → VARCHAR2(20) NOT NULL, CHECK (GK/DF/MF/FW)
 *   height_cm      → NUMBER(5,2) nullable
 *   weight_kg      → NUMBER(5,2) nullable
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id('player_id');
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->date('date_of_birth');
            $table->string('nationality', 100);
            $table->string('position', 20);
            $table->decimal('height_cm', 5, 2)->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->timestamps();
        });

        // Indexes for common search/filter columns
        Schema::table('players', function (Blueprint $table) {
            $table->index('nationality', 'idx_players_nationality');
            $table->index('position', 'idx_players_position');
        });

        // CHECK constraint: position must be a valid value
        DB::statement("
            ALTER TABLE players
            ADD CONSTRAINT chk_player_position
            CHECK (position IN ('GK','DF','MF','FW'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
