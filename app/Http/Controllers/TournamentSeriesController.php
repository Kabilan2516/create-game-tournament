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
use App\Models\TeamResult;
use App\Models\MatchResultEntry;
use App\Models\TournamentSeriesPrize;
use App\Models\TournamentJoin;
use App\Models\TournamentJoinMember;
use App\Models\TournamentSeriesRegistration;
use App\Services\MediaUploadService;
use Illuminate\Support\Str;

class TournamentSeriesController extends Controller
{
    public function publicIndex(Request $request)
    {
        $query = TournamentSeries::query()
            ->where('is_published', true)
            ->withCount('tournaments')
            ->with([
                'organizer:id,name',
                'banner',
                'tournaments' => function ($q) {
                    $q->select('tournaments.id', 'tournaments.title', 'tournaments.start_time')
                        ->with([
                            'matchResult' => function ($mq) {
                                $mq->select('id', 'tournament_id', 'is_locked')
                                    ->where('is_locked', true);
                            }
                        ]);
                }
            ]);

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('subtitle', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('game') && $request->game !== 'all') {
            $query->where('game', $request->game);
        }

        if ($request->filled('mode') && $request->mode !== 'all') {
            $query->where('mode', $request->mode);
        }

        $series = $query->latest()->paginate(12)->withQueryString();

        return view('series.public-index', [
            'series' => $series,
        ]);
    }

    public function publicShow(TournamentSeries $series)
    {
        abort_if(!$series->is_published, 404);

        $series->load([
            'organizer:id,name',
            'banner',
            'tournaments' => function ($q) {
                $q->with(['matchResult' => function ($mq) {
                    $mq->where('is_locked', true);
                }])->orderBy('start_time');
            },
            'prizes',
        ]);

        $completedMatches = $series->tournaments->filter(fn($t) => !is_null($t->matchResult))->count();
        $totalMatches = $series->tournaments->count();
        $joinableMatches = $series->tournaments->filter(function ($tournament) {
            return $tournament->status === 'open'
                && !is_null($tournament->registration_close_time)
                && now()->lessThan($tournament->registration_close_time)
                && now()->lessThan($tournament->start_time);
        })->count();

        return view('series.public-show', [
            'series' => $series,
            'completedMatches' => $completedMatches,
            'totalMatches' => $totalMatches,
            'joinableMatches' => $joinableMatches,
        ]);
    }

    public function joinForm(TournamentSeries $series)
    {
        abort_if(!$series->is_published, 404);

        $series->load(['organizer:id,name', 'banner']);
        $joinableTournaments = $series->tournaments()
            ->where('status', 'open')
            ->where('registration_close_time', '>', now())
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->get();
        $registeredCount = TournamentSeriesRegistration::where('tournament_series_id', $series->id)
            ->whereIn('status', ['pending', 'approved'])
            ->count();
        $seriesSlots = $this->resolveSeriesSlotsByMode($series->mode);
        $availableSeriesSlots = max(0, $seriesSlots - $registeredCount);

        return view('series.join', [
            'series' => $series,
            'joinableTournaments' => $joinableTournaments,
            'seriesSlots' => $seriesSlots,
            'registeredCount' => $registeredCount,
            'availableSeriesSlots' => $availableSeriesSlots,
        ]);
    }

