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

@endsection
