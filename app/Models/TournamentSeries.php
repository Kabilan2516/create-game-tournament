<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentSeries extends Model
{
    protected $fillable = [
        'organizer_id',
        'title',
        'description',
        'mode',
        'start_date',
        'end_date',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /* =========================
       RELATIONSHIPS
    ========================= */

    public function tournaments()
    {
        return $this->belongsToMany(
            Tournament::class,
            'tournament_series_tournaments'
        );
    }

    public function pointsRules()
    {
        return $this->hasMany(TournamentSeriesPoint::class);
    }

    public function standings()
    {
        return $this->hasMany(TournamentSeriesStanding::class);
    }
}