    public function joinStore(Request $request, TournamentSeries $series)
    {
        abort_if(!$series->is_published, 404);

        $baseMax = match ($series->mode) {
            'solo' => 1,
            'duo' => 2,
            'squad' => 4,
            default => 1,
        };
        $maxPlayers = $baseMax + (int) ($series->substitute_count ?? 0);

        $request->validate([
            'team_name' => 'nullable|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'members' => 'required|array|min:1',
            'members.*.ign' => 'required|string|max:100',
            'members.*.game_id' => 'required|string|max:100',
            'notes' => 'nullable|string|max:1000',
            'payment_proof' => $series->is_paid ? 'required|image|max:2048' : 'nullable',
        ]);

        if ($series->mode !== 'solo' && empty(trim((string) $request->team_name))) {
            return back()->withErrors(['team_name' => 'Team name is required for duo/squad series.']);
        }

        $members = collect($request->members)
            ->filter(fn($m) => !empty($m['ign']) && !empty($m['game_id']))
            ->values();

        if ($members->isEmpty() || $members->count() > $maxPlayers) {
            return back()->withErrors([
                'members' => "This series allows 1 to {$maxPlayers} players per registration."
            ]);
        }

        $maxRegistrations = $this->resolveSeriesSlotsByMode($series->mode);
        $currentRegistrations = TournamentSeriesRegistration::where('tournament_series_id', $series->id)
            ->whereIn('status', ['pending', 'approved'])
            ->count();
        if ($currentRegistrations >= $maxRegistrations) {
            return back()->withErrors([
                'error' => 'Series registration slots are full.'
            ]);
        }

        $existingRegistration = TournamentSeriesRegistration::where('tournament_series_id', $series->id)
            ->where('email', $request->email)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($existingRegistration) {
            return back()->withErrors([
                'error' => 'You already registered in this series with this email.'
            ]);
        }

        $createdJoins = [];
        $seriesRegistrationCode = null;

        DB::transaction(function () use ($series, $request, $members, &$createdJoins, &$seriesRegistrationCode) {
            $captain = $members->first();
            $isPaid = (bool) $series->is_paid;

            $seriesRegistration = TournamentSeriesRegistration::create([
                'tournament_series_id' => $series->id,
                'organizer_id' => $series->organizer_id,
                'user_id' => Auth::id(),
                'join_code' => $this->generateSeriesRegistrationCode(),
                'team_name' => $series->mode === 'solo' ? null : $request->team_name,
                'captain_ign' => $captain['ign'],
                'captain_game_id' => $captain['game_id'],
                'email' => $request->email,
                'phone' => $request->phone,
                'mode' => $series->mode,
                'substitute_count' => (int) ($series->substitute_count ?? 0),
                'roster' => $members->values()->all(),
                'is_paid' => $isPaid,
                'entry_fee' => $isPaid ? (float) ($series->entry_fee ?? 0) : 0,
                'payment_status' => $isPaid ? 'pending' : 'not_required',
                'status' => 'pending',
                'notes' => $request->notes,
                'registered_at' => now(),
            ]);
            $seriesRegistrationCode = $seriesRegistration->join_code;

            if ($isPaid && $request->hasFile('payment_proof')) {
                MediaUploadService::upload(
                    $request->file('payment_proof'),
                    $seriesRegistration,
                    'payment_proof',
                    'series-registrations/payment-proofs'
                );
            }

            $joinableTournaments = $series->tournaments()
                ->where('status', 'open')
                ->where('registration_close_time', '>', now())
                ->where('start_time', '>', now())
                ->lockForUpdate()
                ->get();

            foreach ($joinableTournaments as $tournament) {
                if ($tournament->filled_slots >= $tournament->slots) {
                    continue;
                }

                $alreadyJoined = TournamentJoin::where('tournament_id', $tournament->id)
                    ->where('email', $request->email)
                    ->exists();
                if ($alreadyJoined) {
                    continue;
                }

                $isAutoApproved = (bool) $tournament->auto_approve && (!$isPaid || $request->hasFile('payment_proof'));
                $status = $isAutoApproved ? 'approved' : 'pending';

                $join = TournamentJoin::create([
                    'tournament_id' => $tournament->id,
                    'organizer_id' => $tournament->organizer_id,
                    'user_id' => Auth::id(),
                    'join_code' => $this->generateSeriesJoinCode(),
                    'team_name' => $series->mode === 'solo' ? null : $request->team_name,
                    'captain_ign' => $captain['ign'],
                    'captain_game_id' => $captain['game_id'],
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'mode' => $series->mode,
                    'is_paid' => $isPaid,
                    'entry_fee' => $isPaid ? (float) ($series->entry_fee ?? 0) : 0,
                    'payment_status' => $isPaid ? 'pending' : 'not_required',
                    'status' => $status,
                    'notes' => $request->notes,
                ]);

                foreach ($members->skip(1) as $member) {
                    TournamentJoinMember::create([
                        'tournament_join_id' => $join->id,
                        'ign' => $member['ign'],
                        'game_id' => $member['game_id'],
                    ]);
                }

                if ($isPaid && $request->hasFile('payment_proof')) {
                    MediaUploadService::upload(
                        $request->file('payment_proof'),
                        $join,
                        'payment_proof',
                        'tournament-joins/payment-proofs'
                    );
                }

                $tournament->increment('filled_slots');
                $createdJoins[] = $join->join_code;
            }
        });

        if (empty($createdJoins)) {
            return redirect()
                ->route('series.public.show', $series)
                ->with('success', 'Series registration saved. Code: ' . $seriesRegistrationCode . '. Open matches were not available now.');
        }

        return redirect()
            ->route('series.public.show', $series)
            ->with('success', 'Series registration saved. Series Code: ' . $seriesRegistrationCode . '. Match Join Codes: ' . implode(', ', $createdJoins));
    }

