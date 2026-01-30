<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentSeriesPoint extends Model
{
    protected $fillable = [
        'tournament_series_id',
        'position',
        'points',
    ];

    public function series()
    {
        return $this->belongsTo(TournamentSeries::class);
    }
}
