<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Referee model — maps to the REFEREES table (section 4.8).
 */
class Referee extends Model
{
    protected $primaryKey = 'referee_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'nationality',
        'fifa_badge_year',
    ];

    // ── Relationships ────────────────────────────────────────────────────

    /** All matches officiated by this referee. */
    public function matches()
    {
        return $this->hasMany(GameMatch::class, 'referee_id', 'referee_id');
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    /** Full name accessor. */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