    private function generateSeriesJoinCode(): string
    {
        return 'GS-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
    }

    private function generateSeriesRegistrationCode(): string
    {
        return 'SR-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
    }

    private function resolveSeriesSlotsByMode(string $mode): int
    {
        return match (strtolower($mode)) {
            'solo' => 100,
            'duo' => 50,
            'squad' => 25,
            default => 100,
        };
    }

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

    public function setPublished(Request $request, TournamentSeries $series)
    {
        abort_if($series->organizer_id !== Auth::id(), 403);

        $request->validate([
            'is_published' => 'required|boolean',
        ]);

        $series->update([
            'is_published' => (bool) $request->boolean('is_published'),
        ]);

        return back()->with('success', $series->is_published
            ? '✅ Series published. Players can register now.'
            : '✅ Series unpublished. Player registration is blocked.');
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

    public function createInstant()
    {
        return view('series.instant-create');
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
            'substitute_count' => (int) ($tournaments->first()->substitute_count ?? 0),
            'registration_slots' => $this->resolveSeriesSlotsByMode($mode),
            'start_date'   => $startDate,
            'end_date'     => $endDate,
            'description' => $request->description,
            'is_published' => false,
        ]);

        // 🔹 ATTACH TOURNAMENTS
        $series->tournaments()->sync($request->tournament_ids);

