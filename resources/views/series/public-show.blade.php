@extends('layouts.app')

@section('title', $series->title . ' – Series Details')

@section('content')
    <section class="relative py-20 border-b border-slate-800 overflow-hidden">
        @if ($series->banner)
            <img src="{{ $series->banner->url }}" alt="{{ $series->title }} banner" class="absolute inset-0 w-full h-full object-cover opacity-30">
            <div class="absolute inset-0 bg-black/65"></div>
        @else
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-cyan-900 to-slate-900"></div>
        @endif

        <div class="relative max-w-6xl mx-auto px-6">
            <p class="text-cyan-300 text-sm mb-2">Series Details</p>
            <h1 class="text-4xl font-extrabold mb-3">{{ $series->title }}</h1>
            @if ($series->subtitle)
                <p class="text-gray-200 mb-2">{{ $series->subtitle }}</p>
            @endif
            <p class="text-gray-300 text-sm">
                {{ strtoupper($series->mode ?? 'N/A') }} • {{ $series->game ?? 'CODM' }} • Organizer: {{ $series->organizer?->name ?? 'Unknown' }}
            </p>
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="{{ route('series.join.form', $series) }}"
                    class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 font-semibold">
                    Join Series
                </a>
                <a href="{{ route('series.results.public', $series) }}"
                    class="px-4 py-2 rounded-lg bg-cyan-600 hover:bg-cyan-700 font-semibold">
                    View Overall Results
                </a>
                <a href="{{ route('series.public.index') }}"
                    class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 font-semibold border border-slate-700">
                    Back To Series
                </a>
            </div>
        </div>
    </section>

    @if (session('success'))
        <section class="bg-black pt-6">
            <div class="max-w-6xl mx-auto px-6">
                <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-emerald-200 text-sm">
                    {{ session('success') }}
                </div>
            </div>
        </section>
    @endif

    <section class="bg-black py-10 border-b border-slate-800">
        <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
                <p class="text-xs text-gray-400">Matches Done</p>
                <p class="text-2xl font-bold text-green-400">{{ $completedMatches }}/{{ $totalMatches }}</p>
            </div>
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
                <p class="text-xs text-gray-400">Match Type</p>
                <p class="text-2xl font-bold">{{ $series->match_type ?? '-' }}</p>
            </div>
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
                <p class="text-xs text-gray-400">Map</p>
                <p class="text-2xl font-bold">{{ $series->map ?? '-' }}</p>
            </div>
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
                <p class="text-xs text-gray-400">Region</p>
                <p class="text-2xl font-bold">{{ $series->region ?? '-' }}</p>
            </div>
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
                <p class="text-xs text-gray-400">Entry Fee</p>
                <p class="text-2xl font-bold">{{ $series->is_paid ? ('₹' . number_format((float) $series->entry_fee, 0)) : 'FREE' }}</p>
            </div>
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
                <p class="text-xs text-gray-400">Substitutes/Team</p>
                <p class="text-2xl font-bold">{{ (int) ($series->substitute_count ?? 0) }}</p>
            </div>
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
                <p class="text-xs text-gray-400">Prize Pool</p>
                <p class="text-2xl font-bold text-yellow-300">₹{{ number_format((float) $series->prize_total, 0) }}</p>
            </div>
        </div>
    </section>

    <section class="bg-black py-8">
        <div class="max-w-6xl mx-auto px-6 grid lg:grid-cols-2 gap-6">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                <h2 class="text-xl font-bold mb-3">Series Information</h2>
                <div class="space-y-2 text-sm text-gray-300">
                    <p>Start Date: <span class="text-white">{{ optional($series->start_date)->format('d M Y') ?? '-' }}</span></p>
                    <p>End Date: <span class="text-white">{{ optional($series->end_date)->format('d M Y') ?? '-' }}</span></p>
                    <p>Reward Type: <span class="text-white">{{ str_replace('_', ' ', strtoupper((string) $series->reward_type)) }}</span></p>
                    <p>Points Per Kill: <span class="text-white">{{ (int) ($series->kill_point ?? 0) }}</span></p>
                    <p>Substitutes Per Team: <span class="text-white">{{ (int) ($series->substitute_count ?? 0) }}</span></p>
                </div>

                @if ($series->description)
                    <div class="mt-4 border-t border-slate-800 pt-4">
                        <h3 class="font-semibold mb-2">Description</h3>
                        <p class="text-gray-300 whitespace-pre-line">{{ $series->description }}</p>
                    </div>
                @endif

                @if ($series->rules)
                    <div class="mt-4 border-t border-slate-800 pt-4">
                        <h3 class="font-semibold mb-2">Rules</h3>
                        <p class="text-gray-300 whitespace-pre-line">{{ $series->rules }}</p>
                    </div>
                @endif
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                <h2 class="text-xl font-bold mb-3">Scoring System</h2>
                <div class="mb-4 text-sm text-gray-300">
                    Kill Point = <span class="text-white font-semibold">{{ (int) ($series->kill_point ?? 0) }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-gray-300">
                        <thead class="bg-slate-800 text-gray-400">
                            <tr>
                                <th class="px-3 py-2 text-left">Rank</th>
                                <th class="px-3 py-2 text-left">Placement Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $placement = collect($series->placement_points ?? [])->sortKeys();
                            @endphp
                            @forelse ($placement as $position => $points)
                                <tr class="border-t border-slate-800">
                                    <td class="px-3 py-2">#{{ $position }}</td>
                                    <td class="px-3 py-2">{{ $points }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-3 py-4 text-center text-gray-400">No placement points configured.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($series->prizes->isNotEmpty())
                    <div class="mt-5">
                        <h3 class="font-semibold mb-2">Prize Breakdown</h3>
                        <div class="space-y-2">
                            @foreach ($series->prizes as $prize)
                                <div class="flex items-center justify-between text-sm bg-slate-800 border border-slate-700 rounded-lg px-3 py-2">
                                    <span>Position {{ $prize->position }}</span>
                                    <span class="text-yellow-300 font-semibold">₹{{ number_format((float) $prize->amount, 0) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="bg-black py-8">
        <div class="max-w-6xl mx-auto px-6">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                <h2 class="text-xl font-bold mb-4">Series Match Timeline</h2>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-gray-300">
                        <thead class="bg-slate-800 text-gray-400">
                            <tr>
                                <th class="px-3 py-3 text-left">#</th>
                                <th class="px-3 py-3 text-left">Match</th>
                                <th class="px-3 py-3 text-left">Date</th>
                                <th class="px-3 py-3 text-left">Status</th>
                                <th class="px-3 py-3 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($series->tournaments as $index => $match)
                                @php $isDone = !is_null($match->matchResult); @endphp
                                <tr class="border-t border-slate-800">
                                    <td class="px-3 py-3">M{{ $index + 1 }}</td>
                                    <td class="px-3 py-3 font-semibold">{{ $match->title }}</td>
                                    <td class="px-3 py-3">{{ optional($match->start_time)->format('d M Y, h:i A') }}</td>
                                    <td class="px-3 py-3">
                                        @if ($isDone)
                                            <span class="text-green-400">Completed</span>
                                        @else
                                            <span class="text-yellow-300">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3">
                                        @if ($isDone)
                                            <a href="{{ route('tournaments.results.show', $match) }}" class="text-cyan-300 hover:text-cyan-200">View Match Result</a>
                                        @else
                                            <span class="text-gray-500">Not published</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-6 text-center text-gray-400">No matches added yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
