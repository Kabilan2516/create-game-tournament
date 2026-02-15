<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tournament;
use App\Models\MatchResult;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TournamentJoin;
use App\Mail\TournamentJoinedMail;
use Illuminate\Support\Facades\DB;
use App\Models\TournamentJoinMember;
use App\Services\MediaUploadService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\TournamentJoinMessage;
use Illuminate\Support\Facades\Crypt;

class TournamentController extends Controller
{
    // Show all tournaments


    public function index(Request $request)
    {
        $query = Tournament::with('organizer');

        // 🔹 STATUS TABS
        if ($request->filled('tab')) {
            if ($request->tab === 'upcoming') {
                $query->where('start_time', '>', now());
            }
            if ($request->tab === 'ongoing') {
                $query->where('status', 'ongoing');
            }
            if ($request->tab === 'completed') {
                $query->where('status', 'completed');
            }
        } else {
            // default = upcoming + open
            $query->where('status', 'open');
        }

        // 🔹 SEARCH
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // 🔹 GAME FILTER
        if ($request->filled('game') && $request->game !== 'all') {
            $query->where('game', $request->game);
        }

        // 🔹 MODE FILTER
        if ($request->filled('mode') && $request->mode !== 'all') {
            $query->where('mode', $request->mode);
        }

        // 🔹 FREE / PAID
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('is_paid', $request->type === 'paid' ? 1 : 0);
        }

        // 🔹 DATE FILTER
        if ($request->filled('date')) {
            if ($request->date === 'today') {
                $query->whereDate('start_time', Carbon::today());
            }
            if ($request->date === 'week') {
                $query->whereBetween('start_time', [Carbon::now(), Carbon::now()->endOfWeek()]);
            }
        }

        // 🔹 SORTING
        if ($request->filled('sort')) {
            if ($request->sort === 'prize') {
                $query->orderByDesc('prize_pool');
            }
            if ($request->sort === 'fee') {
                $query->orderBy('entry_fee');
            }
            if ($request->sort === 'slots') {
                $query->orderByDesc('slots');
            }
        } else {
            // Default sorting
            $query->orderByDesc('is_featured')->orderBy('start_time');
        }

        $tournaments = $query->paginate(9)->withQueryString();