        return redirect()
            ->route('series.show', $series)
            ->with('success', '✅ Series created successfully. Leaderboard will combine all results.');
    }

    public function storeInstant(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'rules' => 'nullable|string',
            'mode' => 'required|in:solo,duo,squad',
            'substitute_count' => 'nullable|integer|min:0|max:10',
            'match_type' => 'required|string|max:50',
            'map' => 'nullable|string|max:100',
            'kill_point' => 'required|integer|min:0',
            'placement_points' => 'required|array|min:1',
            'placement_points.*.position' => 'required|integer|min:1',
            'placement_points.*.points' => 'required|integer|min:0',
            'region' => 'nullable|string|max:100',
            'is_paid' => 'required|boolean',
            'entry_fee' => 'nullable|numeric|min:0',
            'reward_type' => 'required|in:free,organizer_prize,platform_points',
            'prize_positions' => 'nullable|array',
            'prize_positions.*' => 'nullable|integer|min:1',
            'prize_amounts' => 'nullable|array',
            'prize_amounts.*' => 'nullable|numeric|min:0',
            'upi_id' => 'nullable|string',
            'upi_name' => 'nullable|string',
            'upi_qr' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:4096',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $points = collect($request->placement_points)
            ->mapWithKeys(fn($row) => [(int) $row['position'] => (int) $row['points']])
            ->toArray();

        if ($request->boolean('is_paid') && !$request->upi_id) {
            return back()->withErrors([
                'upi_id' => 'UPI ID is required for paid entry series.'
            ]);
        }

        if (!$request->boolean('is_paid')) {
            $request->merge([
                'entry_fee' => 0,
                'upi_id' => null,
                'upi_name' => null,
                'upi_qr' => null,
            ]);
        }

        if ($request->reward_type !== 'organizer_prize') {
            $request->merge([
                'prize_positions' => [],
                'prize_amounts' => [],
            ]);
        }

        $prizes = collect();
        if ($request->reward_type === 'organizer_prize') {
            $positions = $request->input('prize_positions', []);
            $amounts = $request->input('prize_amounts', []);

            $prizes = collect($positions)->map(function ($pos, $index) use ($amounts) {
                $position = (int) $pos;
                $amount = isset($amounts[$index]) ? (float) $amounts[$index] : 0;

                return [
                    'position' => $position,
                    'amount' => $amount,
                ];
            })->filter(function ($row) {
                return $row['position'] > 0 && $row['amount'] >= 0;
            })->unique('position')->sortBy('position')->values();
        }

        $series = TournamentSeries::create([
            'organizer_id' => Auth::id(),
            'game' => 'CODM',
            'title' => $request->name,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'rules' => $request->rules,
            'mode' => $request->mode,
            'substitute_count' => (int) ($request->substitute_count ?? 0),
            'registration_slots' => $this->resolveSeriesSlotsByMode($request->mode),
            'match_type' => $request->match_type,
            'map' => $request->map,
            'kill_point' => $request->kill_point,
            'placement_points' => $points,
            'region' => $request->region,
            'reward_type' => $request->reward_type,
            'is_paid' => $request->boolean('is_paid'),
            'entry_fee' => $request->entry_fee ?? 0,
            'prize_pool' => $prizes->sum('amount'),
            'upi_id' => $request->upi_id,
            'upi_name' => $request->upi_name,
            'upi_qr' => null,
            'start_date' => $request->start_date ?? now()->toDateString(),
            'end_date' => $request->end_date ?? now()->toDateString(),
            'is_published' => false,
        ]);

        if ($request->hasFile('upi_qr')) {
            $path = $request->file('upi_qr')->store('series/upi_qr', 'public');
            $series->update(['upi_qr' => $path]);
        }

        if ($request->hasFile('banner')) {
            MediaUploadService::upload(
                $request->file('banner'),
                $series,
                'banner',
                'series/banners'
            );
        }

        if ($prizes->isNotEmpty()) {
            $series->prizes()->createMany($prizes->toArray());
        }

        return redirect()
            ->route('series.show', $series)
            ->with('success', '✅ Series created. Add match results to build the leaderboard.');
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

    public function publicResults(TournamentSeries $series)
    {
        $series->load('tournaments');

        $tournamentIds = $series->tournaments->pluck('id');
        $matchResultIds = MatchResult::whereIn('tournament_id', $tournamentIds)
            ->where('is_locked', true)
            ->pluck('id');

        $teamResults = TeamResult::whereIn('match_result_id', $matchResultIds)->get();

        if ($teamResults->isNotEmpty()) {
            $leaderboard = $teamResults->groupBy(function ($row) {
                return $row->team_name ?: ('Team ' . $row->tournament_join_id);
            })->map(function ($rows) {
                $matchCount = $rows->pluck('match_result_id')->unique()->count();

                return [
                    'name' => $rows->first()->team_name ?: ('Team ' . $rows->first()->tournament_join_id),
                    'mp' => $matchCount,
                    'kp' => $rows->sum('kp'),
                    'pp' => $rows->sum('pp'),
                    'tt' => $rows->sum('tt'),
                    'cd' => $rows->sum('cd'),
                ];
            })->sortByDesc('tt')->sortByDesc('kp')->values();
        } else {
            $entries = MatchResultEntry::whereIn('match_result_id', $matchResultIds)->get();

            $leaderboard = $entries->groupBy(function ($entry) {
                return $entry->team_name ?: $entry->player_ign;
            })->map(function ($rows) {
                $matchCount = $rows->pluck('match_result_id')->unique()->count();
                $kp = $rows->sum('kp') > 0 ? $rows->sum('kp') : $rows->sum('kills');
                $pp = $rows->sum('pp');
                $tt = $rows->sum('tt') > 0 ? $rows->sum('tt') : $rows->sum('points');

                return [
                    'name' => $rows->first()->team_name ?: $rows->first()->player_ign,
                    'mp' => $matchCount,
                    'kp' => $kp,
                    'pp' => $pp,
                    'tt' => $tt,
                    'cd' => $rows->where('rank', 1)->count(),
                ];
            })->sortByDesc('tt')->sortByDesc('kp')->values();
        }

        return view('series.public-results', [
            'series' => $series,
            'leaderboard' => $leaderboard,
            'completedMatches' => $matchResultIds->count(),
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
