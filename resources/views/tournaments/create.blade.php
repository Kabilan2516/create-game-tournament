@extends('layouts.dashboard')

@section('title', 'Create CODM Tournament – GameConnect')

@section('dashboard-content')
    <script>
        window.CODM_DATA = @json(config('codm'));
    </script>

    <!-- HEADER -->
    <div class="bg-slate-900 border-b border-slate-800 px-8 py-6">
        <h1 class="text-3xl font-bold">🎮 Create CODM Tournament</h1>
        <p class="text-gray-400">Set up your Call of Duty: Mobile tournament and start accepting teams</p>
    </div>

    <!-- MAIN FORM -->
    <section class="px-8 py-12 max-w-6xl" x-data="codmTournamentForm()" x-init="init()">

        <form method="POST" action="{{ route('organizer.tournaments.store') }}" enctype="multipart/form-data"
            class="space-y-12">

            @csrf

            <!-- 🔹 TOURNAMENT BANNER UPLOAD (PREMIUM STYLE) -->
            <x-banner-upload name="banner" label="Tournament Banner / Poster" hint="(1200×600 recommended)" />

            <input type="hidden" name="game" value="CODM">

            <!-- BASIC DETAILS -->
            <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                <h2 class="text-2xl font-bold mb-6">🏆 Tournament Basics</h2>

                <div class="grid md:grid-cols-2 gap-6">

                    <!-- TITLE -->
                    <input type="text" name="title" placeholder="Tournament Title" required
                        class="px-4 py-3 rounded bg-slate-800 border border-slate-700">

                    <input type="text" name="subtitle" placeholder="Short Tagline (optional)"
                        class="px-4 py-3 rounded bg-slate-800 border border-slate-700">

                    <!-- TEAM MODE -->
                    <select name="mode" x-model="mode" @change="updateSlots"
                        class="px-4 py-3 rounded bg-slate-800 border border-slate-700">

                        <option value="solo">Solo (1v1)</option>
                        <option value="duo">Duo (2v2)</option>
                        <option value="squad">Squad (4v4)</option>
                    </select>

                    <!-- MATCH TYPE -->
                    <select name="match_type" x-model="matchType" @change="updateOptions"
                        class="px-4 py-3 rounded bg-slate-800 border border-slate-700">

                        <option value="br">Battle Royale</option>
                        <option value="mp">Multiplayer</option>
                    </select>

                    <!-- MAP -->
                    <select name="map" x-model="selectedMap"
                        class="px-4 py-3 rounded bg-slate-800 border border-slate-700">

                        <template x-for="map in maps" :key="map">
                            <option x-text="map" :value="map"></option>
                        </template>
                    </select>

                    <!-- GAME MODE -->
                    <select name="game_mode" x-model="selectedGameMode"
                        class="px-4 py-3 rounded bg-slate-800 border border-slate-700">

                        <template x-for="mode in gameModes" :key="mode">
                            <option x-text="mode" :value="mode"></option>
                        </template>
                    </select>

                </div>
            </div>



            <!-- 🔹 SLOTS & TIMING -->
            <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                <h2 class="text-2xl font-bold mb-6">⏰ Slots & Schedule</h2>

                <div class="grid md:grid-cols-2 gap-6">

                    <!-- TOTAL TEAMS / SLOTS -->
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">
                            Total Teams / Slots *
                        </label>

                        <input type="number" name="slots" x-model="slots" min="2" step="1"
                            class="px-4 py-3 rounded bg-slate-800 border border-slate-700 w-full">

                        <!-- Helper text -->
                        <p class="text-xs text-gray-400 mt-1">
                            Recommended for
                            <span class="font-semibold text-white" x-text="mode.toUpperCase()"></span> :
                            <span class="text-cyan-400 font-semibold" x-text="recommendedSlots"></span> teams
                        </p>
                    </div>

                    <!-- MATCH START TIME -->
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Match Start Time *</label>
                        <input type="datetime-local" name="start_time" x-model="startTime"
                            class="px-4 py-3 rounded bg-slate-800 border border-slate-700 w-full">
                    </div>

                    <!-- REGISTRATION CLOSE TIME -->
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Registration Closes At *</label>
                        <input type="datetime-local" name="registration_close_time" x-model="registrationClose"
                            class="px-4 py-3 rounded bg-slate-800 border border-slate-700 w-full">

                        <p class="text-xs text-gray-400 mt-1">
                            Players cannot join after this time
                        </p>
                    </div>

                    <!-- REGION -->
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Region *</label>
                        <select name="region" class="px-4 py-3 rounded bg-slate-800 border border-slate-700 w-full">
                            <option value="India">India</option>
                            <option value="Asia">Asia</option>
                            <option value="Global">Global</option>
                        </select>
                    </div>

                </div>
            </div>


            <!-- 🎁 TOURNAMENT REWARD & ENTRY -->
            <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700" x-data="{
                entryType: 'free',
                rewardType: 'free'
            }">

                <h2 class="text-2xl font-bold mb-6">🎁 Tournament Rewards & Entry</h2>

                <!-- 🔹 ENTRY TYPE -->
                <h3 class="text-lg font-semibold mb-4">🚪 Entry Type</h3>
                <div class="grid md:grid-cols-2 gap-6 mb-8">

                    <label class="bg-slate-800 p-4 rounded-xl cursor-pointer flex gap-3">
                        <input type="radio" name="is_paid" value="0" x-model="entryType">
                        <div>
                            <p class="font-semibold">🆓 Free Entry</p>
                            <p class="text-xs text-gray-400">Players join without payment</p>
                        </div>
                    </label>

                    <label class="bg-slate-800 p-4 rounded-xl cursor-pointer flex gap-3">
                        <input type="radio" name="is_paid" value="1" x-model="entryType">
                        <div>
                            <p class="font-semibold">💳 Paid Entry</p>
                            <p class="text-xs text-gray-400">Players pay entry fee</p>
                        </div>
                    </label>

                </div>

                <!-- 🔹 REWARD TYPE -->
                <h3 class="text-lg font-semibold mb-4">🏆 Reward Type</h3>
                <div class="grid md:grid-cols-3 gap-6 mb-10">

                    <label class="bg-slate-800 p-4 rounded-xl cursor-pointer flex gap-3">
                        <input type="radio" name="reward_type" value="free" x-model="rewardType">
                        <div>
                            <p class="font-semibold">🆓 No Rewards</p>
                            <p class="text-xs text-gray-400">Practice / friendly match</p>
                        </div>
                    </label>

                    <label class="bg-slate-800 p-4 rounded-xl cursor-pointer flex gap-3">
                        <input type="radio" name="reward_type" value="organizer_prize" x-model="rewardType">
                        <div>
                            <p class="font-semibold">💰 Organizer Prize</p>
                            <p class="text-xs text-gray-400">Cash prizes by organizer</p>
                        </div>
                    </label>

                    <label class="bg-slate-800 p-4 rounded-xl cursor-pointer flex gap-3">
                        <input type="radio" name="reward_type" value="platform_points" x-model="rewardType">
                        <div>
                            <p class="font-semibold">🎯 Platform Points</p>
                            <p class="text-xs text-gray-400">Earn redeemable points</p>
                        </div>
                    </label>

                </div>

                <!-- 🔹 APPROVAL MODE (ALWAYS VISIBLE) -->
                <h3 class="text-lg font-semibold mb-4">✅ Join Approval Mode</h3>
                <div class="grid md:grid-cols-2 gap-6 mb-10">

                    <label class="bg-slate-800 p-4 rounded-xl flex gap-3 cursor-pointer">
                        <input type="radio" name="auto_approve" value="0" checked>
                        <div>
                            <p class="font-semibold">🛂 Manual Approval</p>
                            <p class="text-xs text-gray-400">Organizer approves players</p>
                        </div>
                    </label>

                    <label class="bg-slate-800 p-4 rounded-xl flex gap-3 cursor-pointer">
                        <input type="radio" name="auto_approve" value="1">
                        <div>
                            <p class="font-semibold">⚡ Auto Approve</p>
                            <p class="text-xs text-gray-400">Players join instantly</p>
                        </div>
                    </label>

                </div>

                <!-- 🔹 PAID ENTRY SECTION -->
                <div x-show="entryType === '1'" x-transition class="mb-10">

                    <h3 class="text-xl font-bold mb-4">💳 Entry Fee</h3>

                    <input type="number" name="entry_fee" placeholder="Entry Fee (₹)" min="0"
                        class="mb-6 px-4 py-3 rounded bg-slate-800 border border-slate-700 w-full">

                    <div class="bg-slate-900 p-6 rounded-2xl border border-green-700">
                        <h4 class="font-bold mb-4">UPI Payment Details</h4>

                        <input type="text" name="upi_id" placeholder="UPI ID (example@upi)"
                            class="mb-4 px-4 py-3 rounded bg-slate-800 border border-slate-700 w-full">

                        <input type="text" name="upi_name" placeholder="Payee Name"
                            class="mb-4 px-4 py-3 rounded bg-slate-800 border border-slate-700 w-full">

                        <input type="file" name="upi_qr" accept="image/*"
                            class="px-4 py-3 rounded bg-slate-800 border border-slate-700 w-full">
                    </div>

                </div>

                <!-- 🔹 PRIZE DETAILS -->
                <div x-show="rewardType === 'organizer_prize'" x-transition class="mb-10">

                    <h3 class="text-xl font-bold mb-4">🏆 Prize Distribution</h3>

                    <div class="grid md:grid-cols-3 gap-6">
                        <input type="number" name="first_prize" placeholder="1st Prize (₹)"
                            class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
                        <input type="number" name="second_prize" placeholder="2nd Prize (₹)"
                            class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
                        <input type="number" name="third_prize" placeholder="3rd Prize (₹)"
                            class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
                    </div>

                </div>

                <!-- 🔹 PLATFORM POINTS INFO -->
                <div x-show="rewardType === 'platform_points'" x-transition
                    class="bg-slate-800 p-6 rounded-xl border border-purple-500/40">

                    <p class="text-purple-300 font-semibold">
                        🎯 Players earn platform points based on match performance.
                        Points can be redeemed for rewards later.
                    </p>

                </div>

            </div>


            <!-- RULES & DESCRIPTION -->
            <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                <h2 class="text-2xl font-bold mb-6">📜 Rules & Description</h2>

                <textarea name="description" rows="5" placeholder="About this tournament..."
                    class="w-full px-4 py-3 rounded bg-slate-800 border border-slate-700"></textarea>

                <textarea name="rules" rows="6" placeholder="Tournament rules, bans, reporting rules..."
                    class="w-full mt-6 px-4 py-3 rounded bg-slate-800 border border-slate-700"></textarea>
            </div>

            <!-- 🔐 ROOM DETAILS -->
            <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700" x-data="{ addNow: false, showPass: false }">

                <h2 class="text-2xl font-bold mb-4">🔐 Room Details</h2>

                <p class="text-sm text-gray-400 mb-6">
                    Room ID & Password will be shared with approved teams before match start.
                    You can add or update them later from dashboard.
                </p>

                <!-- TOGGLE -->
                <label class="flex items-center space-x-3 mb-6 cursor-pointer">
                    <input type="checkbox" x-model="addNow">
                    <span class="font-semibold text-cyan-300">
                        Add room details now (optional)
                    </span>
                </label>

                <!-- ROOM FORM -->
                <div x-show="addNow" x-transition class="space-y-6">

                    <div class="grid md:grid-cols-2 gap-6">

                        <!-- ROOM ID -->
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Room ID</label>
                            <input type="text" name="room_id" placeholder="Enter Room ID"
                                class="px-4 py-3 rounded bg-slate-800 border border-slate-700 w-full">
                        </div>

                        <!-- ROOM PASSWORD -->
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Room Password</label>

                            <div class="relative">
                                <input :type="showPass ? 'text' : 'password'" name="room_password"
                                    placeholder="Enter Room Password"
                                    class="px-4 py-3 rounded bg-slate-800 border border-slate-700 w-full pr-12">

                                <!-- SHOW / HIDE -->
                                <button type="button" @click="showPass = !showPass"
                                    class="absolute right-3 top-3 text-gray-400 hover:text-cyan-400">
                                    <span x-text="showPass ? '🙈' : '👁️'"></span>
                                </button>
                            </div>
                        </div>

                    </div>

                    <!-- SECURITY NOTE -->
                    <div class="bg-slate-800 p-4 rounded-xl border border-yellow-500/40">
                        <p class="text-sm text-yellow-300">
                            🔒 These details will NOT be shown to players until you release them manually before the match.
                        </p>
                    </div>

                </div>

                <!-- HIDDEN FLAGS -->
                <input type="hidden" name="room_released" value="0">

            </div>


            <!-- SUBMIT -->
            <div class="flex justify-end pt-8">
                <button type="submit"
                    class="px-10 py-4 rounded-xl font-bold text-lg bg-gradient-to-r from-cyan-500 to-purple-600 hover:opacity-90">
                    🚀 Publish Tournament
                </button>
            </div>

        </form>
    </section>

    <!-- ALPINE LOGIC -->
    <script>
        function codmTournamentForm() {
            return {

                /* BASIC STATE */
                mode: 'solo',
                matchType: 'br',

                /* MAP & MODE DATA (TEMP STATIC FILE STYLE) */
                codmData: window.CODM_DATA,

                /* DYNAMIC OPTIONS */
                maps: [],
                gameModes: [],
                selectedMap: '',
                selectedGameMode: '',

                /* SLOTS LOGIC */
                slots: 50,
                recommendedSlots: 50,

                /* TIMING */
                startTime: '',
                registrationClose: '',

                /* INIT */
                init() {
                    this.updateOptions();
                    this.updateSlots();
                },

                /* WHEN BR / MP CHANGES */
                updateOptions() {
                    const data = this.codmData[this.matchType];

                    this.maps = data.maps;
                    this.gameModes = data.modes;

                    this.selectedMap = this.maps[0];
                    this.selectedGameMode = this.gameModes[0];
                },

                /* WHEN MODE CHANGES */
                updateSlots() {

                    if (this.mode === 'solo') {
                        this.recommendedSlots = 50;
                    }

                    if (this.mode === 'duo') {
                        this.recommendedSlots = 25;
                    }

                    if (this.mode === 'squad') {
                        this.recommendedSlots = 16;
                    }

                    // Auto set slots if empty or first change
                    if (!this.slots || this.slots === 0 || this.slots === this.recommendedSlots) {
                        this.slots = this.recommendedSlots;
                    }
                }
            }
        }
    </script>


@endsection
