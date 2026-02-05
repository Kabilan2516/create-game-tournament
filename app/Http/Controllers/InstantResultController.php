<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\MatchResult;
use Illuminate\Http\Request;
use App\Models\TournamentJoin;
use App\Models\MatchResultEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InstantResultController extends Controller
{
    public function index()
    {
        return view('tournaments.instent-result-index');
    }

        public function create()
    {
        return view('tournaments.create-instant-result');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'mode'    => 'required|in:solo,duo,squad',
            'results' => 'required|string',
        ]);

        $results = json_decode($request->results, true);

        if (!is_array($results) || empty($results)) {
            return back()->withErrors(['error' => 'Invalid results data']);
        }

        DB::transaction(function () use ($request, $results) {

            /* ===============================
               1️⃣ CREATE TOURNAMENT
            =============================== */
            $tournament = Tournament::create([
                'title' => $request->title,
                'mode' => $request->mode,
                'organizer_id' => Auth::id(),
                'status' => 'completed',
                'start_time' => now(),
                'registration_close_time' => now(),
                'entry_fee' => 0,
                'is_instant' => true,
            ]);

            /* ===============================
               2️⃣ CREATE MATCH RESULT MASTER
            =============================== */
            $matchResult = MatchResult::create([
                'tournament_id' => $tournament->id,
                'organizer_id' => Auth::id(),
                'published_at' => now(),
                'is_locked' => true,
            ]);

            /* ===============================
               3️⃣ CREATE JOINS + RESULT ENTRIES
            =============================== */
            foreach ($results as $row) {

                $join = TournamentJoin::create([
                    'tournament_id' => $tournament->id,
                    'status' => 'approved',
                    'team_name' => $row['team_name'] ?? null,
                    'captain_ign' => $row['player_ign'],
                ]);

                MatchResultEntry::create([
                    'match_result_id' => $matchResult->id,
                    'tournament_join_id' => $join->id,
                    'player_ign' => $row['player_ign'],
                    'team_name' => $row['team_name'] ?? null,
                    'rank' => $row['rank'] ?? null,
                    'kills' => $row['kills'] ?? 0,
                    'points' => $row['points'] ?? 0,
                    'winner_position' => $row['winner_position'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('dashboard')
            ->with('success', '🏆 Instant results published successfully!');
    }
}
