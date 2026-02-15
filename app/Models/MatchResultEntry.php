<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchResultEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_result_id',
        'tournament_join_id',
        'player_ign',
        'player_game_id',
        'team_name',
        'rank',
        'kills',
        'points',
        'winner_position',
        'kp',
        'pp',
        'tt',
        'cd',
    ];

    protected $casts = [
        'rank'   => 'integer',
        'kills'  => 'integer',
        'points' => 'integer',
        'kp'     => 'integer',
        'pp'     => 'integer',
        'tt'     => 'integer',
        'cd'     => 'integer',
    ];

    /* =========================
       RELATIONSHIPS
    ========================= */

    // Parent match result
    public function matchResult()
    {
        return $this->belongsTo(MatchResult::class);
    }

    // Tournament join (team / solo entry)
    public function join()
    {
        return $this->belongsTo(TournamentJoin::class, 'tournament_join_id');
    }
}
