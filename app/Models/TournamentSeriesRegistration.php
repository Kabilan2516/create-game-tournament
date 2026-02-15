<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TournamentSeriesRegistration extends Model
{
    protected $fillable = [
        'tournament_series_id',
        'organizer_id',
        'user_id',
        'join_code',
        'team_name',
        'captain_ign',
        'captain_game_id',
        'email',
        'phone',
        'mode',
        'substitute_count',
        'roster',
        'is_paid',
        'entry_fee',
        'payment_status',
        'status',
        'notes',
        'registered_at',
    ];

    protected $casts = [
        'roster' => 'array',
        'is_paid' => 'boolean',
        'entry_fee' => 'float',
        'registered_at' => 'datetime',
    ];

    public function series(): BelongsTo
    {
        return $this->belongsTo(TournamentSeries::class, 'tournament_series_id');
    }
}
