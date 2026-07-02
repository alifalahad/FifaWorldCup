<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * GroupStanding model — maps to the GROUP_STANDINGS view (section 6/8).
 *
 * This is a read-only model backed by an Oracle VIEW, not a physical table.
 * It computes standings live from completed group-stage matches.
 */
class GroupStanding extends Model
{
    protected $table = 'group_standings';

    /** View has no single-row primary key. */
    protected $primaryKey = null;
    public $incrementing = false;

    /** View — no timestamps. */
    public $timestamps = false;

    // ── Relationships ────────────────────────────────────────────────────

    /** The group this standing row belongs to. */
    public function group()
    {
        return $this->belongsTo(TournamentGroup::class, 'group_id', 'group_id');
    }

    /** The tournament this standing row belongs to. */
    public function tournament()
    {
        return $this->belongsTo(Tournament::class, 'tournament_id', 'tournament_id');
    }

    /** The team this standing row belongs to. */
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'team_id');
    }
}
