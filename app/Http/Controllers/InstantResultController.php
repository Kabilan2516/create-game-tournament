<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\MatchResult;
use Illuminate\Http\Request;
use App\Models\MatchResultEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\TeamResult;
use App\Models\CodmTeam;
use App\Models\TournamentSeries;
use App\Models\TournamentPrize;
use Carbon\Carbon;

class InstantResultController extends Controller
{
    public function index()
    {
        $series = TournamentSeries::where('organizer_id', Auth::id())
            ->latest()
            ->get();

        return view('tournaments.instent-result-index', [
            'series' => $series,
        ]);
    }

    public function create()
    {
        return view('tournaments.create-instant-result');
    }

    public function codm(Request $request)
    {
        $series = null;
        if ($request->filled('series_id')) {
            $series = TournamentSeries::where('organizer_id', Auth::id())
                ->where('id', $request->series_id)
                ->first();
        }

        $candidateMatches = Tournament::where('organizer_id', Auth::id())
            ->where('is_instant', true)
            ->whereHas('matchResult', function ($q) {
                $q->where('is_locked', true);
            })
            ->latest('start_time')
            ->get();

        if ($candidateMatches->isEmpty()) {
            $candidateMatches = Tournament::where('organizer_id', Auth::id())
                ->where('is_instant', true)
                ->latest('start_time')
                ->get();
        }

        if ($candidateMatches->isEmpty()) {
            $candidateMatches = Tournament::where('organizer_id', Auth::id())
                ->latest('start_time')
                ->get();
        }

        // Only allow selecting the latest match from each linked series.
        // Standalone instant matches are also allowed.
        $seriesLatest = collect();
        $seriesIdsCovered = collect();

        $organizerSeries = TournamentSeries::where('organizer_id', Auth::id())
            ->with(['tournaments' => function ($q) {
                $q->orderByDesc('start_time')
                    ->orderByDesc('id');
            }])
            ->get();

        foreach ($organizerSeries as $s) {
            if ($s->tournaments->isEmpty()) {
                continue;
            }
            $latestInSeries = $s->tournaments->first();
            $seriesLatest->push($latestInSeries);
            $seriesIdsCovered = $seriesIdsCovered->merge($s->tournaments->pluck('id'));
        }

        $standalone = $candidateMatches
            ->whereNotIn('id', $seriesIdsCovered->unique()->values());

        $previous = $seriesLatest
            ->concat($standalone)
            ->unique('id')
            ->sortByDesc(function ($tournament) {
                $time = optional($tournament->start_time)->timestamp ?? 0;
                return ($time * 1000000) + (int) $tournament->id;
            })
            ->values();

        return view('tournaments.create-instant-result', [
            'game' => 'CODM',
            'previousMatches' => $previous,
            'series' => $series,
        ]);
    }

