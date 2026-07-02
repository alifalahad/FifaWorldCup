<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Coach model — maps to the COACHES table (section 4.5).
 */
class Coach extends Model
{
    protected $primaryKey = 'coach_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'nationality',
        'coaching_license',
    ];

    // ── Relationships ────────────────────────────────────────────────────

    /** Team-tournament entries where this coach is assigned. */
    public function teamTournaments()
    {
        return $this->hasMany(TeamTournament::class, 'coach_id', 'coach_id');
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    /** Full name accessor. */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
