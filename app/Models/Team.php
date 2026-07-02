<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Team model — maps to the TEAMS table (section 4.4).
 */
class Team extends Model
{
    protected $primaryKey = 'team_id';

    protected $fillable = [
        'country_name',
        'abbreviation',
        'continent',
        'fifa_ranking',
    ];

    // ── Relationships ────────────────────────────────────────────────────

    /** Tournaments this team has participated in (M:N through team_tournament). */
    public function tournaments()
    {
        return $this->belongsToMany(Tournament::class, 'team_tournament', 'team_id', 'tournament_id')
                    ->withPivot('group_id', 'coach_id', 'seed_position', 'elimination_stage')
                    ->withTimestamps();
    }

    /** Team-tournament pivot records for this team. */
    public function teamTournaments()
    {
        return $this->hasMany(TeamTournament::class, 'team_id', 'team_id');
    }

    /** Matches where this team is the home team. */
    public function homeMatches()
    {
        return $this->hasMany(GameMatch::class, 'home_team_id', 'team_id');
    }

    /** Matches where this team is the away team. */
    public function awayMatches()
    {
        return $this->hasMany(GameMatch::class, 'away_team_id', 'team_id');
    }

    /** Goals credited to this team. */
    public function goals()
    {
        return $this->hasMany(Goal::class, 'team_id', 'team_id');
    }

    /** Cards given to players of this team. */
    public function cards()
    {
        return $this->hasMany(Card::class, 'team_id', 'team_id');
    }
}
