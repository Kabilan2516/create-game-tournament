@extends('layouts.app')

@section('title', $tournament->title . ' – GameConnect')

@section('meta')
    <meta property="og:title" content="{{ $tournament->title }}">
    <meta property="og:description" content="🏆 Prize ₹{{ $tournament->prize }} | 📅 {{ $tournament->start_date }}">
    <meta property="og:image" content="{{ $tournament->banner_url }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
@endsection

@section('content')

    <!-- 🔹 HERO / COVER SECTION -->
    <section class="relative bg-gradient-to-br from-slate-900 via-purple-900 to-cyan-900 py-24 overflow-hidden">

        @if ($tournament->banner)
            <div class="absolute inset-0 opacity-30 bg-cover"
                style="background-image:url('{{ asset('storage/' . $tournament->banner) }}')"></div>
        @endif

        <div class="relative max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-12 items-center fade-up">

            <!-- Left: Main Info -->
            <div>

                <!-- BADGES -->
                <div class="flex space-x-3 mb-4">
                    <span class="px-4 py-1 rounded-full bg-cyan-500/20 text-cyan-300">
                        {{ $tournament->game }}
                    </span>

                    <span class="px-4 py-1 rounded-full bg-purple-500/20 text-purple-300">
                        {{ strtoupper($tournament->mode) }}
                    </span>

                    @if ($tournament->is_featured)
                        <span class="px-4 py-1 rounded-full bg-yellow-400/20 text-yellow-300">
                            ⭐ FEATURED
                        </span>
                    @endif
                </div>

                <h1 class="text-5xl font-extrabold mb-4">
                    {{ $tournament->title }}
                </h1>

                <p class="text-gray-200 max-w-xl mb-6">
                    {{ $tournament->subtitle ?? $tournament->description }}
                </p>

                <!-- QUICK STATS -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">

                    <!-- PRIZE -->
                    <div class="bg-black/40 p-4 rounded-xl text-center">
                        <p class="text-sm text-gray-400">Prize Pool</p>
                        <p class="text-xl font-bold text-yellow-300">
                            ₹{{ number_format(
                                ($tournament->first_prize ?? 0) + ($tournament->second_prize ?? 0) + ($tournament->third_prize ?? 0),
                            ) }}
                        </p>
                    </div>

                    <!-- ENTRY FEE -->
                    <div class="bg-black/40 p-4 rounded-xl text-center">
                        <p class="text-sm text-gray-400">Entry Fee</p>
                        <p class="text-xl font-bold text-cyan-300">
                            {{ $tournament->entry_fee > 0 ? '₹' . $tournament->entry_fee : 'FREE' }}
                        </p>
                    </div>

                    <!-- SLOTS -->
                    <div class="bg-black/40 p-4 rounded-xl text-center">
                        <p class="text-sm text-gray-400">Slots</p>
                        <p class="text-xl font-bold">
                            {{ $tournament->filled_slots }} / {{ $tournament->slots }}
                        </p>
                    </div>

                    <!-- START TIME -->
                    <div class="bg-black/40 p-4 rounded-xl text-center">
                        <p class="text-sm text-gray-400">Starts At</p>
                        <p class="text-lg font-bold text-purple-300">
                            {{ $tournament->start_time->format('d M, h:i A') }}
                        </p>
                    </div>

                </div>

                <!-- CTA BUTTON -->
                <div class="flex flex-wrap gap-4">

                    {{-- 🏆 RESULTS (ONLY IF PUBLISHED) --}}
                    @if ($tournament->matchResult && $tournament->matchResult->is_locked)
                        <a href="{{ route('tournaments.results.show', $tournament) }}"
                            class="px-8 py-3 rounded-xl font-bold
                  bg-gradient-to-r from-emerald-500 to-green-600
                  hover:from-emerald-400 hover:to-green-500
                  shadow-lg transition flex items-center gap-2">
                            🏆 View Results
                        </a>

                        {{-- 🚀 JOIN (ONLY BEFORE START) --}}
                    @elseif(!$tournament->join_closed && $slotsLeft > 0)
                        <a href="{{ route('tournaments.join.form', $tournament) }}"
                            class="px-8 py-3 rounded-xl font-bold
                  bg-gradient-to-r from-cyan-500 to-purple-600
                  hover:scale-105 transition shadow-lg">
                            🚀 Join Tournament
                        </a>

                        {{-- ❌ FULL --}}
                    @elseif(!$tournament->join_closed && $slotsLeft <= 0)
                        <button disabled
                            class="px-8 py-3 rounded-xl font-bold
                   bg-gray-600 cursor-not-allowed">
                            ❌ Slots Full
                        </button>

                        {{-- 🔒 STARTED / CLOSED --}}
                    @else
                        <button disabled
                            class="px-8 py-3 rounded-xl font-bold
                   bg-gray-700 cursor-not-allowed">
                            🔒 Registration Closed
                        </button>
                    @endif

                </div>

            </div>
            <!-- Right: Banner Image -->
            <div class="relative group float">

                @if ($tournament->banner)
                    <img src="{{ asset('storage/' . $tournament->banner->file_path) }}"
                        class="w-full h-96 object-cover rounded-3xl shadow-2xl border border-slate-700">
                @else
                    <img src="{{ asset('images/tournament-default.jpg') }}"
                        class="w-full h-96 object-cover rounded-3xl shadow-2xl border border-slate-700">
                @endif

            </div>

        </div>
    </section>

    <!-- 🔹 MAIN CONTENT -->
    <section class="py-24 bg-black">
        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-3 gap-12">

            <!-- 🔹 LEFT CONTENT -->
            <div class="md:col-span-2 space-y-12">

                <!-- ABOUT -->
                <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                    <h2 class="text-2xl font-bold mb-4">📖 About This Tournament</h2>
                    <p class="text-gray-300 leading-relaxed">
                        {{ $tournament->description }}
                    </p>
                </div>

                <!-- RULES -->
                @if ($tournament->rules)
                    <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                        <h2 class="text-2xl font-bold mb-6">📜 Rules & Format</h2>
                        <pre class="text-gray-300 whitespace-pre-line">{{ $tournament->rules }}</pre>
                    </div>
                @endif

                <!-- SCHEDULE -->
                <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                    <h2 class="text-2xl font-bold mb-6">🗓️ Match Schedule</h2>

                    <div class="space-y-4 text-gray-300">
                        <div class="flex justify-between">
                            <span>Registration Closes</span>
                            <span class="text-white font-semibold">
                                {{-- {{ $tournament->registration_close_time->format('d M, h:i A') }} --}}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span>Match Starts</span>
                            <span class="text-white font-semibold">
                                {{ $tournament->start_time->format('d M, h:i A') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- PRIZE BREAKDOWN -->
                @if ($tournament->first_prize || $tournament->second_prize || $tournament->third_prize)

                    <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                        <h2 class="text-2xl font-bold mb-6">🏆 Prize Distribution</h2>

                        <div class="grid md:grid-cols-3 gap-6">

                            @if ($tournament->first_prize)
                                <div class="bg-yellow-400/10 p-6 rounded-xl text-center">
                                    <p class="text-lg font-bold">🥇 1st Place</p>
                                    <p class="text-yellow-300 text-xl">₹{{ $tournament->first_prize }}</p>
                                </div>
                            @endif

                            @if ($tournament->second_prize)
                                <div class="bg-slate-700/40 p-6 rounded-xl text-center">
                                    <p class="text-lg font-bold">🥈 2nd Place</p>
                                    <p class="text-gray-200 text-xl">₹{{ $tournament->second_prize }}</p>
                                </div>
                            @endif

                            @if ($tournament->third_prize)
                                <div class="bg-orange-500/10 p-6 rounded-xl text-center">
                                    <p class="text-lg font-bold">🥉 3rd Place</p>
                                    <p class="text-orange-300 text-xl">₹{{ $tournament->third_prize }}</p>
                                </div>
                            @endif

                        </div>
                    </div>

                @endif

                <!-- ROOM DETAILS -->
                <!-- ROOM DETAILS -->
                @if ($tournament->hasEnded)

                    {{-- 🏁 MATCH ENDED --}}
                    <div class="bg-slate-900 p-8 rounded-3xl border border-gray-700">
                        <h2 class="text-xl font-bold mb-2 text-gray-300">🏁 Match Ended</h2>
                        <p class="text-gray-400">
                            This match has already ended. Room details are no longer active.
                        </p>
                    </div>
                @elseif($tournament->room_released)
                    {{-- 🔐 ROOM AVAILABLE --}}
                    <div class="bg-slate-900 p-8 rounded-3xl border border-green-700">
                        <h2 class="text-2xl font-bold mb-4">🔐 Room Details</h2>

                        <div class="space-y-3 text-lg">
                            <p>
                                🆔 Room ID:
                                
                            </p>

                            <p>
                                🔑 Password:
                        
                            </p>
                            <span>Go to the <a href="{{ join.code.index }}">join code page enter your tornament registration code you get your room id and pass</a></span>
                        </div>

                        @if ($tournament->hasStarted)
                            <p class="mt-4 text-sm text-green-400">
                                🟢 Match is LIVE — join immediately
                            </p>
                        @endif
                    </div>
                @elseif($tournament->hasStarted)
                    {{-- ⚠️ STARTED BUT NOT RELEASED --}}
                    <div class="bg-slate-900 p-8 rounded-3xl border border-red-700">
                        <h2 class="text-xl font-bold mb-2 text-red-400">⚠️ Room Not Released</h2>
                        <p class="text-gray-400">
                            The match has started, but the organizer has not released room details yet.
                            Please contact the organizer immediately.
                        </p>
                    </div>
                @else
                    {{-- 🔒 UPCOMING --}}
                    <div class="bg-slate-900 p-8 rounded-3xl border border-yellow-700">
                        <h2 class="text-xl font-bold mb-2 text-yellow-400">🔒 Room Details Locked</h2>
                        <p class="text-gray-400">
                            Room ID & password will be shared shortly before the match starts.
                        </p>
                        <p class="text-sm text-gray-500 mt-2">
                            ⏰ Match starts at {{ $tournament->start_time->format('d M Y, h:i A') }}
                        </p>
                    </div>

                @endif


            </div>

            <!-- 🔹 RIGHT SIDEBAR -->
            <div class="space-y-10">

                <!-- ORGANIZER -->
                <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                    <h3 class="text-xl font-bold mb-4">👤 Organizer</h3>

                    <div class="flex items-center space-x-4 mb-4">
                        <img src="https://ui-avatars.com/api/?name={{ $organizer->name }}" class="w-12 h-12 rounded-full">

                        <div>
                            <p class="font-semibold">{{ $organizer->name }}</p>
                            <p class="text-sm text-gray-400">Verified Organizer</p>
                        </div>
                    </div>

                    <a href="{{ route('organizers.public', $tournament->organizer) }}"
                        class="block w-full text-center py-3 rounded-xl bg-gradient-to-r from-purple-600 to-cyan-500 font-bold">
                        View Organizer Profile
                    </a>
                </div>

                <!-- SLOT STATUS -->
                <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                    <h3 class="text-xl font-bold mb-4">👥 Slots Status</h3>

                    <div class="flex justify-between text-sm mb-2">
                        <span>Filled</span>
                        <span>{{ $tournament->filled_slots }} / {{ $tournament->slots }}</span>
                    </div>

                    <div class="w-full bg-slate-700 rounded-full h-3 overflow-hidden mb-4">
                        <div class="bg-gradient-to-r from-cyan-400 to-purple-500 h-3"
                            style="width: {{ ($tournament->filled_slots / $tournament->slots) * 100 }}%">
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

@endsection