    public function dummy(Request $request)
    {
        $request->validate([
            'mode' => 'required|in:solo,duo,squad',
            'rank_points' => 'nullable|string',
            'kill_point' => 'nullable|numeric|min:0',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $teams = CodmTeam::orderBy('id')->get();
        if ($teams->isEmpty()) {
            $seed = config('dummy_teams.codm', []);
            foreach ($seed as $team) {
                CodmTeam::create([
                    'name' => $team['name'],
                    'players' => $team['players'] ?? [],
                ]);
            }
            $teams = CodmTeam::orderBy('id')->get();
        }

        $rankPoints = config('points.codm.placement_points', []);
        if ($request->filled('rank_points')) {
            $decoded = json_decode($request->rank_points, true);
            if (is_array($decoded)) {
                $rankPoints = $decoded;
            }
        }
        $killPoint = $request->filled('kill_point')
            ? (int) $request->kill_point
            : (int) config('points.codm.kill_point', 1);

        $defaultLimit = match ($request->mode) {
            'solo' => 100,
            'duo' => 50,
            'squad' => 25,
            default => 100,
        };
        $limit = (int) ($request->limit ?? $defaultLimit);
        $results = [];

        $getPlacementPoints = function ($rank) use ($rankPoints) {
            if (!$rank) {
                return 0;
            }
            return $rankPoints[$rank] ?? 0;
        };

        if ($request->mode === 'solo') {
            $players = [];
            foreach ($teams as $team) {
                $memberPool = is_array($team->players) ? $team->players : [];
                if (empty($memberPool)) {
                    $memberPool = [$team->name];
                }
                foreach ($memberPool as $member) {
                    $players[] = [
                        'player_ign' => (string) $member,
                        'player_game_id' => 'CODM-' . strtoupper(Str::random(6)),
                    ];
                }
            }

            while (count($players) < $limit) {
                $players[] = [
                    'player_ign' => 'Player ' . str_pad((string) (count($players) + 1), 3, '0', STR_PAD_LEFT),
                    'player_game_id' => 'CODM-' . strtoupper(Str::random(6)),
                ];
            }
            $players = collect($players)->take($limit)->values();

            foreach ($players as $index => $player) {
                $rank = $index + 1;
                $kills = rand(0, 20);
                $pp = $getPlacementPoints($rank);
                $kp = $kills * $killPoint;
                $tt = $pp + $kp;
                $cd = $rank === 1 ? 1 : 0;

                $results[] = [
                    'team_name' => null,
                    'player_ign' => $player['player_ign'],
                    'player_game_id' => $player['player_game_id'],
                    'rank' => $rank,
                    'kills' => $kills,
                    'points' => $tt,
                    'winner_position' => $rank <= 3 ? (string) $rank : null,
                    'mp' => 1,
                    'kp' => $kp,
                    'pp' => $pp,
                    'tt' => $tt,
                    'cd' => $cd,
                ];
            }
        } else {
            $playersPerTeam = $request->mode === 'duo' ? 2 : 4;
            $sourceTeams = $teams->values();

            for ($index = 0; $index < $limit; $index++) {
                $team = $sourceTeams->isNotEmpty()
                    ? $sourceTeams[$index % $sourceTeams->count()]
                    : null;
                $rank = $index + 1;
                $killsPerPlayer = [];
                for ($i = 0; $i < $playersPerTeam; $i++) {
                    $killsPerPlayer[] = rand(0, 8);
                }
                $teamKills = array_sum($killsPerPlayer);
                $pp = $getPlacementPoints($rank);
                $kp = $teamKills * $killPoint;
                $tt = $pp + $kp;
                $cd = $rank === 1 ? 1 : 0;
                $teamName = $team?->name
                    ? ($team->name . ' #' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT))
                    : ('Team ' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT));
                $memberPool = $team && is_array($team->players) ? $team->players : [];

                for ($i = 0; $i < $playersPerTeam; $i++) {
                    $playerName = $memberPool[$i] ?? ($teamName . '_P' . ($i + 1));
                    $results[] = [
                        'team_name' => $teamName,
                        'player_ign' => $playerName,
                        'player_game_id' => 'CODM-' . strtoupper(Str::random(6)),
                        'rank' => $rank,
                        'kills' => $killsPerPlayer[$i],
                        'points' => $tt,
                        'winner_position' => $rank <= 3 ? (string) $rank : null,
                        'mp' => 1,
                        'kp' => $kp,
                        'pp' => $pp,
                        'tt' => $tt,
                        'cd' => $cd,
                    ];
                }
            }
        }

        return response()->json([
            'results' => $results,
        ]);
    }

