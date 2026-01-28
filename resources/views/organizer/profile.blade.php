{{-- resources/views/organizer/profile.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Organizer Profile – GameConnect')

@section('dashboard-content')

    <!-- HEADER / COVER -->
    <div class="relative h-60 bg-gradient-to-r from-purple-900 via-slate-900 to-cyan-900">
        <div class="absolute inset-0 bg-black/40"></div>

        <div class="relative max-w-7xl mx-auto px-8 h-full flex items-end pb-6">
            <div class="flex items-center space-x-6">
                <img src="https://ui-avatars.com/api/?name=EliteArena&background=0F172A&color=22D3EE&size=128"
                    class="w-28 h-28 rounded-full border-4 border-cyan-400 shadow-xl">

                <div>
                    <h1 class="text-3xl font-extrabold">EliteArena</h1>
                    <p class="text-gray-300">Verified Tournament Organizer</p>
                    <div class="flex items-center space-x-4 mt-2">
                        <span class="px-3 py-1 rounded-full bg-green-600 text-sm">✔ Verified</span>
                        <span class="text-yellow-300">⭐ 4.9 (312 Reviews)</span>
                    </div>
                </div>
            </div>

            <div class="ml-auto">
                <a href="#" class="px-6 py-3 rounded-xl font-bold bg-gradient-to-r from-cyan-500 to-purple-600">
                    ✏️ Edit Profile
                </a>
            </div>
        </div>
    </div>

    <!-- 🔹 PROFILE STATS -->
    <section class="px-8 py-10 grid md:grid-cols-4 gap-8">
        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 text-center">
            <p class="text-gray-400">Tournaments Hosted</p>
            <h2 class="text-3xl font-bold">42</h2>
        </div>

        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 text-center">
            <p class="text-gray-400">Total Players</p>
            <h2 class="text-3xl font-bold">3,284</h2>
        </div>

        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 text-center">
            <p class="text-gray-400">Total Earnings</p>
            <h2 class="text-3xl font-bold text-yellow-300">₹2,48,600</h2>
        </div>

        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 text-center">
            <p class="text-gray-400">Success Rate</p>
            <h2 class="text-3xl font-bold text-cyan-400">96%</h2>
        </div>
    </section>

    <!-- 🔹 GOOGLE ADS – PROFILE TOP BANNER -->
    <div class="max-w-7xl mx-auto my-6 px-8">
        <div class="bg-slate-800 py-6 text-center rounded border border-dashed border-gray-600">
            <span class="text-gray-400">Google Ad – Organizer Profile Top Banner</span>
        </div>
    </div>

    <!-- 🔹 MAIN PROFILE GRID -->
    <section class="px-8 py-10 grid md:grid-cols-3 gap-12">

        <!-- 🔹 LEFT: ABOUT ORGANIZER -->
        <div class="md:col-span-2 space-y-10">

            <!-- ABOUT CARD -->
            <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                <h2 class="text-2xl font-bold mb-4">👤 About Organizer</h2>
                <p class="text-gray-300 leading-relaxed">
                    EliteArena is a professional esports tournament organizer hosting premium PUBG, CODM and Valorant events
                    since 2022.
                    Known for fair play, instant payouts and high quality competitive lobbies. Trusted by more than 3000+
                    players across India.
                </p>
            </div>

            <!-- SOCIAL & CONTACT -->
            <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                <h2 class="text-2xl font-bold mb-6">🌐 Contact & Community</h2>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-slate-800 p-4 rounded-xl">📧 support@elitearena.gg</div>
                    <div class="bg-slate-800 p-4 rounded-xl">📱 +91 98765 43210</div>
                    <div class="bg-slate-800 p-4 rounded-xl">💬 Discord: discord.gg/elitearena</div>
                    <div class="bg-slate-800 p-4 rounded-xl">🌍 Website: elitearena.gg</div>
                </div>
            </div>

            <!-- RECENT TOURNAMENTS -->
            <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">🏆 Recent Tournaments</h2>
                    <a href="#" class="text-cyan-400">View All</a>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between bg-slate-800 p-4 rounded-xl">
                        <span>Champions Clash Finals</span>
                        <span class="text-yellow-300">₹12,000 Prize</span>
                    </div>
                    <div class="flex justify-between bg-slate-800 p-4 rounded-xl">
                        <span>Night Warriors Cup</span>
                        <span class="text-yellow-300">₹8,000 Prize</span>
                    </div>
                    <div class="flex justify-between bg-slate-800 p-4 rounded-xl">
                        <span>Valorant Pro Scrims</span>
                        <span class="text-yellow-300">₹6,500 Prize</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- 🔹 RIGHT: RATINGS + BADGES + ADS -->
        <div class="space-y-10">

            <!-- RATING CARD -->
            <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                <h3 class="text-xl font-bold mb-4">⭐ Organizer Rating</h3>

                <p class="text-4xl font-extrabold text-yellow-300 mb-2">4.9 / 5</p>
                <p class="text-gray-400">Based on 312 player reviews</p>

                <div class="mt-6 space-y-3">
                    <p>Fair Play: ⭐⭐⭐⭐⭐</p>
                    <p>Payout Speed: ⭐⭐⭐⭐⭐</p>
                    <p>Communication: ⭐⭐⭐⭐☆</p>
                </div>
            </div>

            <!-- BADGES -->
            <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                <h3 class="text-xl font-bold mb-6">🏅 Achievements & Badges</h3>

                <div class="grid grid-cols-3 gap-4 text-center">
                    <div class="bg-slate-800 p-4 rounded-xl">🔥 100+ Matches</div>
                    <div class="bg-slate-800 p-4 rounded-xl">💎 Premium Host</div>
                    <div class="bg-slate-800 p-4 rounded-xl">⚡ Fast Payout</div>
                    <div class="bg-slate-800 p-4 rounded-xl">🛡 Trusted</div>
                    <div class="bg-slate-800 p-4 rounded-xl">🎯 Top Rated</div>
                    <div class="bg-slate-800 p-4 rounded-xl">🏆 Champion Series</div>
                </div>
            </div>

            <!-- 🔹 SIDEBAR AD -->
            <div class="bg-slate-800 py-10 text-center rounded border border-dashed border-gray-600">
                <span class="text-gray-400">Google Ad – Organizer Profile Sidebar</span>
            </div>

        </div>
        <!-- 🔹 CREATOR SOCIAL LINKS -->
        <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
            <h3 class="text-xl font-bold mb-6">🌐 Creator & Community</h3>

            <div class="space-y-4">

                <a href="#" target="_blank"
                    class="flex items-center space-x-4 bg-red-600/10 hover:bg-red-600/20 p-4 rounded-xl transition">
                    <span class="text-red-500 text-2xl">▶️</span>
                    <div>
                        <p class="font-semibold">YouTube Channel</p>
                        <p class="text-sm text-gray-400">Subscribe for match highlights</p>
                    </div>
                </a>

                <a href="#" target="_blank"
                    class="flex items-center space-x-4 bg-purple-600/10 hover:bg-purple-600/20 p-4 rounded-xl transition">
                    <span class="text-purple-400 text-2xl">🎮</span>
                    <div>
                        <p class="font-semibold">Twitch Stream</p>
                        <p class="text-sm text-gray-400">Watch live tournaments</p>
                    </div>
                </a>

                <a href="#" target="_blank"
                    class="flex items-center space-x-4 bg-pink-600/10 hover:bg-pink-600/20 p-4 rounded-xl transition">
                    <span class="text-pink-400 text-2xl">📸</span>
                    <div>
                        <p class="font-semibold">Instagram</p>
                        <p class="text-sm text-gray-400">Behind the scenes & updates</p>
                    </div>
                </a>

                <a href="#" target="_blank"
                    class="flex items-center space-x-4 bg-indigo-600/10 hover:bg-indigo-600/20 p-4 rounded-xl transition">
                    <span class="text-indigo-400 text-2xl">💬</span>
                    <div>
                        <p class="font-semibold">Discord Community</p>
                        <p class="text-sm text-gray-400">Join official tournament server</p>
                    </div>
                </a>

            </div>
        </div>

    </section>

    <!-- 🔹 GOOGLE ADS – BOTTOM BANNER -->
    <div class="max-w-7xl mx-auto my-10 px-8">
        <div class="bg-slate-800 py-8 text-center rounded border border-dashed border-gray-600">
            <span class="text-gray-400">Google Ad – Organizer Profile Bottom Banner</span>
        </div>
    </div>

@endsection
