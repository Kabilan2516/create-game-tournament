<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tournament;
use App\Models\MatchResult;
use Illuminate\Http\Request;
use App\Models\MatchResultEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\TournamentJoinMessage;
use App\Models\TeamResult;

class OrganizerResultController extends Controller
{
    public function create(Tournament $tournament)
    {
        // 🔐 Security
        abort_if($tournament->organizer_id !== Auth::id(), 403);

        // ⏰ Match must be started
        abort_if(now()->lt($tournament->start_time), 403);

        // ✅ LOAD APPROVED JOINS + MEMBERS
        $joins = $tournament->joins()
            ->where('status', 'approved')
            ->with('members')
            ->orderBy('id')
            ->get();

        // ✅ LOAD MATCH RESULT WITH REQUIRED RELATIONS
        $matchResult = MatchResult::with([
            'entries',
            'entries.join',
            'entries.join.members',
        ])
            ->where('tournament_id', $tournament->id)
            ->first();

        // 🔹 MAP DB RESULTS → JS KEYS
        $dbResults = [];

        if ($matchResult) {
            foreach ($matchResult->entries as $e) {

                // SAFETY (in case join deleted)
                if (!$e->join) {
                    continue;
                }

                // 🧑‍✈️ CAPTAIN
                if ($e->player_ign === $e->join->captain_ign) {
                    $key = "join_{$e->tournament_join_id}_captain";
                } else {
                    // 👥 MEMBER INDEX MATCH (ORDER IS IMPORTANT)
                    $index = $e->join->members
                        ->pluck('ign')
                        ->search($e->player_ign);

                    if ($index === false) {
                        continue; // safety
                    }

                    $key = "join_{$e->tournament_join_id}_member_{$index}";
                }

                $dbResults[$key] = [
                    'rank'   => $e->rank,
                    'kills'  => $e->kills,
                    'points' => $e->points,
                    'winner' => $e->winner_position,
                ];
            }
        }

        // ✅ RETURN VIEW (YOU WERE MISSING THIS)
        return view('organizer.results.upload', [
            'tournament'  => $tournament,
            'joins'       => $joins,
            'matchResult' => $matchResult,
            'dbResults'   => $dbResults,
        ]);
    }

