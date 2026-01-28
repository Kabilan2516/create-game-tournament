@extends('layouts.player')

@section('title', 'Player Dashboard – GameConnect')
@section('page-title', '🎮 Player Dashboard')

@section('player-dashboard-content')

<!-- 🔹 QUICK STATS -->
<section class="px-8 py-10 grid md:grid-cols-4 gap-6">

    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
        <p class="text-gray-400 text-sm">Tournaments Joined</p>
        <h2 class="text-3xl font-bold">
            {{ $stats['joined'] ?? 0 }}
        </h2>
    </div>

    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
        <p class="text-gray-400 text-sm">Approved Matches</p>
        <h2 class="text-3xl font-bold text-green-400">
            {{ $stats['approved'] ?? 0 }}
        </h2>
    </div>

    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
        <p class="text-gray-400 text-sm">Pending Requests</p>
        <h2 class="text-3xl font-bold text-yellow-400">
            {{ $stats['pending'] ?? 0 }}
        </h2>
    </div>

    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
        <p class="text-gray-400 text-sm">Matches Played</p>
        <h2 class="text-3xl font-bold text-cyan-400">
            {{ $stats['played'] ?? 0 }}
        </h2>
    </div>

</section>

<!-- 🔹 QUICK ACTIONS -->
<section class="px-8 grid md:grid-cols-4 gap-6 mb-12">

    <a href="{{ route('tournaments.index') }}"
       class="bg-gradient-to-r from-cyan-500 to-purple-600 p-5 rounded-2xl text-center font-bold">
        🔍 Browse Tournaments
    </a>

    <a href="{{ route('player.tournaments') }}"
       class="bg-slate-800 p-5 rounded-2xl text-center">
        🏆 My Tournaments
    </a>

    <a href="{{ route('player.rooms') }}"
       class="bg-slate-800 p-5 rounded-2xl text-center">
        🔐 My Rooms
    </a>

    <a href="{{ route('player.notifications') }}"
       class="bg-slate-800 p-5 rounded-2xl text-center">
        🔔 Notifications
    </a>

</section>

<!-- 🔹 MAIN GRID -->
<section class="px-8 grid md:grid-cols-3 gap-10 pb-20">

    <!-- LEFT COLUMN -->
    <div class="md:col-span-2 space-y-10">

        <!-- UPCOMING MATCHES -->
        <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
            <div class="flex justify-between mb-6">
                <h2 class="text-2xl font-bold">⏰ Upcoming Matches</h2>
                <a href="{{ route('player.tournaments') }}" class="text-cyan-400">View All</a>
            </div>

            @forelse($upcoming as $join)
                <div class="flex justify-between items-center bg-slate-800 p-4 rounded-xl mb-4">
                    <div>
                        <h4 class="font-bold">{{ $join->tournament->title }}</h4>
                        <p class="text-sm text-gray-400">
                            {{ $join->tournament->game }} •
                            {{ ucfirst($join->tournament->mode) }} •
                            {{ $join->tournament->start_time->format('d M, h:i A') }}
                        </p>
                    </div>

                    @if($join->room_visible)
                        <span class="text-green-400 font-semibold">Room Ready</span>
                    @else
                        <span class="text-yellow-400 font-semibold">Waiting</span>
                    @endif
                </div>
            @empty
                <p class="text-gray-400 text-sm">
                    No upcoming matches yet.
                </p>
            @endforelse
        </div>

        <!-- RECENT ACTIVITY -->
        <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
            <h2 class="text-2xl font-bold mb-6">📰 Recent Updates</h2>

            @forelse($activities as $activity)
                <div class="text-gray-300 text-sm mb-3">
                    • {{ $activity }}
                </div>
            @empty
                <p class="text-gray-400 text-sm">
                    No recent activity.
                </p>
            @endforelse
        </div>

    </div>

    <!-- RIGHT COLUMN -->
    <div class="space-y-10">

        <!-- PROFILE SNAPSHOT -->
        <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
            <h3 class="text-xl font-bold mb-4">👤 Player Profile</h3>

            <p class="text-sm text-gray-400">IGN</p>
            <p class="font-semibold mb-3">{{ auth()->user()->ign ?? 'Not set' }}</p>

            <p class="text-sm text-gray-400">Email</p>
            <p class="font-semibold mb-4">{{ auth()->user()->email }}</p>

            <a href="{{ route('player.profile') }}"
               class="block text-center py-3 rounded-xl bg-slate-800 hover:bg-slate-700">
                Edit Profile
            </a>
        </div>

    </div>

</section>

@endsection
