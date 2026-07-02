<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tournament model — maps to the TOURNAMENTS table (section 4.3).
 */
class Tournament extends Model
{
    protected $primaryKey = 'tournament_id';

    protected $fillable = [
        'name',
        'year',
        'host_country',
        'start_date',
        'end_date',
        'total_teams',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────

    /** All groups in this tournament. */
    public function groups()
    {
        return $this->hasMany(TournamentGroup::class, 'tournament_id', 'tournament_id');
    }

    /** All matches in this tournament. */
    public function matches()
    {
        return $this->hasMany(GameMatch::class, 'tournament_id', 'tournament_id');
    }

    /** Team-tournament pivot records for this tournament. */
    public function teamTournaments()
    {
        return $this->hasMany(TeamTournament::class, 'tournament_id', 'tournament_id');
    }

    /** Teams participating in this tournament (M:N through team_tournament). */
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_tournament', 'tournament_id', 'team_id')
                    ->withPivot('group_id', 'coach_id', 'seed_position', 'elimination_stage')
                    ->withTimestamps();
    }
}
