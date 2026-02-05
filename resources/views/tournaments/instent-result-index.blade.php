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
                Instantly create match results or full tournaments.
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
