@extends('layouts.dashboard')

@section('title','Analytics – GameConnect')

@section('dashboard-content')

<!-- HEADER -->
<div class="bg-slate-900 border-b border-slate-800 px-8 py-6">
    <h1 class="text-3xl font-bold">📊 Organizer Analytics</h1>
    <p class="text-gray-400">
        Understand growth, revenue & player behavior — make smarter tournaments
    </p>
</div>

<!-- 🔹 KEY METRICS -->
<section class="px-8 py-10 grid md:grid-cols-4 gap-8">

    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
        <p class="text-gray-400">Tournaments Hosted</p>
        <h2 class="text-3xl font-bold">{{ $totalTournaments }}</h2>
        <p class="text-xs text-gray-500">Lifetime</p>
    </div>

    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
        <p class="text-gray-400">Total Participants</p>
        <h2 class="text-3xl font-bold">{{ $totalPlayers }}</h2>
        <p class="text-xs text-gray-500">Across all tournaments</p>
    </div>

    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
        <p class="text-gray-400">Revenue Generated</p>
        <h2 class="text-3xl font-bold text-yellow-300">₹{{ number_format($totalRevenue) }}</h2>
        <p class="text-xs text-gray-500">Approved payments only</p>
    </div>

    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
        <p class="text-gray-400">Approval Success</p>
        <h2 class="text-3xl font-bold text-green-400">{{ $approvedRate }}</h2>
        <p class="text-xs text-gray-500">Approved joins</p>
    </div>

</section>

<!-- 🔹 PERFORMANCE BY GAME -->
<section class="px-8 py-10">
    <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
        <h2 class="text-2xl font-bold mb-6">🎮 Performance by Game</h2>

        <table class="w-full text-left">
            <thead class="bg-slate-800 text-gray-300">
                <tr>
                    <th class="p-4">Game</th>
                    <th class="p-4">Tournaments</th>
                    <th class="p-4">Players</th>
                    <th class="p-4">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse($byGame as $row)
                    <tr class="border-t border-slate-800">
                        <td class="p-4 font-semibold">{{ $row->game }}</td>
                        <td class="p-4">{{ $row->tournaments }}</td>
                        <td class="p-4">{{ $row->players }}</td>
                        <td class="p-4 text-yellow-300">₹{{ number_format($row->revenue) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-6 text-gray-400 text-center">
                            No data yet
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<!-- 🔹 TOP TOURNAMENTS -->
<section class="px-8 py-10 grid md:grid-cols-2 gap-10">

    <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
        <h2 class="text-xl font-bold mb-6">🏆 Best Performing Tournaments</h2>

        <ul class="space-y-4">
            @forelse($topTournaments as $tournament)
                <li class="flex justify-between bg-slate-800 p-4 rounded-xl">
                    <div>
                        <p class="font-semibold">{{ $tournament->title }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $tournament->game }} • {{ ucfirst($tournament->mode) }}
                        </p>
                    </div>
                    <span class="text-cyan-400 font-bold">
                        {{ $tournament->filled_slots }} players
                    </span>
                </li>
            @empty
                <p class="text-gray-400">No tournaments yet</p>
            @endforelse
        </ul>
    </div>

    <!-- ORGANIZER INSIGHTS -->
    <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
        <h2 class="text-xl font-bold mb-6">🧠 Organizer Insights</h2>

        <ul class="space-y-4 text-gray-300">
            <li>• High fill-rate tournaments convert better</li>
            <li>• Paid tournaments perform best in evenings</li>
            <li>• Squads have higher repeat participation</li>
            <li>• Approval speed impacts join success</li>
        </ul>
    </div>

</section>

<!-- 🔹 NEXT ACTIONS -->
<section class="px-8 py-10">
    <div class="bg-gradient-to-r from-cyan-500 to-purple-600 p-8 rounded-3xl text-black">
        <h2 class="text-2xl font-extrabold mb-2">🚀 Grow Faster</h2>
        <p class="mb-6">
            Use analytics to launch better tournaments and increase revenue.
        </p>
        <a href="{{ route('tournaments.create') }}"
           class="inline-block bg-black text-white px-6 py-3 rounded-xl font-bold">
            ➕ Create New Tournament
        </a>
    </div>
</section>

@endsection
