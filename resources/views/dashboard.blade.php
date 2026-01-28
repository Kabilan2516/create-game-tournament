{{-- resources/views/dashboard.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Organizer Dashboard – GameConnect')
@section('page-title', '🎮 Organizer Dashboard')

@section('dashboard-content')

    <!-- 🔹 QUICK STATS -->
    <section class="py-10 px-8 grid md:grid-cols-4 gap-8">
        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
            <p class="text-gray-400">Tournaments Hosted</p>
            <h2 class="text-3xl font-bold">12</h2>
        </div>
        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
            <p class="text-gray-400">Players Joined</p>
            <h2 class="text-3xl font-bold">842</h2>
        </div>
        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
            <p class="text-gray-400">Total Earnings</p>
            <h2 class="text-3xl font-bold text-yellow-300">₹38,500</h2>
        </div>
        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
            <p class="text-gray-400">Organizer Rating</p>
            <h2 class="text-3xl font-bold text-cyan-400">⭐ 4.9</h2>
        </div>
    </section>

    <!-- 🔹 QUICK ACTION BUTTONS -->
    <section class="px-8 grid md:grid-cols-4 gap-6 mb-12">
        <a href="{{ route('tournaments.create') }}"
           class="bg-gradient-to-r from-cyan-500 to-purple-600 p-6 rounded-2xl font-bold text-center">
           ➕ Create New Tournament
        </a>

        <a href="{{ route('tournaments.requests') }}"
           class="bg-slate-800 p-6 rounded-2xl text-center">
           👥 View Join Requests
        </a>

        <a href="#"
           class="bg-slate-800 p-6 rounded-2xl text-center">
           ⭐ Promote Tournament
        </a>

        <a href="#"
           class="bg-slate-800 p-6 rounded-2xl text-center">
           💰 Withdraw Earnings
        </a>
    </section>

    <!-- 🔹 CONTENT GRID -->
    <section class="px-8 grid md:grid-cols-3 gap-10 pb-20">

        <!-- LEFT COLUMN -->
        <div class="md:col-span-2 space-y-10">

            <!-- Live Tournaments -->
            <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                <div class="flex justify-between mb-6">
                    <h2 class="text-2xl font-bold">🔥 Live & Upcoming Tournaments</h2>
                    <a href="{{ route('tournaments.my') }}" class="text-cyan-400">View All</a>
                </div>

                <div class="space-y-5">
                    <div class="flex justify-between bg-slate-800 p-4 rounded-xl">
                        <div>
                            <h4 class="font-bold">Champions Clash</h4>
                            <p class="text-sm text-gray-400">PUBG • Squad • Today 8 PM</p>
                        </div>
                        <div class="text-cyan-400">64 / 100</div>
                    </div>
                </div>
            </div>

            <!-- Activity Feed -->
            <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                <h2 class="text-2xl font-bold mb-6">📰 Recent Activity</h2>
                <ul class="space-y-3 text-gray-300">
                    <li>✅ Team Alpha joined Champions Clash</li>
                    <li>⭐ You received a new 5-star rating</li>
                    <li>💰 ₹500 credited from Night Warriors Cup</li>
                </ul>
            </div>

        </div>

        <!-- RIGHT COLUMN -->
        <div class="space-y-10">

            <!-- Wallet -->
            <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                <h3 class="text-xl font-bold mb-4">💰 Wallet</h3>
                <p class="text-4xl font-bold text-yellow-300">₹12,300</p>
                <p class="text-sm text-gray-400">Available Balance</p>
                <a href="#"
                   class="block mt-4 text-center py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-purple-600">
                   Withdraw Now
                </a>
            </div>

        </div>
    </section>

@endsection
