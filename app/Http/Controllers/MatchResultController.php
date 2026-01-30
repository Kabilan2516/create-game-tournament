<?php

namespace App\Http\Controllers;

use App\Models\MatchResult;
use App\Models\MatchResultEntry;
use App\Models\Tournament;
use App\Models\TournamentJoin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MatchResultController extends Controller
{
    /**
     * Save / Update match results (draft or publish)
     */
    public function store(Request $request, Tournament $tournament)
    {
        // 🔐 Security
        abort_if($tournament->organizer_id !== Auth::id(), 403);

        // 🔒 Match must be started
        abort_if(now()->lt($tournament->start_time), 403);

        // ✅ Validate input
        $request->validate([
            'results' => 'required|string',
            'publish' => 'nullable|boolean',
        ]);

        $entries = json_decode($request->results, true);

        if (!is_array($entries) || empty($entries)) {
            return back()->withErrors(['error' => 'Invalid result data.']);
        }

        DB::beginTransaction();

        try {

            /* =========================
               MATCH RESULT (MASTER)
            ========================= */

            $matchResult = MatchResult::firstOrCreate(
                [
                    'tournament_id' => $tournament->id,
                ],
                [
                    'organizer_id' => Auth::id(),
                ]
            );

            // 🔁 If already published → block edits
            if ($matchResult->is_locked) {
                return back()->withErrors([
                    'error' => 'Results are already published and locked.'
                ]);
            }

            /* =========================
               SAVE ENTRIES
            ========================= */

            foreach ($entries as $row) {

                // Skip empty rows
                if (
                    empty($row['player_ign']) &&
                    empty($row['kills']) &&
                    empty($row['rank'])
                ) {
                    continue;
                }

                MatchResultEntry::updateOrCreate(
                    [
                        'match_result_id'      => $matchResult->id,
                        'tournament_join_id'   => $row['tournament_join_id'],
                        'player_ign'           => $row['player_ign'],
                    ],
                    [
                        'rank'            => $row['rank'],
                        'kills'           => $row['kills'] ?? 0,
                        'points'          => $row['points'] ?? 0,
                        'winner_position' => $row['winner_position'] ?? null,
                    ]
                );
            }

            /* =========================
               PUBLISH RESULTS
            ========================= */

            if ($request->boolean('publish')) {
                $matchResult->update([
                    'is_locked'    => true,
                    'published_at' => now(),
                ]);

                // OPTIONAL: mark tournament completed
                $tournament->update([
                    'status' => 'completed',
                ]);
            }

            DB::commit();

            return redirect()
                ->route('tournaments.my')
                ->with('success', $request->boolean('publish')
                    ? '🏆 Results published successfully!'
                    : '💾 Results saved as draft.');
        } catch (\Throwable $e) {

            DB::rollBack();

            return back()->withErrors([
                'error' => 'Failed to save match results.',
                'debug' => $e->getMessage(), // remove in production
            ]);
        }
    }
}
