<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_tournament_groups_table
 *
 * Section 4.9 of the database design.
 *
 * Oracle column mapping:
 *   group_id      → NUMBER PK IDENTITY
 *   tournament_id → NUMBER FK → tournaments.tournament_id, NOT NULL
 *   group_name    → CHAR(1) NOT NULL, CHECK (A–L)
 *
 * Composite UNIQUE: (tournament_id, group_name)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournament_groups', function (Blueprint $table) {
            $table->id('group_id');
            $table->unsignedBigInteger('tournament_id');
            $table->char('group_name', 1);
            $table->timestamps();

            // Foreign key
            $table->foreign('tournament_id')
                  ->references('tournament_id')
                  ->on('tournaments')
                  ->cascadeOnDelete();

            // Composite UNIQUE: no duplicate group letter within a tournament
            $table->unique(['tournament_id', 'group_name'], 'uq_tgroup_tourn_name');
        });

        // CHECK constraint: group_name must be A through L
        DB::statement("
            ALTER TABLE tournament_groups
            ADD CONSTRAINT chk_tgroup_name
            CHECK (group_name IN ('A','B','C','D','E','F','G','H','I','J','K','L'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_groups');
    }
};
