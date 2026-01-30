<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentSeriesTournament extends Model
{
    protected $fillable = [
        'tournament_series_id',
        'tournament_id',
    ];
}
