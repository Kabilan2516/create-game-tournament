<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\MatchResult;
use Illuminate\Http\Request;
use App\Models\TournamentSeries;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\TournamentMatchResult;
use App\Models\TournamentSeriesPoint;
use App\Models\TournamentSeriesStanding;
use App\Services\SeriesLeaderboardService;

class TournamentSeriesController extends Controller
{
    /* =========================
       LIST SERIES
    ========================= */
    public function index()
    {
        $series = TournamentSeries::where('organizer_id', Auth::id())
            ->latest()
            ->get();

        return view('series.index', compact('series'));
    }

    /* =========================
       CREATE SERIES FORM
    ========================= */
    public function create()
    {
        $tournaments = Tournament::where('organizer_id', Auth::id())
            ->where('start_time', '<=', now()) // 🔥 match started = eligible
            ->latest('start_time')
            ->get();

        return view('series.create', compact('tournaments'));
    }



    /* =========================
       STORE SERIES
    ========================= */
    public function store(Request $request)
    {
        // 🔹 VALIDATE USER INPUT
        $request->validate([
            'tournament_ids' => 'required|array|min:2',
            'tournament_ids.*' => 'exists:tournaments,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // 🔹 FETCH TOURNAMENTS
        $tournaments = Tournament::whereIn('id', $request->tournament_ids)
            ->where('organizer_id', Auth::id())
            ->get();

        if ($tournaments->isEmpty()) {
            return back()->withErrors([
                'error' => 'No valid tournaments selected.'
            ]);
        }

        // 🔹 ENSURE SAME MODE
        $modes = $tournaments->pluck('mode')->unique();

        if ($modes->count() > 1) {
            return back()->withErrors([
                'error' => 'All tournaments in a series must have the same mode.'
            ]);
        }

        // 🔹 AUTO DERIVE VALUES
        $mode = $modes->first();
        $startDate = $tournaments->min('start_time');
        $endDate   = $tournaments->max('start_time');

        // 🔹 CREATE SERIES
        $series = TournamentSeries::create([
            'organizer_id' => Auth::id(),
            'title'        => $request->name,
            'mode'         => $mode,
            'start_date'   => $startDate,
            'end_date'     => $endDate,
            'description' => $request->description,
        ]);

        // 🔹 ATTACH TOURNAMENTS
        $series->tournaments()->sync($request->tournament_ids);

        return redirect()
            ->route('series.show', $series)
            ->with('success', '✅ Series created successfully. Leaderboard will combine all results.');
    }


    /* =========================
       SERIES DASHBOARD
    ========================= */
    public function show(TournamentSeries $series)
    {
        abort_if($series->organizer_id !== Auth::id(), 403);

        $series->load('tournaments');

        $leaderboard = SeriesLeaderboardService::build($series);

        return view('series.show', [
            'series' => $series,
            'leaderboard' => $leaderboard,
            'totalTeams' => $leaderboard->count(),
            'completedMatches' => $leaderboard->sum('matches'),
        ]);
    }

    /* =========================
       ATTACH TOURNAMENTS
    ========================= */
    public function attachTournaments(Request $request, TournamentSeries $series)
    {
        abort_if($series->organizer_id !== Auth::id(), 403);

        $request->validate([
            'tournament_ids' => 'required|array|min:1',
        ]);

        $series->tournaments()->syncWithoutDetaching(
            $request->tournament_ids
        );

        return back()->with('success', '🏆 Tournaments added to series');
    }

    /* =========================
       SAVE POINTS RULE
    ========================= */
    public function savePoints(Request $request, TournamentSeries $series)
    {
        abort_if($series->organizer_id !== Auth::id(), 403);

        $request->validate([
            'points' => 'required|array|min:1',
            'points.*.position' => 'required|integer|min:1',
            'points.*.points' => 'required|integer|min:0',
        ]);

        TournamentSeriesPoint::where('tournament_series_id', $series->id)->delete();

        foreach ($request->points as $rule) {
            TournamentSeriesPoint::create([
                'tournament_series_id' => $series->id,
                'position' => $rule['position'],
                'points' => $rule['points'],
            ]);
        }

        return back()->with('success', '🎯 Points system saved');
    }

    /* =========================
       CALCULATE STANDINGS
    ========================= */
    public function calculateStandings(TournamentSeries $series)
    {
        abort_if($series->organizer_id !== Auth::id(), 403);

        DB::transaction(function () use ($series) {

            // 🔹 Reset old standings
            TournamentSeriesStanding::where('tournament_series_id', $series->id)->delete();

            // 🔹 Points rules map (rank => points)
            $pointsMap = $series->pointsRules
                ->pluck('points', 'position');

            foreach ($series->tournaments as $tournament) {

                // 🔹 Get published & unlocked result
                $matchResult = MatchResult::where('tournament_id', $tournament->id)
                    ->where('is_locked', true)
                    ->first();

                if (!$matchResult) {
                    continue; // result not ready
                }

                // 🔹 Loop through result entries
                foreach ($matchResult->entries as $entry) {

                    if (!$entry->rank) {
                        continue;
                    }

                    // 🔹 Points priority:
                    // 1. Entry points (manual override)
                    // 2. Series rank points
                    $points = $entry->points
                        ?? $pointsMap[$entry->rank]
                        ?? 0;

                    // 🔹 Unique identity (solo = IGN, team = team_name)
                    $identity = $entry->team_name ?: $entry->player_ign;

                    $standing = TournamentSeriesStanding::firstOrCreate(
                        [
                            'tournament_series_id' => $series->id,
                            'identity' => $identity,
                        ],
                        [
                            'team_name' => $entry->team_name,
                            'ign' => $entry->player_ign,
                        ]
                    );

                    $standing->increment('matches_played');
                    $standing->increment('total_points', $points);

                    if ($entry->rank === 1) {
                        $standing->increment('wins');
                    }
                }
            }
        });

        return back()->with('success', '📊 Series standings calculated successfully');
    }
 public function bracket(TournamentSeries $series)
{
    $series->load([
        'tournaments.matchResults.entries'
    ]);

    /*
     Structure:
     [
       tournament_id => [
          player_key => [
              'ign' => '',
              'points' => total,
              'wins' => count
          ]
       ]
     ]
    */

    $tree = [];
    $globalScores = [];

    foreach ($series->tournaments as $tournament) {
        foreach ($tournament->matchResults as $result) {
            foreach ($result->entries as $entry) {

                $key = $entry->player_game_id ?: $entry->player_ign;

                $tree[$tournament->id][$key] = [
                    'ign' => $entry->player_ign,
                    'points' => $entry->points,
                    'rank' => $entry->rank,
                ];

                // GLOBAL SERIES SCORE
                if (!isset($globalScores[$key])) {
                    $globalScores[$key] = [
                        'ign' => $entry->player_ign,
                        'total_points' => 0,
                        'appearances' => 0,
                    ];
                }

                $globalScores[$key]['total_points'] += $entry->points;
                $globalScores[$key]['appearances']++;
            }
        }
    }

    // Sort final winner
    uasort($globalScores, fn($a, $b) =>
        $b['total_points'] <=> $a['total_points']
    );

    $champion = array_key_first($globalScores);

    return view('series.bracket', compact(
        'series',
        'tree',
        'globalScores',
        'champion'
    ));
}

}