        return view('tournaments.index', compact('tournaments'));
    }


    // Show single tournament

    // Show single tournament
    public function show(Tournament $tournament)
    {
        // Organizer
        $organizer = $tournament->organizer;

        // Banner (media system)
        $banner = $tournament->banner;   // may be null

        // Slots
        $slotsLeft = max(0, $tournament->slots - $tournament->filled_slots);

        // Progress %
        $progress = $tournament->slots > 0
            ? round(($tournament->filled_slots / $tournament->slots) * 100)
            : 0;

        // Free / Paid flag
        $isFree = !$tournament->is_paid;

        // Room visibility (only after release)
        $roomId = null;
        $roomPassword = null;

        if ($tournament->room_released && $tournament->room_password) {
            $roomId = $tournament->room_id;
            $roomPassword = Crypt::decryptString($tournament->room_password);
        }

        return view('tournaments.show', compact(
            'tournament',
            'organizer',
            'banner',
            'slotsLeft',
            'progress',
            'isFree',
            'roomId',
            'roomPassword'
        ));
    }




    public function showresult(Tournament $tournament)
    {
        $matchResult = MatchResult::with([
            'entries' => function ($q) {
                $q->orderByRaw('winner_position IS NULL')
                    ->orderBy('winner_position')
                    ->orderBy('rank');
            }
        ])
            ->where('tournament_id', $tournament->id)
            ->where('is_locked', true) // 🔒 only published
            ->first();

        if (!$matchResult) {
            abort(404, 'Results not published yet');
        }

        return view('tournaments.result', [
            'tournament' => $tournament,
            'result' => $matchResult,
            'entries' => $matchResult->entries,
        ]);
    }



    // Organizer create form
    public function create(string $game)
    {
        $allowedGames = ['CODM', 'PUBG'];

        abort_unless(in_array(strtoupper($game), $allowedGames), 404);
        if ($game === 'CODM') {
            return view('tournaments.codm-create', [
                'selectedGame' => strtoupper($game)
            ]);
        }
        return view('tournaments.pubg-create', [
            'selectedGame' => strtoupper($game)
        ]);
    }

    public function selectGame()
    {
        return view('tournaments.select-game');
    }


    // Store tournament


    public function store(Request $request)
    {
        // 🔹 VALIDATION
        $request->validate([

            // Basic
            'title' => 'required|string|max:255',
            'game' => 'required|in:CODM,PUBG',
            'mode' => 'required|in:solo,duo,squad',

            'match_type' => 'required|string',
            'map' => 'required|string',

            'slots' => 'required|integer|min:2',
            'substitute_count' => 'nullable|integer|min:0|max:10',
            'start_date' => 'required|date',
            'start_time_only' => 'required|date_format:H:i',

            'registration_close_date' => 'required|date',
            'registration_close_time_only' => 'required|date_format:H:i',


            'region' => 'required|string',

            // Entry & Reward
            'is_paid' => 'required|boolean',
            'reward_type' => 'required|in:free,organizer_prize,platform_points',
            'auto_approve' => 'required|boolean',

            // Entry fee (only if paid)
            'entry_fee' => 'nullable|numeric|min:0',

            // Prize money (optional even for free entry)
            'first_prize' => 'nullable|numeric|min:0',
            'second_prize' => 'nullable|numeric|min:0',
            'third_prize' => 'nullable|numeric|min:0',
            'prize_positions' => 'nullable|array',
            'prize_positions.*' => 'nullable|integer|min:1',
            'prize_amounts' => 'nullable|array',
            'prize_amounts.*' => 'nullable|numeric|min:0',

            // Payment
            'upi_id' => 'nullable|string',
            'upi_name' => 'nullable|string',
            'upi_qr' => 'nullable|image|max:2048',

            // Room
            'room_id' => 'nullable|string|max:100',
            'room_password' => 'nullable|string|max:255',

            // Banner
            'banner' => 'nullable|image|max:4096',
        ]);

        $isPaid = $request->boolean('is_paid');
        $rewardType = $request->reward_type;

        /*
        |--------------------------------------------------------------------------
        | 🔐 ENTRY PAYMENT RULES
        |--------------------------------------------------------------------------
        */
        if ($isPaid) {
            if (!$request->upi_id) {
                return back()->withErrors([
                    'upi_id' => 'UPI ID is required for paid entry tournaments.'
                ]);
            }
        } else {
            // Force free entry
            $request->merge([
                'entry_fee' => 0,
                'upi_id' => null,
                'upi_name' => null,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 🎁 REWARD RULES
        |--------------------------------------------------------------------------
        */
        if ($rewardType === 'free' || $rewardType === 'platform_points') {
            // No cash prize required
            $request->merge([
                'first_prize' => null,
                'second_prize' => null,
                'third_prize' => null,
            ]);
        }

        $prizes = collect();
        if ($rewardType === 'organizer_prize') {
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

        /*
        |--------------------------------------------------------------------------
        | ⏰ DATETIME HANDLING (FIXED – NO DB CHANGE)
        |--------------------------------------------------------------------------
        */
        $startTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $request->start_date . ' ' . $request->start_time_only
        );

        $registrationClose = Carbon::createFromFormat(
            'Y-m-d H:i',
            $request->registration_close_date . ' ' . $request->registration_close_time_only
        );

        /*
        |--------------------------------------------------------------------------
        | ⛔ SAFETY CHECK (OPTIONAL BUT RECOMMENDED)
        |--------------------------------------------------------------------------
        */
        if ($registrationClose->greaterThanOrEqualTo($startTime)) {
            return back()->withErrors([
                'registration_close_date' => 'Registration must close before tournament start time.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | 🔐 ROOM PASSWORD
        |--------------------------------------------------------------------------
        */
        $roomPassword = null;
        if ($request->room_password) {
            $roomPassword = Crypt::encryptString($request->room_password);
        }

        /*
        |--------------------------------------------------------------------------
        | 🏆 CREATE TOURNAMENT
        |--------------------------------------------------------------------------
        */
        $tournament = Tournament::create([

            // Organizer
            'organizer_id' => Auth::id(),

            // Basic
            'title' => $request->title,
            'game' => $request->game,
            'mode' => $request->mode,

            'map' => $request->map,
            'match_type' => $request->match_type,

            // Slots & Time
            'slots' => $request->slots,
            'substitute_count' => (int) ($request->substitute_count ?? 0),
            'filled_slots' => 0,

            'start_time' => $startTime,
            'registration_close_time' => $registrationClose,

            'region' => $request->region,

            // Content
            'description' => $request->description,
            'rules' => $request->rules,

            // Entry & Reward
            'is_paid' => $isPaid,
            'reward_type' => $rewardType,
            'auto_approve' => $request->boolean('auto_approve'),

            'entry_fee' => $request->entry_fee ?? 0,

            // Prizes
            'first_prize' => $prizes->firstWhere('position', 1)['amount'] ?? $request->first_prize,
            'second_prize' => $prizes->firstWhere('position', 2)['amount'] ?? $request->second_prize,
            'third_prize' => $prizes->firstWhere('position', 3)['amount'] ?? $request->third_prize,
            'prize_pool' => $prizes->sum('amount') > 0
                ? $prizes->sum('amount')
                : (($request->first_prize ?? 0) + ($request->second_prize ?? 0) + ($request->third_prize ?? 0)),

            // Payment
            'upi_id' => $request->upi_id,
            'upi_name' => $request->upi_name,
            'upi_qr' => null,

            // Room
            'room_id' => $request->room_id,
            'room_password' => $roomPassword,
            'room_released' => false,

            // Status
            'status' => 'open',
        ]);

        if ($rewardType === 'organizer_prize' && $prizes->isNotEmpty()) {
            $tournament->prizes()->createMany($prizes->toArray());
        }

        /*
        |--------------------------------------------------------------------------
        | 📷 FILE UPLOADS
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('upi_qr')) {
            $path = $request->file('upi_qr')->store('upi_qr', 'public');
            $tournament->update(['upi_qr' => $path]);
        }

        if ($request->hasFile('banner')) {
            MediaUploadService::upload(
                $request->file('banner'),
                $tournament,
                'banner',
                'tournaments/banners'
            );
        }

        return redirect()
            ->route('tournaments.my')
            ->with('success', '🏆 Tournament created successfully!');
    }



    // Edit tournament
    public function edit(Tournament $tournament)
    {
        // 🔐 Security: only the organizer who created it can edit

        if ($tournament->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('tournaments.edit', compact('tournament'));
    }

    // Update tournament




    public function update(Request $request, Tournament $tournament)
    {
        // 🔐 SECURITY
        if ($tournament->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // 🚫 Prevent editing after match started
        if (now()->greaterThan($tournament->start_time)) {
            return back()->withErrors([
                'error' => 'This tournament has already started and cannot be edited.'
            ]);
        }

        // 🔹 VALIDATE ONLY FIELDS THAT CAN BE EDITED HERE
        $request->validate([

            // Basic editable
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'rules' => 'nullable|string',

            'mode' => 'required|in:solo,duo,squad',
            'slots' => 'required|integer|min:2',
            'substitute_count' => 'nullable|integer|min:0|max:10',

            // Time
            'start_time' => 'required|date',
            'registration_close_time' => 'required|date',

            // Room
            'room_id' => 'nullable|string|max:100',
            'room_password' => 'nullable|string|max:255',

            // Optional uploads
            'upi_qr' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:4096',
        ]);

        // 🔹 CONVERT DATETIME (🔥 VERY IMPORTANT FIX)
        $startTime = Carbon::parse($request->start_time);
        $registrationClose = Carbon::parse($request->registration_close_time);

        // 🔹 ROOM PASSWORD + CHANGE TRACKING
        $roomPassword = $tournament->room_password; // keep old by default
        $roomChanged = false;

        if ($request->filled('room_password')) {
            $roomPassword = Crypt::encryptString($request->room_password);
            $roomChanged = true;
        }

        if ($request->room_id !== $tournament->room_id) {
            $roomChanged = true;
        }

        // 🔹 UPDATE (KEEP OLD VALUES FOR NON-EDITED FIELDS)
        $tournament->update([

            // Editable basics
            'title' => $request->title,
            'description' => $request->description,
            'rules' => $request->rules,

            'mode' => $request->mode,
            'slots' => $request->slots,
            'substitute_count' => (int) ($request->substitute_count ?? 0),

            // Time (as Carbon)
            'start_time' => $startTime,
            'registration_close_time' => $registrationClose,

            // Room
            'room_id' => $request->room_id,
            'room_password' => $roomPassword,

            // 🔥 If room changed, hide again until released
            'room_released' => $roomChanged ? false : $tournament->room_released,
        ]);

        // 🔹 UPLOAD NEW UPI QR (IF EXISTS)
        if ($request->hasFile('upi_qr')) {
            $path = $request->file('upi_qr')->store('upi_qr', 'public');
            $tournament->update(['upi_qr' => $path]);
        }

        // 🔹 UPLOAD NEW BANNER (MEDIA SYSTEM)
        if ($request->hasFile('banner')) {
            MediaUploadService::upload(
                $request->file('banner'),
                $tournament,
                'banner',
                'tournaments/banners'
            );
        }

        return redirect()
            ->route('tournaments.my')
            ->with('success', '✅ Tournament updated successfully!');
    }


    // Delete tournament


    public function destroy(Tournament $tournament)
    {
        // 🔐 SECURITY: only owner
        if ($tournament->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // ⛔ BLOCK DELETE AFTER MATCH START
        if (now()->greaterThanOrEqualTo($tournament->start_time)) {
            return back()->withErrors([
                'error' => '❌ Tournament has already started. Deletion is not allowed.',
            ]);
        }

        DB::beginTransaction();

        try {
            /**
             * Thanks to cascadeOnDelete in migrations,
             * deleting tournament will automatically remove:
             * - tournament_joins
             * - tournament_join_members
             * - participants
             * - match results (current & future)
             */

            $tournament->delete();

            DB::commit();

            return redirect()
                ->route('tournaments.my')
                ->with('success', '🗑️ Tournament deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Something went wrong while deleting the tournament.',
                'debug' => $e->getMessage(), // remove in production
            ]);
        }
    }


    // Join tournament
    public function join($id)
    {
        return back()->with('success', 'Joined successfully!');
    }

    // Show join form

    public function joinForm(Tournament $tournament)
    {
        $series = $tournament->series()
            ->orderByDesc('tournament_series.id')
            ->first();

        if ($series) {
            if ((bool) $series->is_published) {
                return redirect()
                    ->route('series.join.form', $series)
                    ->with('success', 'This tournament is inside a series. Please register from series join page.');
            }

            return redirect()
                ->route('tournaments.show', $tournament)
                ->withErrors(['error' => 'This tournament belongs to a series that is not published yet.']);
        }

        $now = now();

        // 🔐 BLOCK JOIN IF MATCH STARTED
        if (
            $tournament->start_time &&
            $now->greaterThanOrEqualTo($tournament->start_time)
        ) {
            return redirect()
                ->route('tournaments.show', $tournament)
                ->withErrors([
                    'error' => '⛔ Registration closed. The match has already started.'
                ]);
        }
        // Organizer info
        $organizer = $tournament->organizer;

        // Banner (media system)
        $banner = $tournament->banner;

        // Slots left
        $slotsLeft = $tournament->slots - $tournament->filled_slots;

        // ❌ If tournament full or closed, block join
        if ($slotsLeft <= 0 || $tournament->status !== 'open') {
            // abort(403, 'This tournament is no longer accepting players.');
        }

        // Check FREE or PAID
        $isFree = $tournament->entry_fee == 0;

        // Payment info (only if paid)
        $upiId = null;
        $upiName = null;
        $upiQr = null;

        if (!$isFree) {
            $upiId   = $tournament->upi_id;
            $upiName = $tournament->upi_name;
            $upiQr   = $tournament->upiQr; // media relation if you created
        }

        return view('tournaments.join', compact(
            'tournament',
            'organizer',
            'banner',
            'slotsLeft',
            'isFree',
            'upiId',
            'upiName',
            'upiQr'
        ));
    }
    public function manuvaljoinForm()
    {
        return view('tournaments.manual-join');
    }
    private function generateJoinCode(): string
    {
        return 'GS-' .
            strtoupper(Str::random(4)) . '-' .
            strtoupper(Str::random(4));
    }
    public function joinStore(Request $request, Tournament $tournament)
    {
        $series = $tournament->series()
            ->orderByDesc('tournament_series.id')
            ->first();

        if ($series) {
            if ((bool) $series->is_published) {
                return redirect()
                    ->route('series.join.form', $series)
                    ->withErrors(['error' => 'This tournament uses series-based registration.']);
            }

            return redirect()
                ->route('tournaments.show', $tournament)
                ->withErrors(['error' => 'Series registration is currently not published.']);
        }

        /* =====================================================
        BASIC CHECKS
        ===================================================== */
        if ($tournament->status !== 'open') {
            return back()->withErrors(['error' => 'This tournament is not open for joining.']);
        }

        if ($tournament->filled_slots >= $tournament->slots) {
            return back()->withErrors(['error' => 'This tournament is already full.']);
        }

        if (
            TournamentJoin::where('tournament_id', $tournament->id)
            ->where('email', $request->email)
            ->exists()
        ) {
            return back()->withErrors(['error' => 'You already joined using this email.']);
        }

        /* =====================================================
        VALIDATION
        ===================================================== */
        $request->validate([
            'team_name' => 'nullable|string|max:255',

            'email' => 'required|email',
            'phone' => 'required|string|max:20',

            'notes' => 'nullable|string',

            'members' => 'required|array|min:1',
            'members.*.ign' => 'required|string|max:100',
            'members.*.game_id' => 'required|string|max:100',

            'payment_proof' => $tournament->is_paid
                ? 'required|image|max:2048'
                : 'nullable',
        ]);

        /* =====================================================
        MODE LIMITS
        ===================================================== */
        $baseMax = match (strtolower($tournament->mode)) {
            'solo' => 1,
            'duo' => 2,
            'squad' => 4,
            default => 1,
        };
        $limits = [
            'min' => 1,
            'max' => $baseMax + (int) ($tournament->substitute_count ?? 0),
        ];

        /* =====================================================
        NORMALIZE MEMBERS
        ===================================================== */
        $members = collect($request->members)
            ->filter(fn($m) => !empty($m['ign']) && !empty($m['game_id']))
            ->values();

        $totalMembers = $members->count();

        if ($totalMembers < $limits['min'] || $totalMembers > $limits['max']) {
            return back()->withErrors([
                'error' =>
                ucfirst($tournament->mode) .
                    " requires {$limits['min']}–{$limits['max']} players."
            ]);
        }

        /* =====================================================
        CAPTAIN = FIRST MEMBER
        ===================================================== */
        $captain = $members->first();

        DB::beginTransaction();

        try {
            /* =====================================================
            PAYMENT LOGIC
            ===================================================== */
            $isPaidTournament = (bool) $tournament->is_paid;
            $entryFee = $isPaidTournament ? $tournament->entry_fee : 0;

            $isAutoApproved =
                $tournament->auto_approve &&
                (
                    !$isPaidTournament ||
                    ($isPaidTournament && $request->hasFile('payment_proof'))
                );

            $finalStatus = $isAutoApproved ? 'approved' : 'pending';

            /* =====================================================
            JOIN CODE (SAFE, NO DB LOOP)
            ===================================================== */
            $attempts = 0;
            $maxAttempts = 3;

            do {
                try {
                    $joinCode = $this->generateJoinCode();

                    $join = TournamentJoin::create([
                        'tournament_id' => $tournament->id,
                        'organizer_id'  => $tournament->organizer_id,
                        'user_id'       => Auth::id(), // null if guest

                        'join_code' => $joinCode,

                        'team_name' => $request->team_name,
                        'mode'      => strtolower($tournament->mode),

                        // 🔑 CAPTAIN
                        'captain_ign'     => $captain['ign'],
                        'captain_game_id' => $captain['game_id'],

                        'email' => $request->email,
                        'phone' => $request->phone,

                        'is_paid'        => $isPaidTournament,
                        'entry_fee'      => $entryFee,
                        'payment_status' => $isPaidTournament ? 'pending' : 'not_required',

                        'status' => $finalStatus,
                        'notes'  => $request->notes,
                    ]);

                    break;
                } catch (\Illuminate\Database\QueryException $e) {
                    if ($e->getCode() !== '23000' || ++$attempts >= $maxAttempts) {
                        throw $e;
                    }
                }
            } while (true);

            /* =====================================================
            SAVE OTHER MEMBERS (SKIP CAPTAIN)
            ===================================================== */
            foreach ($members->skip(1) as $member) {
                TournamentJoinMember::create([
                    'tournament_join_id' => $join->id,
                    'ign' => $member['ign'],
                    'game_id' => $member['game_id'],
                ]);
            }

            /* =====================================================
            PAYMENT PROOF
            ===================================================== */
            if ($isPaidTournament && $request->hasFile('payment_proof')) {
                MediaUploadService::upload(
                    $request->file('payment_proof'),
                    $join,
                    'payment_proof',
                    'tournament-joins/payment-proofs'
                );
            }

            /* =====================================================
            SYSTEM MESSAGE
            ===================================================== */
            TournamentJoinMessage::create([
                'tournament_join_id' => $join->id,
                'sender' => 'system',
                'message' =>
                "🏆 Tournament: {$tournament->title}\n" .
                    "🎮 Mode: " . ucfirst($join->mode) . "\n" .
                    "🆔 Join Code: {$joinCode}\n\n" .
                    ($finalStatus === 'approved'
                        ? "✅ Your team has been APPROVED!"
                        : "⏳ Your application is pending approval."),
                'is_read' => false,
            ]);

            if ($finalStatus === 'approved') {
                $tournament->increment('filled_slots');
            }

            DB::commit();
            if (config('features.mail.tournament_join')) {
                Mail::to($join->email)->send(
                    new TournamentJoinedMail($tournament, $join, $joinCode)
                );
            }

            return redirect()
                ->route('tournaments.show', $tournament->id)
                ->with('success', 'You have successfully joined! 🎉')
                ->with('join_code', $joinCode);
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Something went wrong while joining.',
                'debug' => $e->getMessage(), // remove in production
            ]);
        }
    }
    // Organizer - My Tournaments
    public function myTournaments()
    {
        $query = Tournament::where('organizer_id', Auth::id());

        // 🔹 Filters (optional, from form later)
        if (request('game')) {
            $query->where('game', request('game'));
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('search')) {
            $query->where('title', 'like', '%' . request('search') . '%');
        }

        // 🔹 Sorting
        if (request('sort') === 'oldest') {
            $query->oldest();
        } elseif (request('sort') === 'prize') {
            $query->orderBy('prize_pool', 'desc');
        } else {
            $query->latest();
        }

        $tournaments = $query->withCount([
            'joins as approved_teams' => function ($q) {
                $q->where('status', 'approved');
            }
        ])->get();

        return view('tournaments.my', compact('tournaments'));
    }



    public function joinRequests(Request $request)
    {
        $organizerId = Auth::id();

        // All tournaments of this organizer (for filter dropdown)
        $tournaments = Tournament::where('organizer_id', $organizerId)->get();

        // Base query: ONLY this organizer’s join requests
        $query = TournamentJoin::with(['tournament'])
            ->where('organizer_id', $organizerId);

        // 🔹 FILTERS

        // Search by team or captain
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('team_name', 'like', "%{$request->search}%")
                    ->orWhere('captain_ign', 'like', "%{$request->search}%");
            });
        }

        // Filter by tournament
        if ($request->tournament_id) {
            $query->where('tournament_id', $request->tournament_id);
        }

        // Filter by mode
        if ($request->mode) {
            $query->where('mode', $request->mode);
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Sort
        if ($request->sort == 'oldest') {
            $query->oldest();
        } else {
            $query->latest(); // default newest first
        }

        $joins = $query->paginate(15);

        // 🔹 STATS (Only this organizer)
        $stats = [
            'pending'  => TournamentJoin::where('organizer_id', $organizerId)->where('status', 'pending')->count(),
            'approved_today' => TournamentJoin::where('organizer_id', $organizerId)
                ->where('status', 'approved')
                ->whereDate('updated_at', Carbon::today())
                ->count(),
            'rejected' => TournamentJoin::where('organizer_id', $organizerId)->where('status', 'rejected')->count(),
            'total'    => TournamentJoin::where('organizer_id', $organizerId)->count(),
        ];

        return view('tournaments.requests', compact('joins', 'tournaments', 'stats'));
    }


    public function approve(TournamentJoin $join)
    {
        // Security
        if ($join->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($join->status !== 'pending') {
            return back()->withErrors(['error' => 'This request is already processed.']);
        }

        $tournament = $join->tournament;

        if ($tournament->filled_slots >= $tournament->slots) {
            return back()->withErrors(['error' => 'Tournament is already full.']);
        }

        // Approve join
        $join->update([
            'status' => 'approved',
        ]);

        $tournament->increment('filled_slots');

        // 🔹 CREATE CHAT MESSAGE (FOR PWA)
        $message = "✅ Your team has been APPROVED for the tournament:\n\n"
            . "🏆 Tournament: {$tournament->title}\n"
            . "🎮 Mode: " . ucfirst($join->mode) . "\n"
            . "🆔 Join ID: {$join->join_code}\n\n"
            . "Room details will be shared soon.\n"
            . "Please be ready before match time.";

        TournamentJoinMessage::create([
            'tournament_join_id' => $join->id,
            'sender' => 'organizer',
            'message' => $message,
            'is_read' => false,
        ]);

        // 🔹 OPTIONAL: OPEN WHATSAPP ALSO (OPTION 3)
        // $whatsappUrl = $this->whatsappLink($join->phone, $message);

        return back()->with('success', '✅ Team approved successfully!');
    }


    // ❌ REJECT JOIN REQUEST

    public function reject(Request $request, TournamentJoin $join)
    {
        // Security
        if ($join->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($join->status !== 'pending') {
            return back()->withErrors(['error' => 'This request is already processed.']);
        }

        // Validate reason
        $request->validate([
            'reject_reason' => 'required|string|max:500',
        ]);

        // Reject
        $join->update([
            'status' => 'rejected',
            'reject_reason' => $request->reject_reason,
        ]);

        $tournament = $join->tournament;

        // 🔹 CREATE CHAT MESSAGE (FOR PWA)
        $message = "❌ Your request for the tournament *{$tournament->title}* has been REJECTED.\n\n"
            . "Reason:\n{$join->reject_reason}\n\n"
            . "You may join other tournaments on GameConnect.";

        \App\Models\TournamentJoinMessage::create([
            'tournament_join_id' => $join->id,
            'sender' => 'organizer',
            'message' => $message,
            'is_read' => false,
        ]);

        // 🔹 OPTIONAL: OPEN WHATSAPP ALSO
        // $whatsappUrl = $this->whatsappLink($join->phone, $message);

        return back()->with('success', '❌ Join rejected & message saved in chat.');
    }
    public function bulkAction(Request $request, Tournament $tournament)
    {
        // 🔐 Security
        if ($tournament->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'selected_joins' => 'required|array|min:1',
            'action' => 'required|in:approve,reject,send_mail',
        ]);

        $joins = TournamentJoin::whereIn('id', $request->selected_joins)
            ->where('tournament_id', $tournament->id)
            ->get();

        if ($joins->isEmpty()) {
            return back()->withErrors(['error' => 'No valid join requests selected.']);
        }

        foreach ($joins as $join) {

            // ✅ APPROVE
            if ($request->action === 'approve') {

                if ($join->status !== 'pending') {
                    continue;
                }

                if ($tournament->filled_slots >= $tournament->slots) {
                    break; // tournament full
                }

                $join->update(['status' => 'approved']);
                $tournament->increment('filled_slots');

                TournamentJoinMessage::create([
                    'tournament_join_id' => $join->id,
                    'sender' => 'organizer',
                    'message' =>
                    "✅ Your team has been APPROVED for the tournament:\n\n" .
                        "🏆 {$tournament->title}\n" .
                        "🎮 Mode: " . ucfirst($join->mode) . "\n" .
                        "🆔 Join ID: {$join->join_code}\n\n" .
                        "Room details will be shared soon.",
                    'is_read' => false,
                ]);
            }

            // ❌ REJECT
            if ($request->action === 'reject') {

                if ($join->status !== 'pending') {
                    continue;
                }

                $join->update([
                    'status' => 'rejected',
                    'reject_reason' => 'Rejected by organizer (bulk action)',
                ]);

                TournamentJoinMessage::create([
                    'tournament_join_id' => $join->id,
                    'sender' => 'organizer',
                    'message' =>
                    "❌ Your request for *{$tournament->title}* has been rejected.\n\n" .
                        "Reason: Organizer bulk action.",
                    'is_read' => false,
                ]);
            }

            // 📧 SEND ROOM DETAILS
            if ($request->action === 'send_mail') {

                if ($join->status !== 'approved') {
                    continue;
                }

                $join->update(['room_visible' => true]);

                TournamentJoinMessage::create([
                    'tournament_join_id' => $join->id,
                    'sender' => 'organizer',
                    'message' =>
                    "📧 Room details are now available.\n\n" .
                        "Please check tournament room info before match time.",
                    'is_read' => false,
                ]);
            }
        }

        return back()->with(
            'success',
            '✅ Bulk action "' . ucfirst(str_replace('_', ' ', $request->action)) . '" completed successfully.'
        );
    }

    public function showdetials(TournamentJoin $join)
    {
        // Security: only organizer can view
        if ($join->organizer_id !== Auth::id()) {
            abort(403);
        }

        $join->load(['tournament', 'members', 'paymentProof']);

        $paymentProof = $join->paymentProof;

        return view('organizer.teams-detials', compact(
            'join',
            'paymentProof'
        ));
    }

    // Organizer - Requests for ONE tournament
    public function requests(Tournament $tournament)
    {
        // Security: only owner can see
        if ($tournament->organizer_id !== Auth::id()) {
            abort(403);
        }

        // Base query
        $query = TournamentJoin::where('tournament_id', $tournament->id)
            ->with(['members', 'tournament']);

        // 🔹 Filters
        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('mode')) {
            $query->where('mode', request('mode'));
        }

        if (request('search')) {
            $query->where(function ($q) {
                $q->where('team_name', 'like', '%' . request('search') . '%')
                    ->orWhere('captain_ign', 'like', '%' . request('search') . '%')
                    ->orWhere('email', 'like', '%' . request('search') . '%');
            });
        }

        // Sorting
        if (request('sort') === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $joins = $query->get();

        // Quick stats
        $pending = $joins->where('status', 'pending')->count();
        $approved = $joins->where('status', 'approved')->count();
        $rejected = $joins->where('status', 'rejected')->count();

        return view('organizer.manage-tournament', compact(
            'tournament',
            'joins',
            'pending',
            'approved',
            'rejected'
        ));
    }
    public function releaseRoom(Tournament $tournament)
    {
        // Only owner
        if ($tournament->organizer_id !== Auth::id()) {
            abort(403);
        }

        // Must have room details
        if (!$tournament->room_id || !$tournament->room_password) {
            return back()->withErrors(['error' => 'Please add Room ID & Password first.']);
        }

        // Mark released
        $tournament->update([
            'room_released' => true,
        ]);

        return back()->with('success', '🔓 Room details released! You can now send them to approved teams.');
    }

    public function sendRoomDetails(Tournament $tournament)
    {
        // 🔐 SECURITY
        if ($tournament->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // ⚠️ Check room exists
        if (!$tournament->room_id || !$tournament->room_password) {
            return back()->withErrors([
                'error' => 'Room ID or Password is missing. Please add them first.'
            ]);
        }

        // 🚫 Prevent duplicate send
        if ($tournament->room_released) {
            return back()->withErrors([
                'error' => 'Room details already sent to teams.'
            ]);
        }

        // 🔓 Decrypt room password
        $roomPassword = Crypt::decryptString($tournament->room_password);

        // 🔹 GET ALL APPROVED TEAMS
        $approvedJoins = TournamentJoin::where('tournament_id', $tournament->id)
            ->where('status', 'approved')
            ->get();

        if ($approvedJoins->count() === 0) {
            return back()->withErrors([
                'error' => 'No approved teams found to send room details.'
            ]);
        }

        // 🔹 MESSAGE TEMPLATE (THIS GOES TO PWA / WHATSAPP LATER)
        foreach ($approvedJoins as $join) {

            $message = "🎮 Match Room Details\n\n"
                . "Tournament: {$tournament->title}\n"
                . "Room ID: {$tournament->room_id}\n"
                . "Password: {$roomPassword}\n\n"
                . "⏰ Start Time: " . $tournament->start_time->format('d M Y, h:i A') . "\n\n"
                . "Good luck & play fair! 🏆";

            // 🔹 SAVE MESSAGE FOR PWA CHAT SYSTEM
            TournamentJoinMessage::create([
                'tournament_join_id' => $join->id,
                'sender' => 'organizer',
                'message' => $message,
            ]);

            // 🔹 MARK ROOM VISIBLE FOR THIS JOIN
            $join->update([
                'room_visible' => true,
            ]);
        }

        // 🔹 MARK TOURNAMENT AS RELEASED
        $tournament->update([
            'room_released' => true,
        ]);

        return back()->with('success', '📤 Room details sent successfully to all approved teams!');
    }





    public function dummyJoin(Tournament $tournament, Request $request)
    {
        abort_if(!app()->environment(['local', 'staging']), 403);

        DB::beginTransaction();

        try {
            $mode = strtolower($tournament->mode);

            $teamSize = match ($mode) {
                'duo'   => 2,
                'squad' => 4,
                default => 1,
            };

            // 🔹 OPTION: RESET EXISTING?
            $reset = $request->boolean('reset'); // true / false

            if ($reset) {
                // 🧹 ERASE EVERYTHING
                DB::table('tournament_join_members')
                    ->whereIn(
                        'tournament_join_id',
                        TournamentJoin::where('tournament_id', $tournament->id)->pluck('id')
                    )->delete();

                TournamentJoin::where('tournament_id', $tournament->id)->delete();

                $tournament->update(['filled_slots' => 0]);
            }

            // 🔹 CURRENT STATE
            $alreadyFilled = $tournament->fresh()->filled_slots;
            $remainingSlots = $tournament->slots - $alreadyFilled;

            if ($remainingSlots <= 0) {
                return back()->with('info', 'Tournament already full.');
            }

            $startIndex = $alreadyFilled + 1;

            for ($i = 0; $i < $remainingSlots; $i++) {

                // 🔐 Join Code (collision-safe)
                do {
                    $joinCode = 'GS-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
                } while (
                    TournamentJoin::where('join_code', $joinCode)->exists()
                );

                $teamNumber = $startIndex + $i;

                // 👑 CAPTAIN (FIRST PLAYER)
                $join = TournamentJoin::create([
                    'tournament_id' => $tournament->id,
                    'organizer_id'  => $tournament->organizer_id,
                    'user_id'       => null,

                    'join_code' => $joinCode,

                    'team_name' => $mode === 'solo' ? null : "Team {$teamNumber}",
                    'mode'      => $mode,

                    'captain_ign'     => "Player_{$teamNumber}_1",
                    'captain_game_id' => "GAME_" . str_pad($teamNumber, 5, '0', STR_PAD_LEFT),

                    'email' => "dummy{$teamNumber}@test.com",
                    'phone' => '90000000' . rand(10, 99),

                    'is_paid'        => false,
                    'entry_fee'      => 0,
                    'payment_status' => 'not_required',

                    'status' => 'approved',
                    'notes'  => 'Dummy auto join',
                ]);

                // 👥 OTHER MEMBERS
                for ($m = 2; $m <= $teamSize; $m++) {
                    TournamentJoinMember::create([
                        'tournament_join_id' => $join->id,
                        'ign' => "Player_{$teamNumber}_{$m}",
                        'game_id' => "GAME_" . rand(10000, 99999),
                    ]);
                }
            }

            // 🔢 UPDATE FILLED SLOTS
            $tournament->increment('filled_slots', $remainingSlots);

            DB::commit();

            return back()->with('success', "{$remainingSlots} dummy teams joined successfully!");
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function dummyResults(Tournament $tournament)
    {
        abort_if(!app()->environment(['local', 'staging']), 403);

        DB::beginTransaction();

        try {
            $joins = TournamentJoin::where('tournament_id', $tournament->id)
                ->where('status', 'approved')
                ->get();

            if ($joins->isEmpty()) {
                return back()->with('error', 'No joined teams found.');
            }

            // 🧹 Clear old results
            DB::table('tournament_results')
                ->where('tournament_id', $tournament->id)
                ->delete();

            $position = 1;

            foreach ($joins as $join) {

                DB::table('tournament_results')->insert([
                    'tournament_id'      => $tournament->id,
                    'tournament_join_id' => $join->id,
                    'position'           => $position,
                    'kills'              => rand(0, 15),
                    'points'             => max(0, 100 - ($position * 5)),
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);

                $position++;
            }

            DB::commit();

            return back()->with('success', 'Dummy results generated successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
