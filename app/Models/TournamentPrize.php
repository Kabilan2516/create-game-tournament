<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentPrize extends Model
{
    protected $fillable = [
        'tournament_id',
        'position',
        'amount',
    ];

    protected $casts = [
        'position' => 'integer',
        'amount' => 'float',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }
}
