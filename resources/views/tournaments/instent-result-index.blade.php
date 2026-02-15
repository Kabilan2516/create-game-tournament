@extends('layouts.dashboard')

@section('title', 'Result Management')

@section('dashboard-content')

<div class="px-8 py-10 max-w-7xl">

    <!-- HEADER -->
    <div class="mb-10">
        <h1 class="text-3xl font-extrabold mb-2">⚡ Result Management</h1>
        <p class="text-gray-400">
            Create instant results or full tournaments without manual hassle.
        </p>
    </div>

    <div class="grid md:grid-cols-3 gap-8">

        {{-- ================= CODM ================= --}}
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
            <h2 class="text-xl font-bold mb-2">🎮 Call of Duty Mobile</h2>
            <p class="text-sm text-gray-400 mb-6">
                Instant results + leaderboard with MP/KP/PP/TT/CD.
            </p>

            <div class="space-y-4">
                <a href="{{ route('organizer.results.instant.codm') }}"
                   class="block text-center py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 font-bold">
                    ⚡ CODM Instant Result
                </a>

                <a href="{{ route('tournaments.create') }}"
                   class="block text-center py-3 rounded-xl bg-cyan-600 hover:bg-cyan-700 font-bold">
                    🏆 CODM Tournament Creation
                </a>
            </div>
        </div>

        {{-- ================= SERIES ================= --}}
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 md:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold">🏆 CODM Series</h2>
                <a href="{{ route('series.instant.create') }}"
                   class="px-4 py-2 rounded-lg bg-gradient-to-r from-cyan-500 to-purple-600 font-bold">
                    ➕ Create Series
                </a>
            </div>
            <p class="text-sm text-gray-400 mb-4">
                Create a series with fixed match type & points, then add multiple instant matches.
            </p>

            @if(isset($series) && $series->count())
                <div class="space-y-3">
                    @foreach($series as $item)
                        <div class="bg-slate-800 border border-slate-700 rounded-2xl p-4 flex items-center justify-between">
                            <div>
                                <p class="font-bold">{{ $item->title }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ strtoupper($item->mode) }} • {{ $item->match_type ?? '—' }}
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('series.show', $item) }}"
                                   class="px-4 py-2 rounded-lg bg-slate-700 hover:bg-slate-600 font-semibold">
                                    View
                                </a>
                                <a href="{{ route('organizer.results.instant.codm', ['series_id' => $item->id]) }}"
                                   class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 font-semibold">
                                    Add Match
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-sm text-gray-400">No series created yet.</div>
            @endif
        </div>

        {{-- ================= PUBG ================= --}}
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 opacity-60">
            <h2 class="text-xl font-bold mb-2">🎮 PUBG</h2>
            <p class="text-sm text-gray-400 mb-6">
                Instant results & tournaments coming soon.
            </p>

            <div class="space-y-4">
                <button disabled
                    class="w-full py-3 rounded-xl bg-slate-700 cursor-not-allowed font-bold">
                    🚧 Coming Soon
                </button>
            </div>
        </div>

        {{-- ================= FREE FIRE ================= --}}
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 opacity-60">
            <h2 class="text-xl font-bold mb-2">🎮 Free Fire</h2>
            <p class="text-sm text-gray-400 mb-6">
                Instant results & tournaments coming soon.
            </p>

            <div class="space-y-4">
                <button disabled
                    class="w-full py-3 rounded-xl bg-slate-700 cursor-not-allowed font-bold">
                    🚧 Coming Soon
                </button>
            </div>
        </div>

    </div>

</div>

@endsection