    public function store(Request $request, Tournament $tournament)
    {
        abort_if($tournament->organizer_id !== Auth::id(), 403);

        $request->validate([
            'results' => 'required|string',
            'publish' => 'required|boolean',
        ]);

        $results = json_decode($request->results, true);
        if (!is_array($results) || empty($results)) {
            return back()->withErrors(['error' => 'Invalid or empty results data']);
        }

        $pointsConfig = config('points.codm');
        $placementPoints = $pointsConfig['placement_points'] ?? [];
        $killPoint = $pointsConfig['kill_point'] ?? 1;

        $getPlacementPoints = function ($rank) use ($placementPoints) {
            if (!$rank) {
                return 0;
            }
            return $placementPoints[$rank] ?? 0;
        };

        DB::beginTransaction();

        try {

            // 🔹 TOTAL EXPECTED PLAYERS
            $totalApprovedPlayers = $tournament->joins()
                ->where('status', 'approved')
                ->count();

            $submittedCount = count($results);

            // 🔹 CREATE / FETCH MASTER RESULT
            $matchResult = MatchResult::firstOrCreate(
                ['tournament_id' => $tournament->id],
                ['organizer_id' => Auth::id()]
            );

            $wasPublishedBefore = !is_null($matchResult->published_at);

            // 🔁 RESET & INSERT (SAFE ALWAYS)
            MatchResultEntry::where('match_result_id', $matchResult->id)->delete();
            TeamResult::where('match_result_id', $matchResult->id)->delete();

            $teamBuckets = [];
            foreach ($results as $row) {
                $rank = $row['rank'] ?? null;
                $pp = $getPlacementPoints($rank);
                $kp = ($row['kills'] ?? 0) * $killPoint;
                $tt = $pp + $kp;
                $cd = ((int) $rank === 1) ? 1 : 0;

                MatchResultEntry::create([
                    'match_result_id' => $matchResult->id,
                    'tournament_join_id' => $row['tournament_join_id'],
                    'player_ign' => $row['player_ign'],
                    'player_game_id' => $row['player_game_id'] ?? null,
                    'team_name' => $row['team_name'] ?? null,
                    'rank' => $row['rank'],
                    'kills' => $row['kills'] ?? 0,
                    'points' => $row['points'] ?? 0,
                    'winner_position' => $row['winner_position'],
                    'kp' => $kp,
                    'pp' => $pp,
                    'tt' => $tt,
                    'cd' => $cd,
                ]);

                $joinId = $row['tournament_join_id'];
                $identityName = $row['team_name'] ?? $row['player_ign'] ?? null;
                if (!isset($teamBuckets[$joinId])) {
                    $teamBuckets[$joinId] = [
                        'team_name' => $identityName,
                        'rank' => $rank,
                        'kp' => 0,
                        'pp' => 0,
                        'tt' => 0,
                        'cd' => 0,
                    ];
                }

                $teamBuckets[$joinId]['team_name'] = $identityName ?? $teamBuckets[$joinId]['team_name'];
                $teamBuckets[$joinId]['rank'] = $teamBuckets[$joinId]['rank'] ?? $rank;
                $teamBuckets[$joinId]['kp'] += $kp;
                $teamBuckets[$joinId]['pp'] = max($teamBuckets[$joinId]['pp'], $pp);
                $teamBuckets[$joinId]['tt'] += $kp;
                $teamBuckets[$joinId]['cd'] = max($teamBuckets[$joinId]['cd'], $cd);
            }

            foreach ($teamBuckets as $joinId => $team) {
                $team['tt'] = ($team['pp'] ?? 0) + ($team['kp'] ?? 0);

                TeamResult::create([
                    'match_result_id' => $matchResult->id,
                    'tournament_join_id' => $joinId,
                    'team_name' => $team['team_name'],
                    'rank' => $team['rank'],
                    'mp' => 1,
                    'kp' => $team['kp'],
                    'pp' => $team['pp'],
                    'tt' => $team['tt'],
                    'cd' => $team['cd'],
                ]);
            }

            /* =========================
           PUBLISH / UPDATE LOGIC
        ========================= */

            if ($request->boolean('publish')) {

                // FIRST TIME PUBLISH
                if (!$wasPublishedBefore) {
                    $matchResult->update([
                        'published_at' => now(),
                    ]);

                    if ($submittedCount < $totalApprovedPlayers) {
                        $this->notifyPartialResults($tournament, $submittedCount, $totalApprovedPlayers);
                    } else {
                        $this->notifyFinalResults($tournament);
                    }
                } else {
                    // UPDATE AFTER PUBLISH
                    $this->notifyResultsUpdated($tournament, $submittedCount, $totalApprovedPlayers);
                }

                session()->flash('clear_draft', true);
            }

            DB::commit();

            return $request->boolean('publish')
                ? redirect()
                ->route('tournaments.results.show', $tournament)
                ->with('success', '🏆 Results published / updated successfully!')
                : back()->with('success', '💾 Draft saved successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => 'Failed to save results.',
                'debug' => $e->getMessage(),
            ]);
        }
    }


    private function notifyPartialResults(Tournament $tournament, int $done, int $total)
    {
        foreach ($tournament->joins()->where('status', 'approved')->get() as $join) {
            TournamentJoinMessage::create([
                'tournament_join_id' => $join->id,
                'sender' => 'system',
                'message' =>
                "⏳ Match results are being updated.\n\n" .
                    "Tournament: {$tournament->title}\n" .
                    "Progress: {$done} / {$total} players processed.\n\n" .
                    "Organizer is still working on remaining results.",
                'is_read' => false,
            ]);
        }
    }

    private function notifyFinalResults(Tournament $tournament)
    {
        foreach ($tournament->joins()->where('status', 'approved')->get() as $join) {
            TournamentJoinMessage::create([
                'tournament_join_id' => $join->id,
                'sender' => 'system',
                'message' =>
                "🏆 Final match results have been published!\n\n" .
                    "Tournament: {$tournament->title}\n" .
                    "Check leaderboard for final standings.",
                'is_read' => false,
            ]);
        }
    }

    private function notifyResultsUpdated(Tournament $tournament, int $done, int $total)
    {
        foreach ($tournament->joins()->where('status', 'approved')->get() as $join) {
            TournamentJoinMessage::create([
                'tournament_join_id' => $join->id,
                'sender' => 'system',
                'message' =>
                "✏️ Match results have been UPDATED.\n\n" .
                    "Tournament: {$tournament->title}\n" .
                    "Current coverage: {$done} / {$total} players.\n\n" .
                    "Please re-check your position.",
                'is_read' => false,
            ]);
        }
    }
}
