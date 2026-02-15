@extends('layouts.dashboard')

@section('title', $series->title . ' – Series Overview')

@section('dashboard-content')

<!-- HEADER -->
<div class="bg-slate-900 border-b border-slate-800 px-8 py-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold">🏆 {{ $series->title }}</h1>
            <p class="text-gray-400 mt-1">
                {{ strtoupper($series->mode) }} •
                {{ $series->start_date->format('d M Y') }} →
                {{ $series->end_date->format('d M Y') }}
            </p>
        </div>

        <a href="{{ route('series.index') }}"
           class="px-6 py-3 rounded-xl bg-slate-700 hover:bg-slate-600">
            ← Back to Series
        </a>
    </div>
</div>

<!-- QUICK STATS -->
<section class="px-8 py-10 grid md:grid-cols-4 gap-8">

    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
        <p class="text-gray-400">Tournaments</p>
        <h2 class="text-3xl font-bold">{{ $series->tournaments->count() }}</h2>
    </div>

    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
        <p class="text-gray-400">Total Teams</p>
        <h2 class="text-3xl font-bold">
            {{ $totalTeams ?? '—' }}
        </h2>
    </div>

    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
        <p class="text-gray-400">Completed Matches</p>
        <h2 class="text-3xl font-bold">
            {{ $completedMatches ?? '—' }}
        </h2>
    </div>

    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
        <p class="text-gray-400">Series Status</p>
        <h2 class="text-2xl font-bold text-green-400">
            {{ now()->gt($series->end_date) ? 'Completed' : 'Ongoing' }}
        </h2>
    </div>

</section>

<!-- MAIN GRID -->
<section class="px-8 grid md:grid-cols-3 gap-10 pb-20">

    <!-- LEFT: DETAILS -->
    <div class="md:col-span-2 space-y-10">

        <!-- TOURNAMENT LIST -->
        <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
            <h2 class="text-2xl font-bold mb-6">🎮 Tournaments in This Series</h2>

            <div class="space-y-4">
                @foreach($series->tournaments as $tournament)
                    <div class="flex justify-between bg-slate-800 p-4 rounded-xl">
                        <div>
                            <h4 class="font-bold">{{ $tournament->title }}</h4>
                            <p class="text-sm text-gray-400">
                                {{ $tournament->game }} •
                                {{ strtoupper($tournament->mode) }} •
                                {{ $tournament->start_time->format('d M Y') }}
                            </p>
                        </div>

                        <span class="text-cyan-400 font-semibold">
                            {{ $tournament->filled_slots }} Teams
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- COMBINED LEADERBOARD -->
        <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
            <h2 class="text-2xl font-bold mb-6">🏅 Combined Leaderboard</h2>

            @if(empty($leaderboard))
                <p class="text-gray-400">
                    Results not calculated yet.
                    Publish match results for all tournaments.
                </p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-800 text-gray-300">
                            <tr>
                                <th class="p-4">Rank</th>
                                <th class="p-4">Team / Player</th>
                                <th class="p-4">Matches</th>
                                <th class="p-4">Kills</th>
                                <th class="p-4">Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaderboard as $index => $row)
                                <tr class="border-t border-slate-800">
                                    <td class="p-4 font-bold">
                                        #{{ $index + 1 }}
                                    </td>
                                    <td class="p-4">
                                        {{ $row['name'] }}
                                    </td>
                                    <td class="p-4">
                                        {{ $row['matches'] }}
                                    </td>
                                    <td class="p-4">
                                        {{ $row['kills'] }}
                                    </td>
                                    <td class="p-4 font-bold text-yellow-300">
                                        {{ $row['points'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>

    <!-- RIGHT: ACTIONS & INFO -->
    <div class="space-y-8">

        <!-- SERIES INFO -->
        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
            <h3 class="font-bold mb-4">📌 Series Info</h3>

            <ul class="text-sm text-gray-400 space-y-2">
                <li>• Game: {{ $series->game ?? 'CODM' }}</li>
                <li>• Mode: {{ strtoupper($series->mode) }}</li>
                <li>• Match Type: {{ $series->match_type ?? '—' }}</li>
                <li>• Map: {{ $series->map ?? '—' }}</li>
                <li>• Reward: {{ strtoupper($series->reward_type ?? 'FREE') }}</li>
                <li>• Prize Pool: ₹{{ number_format($series->prize_total) }}</li>
                <li>• Organizer-controlled series</li>
                <li>• Points combined across tournaments</li>
                <li>• Ranking auto-calculated</li>
            </ul>
        </div>

        @php
            $seriesPrizes = $series->prizes()->orderBy('position')->get();
        @endphp
        @if ($seriesPrizes->isNotEmpty())
            <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
                <h3 class="font-bold mb-4">🏆 Series Prizes</h3>
                <div class="space-y-2 text-sm text-gray-300">
                    @foreach ($seriesPrizes as $prize)
                        <div class="flex justify-between bg-slate-800 rounded-xl px-4 py-2">
                            <span>#{{ $prize->position }}</span>
                            <span class="text-yellow-300 font-semibold">₹{{ $prize->amount }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- FUTURE ACTIONS -->
        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 space-y-4">
            <h3 class="font-bold">⚙️ Actions</h3>

            <a href="{{ route('organizer.results.instant.codm', ['series_id' => $series->id]) }}"
               class="w-full block text-center py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 font-semibold">
                ➕ Insert Match Data
            </a>

            <button class="w-full py-3 rounded-xl bg-slate-700 hover:bg-slate-600">
                📥 Export Leaderboard (CSV)
            </button>

            <button class="w-full py-3 rounded-xl bg-slate-700 hover:bg-slate-600">
                📊 View Detailed Analytics
            </button>
        </div>

    </div>

</section>

@endsection
