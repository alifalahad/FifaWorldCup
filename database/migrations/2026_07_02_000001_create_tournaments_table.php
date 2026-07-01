<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_tournaments_table
 *
 * Section 4.3 of the database design.
 *
 * Oracle column mapping:
 *   tournament_id → NUMBER PK IDENTITY
 *   name          → VARCHAR2(100) NOT NULL
 *   year          → NUMBER(4) NOT NULL UNIQUE
 *   host_country  → VARCHAR2(100) NOT NULL
 *   start_date    → DATE NOT NULL
 *   end_date      → DATE NOT NULL
 *   total_teams   → NUMBER(3) DEFAULT 32
 *   status        → VARCHAR2(20) DEFAULT 'PLANNED', CHECK
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id('tournament_id');
            $table->string('name', 100);
            $table->integer('year')->unique();
            $table->string('host_country', 100);
            $table->date('start_date');
            $table->date('end_date');
            $table->smallInteger('total_teams')->default(32);
            $table->string('status', 20)->default('PLANNED');
            $table->timestamps();
        });

        // CHECK constraint: status must be one of the allowed values
        DB::statement("
            ALTER TABLE tournaments
            ADD CONSTRAINT chk_tournament_status
            CHECK (status IN ('PLANNED','ONGOING','COMPLETED','CANCELLED'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
