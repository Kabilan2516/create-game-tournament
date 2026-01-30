@extends('layouts.dashboard')

@section('title', 'Create Tournament Series')

@section('dashboard-content')

    <!-- HEADER -->
    <div class="bg-slate-900 border-b border-slate-800 px-8 py-6">
        <h1 class="text-3xl font-bold">🏆 Create Tournament Series</h1>
        <p class="text-gray-400 mt-1">
            Combine multiple tournaments to calculate overall champions
        </p>
    </div>

    <section class="px-8 py-10 max-w-6xl">

        <form method="POST" action="{{ route('series.store') }}" class="space-y-10">
            @csrf

            <!-- SERIES INFO -->
            <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                <h2 class="text-2xl font-bold mb-6">📌 Series Details</h2>

                <div>
                    <label class="block text-sm text-gray-400 mb-2">
                        Series Name *
                    </label>
                    <input type="text" name="name" required placeholder="Weekly League – January"
                        class="w-full px-5 py-4 rounded-xl bg-slate-800 border border-slate-700
                              focus:ring-2 focus:ring-cyan-500">
                    <p class="text-xs text-gray-400 mt-2">
                        Example: Weekly League, Pro Circuit S1, Winter Championship
                    </p>
                </div>
            </div>

            <!-- TOURNAMENT SELECTION -->
            <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">🎮 Select Tournaments</h2>
                    <span class="text-sm text-gray-400">
                        Only tournaments with published results are shown
                    </span>
                </div>

                @if ($tournaments->isEmpty())
                    <div class="bg-slate-800 p-6 rounded-xl text-gray-400">
                        ❌ No completed tournaments found.
                        Publish match results first.
                    </div>
                @else
                    <div class="space-y-4 max-h-[420px] overflow-y-auto pr-2">

                        @foreach ($tournaments as $tournament)
                            <label
                                class="flex items-center justify-between bg-slate-800
                                   p-5 rounded-2xl border border-slate-700
                                   hover:border-cyan-500 cursor-pointer transition">

                                <div class="flex items-start gap-4">
                                    <input type="checkbox" name="tournament_ids[]" value="{{ $tournament->id }}"
                                        class="mt-1 h-5 w-5 text-cyan-500 rounded
              bg-slate-900 border-slate-600">


                                    <div>
                                        <h3 class="font-bold text-lg">
                                            {{ $tournament->title }}
                                        </h3>
                                        <p class="text-sm text-gray-400">
                                            {{ $tournament->game }} •
                                            {{ strtoupper($tournament->mode) }} •
                                            {{ $tournament->start_time->format('d M Y') }}
                                        </p>
                                    </div>
                                </div>

                                <span class="text-sm text-gray-400">
                                    {{ $tournament->filled_slots }} teams
                                </span>
                            </label>
                        @endforeach

                    </div>

                    <p class="text-xs text-gray-400 mt-4">
                        ⚠ Select at least <b>2 tournaments</b> to create a series
                    </p>
                @endif
            </div>

            <!-- ACTIONS -->
            <div class="flex justify-between items-center">
                <a href="{{ route('series.index') }}"
                    class="px-6 py-3 rounded-xl bg-slate-700 hover:bg-slate-600 font-semibold">
                    ← Back
                </a>

                <button type="submit"
                    class="px-10 py-4 rounded-xl bg-gradient-to-r
                           from-cyan-500 to-purple-600 font-bold text-lg">
                    🚀 Create Series
                </button>
            </div>

        </form>

    </section>

@endsection
