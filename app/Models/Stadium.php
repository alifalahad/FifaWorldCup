<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Stadium model — maps to the STADIUMS table (section 4.7).
 */
class Stadium extends Model
{
    protected $primaryKey = 'stadium_id';

    protected $fillable = [
        'name',
        'city',
        'country',
        'capacity',
        'surface_type',
    ];

    // ── Relationships ────────────────────────────────────────────────────

    /** All matches played at this stadium. */
    public function matches()
    {
        return $this->hasMany(GameMatch::class, 'stadium_id', 'stadium_id');
    }
}