    public function seriesContext(Request $request)
    {
        $request->validate([
            'tournament_id' => 'required|integer|exists:tournaments,id',
        ]);

        $tournament = Tournament::where('organizer_id', Auth::id())
            ->where('id', $request->tournament_id)
            ->first();

        if (!$tournament) {
            return response()->json([
                'series' => null,
                'matches' => [],
                'message' => 'Selected match not found for this organizer.'
            ], 404);
        }

        $series = $tournament->series()
            ->with(['tournaments' => function ($q) {
                $q->orderBy('start_time');
            }])
            ->latest('id')
            ->first();

        if (!$series) {
            return response()->json([
                'series' => null,
                'matches' => [[
                    'id' => $tournament->id,
                    'title' => $tournament->title,
                    'date' => optional($tournament->start_time)->format('d M Y, h:i A'),
                ]],
                'message' => 'This match is not part of a linked series yet.'
            ]);
        }

        return response()->json([
            'series' => [
                'id' => $series->id,
                'title' => $series->title,
                'mode' => $series->mode,
            ],
            'matches' => $series->tournaments->map(function ($m) {
                return [
                    'id' => $m->id,
                    'title' => $m->title,
                    'date' => optional($m->start_time)->format('d M Y, h:i A'),
                ];
            })->values(),
            'message' => null,
        ]);
    }

