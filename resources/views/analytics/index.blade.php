{{-- resources/views/analytics/index.blade.php --}}
@extends('layouts.dashboard')

@section('title','Analytics – GameConnect')

@section('dashboard-content')

<!-- HEADER -->
<div class="bg-slate-900 border-b border-slate-800 px-8 py-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold">📊 Creator Analytics</h1>
        <p class="text-gray-400">Track performance, growth & earnings like a pro esports organizer</p>
    </div>

    <div class="flex space-x-4">
        <select class="px-4 py-2 rounded bg-slate-800 border border-slate-700">
            <option>Last 7 Days</option>
            <option>Last 30 Days</option>
            <option>This Month</option>
            <option>All Time</option>
        </select>
        <select class="px-4 py-2 rounded bg-slate-800 border border-slate-700">
            <option>All Games</option>
            <option>PUBG</option>
            <option>CODM</option>
            <option>Free Fire</option>
        </select>
    </div>
</div>

<!-- 🔹 KEY METRICS -->
<section class="px-8 py-10 grid md:grid-cols-4 gap-8">

    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
        <p class="text-gray-400">Total Tournaments</p>
        <h2 class="text-3xl font-bold">24</h2>
        <p class="text-sm text-green-400">+4 this month</p>
    </div>

    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
        <p class="text-gray-400">Total Players</p>
        <h2 class="text-3xl font-bold">1,284</h2>
        <p class="text-sm text-green-400">+18% growth</p>
    </div>

    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
        <p class="text-gray-400">Total Revenue</p>
        <h2 class="text-3xl font-bold text-yellow-300">₹72,400</h2>
        <p class="text-sm text-green-400">+₹6,200 this week</p>
    </div>

    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
        <p class="text-gray-400">Average Rating</p>
        <h2 class="text-3xl font-bold text-cyan-400">⭐ 4.8</h2>
        <p class="text-sm text-gray-400">From 312 reviews</p>
    </div>

</section>

<!-- 🔹 GOOGLE ADS – TOP ANALYTICS BANNER -->
<div class="max-w-7xl mx-auto my-6 px-8">
    <div class="bg-slate-800 py-6 text-center rounded border border-dashed border-gray-600">
        <span class="text-gray-400">Google Ad – Analytics Top Banner</span>
    </div>
</div>

<!-- 🔹 CHARTS SECTION -->
<section class="px-8 py-10 grid md:grid-cols-2 gap-10">

    <!-- PLAYER GROWTH CHART -->
    <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
        <h3 class="text-xl font-bold mb-4">📈 Player Growth</h3>
        <div class="h-64 flex items-center justify-center text-gray-400">
            Chart Placeholder (Players Over Time)
        </div>
    </div>

    <!-- REVENUE CHART -->
    <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
        <h3 class="text-xl font-bold mb-4">💰 Revenue Trend</h3>
        <div class="h-64 flex items-center justify-center text-gray-400">
            Chart Placeholder (Revenue Over Time)
        </div>
    </div>

</section>

<!-- 🔹 PERFORMANCE BY GAME -->
<section class="px-8 py-10">
    <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
        <h3 class="text-2xl font-bold mb-6">🎮 Performance by Game</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-800 text-gray-300">
                    <tr>
                        <th class="p-4">Game</th>
                        <th class="p-4">Tournaments</th>
                        <th class="p-4">Players</th>
                        <th class="p-4">Revenue</th>
                        <th class="p-4">Avg Rating</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t border-slate-800">
                        <td class="p-4">PUBG</td>
                        <td class="p-4">12</td>
                        <td class="p-4">642</td>
                        <td class="p-4 text-yellow-300">₹38,200</td>
                        <td class="p-4">⭐ 4.9</td>
                    </tr>
                    <tr class="border-t border-slate-800">
                        <td class="p-4">CODM</td>
                        <td class="p-4">8</td>
                        <td class="p-4">402</td>
                        <td class="p-4 text-yellow-300">₹21,600</td>
                        <td class="p-4">⭐ 4.7</td>
                    </tr>
                    <tr class="border-t border-slate-800">
                        <td class="p-4">Free Fire</td>
                        <td class="p-4">4</td>
                        <td class="p-4">240</td>
                        <td class="p-4 text-yellow-300">₹12,600</td>
                        <td class="p-4">⭐ 4.6</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- 🔹 TOP TOURNAMENTS PERFORMANCE -->
<section class="px-8 py-10 grid md:grid-cols-2 gap-10">

    <!-- TOP EARNING TOURNAMENTS -->
    <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
        <h3 class="text-xl font-bold mb-6">🏆 Top Earning Tournaments</h3>

        <ul class="space-y-4">
            <li class="flex justify-between bg-slate-800 p-4 rounded-xl">
                <span>Champions Clash</span>
                <span class="text-yellow-300">₹8,400</span>
            </li>
            <li class="flex justify-between bg-slate-800 p-4 rounded-xl">
                <span>Night Warriors Cup</span>
                <span class="text-yellow-300">₹6,100</span>
            </li>
            <li class="flex justify-between bg-slate-800 p-4 rounded-xl">
                <span>Pro League Finals</span>
                <span class="text-yellow-300">₹5,300</span>
            </li>
        </ul>
    </div>

    <!-- PLAYER ENGAGEMENT -->
    <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
        <h3 class="text-xl font-bold mb-6">👥 Player Engagement</h3>

        <div class="space-y-4 text-gray-300">
            <p>Avg Players per Tournament: <span class="font-bold text-white">78</span></p>
            <p>Repeat Players Rate: <span class="font-bold text-cyan-400">42%</span></p>
            <p>Join → Play Conversion: <span class="font-bold text-green-400">86%</span></p>
            <p>Drop-out Rate: <span class="font-bold text-red-400">6%</span></p>
        </div>
    </div>

</section>

<!-- 🔹 GOOGLE ADS – BOTTOM ANALYTICS BANNER -->
<div class="max-w-7xl mx-auto my-10 px-8">
    <div class="bg-slate-800 py-8 text-center rounded border border-dashed border-gray-600">
        <span class="text-gray-400">Google Ad – Analytics Bottom Banner</span>
    </div>
</div>

@endsection
