<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentJoin extends Model
{
    protected $fillable = [
        'tournament_id',
        'organizer_id',
        'user_id',
        'join_code',
        'team_name',
        'captain_ign',
        'captain_game_id',
        'email',
        'phone',
        'mode',
        'is_paid',
        'entry_fee',
        'payment_status',
        'status',
        'notes',
        'reject_reason',
        'room_visible',
    ];

    // Relations
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function members()
    {
        return $this->hasMany(TournamentJoinMember::class);
    }

    public function paymentProof()
    {
        return $this->morphOne(Media::class, 'model')
            ->where('collection', 'payment_proof');
    }
    public function messages()
    {
        return $this->hasMany(TournamentJoinMessage::class);
    }
    public function matchResultEntries()
    {
        return $this->hasMany(MatchResultEntry::class);
    }
}
