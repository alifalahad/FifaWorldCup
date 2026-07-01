<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_roles_table
 *
 * Creates the ROLE table as defined in section 4.1 of the database design.
 * Must run BEFORE the users migration because users.role_id FK references this table.
 *
 * Oracle column mapping:
 *   role_id      → NUMBER(10)  PK IDENTITY  ($table->id())
 *   role_name    → VARCHAR2(50) NOT NULL UNIQUE
 *   description  → VARCHAR2(255) nullable
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            // NUMBER(10) PRIMARY KEY IDENTITY
            $table->id('role_id');

            // VARCHAR2(50) NOT NULL UNIQUE
            // Values: 'ADMIN', 'VIEWER'
            $table->string('role_name', 50)->unique();

            // VARCHAR2(255) nullable
            $table->string('description', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
