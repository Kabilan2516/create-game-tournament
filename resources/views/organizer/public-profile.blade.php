@extends('layouts.app')

@section('title', $organizer->name.' – Organizer Profile')

@section('content')

<!-- 🔹 HERO / COVER -->
<section class="relative h-72 bg-gradient-to-r from-purple-900 via-slate-900 to-cyan-900">
    <div class="absolute inset-0 bg-black/60"></div>

    <div class="relative max-w-7xl mx-auto px-6 h-full flex items-end pb-8">

        <div class="flex items-center space-x-6">

            <!-- Avatar -->
            <img src="https://ui-avatars.com/api/?name={{ urlencode($organizer->name) }}&background=0F172A&color=22D3EE&size=128"
                 class="w-28 h-28 rounded-full border-4 border-cyan-400 shadow-xl">

            <!-- Info -->
            <div>
                <h1 class="text-3xl font-extrabold flex items-center space-x-2">
                    <span>{{ $organizer->name }}</span>

                    @if($organizer->is_verified ?? false)
                        <span class="px-2 py-1 bg-green-600 text-xs rounded-full">✔ Verified</span>
                    @endif
                </h1>

                <p class="text-gray-300 mt-1">
                    {{ $organizer->tagline ?? 'Professional Esports Tournament Organizer' }}
                </p>

                <div class="flex items-center space-x-4 mt-3">
                    <span class="text-yellow-300">⭐ {{ $organizer->rating ?? '4.9' }} / 5</span>
                    <span class="text-gray-400">• Member since {{ $organizer->created_at->format('Y') }}</span>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- 🔹 STATS BAR -->
<section class="bg-black py-10 border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-4 gap-8 text-center">

        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
            <p class="text-gray-400">🏆 Tournaments</p>
            <h2 class="text-3xl font-bold">{{ $totalTournaments }}</h2>
        </div>

        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
            <p class="text-gray-400">👥 Players Joined</p>
            <h2 class="text-3xl font-bold">{{ $totalPlayers }}</h2>
        </div>

        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
            <p class="text-gray-400">💰 Prize Distributed</p>
            <h2 class="text-3xl font-bold text-yellow-300">₹{{ number_format($totalPrize) }}</h2>
        </div>

        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
            <p class="text-gray-400">⭐ Rating</p>
            <h2 class="text-3xl font-bold text-cyan-400">{{ $organizer->rating ?? '4.9' }}</h2>
        </div>

    </div>
</section>

<!-- 🔹 MAIN GRID -->
<section class="py-20 bg-black">
    <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-3 gap-12">

        <!-- 🔹 LEFT SIDE -->
        <div class="md:col-span-2 space-y-12">

            <!-- ABOUT -->
            <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                <h2 class="text-2xl font-bold mb-4">👤 About Organizer</h2>
                <p class="text-gray-300 leading-relaxed">
                    {{ $organizer->bio ?? 'This organizer is a professional esports host known for fair play, instant payouts and high quality tournaments.' }}
                </p>
            </div>

            <!-- UPCOMING TOURNAMENTS -->
            <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                <h2 class="text-2xl font-bold mb-6">🔥 Upcoming Tournaments</h2>

                @if($upcoming->count())
                    <div class="grid md:grid-cols-2 gap-6">
                        @foreach($upcoming as $tournament)
                            <div class="bg-slate-800 p-4 rounded-xl flex justify-between items-center">
                                <div>
                                    <p class="font-semibold">{{ $tournament->title }}</p>
                                    <p class="text-sm text-gray-400">
                                        {{ $tournament->start_time->format('d M, h:i A') }}
                                    </p>
                                </div>
                                <a href="{{ route('tournaments.show', $tournament) }}"
                                   class="px-4 py-2 bg-cyan-500 rounded-lg font-bold text-black">
                                    View
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400">No upcoming tournaments right now.</p>
                @endif
            </div>

            <!-- COMPLETED -->
            <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                <h2 class="text-2xl font-bold mb-6">🏁 Recently Completed</h2>

                <div class="space-y-3">
                    @foreach($completed as $t)
                        <div class="flex justify-between bg-slate-800 p-4 rounded-xl">
                            <span>{{ $t->title }}</span>
                            <span class="text-yellow-300">₹{{ $t->prize_pool }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- 🔹 RIGHT SIDE -->
        <div class="space-y-10">

            <!-- SOCIAL LINKS -->
            <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                <h3 class="text-xl font-bold mb-6">🌐 Community & Social</h3>

                <div class="space-y-4">

                    @if($organizer->youtube_url)
                        <a href="{{ $organizer->youtube_url }}" target="_blank"
                           class="flex items-center space-x-4 bg-red-600/10 hover:bg-red-600/20 p-4 rounded-xl">
                            ▶️ <span>YouTube Channel</span>
                        </a>
                    @endif

                    @if($organizer->discord_url)
                        <a href="{{ $organizer->discord_url }}" target="_blank"
                           class="flex items-center space-x-4 bg-indigo-600/10 hover:bg-indigo-600/20 p-4 rounded-xl">
                            💬 <span>Discord Community</span>
                        </a>
                    @endif

                    @if($organizer->instagram_url)
                        <a href="{{ $organizer->instagram_url }}" target="_blank"
                           class="flex items-center space-x-4 bg-pink-600/10 hover:bg-pink-600/20 p-4 rounded-xl">
                            📸 <span>Instagram</span>
                        </a>
                    @endif

                </div>
            </div>

            <!-- BADGES -->
            <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                <h3 class="text-xl font-bold mb-6">🏅 Achievements</h3>

                <div class="grid grid-cols-3 gap-4 text-center text-sm">
                    <div class="bg-slate-800 p-4 rounded-xl">🔥 100+ Matches</div>
                    <div class="bg-slate-800 p-4 rounded-xl">🛡 Trusted</div>
                    <div class="bg-slate-800 p-4 rounded-xl">⚡ Fast Payout</div>
                </div>
            </div>

        </div>

    </div>
</section>

@endsection
