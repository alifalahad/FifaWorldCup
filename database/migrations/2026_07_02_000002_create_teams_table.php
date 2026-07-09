<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_teams_table
 *
 * Section 4.4 of the database design.
 *
 * Oracle column mapping:
 *   team_id       → NUMBER PK IDENTITY
 *   country_name  → VARCHAR2(100) NOT NULL UNIQUE
 *   abbreviation  → CHAR(3) NOT NULL UNIQUE
 *   continent     → VARCHAR2(50) NOT NULL, CHECK (confederation values)
 *   fifa_ranking  → NUMBER(5) nullable
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id('team_id');
            $table->string('country_name', 100)->unique();
            $table->char('abbreviation', 3)->unique();
            $table->string('continent', 50);
            $table->integer('fifa_ranking')->nullable();
            $table->timestamps();
        });

        // Index on country_name for search/filter queries
        // (unique index already created by ->unique() above)

        // CHECK constraint: continent must be a FIFA confederation
        try {
            DB::statement("
            ALTER TABLE teams
            ADD CONSTRAINT chk_team_continent
            CHECK (continent IN ('AFC','CAF','CONCACAF','CONMEBOL','OFC','UEFA'))
        ");
        } catch (\Exception $e) {
            // Oracle-specific DDL — silently skipped on SQLite (test env)
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
