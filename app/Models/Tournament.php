<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;
use App\Models\TournamentPrize;
use App\Models\TournamentSeries;

class Tournament extends Model
{
    protected $fillable = [
        'organizer_id',
        'title',
        'game',
        'mode',
        'map',
        'match_type',
        'team_size',
        'reward_type',
        'entry_fee',
        'prize_pool',
        'slots',
        'substitute_count',
        'filled_slots',
        'start_time',
        'registration_close_time',
        'first_prize',
        'second_prize',
        'third_prize',
        'description',
        'rules',
        'room_id',
        'room_password',
        'is_featured',
        'status',
        'is_paid',
        'upi_id',
        'upi_name',
        'upi_qr',
        'auto_approve',
    ];
    protected $casts = [
        'first_prize'   => 'float',
        'second_prize'   => 'float',
        'third_prize'   => 'float',
        'entry_fee'     => 'float',
        'prize_pool'    => 'float',
        'substitute_count' => 'integer',
        'start_time'    => 'datetime',
        'registration_close_time' => 'datetime',
        'is_featured'   => 'boolean',
    ];

    // Organizer (User)
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    // Participants (Players)
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'participants')
            ->withPivot('status')
            ->withTimestamps();
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
    public function joins()
    {
        return $this->hasMany(TournamentJoin::class);
    }

    public function prizes()
    {
        return $this->hasMany(TournamentPrize::class)->orderBy('position');
    }

    public function getPrizeTotalAttribute(): float
    {
        if ($this->relationLoaded('prizes') && $this->prizes->isNotEmpty()) {
            return (float) $this->prizes->sum('amount');
        }

        if ($this->prizes()->exists()) {
            return (float) $this->prizes()->sum('amount');
        }

        return (float) (($this->first_prize ?? 0) + ($this->second_prize ?? 0) + ($this->third_prize ?? 0));
    }

    public function getStartsSoonAttribute()
    {
        return Carbon::now()->diffInMinutes($this->start_time, false) <= 30
            && Carbon::now()->lessThan($this->start_time);
    }

    public function getIsOngoingAttribute()
    {
        return Carbon::now()->between($this->start_time, $this->start_time->addHours(3));
    }

    public function getIsCompletedAttribute()
    {
        return Carbon::now()->greaterThan($this->start_time->addHours(3));
    }

    public function getAlmostFullAttribute()
    {
        if ($this->slots == 0) return false;
        return ($this->filled_slots / $this->slots) >= 0.8; // 80% full
    }

    public function getJoinClosedAttribute()
    {
        return Carbon::now()->greaterThanOrEqualTo($this->start_time);
    }

    public function getHasStartedAttribute(): bool
    {
        return Carbon::now()->greaterThanOrEqualTo($this->start_time);
    }

    public function getHasEndedAttribute(): bool
    {
        // optional buffer if needed
        return Carbon::now()->greaterThan($this->start_time);
    }
    public function matchResult()
    {
        return $this->hasOne(MatchResult::class);
    }
    public function matchResults()
    {
        return $this->hasMany(MatchResult::class);
    }

    public function series()
    {
        return $this->belongsToMany(
            TournamentSeries::class,
            'tournament_series_tournaments'
        );
    }
}
