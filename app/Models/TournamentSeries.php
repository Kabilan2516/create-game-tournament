<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\TournamentSeriesPrize;
use App\Models\Media;
use App\Models\User;

class TournamentSeries extends Model
{
    protected $fillable = [
        'organizer_id',
        'game',
        'title',
        'subtitle',
        'description',
        'rules',
        'mode',
        'substitute_count',
        'registration_slots',
        'match_type',
        'map',
        'region',
        'reward_type',
        'is_paid',
        'entry_fee',
        'prize_pool',
        'upi_id',
        'upi_name',
        'upi_qr',
        'kill_point',
        'placement_points',
        'start_date',
        'end_date',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'placement_points' => 'array',
        'substitute_count' => 'integer',
        'registration_slots' => 'integer',
        'entry_fee' => 'float',
        'prize_pool' => 'float',
        'is_paid' => 'boolean',
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

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function pointsRules()
    {
        return $this->hasMany(TournamentSeriesPoint::class);
    }

    public function registrations()
    {
        return $this->hasMany(TournamentSeriesRegistration::class, 'tournament_series_id');
    }

    public function standings()
    {
        return $this->hasMany(TournamentSeriesStanding::class);
    }

    public function prizes()
    {
        return $this->hasMany(TournamentSeriesPrize::class)->orderBy('position');
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function banner()
    {
        return $this->morphOne(Media::class, 'model')
            ->where('collection', 'banner');
    }

    public function getPrizeTotalAttribute(): float
    {
        if ($this->relationLoaded('prizes') && $this->prizes->isNotEmpty()) {
            return (float) $this->prizes->sum('amount');
        }

        if ($this->prizes()->exists()) {
            return (float) $this->prizes()->sum('amount');
        }

        return (float) ($this->prize_pool ?? 0);
    }
}
