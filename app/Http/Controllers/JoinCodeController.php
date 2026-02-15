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
        return $this->renderJoinView($join);
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
            'members',
            'messages' => fn($q) => $q->orderBy('created_at')
        ])
            ->where('join_code', strtoupper($request->join_code))
            ->first();

        if (!$join) {
            return back()->withErrors([
                'error' => 'Invalid Join Code'
            ]);
        }

        return $this->renderJoinView($join);
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

        $baseMax = match ($join->mode) {
            'solo' => 1,
            'duo' => 2,
            'squad' => 4,
            default => 1,
        };
        $limits = [
            'min' => 1,
            'max' => $baseMax + (int) ($tournament->substitute_count ?? 0),
        ];

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

    private function renderJoinView(TournamentJoin $join)
    {
        $join->loadMissing(['tournament', 'members', 'messages' => fn($q) => $q->orderBy('created_at')]);

        $tournament = $join->tournament;
        $isMatchStarted = now()->greaterThanOrEqualTo($tournament->start_time);
        $canEdit = !$isMatchStarted && in_array($join->status, ['pending', 'approved'], true);

        $series = $tournament->series()
            ->with([
                'tournaments' => function ($q) {
                    $q->orderBy('start_time')
                        ->with(['matchResult' => fn($mq) => $mq->where('is_locked', true)]);
                }
            ])
            ->orderByDesc('tournament_series.id')
            ->first();

        $seriesCompletedMatches = 0;
        $seriesTotalMatches = 0;
        if ($series) {
            $seriesTotalMatches = $series->tournaments->count();
            $seriesCompletedMatches = $series->tournaments->filter(fn($m) => !is_null($m->matchResult))->count();
        }

        return view('join-code.view', compact(
            'join',
            'tournament',
            'canEdit',
            'isMatchStarted',
            'series',
            'seriesCompletedMatches',
            'seriesTotalMatches'
        ));
    }
}
