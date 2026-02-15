<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamResult extends Model
{
    protected $fillable = [
        'match_result_id',
        'tournament_join_id',
        'team_name',
        'rank',
        'mp',
        'kp',
        'pp',
        'tt',
        'cd',
    ];

    public function matchResult()
    {
        return $this->belongsTo(MatchResult::class);
    }

    public function join()
    {
        return $this->belongsTo(TournamentJoin::class, 'tournament_join_id');
    }
}
