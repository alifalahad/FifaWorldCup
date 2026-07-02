<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * GroupStanding model — maps to the GROUP_STANDINGS view (section 6/8).
 *
 * Read-only model backed by an Oracle VIEW — no primary key, no timestamps.
 * The view computes points/GD/GF/GA live from completed group-stage matches.
 */
class GroupStanding extends Model
{
    protected $table = 'group_standings';

    /** View has no single-row primary key. */
    protected $primaryKey = null;
    public $incrementing = false;

    /** View — no managed timestamps. */
    public $timestamps = false;

    // ── Query scopes ─────────────────────────────────────────────────────

    /**
     * Scope: standings for a single group, ranked by:
     *   1. points DESC
     *   2. goal_difference DESC
     *   3. goals_for DESC
     *
     * Matches the RANK() OVER query in section 6 of the design document.
     *
     * Usage:
     *   GroupStanding::forGroup($groupId)->get();
     */
    public function scopeForGroup($query, int $groupId)
    {
        return $query
            ->where('group_id', $groupId)
            ->orderByDesc('points')
            ->orderByDesc('goal_difference')
            ->orderByDesc('goals_for');
    }

    /**
     * Static helper: returns ranked standings for a group, eager-loading
     * the Team relationship so views can access $standing->team->country_name.
     *
     * @param int $groupId
     * @return Collection<GroupStanding>
     */
    public static function rankedForGroup(int $groupId): Collection
    {
        return static::with('team')
            ->forGroup($groupId)
            ->get();
    }

    /**
     * Static helper: all standings for an entire tournament, grouped by group_id,
     * each group sorted by points → GD → GF.
     *
     * @param int $tournamentId
     * @return \Illuminate\Support\Collection   keyed by group_id
     */
    public static function forTournament(int $tournamentId): \Illuminate\Support\Collection
    {
        return static::with('team')
            ->where('tournament_id', $tournamentId)
            ->orderByDesc('points')
            ->orderByDesc('goal_difference')
            ->orderByDesc('goals_for')
            ->get()
            ->groupBy('group_id');
    }

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

    /** The team this standing row is for. */
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'team_id');
    }
}
