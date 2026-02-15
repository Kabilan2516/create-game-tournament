{{-- resources/views/tournaments/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Browse Tournaments – GameConnect')

@section('content')

    <!-- 🔹 HERO HEADER -->
    <section class="bg-gradient-to-br from-slate-900 via-purple-900 to-cyan-900 py-24">
        <div class="max-w-7xl mx-auto px-6 text-center fade-up">
            <h1 class="text-5xl font-extrabold mb-4">🎮 Browse Tournaments</h1>
            <p class="text-gray-200 max-w-2xl mx-auto">Discover live & upcoming CODM and PUBG matches. Join battles, win
                prizes, and climb the leaderboard.</p>
        </div>
    </section>
    <div class="bg-slate-950/90 border-b border-slate-800 backdrop-blur">
        <div class="max-w-7xl mx-auto px-6 py-4 flex flex-wrap items-center justify-between gap-4">
            <div class="inline-flex items-center gap-2 bg-slate-900 border border-slate-700 rounded-xl p-1">
                <a href="{{ route('tournaments.index') }}"
                    class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('tournaments.index') ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white shadow-lg shadow-cyan-500/20' : 'text-gray-300 hover:text-white hover:bg-slate-800' }}">
                    🎮 Tournaments
                </a>
                <a href="{{ route('series.public.index') }}"
                    class="px-4 py-2 rounded-lg text-sm font-semibold transition text-gray-300 hover:text-white hover:bg-slate-800">
                    🏆 Series
                </a>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('tournaments.index', ['tab' => 'upcoming']) }}"
                    class="px-3 py-2 rounded-lg text-sm font-semibold border transition {{ request('tab', 'upcoming') == 'upcoming' ? 'bg-cyan-500/15 border-cyan-400 text-cyan-300' : 'bg-slate-900 border-slate-700 text-gray-300 hover:border-cyan-500/40 hover:text-cyan-300' }}">
                    ⏳ Upcoming
                </a>

                <a href="{{ route('tournaments.index', ['tab' => 'ongoing']) }}"
                    class="px-3 py-2 rounded-lg text-sm font-semibold border transition {{ request('tab') == 'ongoing' ? 'bg-red-500/15 border-red-400 text-red-300' : 'bg-slate-900 border-slate-700 text-gray-300 hover:border-red-500/40 hover:text-red-300' }}">
                    🔴 Ongoing
                </a>

                <a href="{{ route('tournaments.index', ['tab' => 'completed']) }}"
                    class="px-3 py-2 rounded-lg text-sm font-semibold border transition {{ request('tab') == 'completed' ? 'bg-emerald-500/15 border-emerald-400 text-emerald-300' : 'bg-slate-900 border-slate-700 text-gray-300 hover:border-emerald-500/40 hover:text-emerald-300' }}">
                    ✅ Completed
                </a>
            </div>
        </div>
    </div>
    <!-- 🔹 FILTER + SEARCH BAR -->
    <section class="bg-slate-950 py-10 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-6">
            <form method="GET" class="grid md:grid-cols-5 gap-4 items-end">

                <!-- Search -->
                <div>
                    <label class="text-sm text-gray-400">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tournament name…"
                        class="w-full mt-1 px-4 py-2 rounded bg-slate-800 border border-slate-700">
                </div>

                <!-- Game -->
                <div>
                    <label class="text-sm text-gray-400">Game</label>
                    <select name="game" class="w-full mt-1 px-4 py-2 rounded bg-slate-800 border border-slate-700">
                        <option value="all">All Games</option>
                        <option value="CODM" @selected(request('game') == 'CODM')>CODM</option>
                        <option value="PUBG" @selected(request('game') == 'PUBG')>PUBG</option>
                    </select>
                </div>

                <!-- Mode -->
                <div>
                    <label class="text-sm text-gray-400">Mode</label>
                    <select name="mode" class="w-full mt-1 px-4 py-2 rounded bg-slate-800 border border-slate-700">
                        <option value="all">All Modes</option>
                        <option value="Solo" @selected(request('mode') == 'Solo')>Solo</option>
                        <option value="Duo" @selected(request('mode') == 'Duo')>Duo</option>
                        <option value="Squad" @selected(request('mode') == 'Squad')>Squad</option>
                    </select>
                </div>
                <!-- Date Filter -->
                <div>
                    <label class="text-sm text-gray-400">Date</label>
                    <select name="date" class="w-full mt-1 px-4 py-2 rounded bg-slate-800 border border-slate-700">
                        <option value="">Any Time</option>
                        <option value="today" @selected(request('date') == 'today')>Today</option>
                        <option value="week" @selected(request('date') == 'week')>This Week</option>
                    </select>
                </div>
                <!-- Type -->
                <div>
                    <label class="text-sm text-gray-400">Type</label>
                    <select name="type" class="w-full mt-1 px-4 py-2 rounded bg-slate-800 border border-slate-700">
                        <option value="all">All</option>
                        <option value="free" @selected(request('type') == 'free')>Free</option>
                        <option value="paid" @selected(request('type') == 'paid')>Paid</option>
                    </select>
                </div>

                <!-- Submit -->
                <div>
                    <button
                        class="w-full bg-gradient-to-r from-cyan-500 to-purple-600 py-3 rounded-xl font-bold hover:opacity-90">
                        🔍 Filter
                    </button>
                </div>

            </form>

        </div>
    </section>

    <!-- 🔹 ADS SLOT (TOP BANNER) -->
    <x-ad-slot page="tournaments" position="header" />

    <!-- 🔹 TOURNAMENT GRID -->
    <section class="py-24 bg-black">
        <div class="max-w-7xl mx-auto px-6">

            <div class="grid md:grid-cols-3 gap-12">

                @forelse($tournaments as $tournament)
                    <x-tournament-card :tournament="$tournament" />
                @empty
                    <p class="text-gray-400 col-span-3 text-center">
                        😔 No tournaments found for this filter.
                    </p>
                @endforelse

            </div>

        </div>
    </section>

    <!-- 🔹 ADS SLOT (TOP BANNER) -->
    <x-ad-slot page="tournaments" position="header" />

    <!-- 🔹 PAGINATION -->
    <section class="py-16 bg-slate-950">
        <div class="mt-16 flex justify-center">
            {{ $tournaments->links() }}
        </div>

    </section>

    <!-- 🔹 FOOTER ADS SAFE GAP -->
    <div class="h-20"></div>

@endsection
