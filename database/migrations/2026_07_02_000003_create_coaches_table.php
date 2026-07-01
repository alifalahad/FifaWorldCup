<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_coaches_table
 *
 * Section 4.5 (Tier 2) of the database design.
 *
 * Oracle column mapping:
 *   coach_id         → NUMBER PK IDENTITY
 *   first_name       → VARCHAR2(50) NOT NULL
 *   last_name        → VARCHAR2(50) NOT NULL
 *   nationality      → VARCHAR2(100) NOT NULL
 *   coaching_license  → VARCHAR2(50) nullable
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coaches', function (Blueprint $table) {
            $table->id('coach_id');
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('nationality', 100);
            $table->string('coaching_license', 50)->nullable();
            $table->timestamps();
        });

        // Index on nationality for filtering
        Schema::table('coaches', function (Blueprint $table) {
            $table->index('nationality', 'idx_coaches_nationality');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coaches');
    }
};
