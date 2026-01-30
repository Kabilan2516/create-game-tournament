<?php

namespace App\Http\Controllers;

use App\Models\TournamentJoin;
use App\Models\TournamentJoinMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JoinCodeController extends Controller
{
    /* =========================
       JOIN CODE INPUT PAGE
    ========================= */
    public function index()
    {
        return view('join-code.index');
    }
    public function show(TournamentJoin $join)
    {
        $tournament = $join->tournament;

        $isMatchStarted = now()->greaterThanOrEqualTo($tournament->start_time);

        return view('join-code.view', compact(
            'join',
            'tournament',
            'canEdit',
            'isMatchStarted'
        ));
    }

    /* =========================
       LOOKUP JOIN CODE
    ========================= */
    public function lookup(Request $request)
    {
        $request->validate([
            'join_code' => 'required|string'
        ]);

        $join = TournamentJoin::with([
            'tournament',
            'members'
        ])
            ->where('join_code', strtoupper($request->join_code))
            ->first();

        if (!$join) {
            return back()->withErrors([
                'error' => 'Invalid Join Code'
            ]);
        }

        $tournament = $join->tournament;

        $isMatchStarted = now()->greaterThanOrEqualTo($tournament->start_time);

        $canEdit =
            !$isMatchStarted &&
            in_array($join->status, ['pending', 'approved']);

        return view('join-code.view', compact(
            'join',
            'tournament',
            'canEdit',
            'isMatchStarted'
        ));
    }

    /* =========================
       UPDATE TEAM DETAILS
    ========================= */
    public function update(Request $request, TournamentJoin $join)
    {
        $tournament = $join->tournament;

        if (now()->greaterThanOrEqualTo($tournament->start_time)) {
            return back()->withErrors([
                'error' => 'Match already started. Editing disabled.'
            ]);
        }

        $limits = match ($join->mode) {
            'solo'  => ['min' => 1, 'max' => 1],
            'duo'   => ['min' => 1, 'max' => 2],
            'squad' => ['min' => 1, 'max' => 4],
        };

        $request->validate([
            'team_name' => 'nullable|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'captain_ign' => 'required|string|max:100',
            'captain_game_id' => 'required|string|max:100',
            'members' => 'nullable|array',
            'members.*.ign' => 'nullable|string|max:100',
            'members.*.game_id' => 'nullable|string|max:100',
        ]);

        $members = collect($request->members ?? [])
            ->filter(
                fn($m) =>
                !empty($m['ign']) && !empty($m['game_id'])
            )
            ->values();

        $totalMembers = 1 + $members->count();

        if ($totalMembers < $limits['min'] || $totalMembers > $limits['max']) {
            return back()->withErrors([
                'error' => "Invalid team size for {$join->mode} mode."
            ]);
        }

        DB::transaction(function () use ($request, $join, $members) {

            // Update captain
            $join->update([
                'team_name' => $request->team_name,
                'captain_ign' => $request->captain_ign,
                'captain_game_id' => $request->captain_game_id,
            ]);

            // Replace members
            TournamentJoinMember::where('tournament_join_id', $join->id)->delete();

            foreach ($members as $member) {
                TournamentJoinMember::create([
                    'tournament_join_id' => $join->id,
                    'ign' => $member['ign'],
                    'game_id' => $member['game_id'],
                ]);
            }
        });

        return redirect()
            ->route('join.code.show', $join)
            ->with('success', '✅ Team updated successfully');
    }
}
