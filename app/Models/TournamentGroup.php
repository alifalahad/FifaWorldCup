<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * TournamentGroup model — maps to the TOURNAMENT_GROUPS table (section 4.9).
 */
class TournamentGroup extends Model
{
    protected $table = 'tournament_groups';
    protected $primaryKey = 'group_id';

    protected $fillable = [
        'tournament_id',
        'group_name',
    ];

    // ── Relationships ────────────────────────────────────────────────────

    /** The tournament this group belongs to. */
    public function tournament()
    {
        return $this->belongsTo(Tournament::class, 'tournament_id', 'tournament_id');
    }

    /** Team-tournament entries assigned to this group. */
    public function teamTournaments()
    {
        return $this->hasMany(TeamTournament::class, 'group_id', 'group_id');
    }

    /** Matches played in this group. */
    public function matches()
    {
        return $this->hasMany(GameMatch::class, 'group_id', 'group_id');
    }
}
