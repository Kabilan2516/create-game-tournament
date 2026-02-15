@extends('layouts.app')

@section('title', 'Browse Series – GameConnect')

@section('content')
    <section class="bg-gradient-to-br from-slate-900 via-emerald-900 to-cyan-900 py-24">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h1 class="text-5xl font-extrabold mb-4">🏆 Browse Series</h1>
            <p class="text-gray-200 max-w-2xl mx-auto">
                Track ongoing CODM/PUBG series with overall standings, completed matches, and live result progression.
            </p>
        </div>
    </section>

    <div class="bg-slate-900 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-6 flex flex-wrap items-center gap-6 py-4">
            <a href="{{ route('tournaments.index') }}"
                class="font-semibold text-gray-400 hover:text-cyan-300 pb-1">
                🎮 Tournaments
            </a>

            <a href="{{ route('series.public.index') }}"
                class="font-semibold text-white border-b-2 border-cyan-400 pb-1">
                🏆 Series
            </a>
        </div>
    </div>

    <section class="bg-slate-950 py-10 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-6">
            <form method="GET" class="grid md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="text-sm text-gray-400">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Series name..."
                        class="w-full mt-1 px-4 py-2 rounded bg-slate-800 border border-slate-700">
                </div>

                <div>
                    <label class="text-sm text-gray-400">Game</label>
                    <select name="game" class="w-full mt-1 px-4 py-2 rounded bg-slate-800 border border-slate-700">
                        <option value="all">All Games</option>
                        <option value="CODM" @selected(request('game') == 'CODM')>CODM</option>
                        <option value="PUBG" @selected(request('game') == 'PUBG')>PUBG</option>
                    </select>
                </div>

                <div>
                    <label class="text-sm text-gray-400">Mode</label>
                    <select name="mode" class="w-full mt-1 px-4 py-2 rounded bg-slate-800 border border-slate-700">
                        <option value="all">All Modes</option>
                        <option value="solo" @selected(request('mode') == 'solo')>Solo</option>
                        <option value="duo" @selected(request('mode') == 'duo')>Duo</option>
                        <option value="squad" @selected(request('mode') == 'squad')>Squad</option>
                    </select>
                </div>

                <div>
                    <button class="w-full bg-gradient-to-r from-cyan-500 to-emerald-600 py-3 rounded-xl font-bold hover:opacity-90">
                        🔍 Filter
                    </button>
                </div>
            </form>
        </div>
    </section>

    <section class="py-24 bg-black">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid md:grid-cols-3 gap-8">
                @forelse($series as $item)
                    @php
                        $completedMatches = $item->tournaments->filter(fn($t) => !is_null($t->matchResult))->count();
                        $totalMatches = (int) $item->tournaments_count;
                        $progress = $totalMatches > 0 ? round(($completedMatches / $totalMatches) * 100) : 0;
                    @endphp

                    <article class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
                        <div class="mb-4 rounded-xl overflow-hidden border border-slate-800 h-40 bg-slate-800">
                            @if ($item->banner)
                                <img src="{{ $item->banner->url }}" alt="{{ $item->title }} banner" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-slate-800 via-cyan-900/40 to-emerald-900/40"></div>
                            @endif
                        </div>

                        <div class="flex items-start justify-between gap-2 mb-3">
                            <h2 class="text-xl font-bold text-white leading-tight">{{ $item->title }}</h2>
                            <span class="text-xs px-2 py-1 rounded-full bg-slate-800 text-cyan-300 uppercase">
                                {{ $item->mode ?? 'N/A' }}
                            </span>
                        </div>

                        @if ($item->subtitle)
                            <p class="text-sm text-gray-300 mb-3">{{ $item->subtitle }}</p>
                        @endif

                        <div class="space-y-1 text-sm text-gray-400 mb-4">
                            <p>Game: <span class="text-gray-200">{{ $item->game ?? 'CODM' }}</span></p>
                            <p>Organizer: <span class="text-gray-200">{{ $item->organizer?->name ?? 'Unknown' }}</span></p>
                            <p>Substitutes/Team: <span class="text-gray-200">{{ (int) ($item->substitute_count ?? 0) }}</span></p>
                            <p>Matches Done: <span class="text-green-400">{{ $completedMatches }}</span>/{{ $totalMatches }}</p>
                            <p>Prize Pool: <span class="text-yellow-300">₹{{ number_format((float) $item->prize_total, 0) }}</span></p>
                        </div>

                        <div class="w-full bg-slate-800 rounded-full h-2 mb-4">
                            <div class="bg-gradient-to-r from-emerald-500 to-cyan-500 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <a href="{{ route('series.public.show', $item) }}"
                                class="inline-flex w-full justify-center px-4 py-2 rounded-lg bg-cyan-600 hover:bg-cyan-700 font-semibold">
                                Details
                            </a>
                            <a href="{{ route('series.results.public', $item) }}"
                                class="inline-flex w-full justify-center px-4 py-2 rounded-lg bg-slate-700 hover:bg-slate-600 font-semibold">
                                Results
                            </a>
                        </div>
                    </article>
                @empty
                    <p class="text-gray-400 col-span-3 text-center">No series found right now.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="py-12 bg-slate-950">
        <div class="max-w-7xl mx-auto px-6 flex justify-center">
            {{ $series->links() }}
        </div>
    </section>
@endsection
