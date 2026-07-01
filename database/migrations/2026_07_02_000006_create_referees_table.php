<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_referees_table
 *
 * Section 4.8 (Tier 2) of the database design.
 *
 * Oracle column mapping:
 *   referee_id      → NUMBER PK IDENTITY
 *   first_name      → VARCHAR2(50) NOT NULL
 *   last_name       → VARCHAR2(50) NOT NULL
 *   nationality     → VARCHAR2(100) NOT NULL
 *   fifa_badge_year → NUMBER(4) nullable
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referees', function (Blueprint $table) {
            $table->id('referee_id');
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('nationality', 100);
            $table->integer('fifa_badge_year')->nullable();
            $table->timestamps();
        });

        // Index on nationality for filtering
        Schema::table('referees', function (Blueprint $table) {
            $table->index('nationality', 'idx_referees_nationality');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referees');
    }
};
