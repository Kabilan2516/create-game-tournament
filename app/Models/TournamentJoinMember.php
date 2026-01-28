<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentJoinMember extends Model
{
    protected $fillable = [
        'tournament_join_id',
        'ign',
        'game_id',
    ];

    public function join()
    {
        return $this->belongsTo(TournamentJoin::class, 'tournament_join_id');
    }
}

