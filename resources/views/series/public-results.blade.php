@extends('layouts.app')

@section('title', 'Series Results – ' . $series->title)

@section('content')
<section class="bg-black py-16">
    <div class="max-w-6xl mx-auto px-6">

        <!-- HEADER -->
        <div class="mb-10">
            <h1 class="text-4xl font-extrabold mb-2">🏆 Series Results</h1>
            <p class="text-gray-400">
                {{ $series->title }} • {{ strtoupper($series->mode) }}
            </p>
            <p class="text-sm text-gray-500 mt-1">
                Matches Combined: {{ $completedMatches }}
            </p>
        </div>

        <!-- LEADERBOARD -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold">Overall Standings</h2>
                <div class="text-xs text-gray-400">MP / KP / PP / TT / CD</div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-gray-300">
                    <thead class="bg-slate-800 text-gray-400">
                        <tr>
                            <th class="px-3 py-3">POS</th>
                            <th class="px-3 py-3">Team / Player</th>
                            <th class="px-3 py-3 text-center">MP</th>
                            <th class="px-3 py-3 text-center">KP</th>
                            <th class="px-3 py-3 text-center">PP</th>
                            <th class="px-3 py-3 text-center">TT</th>
                            <th class="px-3 py-3 text-center">CD</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leaderboard as $index => $row)
                            <tr class="border-t border-slate-800">
                                <td class="px-3 py-3 font-bold">{{ $index + 1 }}</td>
                                <td class="px-3 py-3 font-semibold">{{ $row['name'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['mp'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['kp'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['pp'] }}</td>
                                <td class="px-3 py-3 text-center text-yellow-300 font-semibold">{{ $row['tt'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['cd'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>
@endsection
