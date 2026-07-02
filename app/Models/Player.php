<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Player model — maps to the PLAYERS table (section 4.6).
 */
class Player extends Model
{
    protected $primaryKey = 'player_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth',
        'nationality',
        'position',
        'height_cm',
        'weight_kg',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'height_cm'     => 'decimal:2',
            'weight_kg'     => 'decimal:2',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────

    /** Player-tournament registrations for this player. */
    public function playerTournaments()
    {
        return $this->hasMany(PlayerTournament::class, 'player_id', 'player_id');
    }

    /** Goals scored by this player. */
    public function goalsScored()
    {
        return $this->hasMany(Goal::class, 'scorer_player_id', 'player_id');
    }

    /** Goals assisted by this player. */
    public function goalsAssisted()
    {
        return $this->hasMany(Goal::class, 'assist_player_id', 'player_id');
    }

    /** Cards received by this player. */
    public function cards()
    {
        return $this->hasMany(Card::class, 'player_id', 'player_id');
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    /** Full name accessor. */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
