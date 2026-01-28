@extends('layouts.app')

@section('title', 'Join Tournament – ' . $tournament->title)

@section('content')

    <!-- 🔹 HERO / BANNER -->
    <section class="relative bg-black">

        @if ($banner)
            <div class="h-72 bg-cover bg-center" style="background-image:url('{{ asset('storage/' . $banner->file_path) }}')">
            </div>
        @else
            <div class="h-72 bg-gradient-to-br from-slate-900 via-purple-900 to-cyan-900"></div>
        @endif

        <div class="absolute inset-0 bg-black/60"></div>

        <div class="absolute inset-0 flex items-center">
            <div class="max-w-6xl mx-auto px-6 text-white">
                <h1 class="text-4xl font-extrabold mb-2">🚀 Join {{ $tournament->title }}</h1>
                <p class="text-gray-300">{{ $tournament->subtitle }}</p>
            </div>
        </div>
    </section>

    <!-- 🔹 MAIN CONTENT -->
    <section class="py-20 bg-black">
        <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-3 gap-12">

            <!-- 🔹 LEFT: JOIN FORM -->
            <div class="md:col-span-2 bg-slate-900 p-10 rounded-3xl border border-slate-700">

                <h2 class="text-3xl font-bold mb-8">🎯 Player / Team Registration</h2>

                <form method="POST" action="{{ route('tournaments.join.store', $tournament) }}"
                    enctype="multipart/form-data" class="space-y-8" x-data="joinForm('{{ strtolower($tournament->mode) }}')">

                    @csrf
                    <p class="text-gray-300">
                        👥 Mode:
                        <span class="font-bold text-white">{{ strtoupper($tournament->mode) }}</span>
                    </p>


                    <!-- TEAM NAME -->
                    <div>
                        <label class="text-sm text-gray-400">Player / Team Name *</label>
                        <input type="text" name="team_name" required
                            class="w-full mt-1 px-4 py-3 rounded bg-slate-800 border border-slate-700">
                    </div>

                    <!-- CAPTAIN INFO -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm text-gray-400">Captain In-Game ID *</label>
                            <input type="text" name="captain_game_id" required
                                class="w-full mt-1 px-4 py-3 rounded bg-slate-800 border border-slate-700">
                        </div>

                        <div>
                            <label class="text-sm text-gray-400">Captain IGN *</label>
                            <input type="text" name="captain_ign" required
                                class="w-full mt-1 px-4 py-3 rounded bg-slate-800 border border-slate-700">
                        </div>
                    </div>

                    <!-- CONTACT -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm text-gray-400">Email *</label>
                            <input type="email" name="email" required
                                class="w-full mt-1 px-4 py-3 rounded bg-slate-800 border border-slate-700">
                        </div>

                        <div>
                            <label class="text-sm text-gray-400">WhatsApp / Phone *</label>
                            <input type="text" name="phone" required
                                class="w-full mt-1 px-4 py-3 rounded bg-slate-800 border border-slate-700">
                        </div>
                    </div>

                    <!-- 🔹 TEAM MEMBERS -->
                    <template x-if="members.length > 0">
                        <div class="space-y-6">
                            <h3 class="text-xl font-bold">👥 Team Members</h3>

                            <template x-for="(member, index) in members" :key="index">
                                <div class="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="text-sm text-gray-400">
                                            Member <span x-text="index+1"></span> In-Game ID
                                        </label>
                                        <input type="text" :name="`members[${index}][game_id]`"
                                            class="w-full mt-1 px-4 py-3 rounded bg-slate-800 border border-slate-700">
                                    </div>

                                    <div>
                                        <label class="text-sm text-gray-400">
                                            Member <span x-text="index+1"></span> IGN
                                        </label>
                                        <input type="text" :name="`members[${index}][ign]`"
                                            class="w-full mt-1 px-4 py-3 rounded bg-slate-800 border border-slate-700">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- NOTES -->
                    <div>
                        <label class="text-sm text-gray-400">Notes (optional)</label>
                        <textarea name="notes" rows="3" class="w-full mt-1 px-4 py-3 rounded bg-slate-800 border border-slate-700"></textarea>
                    </div>

                    <!-- 🔹 PAYMENT SECTION (ONLY IF PAID) -->
                    @if (!$isFree)

                        <div class="bg-slate-800 p-6 rounded-2xl border border-green-600 space-y-4">

                            <h3 class="text-xl font-bold text-green-400">💳 Pay Entry Fee</h3>

                            <p class="text-gray-300">
                                Amount: <span class="font-bold text-yellow-300">₹{{ $tournament->entry_fee }}</span>
                            </p>

                            <p>UPI ID: <span class="font-bold text-cyan-400">{{ $upiId }}</span></p>
                            <p>Payee Name: <span class="font-bold">{{ $upiName }}</span></p>

                            @if ($upiQr)
                                <img src="{{ asset('storage/' . $upiQr->file_path) }}" class="w-48 rounded-xl">
                            @endif

                            <div>
                                <label class="text-sm text-gray-400">Upload Payment Screenshot *</label>
                                <input type="file" name="payment_proof" required
                                    class="w-full mt-1 px-4 py-3 rounded bg-slate-900 border border-slate-700">
                            </div>

                        </div>

                    @endif

                    <!-- TERMS -->
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" required>
                        <span class="text-sm text-gray-400">I agree to tournament rules & fair play policy</span>
                    </div>

                    <!-- SUBMIT -->
                    <button type="submit"
                        class="w-full py-4 rounded-xl font-bold text-lg 
                               bg-gradient-to-r from-cyan-500 to-purple-600 hover:opacity-90">
                        ✅ Confirm & Join Tournament
                    </button>

                </form>
            </div>

            <!-- 🔹 RIGHT: TOURNAMENT SUMMARY -->
            <div class="space-y-8">

                <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                    <h3 class="text-xl font-bold mb-4">🏆 Tournament Summary</h3>

                    <div class="space-y-3 text-gray-300">
                        <p>🎮 Game: <span class="text-white">{{ $tournament->game }}</span></p>
                        <p>👥 Mode: <span class="text-white">{{ ucfirst($tournament->mode) }}</span></p>
                        <p>💰 Prize Pool: <span class="text-yellow-300">
                                ₹{{ number_format(
                                    ($tournament->first_prize ?? 0) + ($tournament->second_prize ?? 0) + ($tournament->third_prize ?? 0),
                                ) }}</span>
                        </p>

                        @if ($isFree)
                            <p>🎯 Entry Fee: <span class="text-green-400 font-bold">FREE</span></p>
                        @else
                            <p>🎯 Entry Fee: <span class="text-cyan-300">₹{{ $tournament->entry_fee }}</span></p>
                        @endif

                        <p>⏰ Starts: <span class="text-white">{{ $tournament->start_time->format('d M Y, h:i A') }}</span>
                        </p>
                    </div>

                    <div class="mt-6">
                        <div class="flex justify-between text-sm mb-1">
                            <span>Slots Left</span>
                            <span>{{ $slotsLeft }} / {{ $tournament->slots }}</span>
                        </div>

                        <div class="w-full bg-slate-700 rounded-full h-2 overflow-hidden">
                            <div class="bg-gradient-to-r from-cyan-400 to-purple-500 h-2"
                                style="width: {{ ($tournament->filled_slots / $tournament->slots) * 100 }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- ORGANIZER -->
                <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                    <h3 class="text-xl font-bold mb-4">👤 Organizer</h3>

                    <p class="font-semibold">{{ $organizer->name }}</p>
                    <p class="text-sm text-gray-400">Contact shared after approval</p>
                </div>

            </div>
        </div>
    </section>

    <!-- 🔹 ALPINE LOGIC -->
    <script>
        function joinForm(mode) {
            return {
                members: [],

                init() {
                    this.updateMembers(mode);
                },

                updateMembers(mode) {
                    this.members = [];

                    if (mode === 'duo') this.members = [{}, {}];
                    if (mode === 'squad') this.members = [{}, {}, {}, {}];
                }
            }
        }
    </script>

@endsection
