<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_stadiums_table
 *
 * Section 4.7 of the database design.
 *
 * Oracle column mapping:
 *   stadium_id   → NUMBER PK IDENTITY
 *   name         → VARCHAR2(150) NOT NULL
 *   city         → VARCHAR2(100) NOT NULL
 *   country      → VARCHAR2(100) NOT NULL
 *   capacity     → NUMBER(8) NOT NULL
 *   surface_type → VARCHAR2(50) DEFAULT 'NATURAL GRASS'
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stadiums', function (Blueprint $table) {
            $table->id('stadium_id');
            $table->string('name', 150);
            $table->string('city', 100);
            $table->string('country', 100);
            $table->integer('capacity');
            $table->string('surface_type', 50)->default('NATURAL GRASS');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stadiums');
    }
};
