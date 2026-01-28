<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentJoinMessage extends Model
{
    protected $fillable = [
        'tournament_join_id',
        'sender',
        'message',
        'is_read',
    ];

    public function join()
    {
        return $this->belongsTo(TournamentJoin::class, 'tournament_join_id');
    }
}
