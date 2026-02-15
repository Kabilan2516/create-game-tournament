@extends('layouts.app')

@section('title', 'Results – ' . $tournament->title)

@section('content')

    <section class="bg-black py-16">
        <div class="max-w-6xl mx-auto px-6">

            <!-- HEADER -->
            <div class="mb-10">
                <h1 class="text-4xl font-extrabold mb-2">🏆 Tournament Results</h1>
                <p class="text-gray-400">
                    {{ $tournament->title }} • {{ strtoupper($tournament->mode) }}
                </p>
                <p class="text-sm text-gray-500 mt-1">
                    Published on {{ $result->published_at->format('d M Y, h:i A') }}
                </p>
            </div>

            <!-- SHARE BAR -->
            <div class="mb-10 bg-slate-900 border border-slate-800 rounded-2xl p-4 flex flex-wrap items-center gap-3">
                <div class="text-sm text-gray-400">Share this results link:</div>
                <input id="share-link" type="text" readonly
                    class="flex-1 min-w-[240px] bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm"
                    value="{{ url()->current() }}" />
                <button id="copy-link"
                    class="px-4 py-2 rounded-lg bg-cyan-600 hover:bg-cyan-700 font-semibold">
                    Copy Link
                </button>
                <span id="copy-status" class="text-xs text-emerald-400 hidden">Copied!</span>
            </div>

            @php
                $pointsConfig = config('points.codm');
                $placementPoints = $pointsConfig['placement_points'] ?? [];
                $killPoint = $pointsConfig['kill_point'] ?? 1;

                $getPlacementPoints = function ($rank) use ($placementPoints) {
                    if (!$rank) {
                        return 0;
                    }
                    return $placementPoints[$rank] ?? 0;
                };

                $teamsByJoin = $entries->groupBy('tournament_join_id');
            @endphp

            <!-- POINT SYSTEM + STANDINGS -->
            <div class="grid lg:grid-cols-3 gap-6 mb-12">
                <div class="lg:col-span-1 bg-gradient-to-br from-slate-900 to-slate-950 border border-slate-800 rounded-3xl p-6">
                    <h2 class="text-xl font-bold mb-4">Point System</h2>

                    <div class="text-sm text-gray-400 mb-2">Placement Points</div>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        @foreach ([1,2,3,4,5,6,7,8,9,10] as $pos)
                            <div class="flex items-center justify-between bg-slate-800/60 border border-slate-700 rounded-lg px-3 py-2">
                                <span class="text-gray-300">#{{ $pos }}</span>
                                <span class="text-yellow-300 font-semibold">{{ $placementPoints[$pos] ?? 0 }}</span>
                            </div>
                        @endforeach
                        <div class="flex items-center justify-between bg-slate-800/60 border border-slate-700 rounded-lg px-3 py-2 col-span-2">
                            <span class="text-gray-300">#11 and above</span>
                            <span class="text-yellow-300 font-semibold">0</span>
                        </div>
                    </div>

                    <div class="mt-4 text-sm text-gray-400">
                        Kill Points: <span class="text-yellow-300 font-semibold">1 kill = {{ $killPoint }} point</span>
                    </div>
                    <div class="mt-2 text-xs text-gray-500">
                        MP = Matches Played, KP = Kill Points, PP = Placement Points, TT = Total, CD = Wins
                    </div>
                </div>

                <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-3xl p-6 overflow-hidden">
                    <h2 class="text-xl font-bold mb-4">Leaderboard</h2>

                    @if ($tournament->mode === 'solo')
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-gray-300">
                                <thead class="bg-slate-800 text-gray-400">
                                    <tr>
                                        <th class="px-3 py-3">POS</th>
                                        <th class="px-3 py-3">Player</th>
                                        <th class="px-3 py-3 text-center">MP</th>
                                        <th class="px-3 py-3 text-center">KP</th>
                                        <th class="px-3 py-3 text-center">PP</th>
                                        <th class="px-3 py-3 text-center">TT</th>
                                        <th class="px-3 py-3 text-center">CD</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($entries->sortBy('rank') as $row)
                                        @php
                                            $pp = $getPlacementPoints($row->rank);
                                            $kp = ($row->kills ?? 0) * $killPoint;
                                            $tt = $pp + $kp;
                                            $cd = ((int) $row->rank === 1) ? 1 : 0;
                                        @endphp
                                        <tr class="border-t border-slate-800">
                                            <td class="px-3 py-3 font-bold">{{ $row->rank ?? '—' }}</td>
                                            <td class="px-3 py-3 font-semibold">{{ $row->player_ign }}</td>
                                            <td class="px-3 py-3 text-center">1</td>
                                            <td class="px-3 py-3 text-center">{{ $kp }}</td>
                                            <td class="px-3 py-3 text-center">{{ $pp }}</td>
                                            <td class="px-3 py-3 text-center text-yellow-300 font-semibold">{{ $tt }}</td>
                                            <td class="px-3 py-3 text-center">{{ $cd }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        @php
                            $teams = $teamsByJoin->map(function ($teamEntries) use ($getPlacementPoints, $killPoint) {
                                $first = $teamEntries->first();
                                $rank = $teamEntries->pluck('rank')->filter()->min();
                                $kp = $teamEntries->sum('kills') * $killPoint;
                                $pp = $getPlacementPoints($rank);
                                $tt = $pp + $kp;
                                $cd = ((int) $rank === 1) ? 1 : 0;

                                return [
                                    'team_name' => $first->team_name ?? 'Team ' . $first->tournament_join_id,
                                    'rank' => $rank,
                                    'mp' => 1,
                                    'kp' => $kp,
                                    'pp' => $pp,
                                    'tt' => $tt,
                                    'cd' => $cd,
                                ];
                            })->sortBy('rank');
                        @endphp

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-gray-300">
                                <thead class="bg-slate-800 text-gray-400">
                                    <tr>
                                        <th class="px-3 py-3">POS</th>
                                        <th class="px-3 py-3">Team Name</th>
                                        <th class="px-3 py-3 text-center">MP</th>
                                        <th class="px-3 py-3 text-center">KP</th>
                                        <th class="px-3 py-3 text-center">PP</th>
                                        <th class="px-3 py-3 text-center">TT</th>
                                        <th class="px-3 py-3 text-center">CD</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($teams as $team)
                                        <tr class="border-t border-slate-800">
                                            <td class="px-3 py-3 font-bold">{{ $team['rank'] ?? '—' }}</td>
                                            <td class="px-3 py-3 font-semibold">{{ $team['team_name'] }}</td>
                                            <td class="px-3 py-3 text-center">{{ $team['mp'] }}</td>
                                            <td class="px-3 py-3 text-center">{{ $team['kp'] }}</td>
                                            <td class="px-3 py-3 text-center">{{ $team['pp'] }}</td>
                                            <td class="px-3 py-3 text-center text-yellow-300 font-semibold">{{ $team['tt'] }}</td>
                                            <td class="px-3 py-3 text-center">{{ $team['cd'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- WINNERS -->
            @php
                // Group entries by team
                $teamsByJoin = $entries->groupBy('tournament_join_id');
            @endphp

            <div class="grid md:grid-cols-3 gap-6 mb-12">
                @foreach ([1, 2, 3] as $pos)
                    @php
                        // Find the team that has this winner position
                        $winnerTeam = $teamsByJoin->first(function ($teamEntries) use ($pos) {
                            return $teamEntries->contains('winner_position', (string) $pos);
                        });

                        $first = $winnerTeam?->first();
                    @endphp

                    <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 text-center">
                        <div class="text-3xl mb-2">
                            {{ $pos == 1 ? '🥇' : ($pos == 2 ? '🥈' : '🥉') }}
                        </div>

                        @if ($winnerTeam)
                            <p class="font-bold text-lg">
                                {{ $first->team_name ?? 'Team ' . $first->tournament_join_id }}
                            </p>

                            <p class="text-gray-400 text-sm">
                                {{ $winnerTeam->count() }} players
                            </p>

                            <p class="mt-2 text-yellow-300">
                                {{ $winnerTeam->sum('points') }} pts • {{ $winnerTeam->sum('kills') }} kills
                            </p>
                        @else
                            <p class="text-gray-500">Not assigned</p>
                        @endif
                    </div>
                @endforeach
            </div>


            <!-- FULL TABLE -->
            <!-- FULL RESULTS -->
            @if ($tournament->mode === 'solo')

                {{-- ================= SOLO ================= --}}
                <div class="bg-slate-900 border border-slate-700 rounded-3xl overflow-hidden">
                    <table class="w-full text-sm text-gray-300">
                        <thead class="bg-slate-800 text-gray-400">
                            <tr>
                                <th class="px-4 py-3">Rank</th>
                                <th class="px-4 py-3">Player</th>
                                <th class="px-4 py-3 text-center">Kills</th>
                                <th class="px-4 py-3 text-center">Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($entries as $row)
                                <tr class="border-t border-slate-800">
                                    <td class="px-4 py-3 font-bold">{{ $row->rank ?? '—' }}</td>
                                    <td class="px-4 py-3 font-semibold">{{ $row->player_ign }}</td>
                                    <td class="px-4 py-3 text-center">{{ $row->kills }}</td>
                                    <td class="px-4 py-3 text-center font-bold text-yellow-300">
                                        {{ $row->points }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                {{-- ================= DUO / SQUAD ================= --}}
                @php
                    $teams = $entries->groupBy('tournament_join_id');
                @endphp

                <div class="space-y-10">

                    @foreach ($teams as $joinId => $teamEntries)
                        @php
                            $first = $teamEntries->first();
                            $teamName = $first->team_name ?? 'Team ' . $joinId;
                            $teamPoints = $teamEntries->sum('points');
                            $teamKills = $teamEntries->sum('kills');
                        @endphp

                        <!-- TEAM CARD -->
                        <div class="bg-slate-900 border border-slate-700 rounded-3xl overflow-hidden">

                            <!-- TEAM HEADER -->
                            <div class="px-8 py-6 bg-gradient-to-r from-slate-800 to-slate-900">
                                <h3 class="text-xl font-bold text-cyan-400">
                                    👥 {{ $teamName }}
                                </h3>

                                <p class="text-sm text-gray-400 mt-1">
                                    Total:
                                    <span class="text-yellow-300 font-semibold">
                                        {{ $teamPoints }} pts
                                    </span>
                                    • {{ $teamKills }} kills
                                </p>
                            </div>

                            <!-- PLAYER TABLE -->
                            <table class="w-full text-sm text-gray-300">
                                <thead class="bg-slate-800 text-gray-400">
                                    <tr>
                                        <th class="px-8 py-3">Player</th>
                                        <th class="px-4 py-3 text-center">Kills</th>
                                        <th class="px-4 py-3 text-center">Points</th>
                                        <th class="px-4 py-3 text-center">Rank</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($teamEntries as $player)
                                        <tr class="border-t border-slate-800">
                                            <td class="px-8 py-3 font-semibold">
                                                {{ $player->player_ign }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                {{ $player->kills }}
                                            </td>
                                            <td class="px-4 py-3 text-center font-bold text-yellow-300">
                                                {{ $player->points }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                {{ $player->rank ?? '—' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    @endforeach
                </div>


            @endif


        </div>
    </section>

    <script>
        (() => {
            const btn = document.getElementById('copy-link');
            const input = document.getElementById('share-link');
            const status = document.getElementById('copy-status');
            if (!btn || !input) return;

            btn.addEventListener('click', async () => {
                try {
                    await navigator.clipboard.writeText(input.value);
                    status.classList.remove('hidden');
                    setTimeout(() => status.classList.add('hidden'), 1500);
                } catch (e) {
                    input.select();
                    document.execCommand('copy');
                }
            });
        })();
    </script>
@endsection
