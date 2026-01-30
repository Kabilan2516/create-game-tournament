<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentJoin;
use App\Models\TournamentJoinMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use League\Csv\Reader;

class TournamentJoinController extends Controller
{
    /**
     * Show manual add participants page
     */
    public function create(Tournament $tournament)
    {
        abort_if($tournament->organizer_id !== Auth::id(), 403);

        return view('tournaments.manual-join', compact('tournament'));
    }

    /**
     * Store manual participants (single + bulk)
     */
    public function store(Request $request, Tournament $tournament)
    {
        abort_if($tournament->organizer_id !== Auth::id(), 403);

        // 🔹 CLEAN EMPTY MEMBERS
        $cleanTeams = [];

        foreach ($request->teams as $team) {
            $members = collect($team['members'])
                ->filter(
                    fn($m) =>
                    !empty($m['ign']) && !empty($m['game_id'])
                )
                ->values()
                ->toArray();

            if (count($members) === 0) {
                continue; // skip empty team
            }

            $team['members'] = $members;
            $cleanTeams[] = $team;
        }

        $request->merge(['teams' => $cleanTeams]);

        // 🔹 BASIC VALIDATION
        $request->validate([
            'teams' => 'required|array|min:1',
            'teams.*.members' => 'required|array|min:1',
            'teams.*.members.*.ign' => 'required|string|max:100',
            'teams.*.members.*.game_id' => 'required|string|max:100',
            'teams.*.team_name' => 'nullable|string|max:255',
        ]);

        // 🔹 MODE LIMITS
        $limits = match ($tournament->mode) {
            'solo' => ['min' => 1, 'max' => 1],
            'duo' => ['min' => 1, 'max' => 2],
            'squad' => ['min' => 1, 'max' => 4],
        };

        // 🔹 SLOT CHECK
        if ($tournament->filled_slots + count($request->teams) > $tournament->slots) {
            return back()->withErrors([
                'error' => 'Not enough slots available.'
            ]);
        }

        DB::beginTransaction();

        try {
            foreach ($request->teams as $team) {

                $memberCount = count($team['members']);

                if ($memberCount < $limits['min'] || $memberCount > $limits['max']) {
                    throw new \Exception(
                        ucfirst($tournament->mode) .
                            " teams must have between {$limits['min']} and {$limits['max']} players."
                    );
                }

                // 🔹 CREATE JOIN
                $join = TournamentJoin::create([
                    'tournament_id' => $tournament->id,
                    'organizer_id'  => $tournament->organizer_id,
                    'user_id'       => null,
                    'join_code'     => strtoupper(Str::random(10)),

                    'team_name'     => $team['team_name'] ?? null,
                    'mode'          => $tournament->mode,

                    'captain_ign'     => $team['members'][0]['ign'],
                    'captain_game_id' => $team['members'][0]['game_id'],

                    'email' => 'manual_' . Str::random(6) . '@gameconnect.local',
                    'phone' => '0000000000',

                    'is_paid'        => false,
                    'entry_fee'      => 0,
                    'payment_status' => 'not_required',

                    'status' => 'approved',
                ]);

                // 🔹 SAVE EXTRA MEMBERS
                foreach (array_slice($team['members'], 1) as $member) {
                    TournamentJoinMember::create([
                        'tournament_join_id' => $join->id,
                        'ign' => $member['ign'],
                        'game_id' => $member['game_id'],
                    ]);
                }

                // 🔹 SLOT = ONE TEAM
                $tournament->increment('filled_slots', 1);
            }

            DB::commit();

            return redirect()
                ->route('organizer.requests', $tournament)
                ->with('success', '✅ Participants added successfully!');
        } catch (\Throwable $e) {

            DB::rollBack();

            return back()->withErrors([
                'error' => $e->getMessage(),
            ]);
        }
    }




    public function downloadTemplate(Tournament $tournament)
    {
        abort_if($tournament->organizer_id !== Auth::id(), 403);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="participants_template.csv"',
        ];

        return new StreamedResponse(function () {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'team_name',
                'player_position',
                'ign',
                'game_id',
            ]);

            // Example rows
            fputcsv($handle, ['Team A', 'captain', 'PlayerOne', '12345']);
            fputcsv($handle, ['Team A', 'member', 'PlayerTwo', '67890']);
            fputcsv($handle, ['', 'captain', 'SoloPlayer', '99999']);

            fclose($handle);
        }, 200, $headers);
    }



    public function importPreview(Request $request, Tournament $tournament)
    {
        abort_if($tournament->organizer_id !== Auth::id(), 403);

        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        // ✅ NON-DEPRECATED WAY
        $file = new \SplFileObject(
            $request->file('file')->getRealPath(),
            'r'
        );

        $csv = Reader::createFromFileObject($file);
        $csv->setHeaderOffset(0); // first row = header

        $records = collect($csv->getRecords());

        $teams = [];

        foreach ($records as $row) {

            // fallback for solo rows without team name
            $teamKey = !empty($row['team_name'])
                ? trim($row['team_name'])
                : 'Solo_' . uniqid();

            if (!isset($teams[$teamKey])) {
                $teams[$teamKey] = [
                    'team_name' => $row['team_name'] ?: null,
                    'members' => [],
                ];
            }

            // Skip empty rows safely
            if (empty($row['ign']) || empty($row['game_id'])) {
                continue;
            }

            $teams[$teamKey]['members'][] = [
                'ign' => trim($row['ign']),
                'game_id' => trim($row['game_id']),
            ];
        }

        return response()->json([
            'teams' => array_values($teams),
        ]);
    }
}
