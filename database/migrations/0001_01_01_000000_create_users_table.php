<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_users_table
 *
 * Extends Laravel's default users table with the columns defined in section 4.2
 * of the database design (USERS table), while keeping all standard Laravel auth
 * columns intact so Breeze/Auth scaffolding works without modification.
 *
 * Design spec additions:
 *   username     → VARCHAR2(50) NOT NULL UNIQUE
 *   role_id      → NUMBER(10) FK → roles.role_id, NOT NULL
 *   is_active    → CHAR(1) DEFAULT 'Y', CHECK IN ('Y','N')
 *   created_at   → covered by $table->timestamps()
 *
 * Standard Laravel columns kept:
 *   id (user_id), name, email, email_verified_at, password, remember_token,
 *   created_at, updated_at
 *
 * Oracle column mapping:
 *   id()              → NUMBER(20) PK IDENTITY
 *   string(50)        → VARCHAR2(50)
 *   string(100)       → VARCHAR2(100)
 *   string(256)       → VARCHAR2(256)  (password_hash)
 *   char(1)           → CHAR(1)
 *   timestamp         → TIMESTAMP
 *   foreignId         → NUMBER(20) FK
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // ── Standard Laravel auth columns ──────────────────────────────
            $table->id();                                           // NUMBER(20) PK IDENTITY  (user_id in design)

            // Laravel auth uses 'name'; we keep it + add 'username' for our design
            $table->string('name', 100);                           // VARCHAR2(100) — display name (Laravel convention)
            $table->string('username', 50)->unique();              // VARCHAR2(50) NOT NULL UNIQUE (design spec)
            $table->string('email', 100)->unique();                // VARCHAR2(100) NOT NULL UNIQUE
            $table->timestamp('email_verified_at')->nullable();    // TIMESTAMP nullable
            $table->string('password', 256);                       // VARCHAR2(256) — password_hash in design
            $table->rememberToken();                               // VARCHAR2(100) nullable — for Laravel "remember me"

            // ── Design spec additions ──────────────────────────────────────
            // role_id: FK → roles.role_id, NOT NULL
            // No ->default() at DB level; the VIEWER role_id will be set in seeders/factories.
            $table->unsignedBigInteger('role_id');

            // is_active: CHAR(1) CHECK IN ('Y','N'), DEFAULT 'Y'
            $table->char('is_active', 1)->default('Y');

            // ── Timestamps (covers created_at from design spec) ───────────
            $table->timestamps();                                   // created_at + updated_at TIMESTAMP

            // ── Foreign key constraint ────────────────────────────────────
            $table->foreign('role_id')
                  ->references('role_id')
                  ->on('roles')
                  ->restrictOnDelete();  // prevent deleting a role that has users
        });

        // ── password_reset_tokens (default Laravel table, keep as-is) ─────
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // ── sessions (only needed when SESSION_DRIVER=database) ───────────
        // Currently SESSION_DRIVER=file so this table isn't used, but we
        // create it so switching to database sessions later requires no migration.
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
