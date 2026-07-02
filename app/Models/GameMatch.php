<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * GameMatch model — maps to the MATCHES table (section 4.12).
 *
 * Named "GameMatch" instead of "Match" because match() is a reserved
 * PHP function (pattern matching) and can cause issues in some contexts.
 * The table name is explicitly set to 'matches'.
 */
class GameMatch extends Model
{
    protected $table = 'matches';
    protected $primaryKey = 'match_id';

    protected $fillable = [
        'tournament_id',
        'stadium_id',
        'referee_id',
        'home_team_id',
        'away_team_id',
        'group_id',
        'match_date',
        'stage',
        'home_score',
        'away_score',
        'has_extra_time',
        'has_penalties',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'match_date' => 'date',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────

    /** The tournament this match belongs to. */
    public function tournament()
    {
        return $this->belongsTo(Tournament::class, 'tournament_id', 'tournament_id');
    }

    /** The stadium where this match is played. */
    public function stadium()
    {
        return $this->belongsTo(Stadium::class, 'stadium_id', 'stadium_id');
    }

    /** The referee officiating this match (nullable). */
    public function referee()
    {
        return $this->belongsTo(Referee::class, 'referee_id', 'referee_id');
    }

    /** The home team. */
    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'home_team_id', 'team_id');
    }

    /** The away team. */
    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'away_team_id', 'team_id');
    }

    /** The group this match belongs to (nullable — NULL for knockout). */
    public function group()
    {
        return $this->belongsTo(TournamentGroup::class, 'group_id', 'group_id');
    }

    /** Goals scored in this match. */
    public function goals()
    {
        return $this->hasMany(Goal::class, 'match_id', 'match_id');
    }

    /** Cards given in this match. */
    public function cards()
    {
        return $this->hasMany(Card::class, 'match_id', 'match_id');
    }
}
