<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * TeamTournament model — maps to the TEAM_TOURNAMENT junction table (section 4.10).
 *
 * Resolves the M:N between Team and Tournament, also carrying
 * group assignment, coach, seed position, and elimination stage.
 */
class TeamTournament extends Model
{
    protected $table = 'team_tournament';
    protected $primaryKey = 'team_tournament_id';

    protected $fillable = [
        'team_id',
        'tournament_id',
        'group_id',
        'coach_id',
        'seed_position',
        'elimination_stage',
    ];

    // ── Relationships ────────────────────────────────────────────────────

    /** The team in this registration. */
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'team_id');
    }

    /** The tournament in this registration. */
    public function tournament()
    {
        return $this->belongsTo(Tournament::class, 'tournament_id', 'tournament_id');
    }

    /** The group this team is assigned to (nullable until group draw). */
    public function group()
    {
        return $this->belongsTo(TournamentGroup::class, 'group_id', 'group_id');
    }

    /** The coach for this team in this tournament (nullable). */
    public function coach()
    {
        return $this->belongsTo(Coach::class, 'coach_id', 'coach_id');
    }

    /** Player registrations under this team-tournament entry. */
    public function playerTournaments()
    {
        return $this->hasMany(PlayerTournament::class, 'team_tournament_id', 'team_tournament_id');
    }
}
