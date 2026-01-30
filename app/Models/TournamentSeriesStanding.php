<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentSeriesStanding extends Model
{
    protected $fillable = [
        'tournament_series_id',
        'team_name',
        'ign',
        'matches_played',
        'wins',
        'total_points',
    ];

    public function series()
    {
        return $this->belongsTo(TournamentSeries::class);
    }
}
