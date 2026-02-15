<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentSeriesPrize extends Model
{
    protected $fillable = [
        'tournament_series_id',
        'position',
        'amount',
    ];

    protected $casts = [
        'position' => 'integer',
        'amount' => 'float',
    ];

    public function series()
    {
        return $this->belongsTo(TournamentSeries::class, 'tournament_series_id');
    }
}
