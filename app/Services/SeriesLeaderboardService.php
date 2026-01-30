<?php

namespace App\Services;

use App\Models\TournamentSeries;
use App\Models\MatchResultEntry;
use Illuminate\Support\Collection;

class SeriesLeaderboardService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }



    public static function build(TournamentSeries $series): Collection
    {
        // 1️⃣ Get all tournament IDs in this series
        $tournamentIds = $series->tournaments->pluck('id');

        // 2️⃣ Fetch all result entries for these tournaments
        $entries = MatchResultEntry::whereHas('matchResult', function ($q) use ($tournamentIds) {
            $q->whereIn('tournament_id', $tournamentIds)
                ->where('is_locked', true); // only locked/published results
        })->get();

        // 3️⃣ Group entries by TEAM / PLAYER
        $grouped = $entries->groupBy(function ($entry) {
            return $entry->tournament_join_id;
        });

        // 4️⃣ Build leaderboard rows
        $leaderboard = $grouped->map(function ($rows) {

            return [
                'join_id' => $rows->first()->tournament_join_id,
                'name' => $rows->first()->team_name
                    ?? $rows->first()->player_ign,

                'matches' => $rows->count(),
                'kills'   => $rows->sum('kills'),
                'points'  => $rows->sum('points'),
            ];
        });

        // 5️⃣ Sort leaderboard
        return $leaderboard
            ->sortByDesc('points')
            ->sortByDesc('kills')
            ->values();
    }
}