    public function store(Request $request)
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(120);
        }

        $request->validate([
            'title'   => 'required|string|max:255',
            'game'    => 'required|in:CODM',
            'mode'    => 'required|in:solo,duo,squad',
            'match_type' => 'nullable|string|max:50',
            'map'     => 'nullable|string|max:100',
            'region'  => 'nullable|string|max:100',
            'results' => 'required|string',
            'rank_points' => 'nullable|string',
            'kill_point' => 'nullable|numeric|min:0',
            'previous_tournament_id' => 'nullable|integer|exists:tournaments,id',
            'series_id' => 'nullable|integer|exists:tournament_series,id',
        ]);

        $results = json_decode($request->results, true);

        if (!is_array($results) || empty($results)) {
            return back()->withErrors(['error' => 'Invalid results data']);
        }

        // Strict result integrity checks by mode to avoid partial/invalid series data.
        $rows = collect($results)->filter(fn($row) => is_array($row))->values();
        $mode = $request->mode;

        if ($mode === 'solo') {
            $rows = $rows->filter(fn($row) => !empty(trim((string) ($row['player_ign'] ?? ''))))->values();

            if ($rows->isEmpty()) {
                return back()->withErrors(['results' => 'Add at least one solo player result.']);
            }

            foreach ($rows as $row) {
                $rank = $row['rank'] ?? null;
                if (!is_numeric($rank) || (int) $rank < 1) {
                    return back()->withErrors(['results' => 'Each solo player must have a valid rank (1 or higher).']);
                }
            }
        } else {
            $expectedMembers = $mode === 'duo' ? 2 : 4;
            $nonEmptyRows = $rows->filter(function ($row) {
                return !empty(trim((string) ($row['team_name'] ?? '')))
                    || !empty(trim((string) ($row['player_ign'] ?? '')));
            })->values();

            if ($nonEmptyRows->isEmpty()) {
                return back()->withErrors(['results' => 'Add at least one team result.']);
            }

            if ($nonEmptyRows->contains(fn($row) => empty(trim((string) ($row['team_name'] ?? ''))))) {
                return back()->withErrors(['results' => 'Each duo/squad player row must include a team name.']);
            }

            $grouped = $nonEmptyRows->groupBy(fn($row) => trim((string) ($row['team_name'] ?? '')));
            foreach ($grouped as $teamName => $teamRows) {
                $players = $teamRows->filter(fn($row) => !empty(trim((string) ($row['player_ign'] ?? ''))))->values();
                if ($players->count() !== $expectedMembers) {
                    return back()->withErrors([
                        'results' => "Team '{$teamName}' must have exactly {$expectedMembers} players for {$mode} mode."
                    ]);
                }

                $rankSet = $players
                    ->pluck('rank')
                    ->filter(fn($rank) => $rank !== null && $rank !== '')
                    ->map(fn($rank) => (int) $rank)
                    ->unique()
                    ->values();

                if ($rankSet->count() !== 1 || (int) $rankSet->first() < 1) {
                    return back()->withErrors([
                        'results' => "Team '{$teamName}' must have one valid team rank shared by all team members."
                    ]);
                }
            }

            $rows = $nonEmptyRows;
        }

        $results = $rows->values()->all();

        $series = null;
        if ($request->filled('series_id')) {
            $series = TournamentSeries::where('organizer_id', Auth::id())
                ->where('id', $request->series_id)
                ->first();

            if (!$series) {
                return back()->withErrors(['series_id' => 'Invalid series selected.']);
            }

            if ($series->mode !== $request->mode) {
                return back()->withErrors(['mode' => 'Series mode must match match mode.']);
            }
            if ($series->match_type && $request->filled('match_type') && $series->match_type !== $request->match_type) {
                return back()->withErrors(['match_type' => 'Series match type must match.']);
            }
        }

        $pointsConfig = config('points.codm');
        $placementPoints = $pointsConfig['placement_points'] ?? [];
        $killPoint = $pointsConfig['kill_point'] ?? 1;

        if ($request->filled('rank_points')) {
            $decoded = json_decode($request->rank_points, true);
            if (is_array($decoded)) {
                $placementPoints = $decoded;
            }
        }

        if ($request->filled('kill_point')) {
            $killPoint = (int) $request->kill_point;
        }

        if ($series) {
            if (is_array($series->placement_points) && !empty($series->placement_points)) {
                $placementPoints = $series->placement_points;
            }
            if (!is_null($series->kill_point)) {
                $killPoint = (int) $series->kill_point;
            }
        }

        $getPlacementPoints = function ($rank) use ($placementPoints) {
            if (!$rank) {
                return 0;
            }
            return $placementPoints[$rank] ?? 0;
        };

        // Count slots (teams for duo/squad, players for solo)
        $slotCount = $request->mode === 'solo'
            ? collect($results)->filter(fn($r) => !empty($r['player_ign']))->count()
            : collect($results)->pluck('team_name')->filter()->unique()->count();

        $tournament = DB::transaction(function () use ($request, $results, $slotCount, $getPlacementPoints, $killPoint, $series) {

            /* ===============================
               1️⃣ CREATE TOURNAMENT
            =============================== */
            $tournament = Tournament::create([
                'title' => $request->title,
                'game' => $series?->game ?? $request->game,
                'mode' => $series?->mode ?? $request->mode,
                'match_type' => $series?->match_type ?? $request->match_type,
                'map' => $series?->map ?? $request->map,
                'region' => $series?->region ?? ($request->region ?? 'India'),
                'substitute_count' => (int) ($series?->substitute_count ?? 0),
                'organizer_id' => Auth::id(),
                'status' => 'completed',
                'start_time' => now(),
                'registration_close_time' => now(),
                'entry_fee' => $series?->entry_fee ?? 0,
                'reward_type' => $series?->reward_type ?? 'free',
                'is_paid' => $series?->is_paid ?? false,
                'prize_pool' => $series?->prize_total ?? 0,
                'upi_id' => $series?->upi_id,
                'upi_name' => $series?->upi_name,
                'upi_qr' => $series?->upi_qr,
                'description' => $series?->description,
                'rules' => $series?->rules,
                'auto_approve' => true,
                'slots' => max(2, $slotCount),
                'filled_slots' => $slotCount,
                'is_instant' => true,
            ]);

            if ($series && $series->prizes()->exists()) {
                foreach ($series->prizes()->get() as $prize) {
                    TournamentPrize::create([
                        'tournament_id' => $tournament->id,
                        'position' => $prize->position,
                        'amount' => $prize->amount,
                    ]);
                }
            }

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
            if ($request->mode === 'solo') {
                $entryRows = [];
                $teamRows = [];
                $joinSequence = 1;
                $now = now();

                foreach ($results as $row) {
                    if (empty($row['player_ign'])) {
                        continue;
                    }

                    $joinId = DB::table('tournament_joins')->insertGetId([
                        'tournament_id' => $tournament->id,
                        'organizer_id' => Auth::id(),
                        'user_id' => null,
                        'join_code' => $this->buildJoinCode($tournament->id, $joinSequence++),
                        'team_name' => null,
                        'captain_ign' => $row['player_ign'],
                        'captain_game_id' => $row['player_game_id'] ?? 'CODM-' . strtoupper(Str::random(6)),
                        'email' => 'instant@results.gg',
                        'phone' => '9000000000',
                        'mode' => $request->mode,
                        'is_paid' => false,
                        'entry_fee' => 0,
                        'payment_status' => 'not_required',
                        'status' => 'approved',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    $rank = $row['rank'] ?? null;
                    $winnerPosition = in_array((int) $rank, [1, 2, 3], true) ? (string) $rank : null;
                    $pp = $getPlacementPoints($rank);
                    $kp = ($row['kills'] ?? 0) * $killPoint;
                    $tt = $pp + $kp;
                    $cd = ((int) $rank === 1) ? 1 : 0;

                    $entryRows[] = [
                        'match_result_id' => $matchResult->id,
                        'tournament_join_id' => $joinId,
                        'player_ign' => $row['player_ign'],
                        'player_game_id' => $row['player_game_id'] ?? null,
                        'team_name' => null,
                        'rank' => $rank,
                        'kills' => $row['kills'] ?? 0,
                        'points' => $row['points'] ?? 0,
                        'winner_position' => $row['winner_position'] ?? $winnerPosition,
                        'kp' => $kp,
                        'pp' => $pp,
                        'tt' => $tt,
                        'cd' => $cd,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $teamRows[] = [
                        'match_result_id' => $matchResult->id,
                        'tournament_join_id' => $joinId,
                        'team_name' => $row['player_ign'],
                        'rank' => $rank,
                        'mp' => 1,
                        'kp' => $kp,
                        'pp' => $pp,
                        'tt' => $tt,
                        'cd' => $cd,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                foreach (array_chunk($entryRows, 500) as $chunk) {
                    DB::table('match_result_entries')->insert($chunk);
                }
                foreach (array_chunk($teamRows, 500) as $chunk) {
                    DB::table('team_results')->insert($chunk);
                }
            } else {
                $entryRows = [];
                $teamRows = [];
                $joinSequence = 1;
                $now = now();
                $teams = collect($results)->groupBy(function ($row) {
                    return trim((string) ($row['team_name'] ?? ''));
                });

                foreach ($teams as $teamName => $players) {
                    if ($teamName === '') {
                        $teamName = 'Team ' . strtoupper(Str::random(4));
                    }

                    $teamRank = $players->pluck('rank')->filter()->first();
                    $winnerPosition = in_array((int) $teamRank, [1, 2, 3], true) ? (string) $teamRank : null;
                    $pp = $getPlacementPoints($teamRank);
                    $kp = $players->sum('kills') * $killPoint;
                    $tt = $pp + $kp;
                    $cd = ((int) $teamRank === 1) ? 1 : 0;

                    $captain = $players->first();

                    $joinId = DB::table('tournament_joins')->insertGetId([
                        'tournament_id' => $tournament->id,
                        'organizer_id' => Auth::id(),
                        'user_id' => null,
                        'join_code' => $this->buildJoinCode($tournament->id, $joinSequence++),
                        'team_name' => $teamName,
                        'captain_ign' => $captain['player_ign'] ?? $teamName,
                        'captain_game_id' => $captain['player_game_id'] ?? 'CODM-' . strtoupper(Str::random(6)),
                        'email' => 'instant@results.gg',
                        'phone' => '9000000000',
                        'mode' => $request->mode,
                        'is_paid' => false,
                        'entry_fee' => 0,
                        'payment_status' => 'not_required',
                        'status' => 'approved',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    foreach ($players as $row) {
                        if (empty($row['player_ign'])) {
                            continue;
                        }

                        $rank = $row['rank'] ?? $teamRank;

                        $entryRows[] = [
                            'match_result_id' => $matchResult->id,
                            'tournament_join_id' => $joinId,
                            'player_ign' => $row['player_ign'],
                            'player_game_id' => $row['player_game_id'] ?? null,
                            'team_name' => $teamName,
                            'rank' => $rank,
                            'kills' => $row['kills'] ?? 0,
                            'points' => $row['points'] ?? 0,
                            'winner_position' => $row['winner_position'] ?? $winnerPosition,
                            'kp' => $kp,
                            'pp' => $pp,
                            'tt' => $tt,
                            'cd' => $cd,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }

                    $teamRows[] = [
                        'match_result_id' => $matchResult->id,
                        'tournament_join_id' => $joinId,
                        'team_name' => $teamName,
                        'rank' => $teamRank,
                        'mp' => 1,
                        'kp' => $kp,
                        'pp' => $pp,
                        'tt' => $tt,
                        'cd' => $cd,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                foreach (array_chunk($entryRows, 500) as $chunk) {
                    DB::table('match_result_entries')->insert($chunk);
                }
                foreach (array_chunk($teamRows, 500) as $chunk) {
                    DB::table('team_results')->insert($chunk);
                }
            }

            if ($series) {
                $series->tournaments()->syncWithoutDetaching([$tournament->id]);
            }

            return $tournament;
        });

        if ($request->filled('series_id')) {
            return redirect()
                ->route('tournaments.results.show', $tournament)
                ->with('success', '🏆 Instant results published! This match is added to series standings.');
        }

        if ($request->filled('previous_tournament_id')) {
            $previous = Tournament::where('organizer_id', Auth::id())
                ->where('id', $request->previous_tournament_id)
                ->first();

            if ($previous) {
                if ($previous->mode !== $request->mode) {
                    return back()->withErrors([
                        'previous_tournament_id' => 'Previous match mode must match the current match mode.'
                    ]);
                }

                $series = $previous->series()->latest()->first();

                // If the selected previous match already belongs to a series,
                // force selecting the latest match in that series.
                if ($series) {
                    $latestSeriesMatch = $series->tournaments()
                        ->orderByDesc('start_time')
                        ->orderByDesc('id')
                        ->first();

                    if ($latestSeriesMatch && (int) $latestSeriesMatch->id !== (int) $previous->id) {
                        return back()->withErrors([
                            'previous_tournament_id' => 'Please select the latest match in that series.'
                        ]);
                    }
                }

                if (!$series) {
                    $startDate = Carbon::parse($previous->start_time)->toDateString();
                    $endDate = Carbon::parse($tournament->start_time)->toDateString();

                    $series = TournamentSeries::create([
                        'organizer_id' => Auth::id(),
                        'title' => $previous->title . ' Series',
                        'description' => 'Auto-created series for instant results.',
                        'mode' => $previous->mode,
                        'substitute_count' => (int) ($previous->substitute_count ?? 0),
                        'start_date' => min($startDate, $endDate),
                        'end_date' => max($startDate, $endDate),
                        'is_published' => true,
                    ]);
                }

                $series->tournaments()->syncWithoutDetaching([
                    $previous->id,
                    $tournament->id,
                ]);

                $isLinked = $series->tournaments()
                    ->where('tournaments.id', $tournament->id)
                    ->exists();
                if (!$isLinked) {
                    return back()->withErrors([
                        'previous_tournament_id' => 'Series link failed. Please try publishing again.'
                    ]);
                }

                return redirect()
                    ->route('tournaments.results.show', $tournament)
                    ->with('success', '🏆 Instant results published! This match is linked and series standings updated.');
            }

            return back()->withErrors([
                'previous_tournament_id' => 'Selected previous match was not found.'
            ]);
        }

        return redirect()
            ->route('tournaments.results.show', $tournament)
            ->with('success', '🏆 Instant results published successfully! Share the link from this page.');
    }

    private function buildJoinCode(int $tournamentId, int $sequence): string
    {
        return sprintf(
            'GS-%s-%s',
            strtoupper(base_convert((string) $tournamentId, 10, 36)),
            strtoupper(base_convert((string) $sequence, 10, 36))
        );
    }
}
