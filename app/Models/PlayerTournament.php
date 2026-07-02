<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * PlayerTournament model — maps to the PLAYER_TOURNAMENT junction table (section 4.11).
 *
 * Resolves Player ↔ Tournament M:N through TeamTournament,
 * carrying jersey number and captaincy per edition.
 */
class PlayerTournament extends Model
{
    protected $table = 'player_tournament';
    protected $primaryKey = 'player_tournament_id';

    protected $fillable = [
        'player_id',
        'team_tournament_id',
        'jersey_number',
        'is_captain',
    ];

    // ── Relationships ────────────────────────────────────────────────────

    /** The player in this registration. */
    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id', 'player_id');
    }

    /** The team-tournament entry this player belongs to. */
    public function teamTournament()
    {
        return $this->belongsTo(TeamTournament::class, 'team_tournament_id', 'team_tournament_id');
    }
}
