<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * Includes all columns from the USERS table design spec (section 4.2):
     *   - name           : display name (Laravel convention, kept alongside username)
     *   - username        : VARCHAR2(50) UNIQUE — the login handle in our design
     *   - email           : VARCHAR2(100) UNIQUE
     *   - password        : hashed via cast below
     *   - role_id         : FK → roles.role_id
     *   - is_active       : CHAR(1) 'Y'/'N'
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────

    /**
     * The role assigned to this user.
     * Used in nav layout: auth()->user()->role->role_name === 'ADMIN'
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    /**
     * Convenience method: returns true when this user has ADMIN role.
     */
    public function isAdmin(): bool
    {
        return $this->role && $this->role->role_name === 'ADMIN';
    }

    /**
     * Convenience method: returns true when is_active === 'Y'.
     */
    public function isActive(): bool
    {
        return $this->is_active === 'Y';
    }
}
