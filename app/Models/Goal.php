<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Goal model — maps to the GOALS table (section 4.13).
 */
class Goal extends Model
{
    protected $primaryKey = 'goal_id';

    protected $fillable = [
        'match_id',
        'scorer_player_id',
        'assist_player_id',
        'team_id',
        'goal_minute',
        'goal_type',
        'half',
    ];

    // ── Relationships ────────────────────────────────────────────────────

    /** The match this goal was scored in. */
    public function match()
    {
        return $this->belongsTo(GameMatch::class, 'match_id', 'match_id');
    }

    /** The player who scored this goal. */
    public function scorer()
    {
        return $this->belongsTo(Player::class, 'scorer_player_id', 'player_id');
    }

    /** The player who assisted this goal (nullable). */
    public function assister()
    {
        return $this->belongsTo(Player::class, 'assist_player_id', 'player_id');
    }

    /** The team credited with this goal. */
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'team_id');
    }
}
