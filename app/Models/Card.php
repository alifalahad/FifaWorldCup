<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Card model — maps to the CARDS table (section 4.14).
 */
class Card extends Model
{
    protected $primaryKey = 'card_id';

    protected $fillable = [
        'match_id',
        'player_id',
        'team_id',
        'card_type',
        'card_minute',
        'reason',
    ];

    // ── Relationships ────────────────────────────────────────────────────

    /** The match this card was given in. */
    public function match()
    {
        return $this->belongsTo(GameMatch::class, 'match_id', 'match_id');
    }

    /** The player who received this card. */
    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id', 'player_id');
    }

    /** The team the carded player belongs to. */
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'team_id');
    }
}
