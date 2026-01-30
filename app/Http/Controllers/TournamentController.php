<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tournament;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TournamentJoin;
use Illuminate\Support\Facades\DB;
use App\Models\TournamentJoinMember;
use App\Services\MediaUploadService;
use Illuminate\Support\Facades\Auth;
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

            'start_time' => 'required|date',
            'registration_close_time' => 'required|date',

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

        /*
    |--------------------------------------------------------------------------
    | ⏰ DATETIME HANDLING
    |--------------------------------------------------------------------------
    */
        $startTime = Carbon::parse($request->start_time);
        $registrationClose = Carbon::parse($request->registration_close_time);

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
            'first_prize' => $request->first_prize,
            'second_prize' => $request->second_prize,
            'third_prize' => $request->third_prize,

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
    public function joinStore(Request $request, Tournament $tournament)
    {
        // 🔹 CHECK TOURNAMENT STATUS
        if ($tournament->status !== 'open') {
            return back()->withErrors([
                'error' => 'This tournament is not open for joining.'
            ]);
        }

        // 🔹 CHECK SLOT AVAILABILITY (TEAM-BASED)
        if ($tournament->filled_slots >= $tournament->slots) {
            return back()->withErrors([
                'error' => 'This tournament is already full.'
            ]);
        }

        // 🔹 PREVENT DUPLICATE JOIN (same email)
        $alreadyJoined = TournamentJoin::where('tournament_id', $tournament->id)
            ->where('email', $request->email)
            ->exists();

        if ($alreadyJoined) {
            return back()->withErrors([
                'error' => 'You have already joined this tournament with this email.'
            ]);
        }

        // 🔹 BASIC VALIDATION
        $request->validate([
            'team_name' => 'nullable|string|max:255',

            'captain_ign'     => 'required|string|max:100',
            'captain_game_id' => 'required|string|max:100',

            'email' => 'required|email',
            'phone' => 'required|string|max:20',

            'notes' => 'nullable|string',

            'members' => 'nullable|array',
            'members.*.ign' => 'nullable|string|max:100',
            'members.*.game_id' => 'nullable|string|max:100',

            'payment_proof' => $tournament->is_paid
                ? 'required|image|max:2048'
                : 'nullable',
        ]);

        // 🔹 MODE LIMITS
        $limits = match (strtolower($tournament->mode)) {
            'solo'  => ['min' => 1, 'max' => 1],
            'duo'   => ['min' => 1, 'max' => 2],
            'squad' => ['min' => 1, 'max' => 4],
        };

        // 🔹 NORMALIZE MEMBERS
        $members = collect($request->members ?? [])
            ->filter(
                fn($m) =>
                !empty($m['ign']) && !empty($m['game_id'])
            )
            ->values()
            ->toArray();

        // 🔹 TOTAL MEMBER COUNT = CAPTAIN + MEMBERS
        $totalMembers = 1 + count($members);

        if ($totalMembers < $limits['min'] || $totalMembers > $limits['max']) {
            return back()->withErrors([
                'error' =>
                ucfirst($tournament->mode) .
                    " teams must have between {$limits['min']} and {$limits['max']} players."
            ]);
        }

        DB::beginTransaction();

        try {

            $joinCode = strtoupper(Str::random(10));

            // 🔹 PAYMENT LOGIC
            $isPaidTournament = (bool) $tournament->is_paid;
            $entryFee = $isPaidTournament ? $tournament->entry_fee : 0;

            $isAutoApproved =
                $tournament->auto_approve &&
                (
                    !$isPaidTournament ||
                    ($isPaidTournament && $request->hasFile('payment_proof'))
                );

            $finalStatus = $isAutoApproved ? 'approved' : 'pending';

            // 🔹 CREATE JOIN
            $join = TournamentJoin::create([
                'tournament_id' => $tournament->id,
                'organizer_id'  => $tournament->organizer_id,
                'user_id'       => Auth::id(),

                'join_code' => $joinCode,

                'team_name' => $request->team_name,
                'mode'      => strtolower($tournament->mode),

                'captain_ign'     => $request->captain_ign,
                'captain_game_id' => $request->captain_game_id,

                'email' => $request->email,
                'phone' => $request->phone,

                'is_paid'        => $isPaidTournament,
                'entry_fee'      => $entryFee,
                'payment_status' => $isPaidTournament ? 'pending' : 'not_required',

                'status' => $finalStatus,
                'notes'  => $request->notes,
            ]);

            // 🔹 SAVE EXTRA MEMBERS
            foreach ($members as $member) {
                TournamentJoinMember::create([
                    'tournament_join_id' => $join->id,
                    'ign' => $member['ign'],
                    'game_id' => $member['game_id'],
                ]);
            }

            // 🔹 PAYMENT PROOF
            if ($isPaidTournament && $request->hasFile('payment_proof')) {
                MediaUploadService::upload(
                    $request->file('payment_proof'),
                    $join,
                    'payment_proof',
                    'tournament-joins/payment-proofs'
                );
            }

            // 🔹 SYSTEM MESSAGE
            $message = $finalStatus === 'approved'
                ? "✅ Your team has been APPROVED for the tournament:\n\n"
                . "🏆 Tournament: {$tournament->title}\n"
                . "🎮 Mode: " . ucfirst($join->mode) . "\n"
                . "🆔 Join ID: {$join->join_code}\n\n"
                . "Room details will be shared soon."
                : "✅ Your application has been submitted successfully!\n\n"
                . "🏆 Tournament: {$tournament->title}\n"
                . "🎮 Mode: " . ucfirst($join->mode) . "\n"
                . "🆔 Join Code: {$joinCode}\n\n"
                . "⏳ Status: Pending approval.";

            TournamentJoinMessage::create([
                'tournament_join_id' => $join->id,
                'sender' => 'system',
                'message' => $message,
                'is_read' => false,
            ]);

            if ($finalStatus === 'approved') {
                $tournament->increment('filled_slots');
            }

            DB::commit();

            return redirect()
                ->route('tournaments.show', $tournament->id)
                ->with('success', 'You have successfully joined! 🎉')
                ->with('join_code', $joinCode);
        } catch (\Throwable $e) {

            DB::rollBack();

            return back()->withErrors([
                'error' => 'Something went wrong while joining.',
                'debug' => $e->getMessage(), // remove in prod
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




    public function dummyJoin(Tournament $tournament)
    {
        abort_if(!app()->environment(['local', 'staging']), 403);

        DB::beginTransaction();

        try {
            $mode = strtolower($tournament->mode); // solo | duo | squad

            $teamSize = match ($mode) {
                'duo' => 2,
                'squad' => 4,
                default => 1,
            };

            $totalSlots = $tournament->slots; // e.g. 100 players / 50 teams
            $joinCount = 0;

            while ($joinCount < $totalSlots) {

                $joinCode = strtoupper(Str::random(10));

                $join = TournamentJoin::create([
                    'tournament_id' => $tournament->id,
                    'organizer_id'  => $tournament->organizer_id,
                    'user_id'       => null, // guest
                    'join_code'     => $joinCode,

                    'team_name' => $mode === 'solo'
                        ? null
                        : 'Team ' . ($joinCount + 1),

                    'mode' => $mode,

                    'captain_ign'     => 'Player_' . ($joinCount + 1),
                    'captain_game_id' => 'GAME_' . rand(10000, 99999),

                    'email' => 'dummy' . $joinCount . '@test.com',
                    'phone' => '900000000' . rand(1, 9),

                    'is_paid'        => false,
                    'entry_fee'      => 0,
                    'payment_status' => 'not_required',

                    'status' => 'approved',
                    'notes'  => 'Dummy auto join',
                ]);

                // Add members for duo/squad
                if ($teamSize > 1) {
                    for ($i = 1; $i < $teamSize; $i++) {
                        TournamentJoinMember::create([
                            'tournament_join_id' => $join->id,
                            'ign' => 'Player_' . ($joinCount + 1) . '_' . $i,
                            'game_id' => 'GAME_' . rand(10000, 99999),
                        ]);
                    }
                }

                $joinCount++;
            }

            $tournament->update([
                'filled_slots' => $totalSlots
            ]);

            DB::commit();

            return back()->with('success', 'Dummy players joined successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
