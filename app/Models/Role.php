<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Role model — maps to the ROLES table (section 4.1 of database design).
 *
 * Columns:
 *   role_id     NUMBER(10) PK IDENTITY
 *   role_name   VARCHAR2(50) NOT NULL UNIQUE  (e.g. 'ADMIN', 'VIEWER')
 *   description VARCHAR2(255) nullable
 */
class Role extends Model
{
    /**
     * The primary key column name (matches design spec: role_id).
     */
    protected $primaryKey = 'role_id';

    /**
     * No Laravel-managed timestamps on this lookup/reference table.
     * (The design spec doesn't include created_at/updated_at on ROLE.)
     */
    public $timestamps = false;

    /**
     * Mass-assignable columns.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_name',
        'description',
    ];

    // ── Relationships ────────────────────────────────────────────────────

    /**
     * All users assigned to this role.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'role_id');
    }
}
