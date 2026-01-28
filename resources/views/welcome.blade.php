{{-- ===============================
   FILE 3: resources/views/welcome.blade.php
   MAIN LANDING PAGE
================================ --}}
@extends('layouts.app')

@section('title', 'GameConnect – CODM & PUBG Tournaments')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <!-- 🔹 HERO SECTION (ANIMATED) -->
    <section class="relative overflow-hidden py-32 bg-gradient-to-br from-slate-900 via-purple-900 to-cyan-900">
        <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 px-6 items-center">

            <div class="fade-up">
                <h2 class="text-5xl font-extrabold leading-tight mb-6">
                    Enter the <span class="text-cyan-400">Battle Arena</span><br>
                    Host & Join <span class="text-purple-400">CODM</span> / <span class="text-cyan-400">PUBG</span>
                    Tournaments
                </h2>
                <p class="text-gray-300 mb-8 text-lg">Create rooms, promote matches, compete with top gamers worldwide.</p>

                <div class="space-x-4">
                    <a href="#tournaments" class="px-8 py-3 bg-cyan-500 rounded-xl font-bold glow-cyan">Explore Matches</a>
                    <a href="#organizers" class="px-8 py-3 border border-purple-400 rounded-xl hover:bg-purple-500">Host
                        Tournament</a>
                </div>
            </div>

            <div class="hidden md:block float">
                <img src="{{ asset('build/images/hero-gamer.png') }}" class="rounded-2xl shadow-2xl">
            </div>
        </div>
    </section>

    <!-- 🔹 ADS SLOT (TOP BANNER) -->
    <x-ad-slot page="welcome" position="header" />

    <!-- 🔹 FEATURES SECTION (CARDS + HOVER ANIMATION) -->
    <section id="features" class="py-24 bg-slate-950">
        <div class="max-w-7xl mx-auto px-6">
            <h3 class="text-4xl font-bold mb-14 text-center">Why Gamers Love GameConnect</h3>

            <div class="grid md:grid-cols-4 gap-8">
                @foreach ([['🎯', 'Instant Room Discovery', 'Find active rooms in seconds'], ['🏆', 'High Prize Pools', 'Compete for real rewards'], ['⭐', 'Featured Promotion', 'Boost your tournaments'], ['🛡️', 'Verified Organizers', 'Play safe & trusted']] as $item)
                    <div class="bg-slate-800 p-8 rounded-2xl hover:scale-105 transition glow-cyan">
                        <div class="text-5xl mb-4">{{ $item[0] }}</div>
                        <h4 class="text-xl font-bold mb-2">{{ $item[1] }}</h4>
                        <p class="text-gray-400">{{ $item[2] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <section class="py-20 bg-black">
        <h2 class="text-3xl font-bold text-center mb-10">🔥 Featured Tournaments</h2>

        <div class="swiper featuredSwiper max-w-7xl mx-auto px-6">
            <div class="swiper-wrapper">

                @foreach ($featured as $tournament)
                    <div class="swiper-slide">
                        <x-tournament-card :tournament="$tournament" />
                    </div>
                @endforeach

            </div>
        </div>
    </section>

    <!-- 🔹 PLATFORM FEATURES -->
    <section class="py-24 bg-black">
        <div class="max-w-7xl mx-auto px-6">

            <h2 class="text-4xl font-bold text-center mb-14">
                ⚡ Powerful Features for Players & Organizers
            </h2>

            <div class="grid md:grid-cols-3 gap-10">

                <div class="bg-slate-900 p-8 rounded-2xl border border-slate-700 hover:border-cyan-400 transition">
                    <h3 class="text-xl font-bold mb-3">📧 Auto Room Details Delivery</h3>
                    <p class="text-gray-400">
                        Room ID & password automatically sent via Email and WhatsApp after approval.
                    </p>
                </div>

                <div class="bg-slate-900 p-8 rounded-2xl border border-slate-700 hover:border-purple-400 transition">
                    <h3 class="text-xl font-bold mb-3">👥 Team & Squad Management</h3>
                    <p class="text-gray-400">
                        Register solo, duo or squad. Manage team members easily with one click.
                    </p>
                </div>

                <div class="bg-slate-900 p-8 rounded-2xl border border-slate-700 hover:border-yellow-400 transition">
                    <h3 class="text-xl font-bold mb-3">💳 Secure Entry Fee System</h3>
                    <p class="text-gray-400">
                        UPI based payments with proof verification and fraud protection.
                    </p>
                </div>

                <div class="bg-slate-900 p-8 rounded-2xl border border-slate-700 hover:border-green-400 transition">
                    <h3 class="text-xl font-bold mb-3">⭐ Featured Tournament Promotion</h3>
                    <p class="text-gray-400">
                        Boost your tournaments to the homepage and reach thousands of players.
                    </p>
                </div>

                <div class="bg-slate-900 p-8 rounded-2xl border border-slate-700 hover:border-indigo-400 transition">
                    <h3 class="text-xl font-bold mb-3">🛡 Verified Organizer Profiles</h3>
                    <p class="text-gray-400">
                        Public organizer profiles with ratings, badges and trust score.
                    </p>
                </div>

                <div class="bg-slate-900 p-8 rounded-2xl border border-slate-700 hover:border-pink-400 transition">
                    <h3 class="text-xl font-bold mb-3">📊 Live Join Tracking</h3>
                    <p class="text-gray-400">
                        Track slots filled, pending approvals and team status in real time.
                    </p>
                </div>

            </div>
        </div>
    </section>


    <!-- 🔹 ADS SLOT (TOP BANNER) -->
    <x-ad-slot page="welcome" position="header" />

    <!-- 🔹 FOR ORGANIZERS -->
    <section class="py-28 bg-gradient-to-r from-slate-900 to-slate-950">
        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-14 items-center">

            <div>
                <h2 class="text-4xl font-bold mb-6">
                    🏆 Built for Professional Tournament Organizers
                </h2>

                <p class="text-gray-300 mb-8 text-lg">
                    Manage registrations, approve teams, send room details, track payments
                    and grow your esports brand — all from one dashboard.
                </p>

                <ul class="space-y-4 text-gray-300 mb-10">
                    <li>✅ Create unlimited CODM, PUBG & esports tournaments</li>
                    <li>✅ Approve / reject teams with one click</li>
                    <li>✅ Bulk send room details via email & WhatsApp</li>
                    <li>✅ Organizer profile with ratings & badges</li>
                    <li>✅ Promote tournaments with featured listing</li>
                </ul>

                <div class="mt-10">
                    <a href="{{ route('register') }}" class="px-8 py-3 bg-purple-600 rounded-xl font-bold hover:opacity-90">
                        🚀 Become an Organizer
                    </a>
                </div>
            </div>

            <div class="hidden md:block">
                <img src="{{ asset('build/images/organizer-dashboard.png') }}" class="rounded-2xl shadow-2xl">
            </div>

        </div>
    </section>
    <!-- 🔹 SECURITY & TRUST -->
    <section class="py-24 bg-black">
        <div class="max-w-6xl mx-auto px-6 text-center">

            <h2 class="text-4xl font-bold mb-10">🔐 Safe, Secure & Trusted Platform</h2>

            <div class="grid md:grid-cols-3 gap-10">

                <div class="bg-slate-900 p-8 rounded-2xl">
                    <h3 class="text-xl font-bold mb-3">🛡 Anti-Fraud System</h3>
                    <p class="text-gray-400">
                        Manual payment verification & organizer monitoring to prevent scams.
                    </p>
                </div>

                <div class="bg-slate-900 p-8 rounded-2xl">
                    <h3 class="text-xl font-bold mb-3">📜 Fair Play Policy</h3>
                    <p class="text-gray-400">
                        Strict rules against hacking, teaming and cheating in tournaments.
                    </p>
                </div>

                <div class="bg-slate-900 p-8 rounded-2xl">
                    <h3 class="text-xl font-bold mb-3">⭐ Rating & Review System</h3>
                    <p class="text-gray-400">
                        Players rate organizers. Only top hosts get verified badge.
                    </p>
                </div>

            </div>
        </div>
    </section>
    <!-- 🔹 INSTANT NOTIFICATIONS -->
    <section class="py-24 bg-gradient-to-r from-cyan-900 to-purple-900">
        <div class="max-w-6xl mx-auto px-6 text-center">

            <h2 class="text-4xl font-bold mb-6">📲 Instant Room Details on WhatsApp & Email</h2>

            <p class="text-gray-200 text-lg mb-10">
                No more waiting. Once approved, players receive room ID & password instantly
                through Email and WhatsApp notifications.
            </p>

            <div class="grid md:grid-cols-3 gap-10">

                <div class="bg-black/40 p-6 rounded-2xl">
                    <h3 class="font-bold mb-2">⚡ Fast Approval</h3>
                    <p class="text-gray-300">Organizers approve teams instantly</p>
                </div>

                <div class="bg-black/40 p-6 rounded-2xl">
                    <h3 class="font-bold mb-2">📧 Auto Email System</h3>
                    <p class="text-gray-300">Room details delivered securely</p>
                </div>

                <div class="bg-black/40 p-6 rounded-2xl">
                    <h3 class="font-bold mb-2">💬 WhatsApp Alerts</h3>
                    <p class="text-gray-300">Never miss your match time</p>
                </div>

            </div>

        </div>
    </section>
    <!-- 🔹 COMMUNITY & GROWTH -->
    <section class="py-24 bg-slate-950">
        <div class="max-w-7xl mx-auto px-6">

            <h2 class="text-4xl font-bold text-center mb-14">
                🌍 Join India’s Fastest Growing Esports Community
            </h2>

            <div class="grid md:grid-cols-4 gap-8 text-center">

                <div class="bg-slate-900 p-6 rounded-2xl">
                    <h3 class="text-3xl font-bold text-cyan-400">10K+</h3>
                    <p class="text-gray-400">Monthly Matches</p>
                </div>

                <div class="bg-slate-900 p-6 rounded-2xl">
                    <h3 class="text-3xl font-bold text-purple-400">500+</h3>
                    <p class="text-gray-400">Daily Active Players</p>
                </div>

                <div class="bg-slate-900 p-6 rounded-2xl">
                    <h3 class="text-3xl font-bold text-green-400">₹1Cr+</h3>
                    <p class="text-gray-400">Annual Prize Money</p>
                </div>

                <div class="bg-slate-900 p-6 rounded-2xl">
                    <h3 class="text-3xl font-bold text-yellow-300">99%</h3>
                    <p class="text-gray-400">Match Completion Rate</p>
                </div>

            </div>
        </div>
    </section>


@endsection
