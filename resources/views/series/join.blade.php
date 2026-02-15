@extends('layouts.app')

@section('title', 'Join Series – ' . $series->title)

@section('content')
    @php
        $baseTeamSize = match (strtolower($series->mode)) {
            'solo' => 1,
            'duo' => 2,
            'squad' => 4,
            default => 1,
        };
        $substituteCount = (int) ($series->substitute_count ?? 0);
        $maxTeamSize = $baseTeamSize + $substituteCount;
        $openMatchCount = $joinableTournaments->count();
        $seriesSlots = (int) ($seriesSlots ?? (strtolower($series->mode) === 'squad' ? 25 : (strtolower($series->mode) === 'duo' ? 50 : 100)));
    @endphp

    <section class="relative bg-black">
        @if ($series->banner)
            <div class="h-72 bg-cover bg-center" style="background-image:url('{{ $series->banner->url }}')"></div>
        @else
            <div class="h-72 bg-gradient-to-br from-slate-900 via-cyan-900 to-slate-900"></div>
        @endif

        <div class="absolute inset-0 bg-black/60"></div>
        <div class="absolute inset-0 flex items-center">
            <div class="max-w-6xl mx-auto px-6 text-white">
                <h1 class="text-4xl font-extrabold mb-2">🚀 Join {{ $series->title }}</h1>
                <p class="text-gray-300">Register once to enter all open matches in this series.</p>
            </div>
        </div>
    </section>

    <section class="py-20 bg-black">
        <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-3 gap-12">
            <div class="md:col-span-2 bg-slate-900 p-10 rounded-3xl border border-slate-700">
                <h2 class="text-3xl font-bold mb-8">🎯 Series Registration</h2>

                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-red-200 text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if ($joinableTournaments->isEmpty())
                    <div class="mb-6 rounded-xl border border-amber-500/40 bg-amber-500/10 px-4 py-4 text-amber-200 text-sm">
                        No open matches right now. You can still register for this series and organizer can map you to upcoming matches.
                    </div>
                @endif

                <form method="POST" action="{{ route('series.join.store', $series) }}" enctype="multipart/form-data"
                    class="space-y-8" x-data="seriesJoinForm({{ $maxTeamSize }})">
                    @csrf

                    <p class="text-gray-300">
                        👥 Mode: <span class="font-bold text-white">{{ strtoupper($series->mode) }}</span>
                    </p>
                    <p class="text-sm text-gray-400 -mt-4">
                        Base Team Size: {{ $baseTeamSize }}
                        @if ($substituteCount > 0)
                            • Substitutes: {{ $substituteCount }} • Max Total: {{ $maxTeamSize }}
                        @endif
                    </p>

                    @if (strtolower($series->mode) !== 'solo')
                        <div>
                            <label class="text-sm text-gray-400">Team Name *</label>
                            <input type="text" name="team_name" required
                                class="w-full mt-1 px-4 py-3 rounded bg-slate-800 border border-slate-700">
                        </div>
                    @endif

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

                    <div class="space-y-6">
                        <h3 class="text-xl font-bold">👥 Players</h3>
                        @if ($substituteCount > 0)
                            <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                                First {{ $baseTeamSize }} field(s) are main lineup. Last {{ $substituteCount }} field(s) are substitutes.
                            </div>
                        @endif

                        <template x-for="(member, index) in members" :key="index">
                            <div class="grid md:grid-cols-2 gap-6 p-6 rounded-2xl border"
                                :class="index >= {{ $baseTeamSize }} ? 'bg-emerald-900/20 border-emerald-500/40' : 'bg-slate-800/40 border-slate-700'">
                                <div class="md:col-span-2">
                                    <div class="flex items-center gap-2">
                                        <p class="font-semibold text-cyan-400"
                                            x-text="index === 0 ? '⭐ Captain' : (index >= {{ $baseTeamSize }} ? 'Substitute ' + (index - {{ $baseTeamSize }} + 1) : 'Member ' + (index + 1))"></p>
                                        @if ($substituteCount > 0)
                                            <span x-show="index >= {{ $baseTeamSize }}"
                                                class="text-xs px-2 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                                                Substitute
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <label class="text-sm text-gray-400">In-Game ID *</label>
                                    <input type="text" :name="`members[${index}][game_id]`" required
                                        class="w-full mt-1 px-4 py-3 rounded bg-slate-900 border border-slate-700">
                                </div>

                                <div>
                                    <label class="text-sm text-gray-400">In-Game Name (IGN) *</label>
                                    <input type="text" :name="`members[${index}][ign]`" required
                                        class="w-full mt-1 px-4 py-3 rounded bg-slate-900 border border-slate-700">
                                </div>
                            </div>
                        </template>
                    </div>

                    <div>
                        <label class="text-sm text-gray-400">Notes (optional)</label>
                        <textarea name="notes" rows="3" class="w-full mt-1 px-4 py-3 rounded bg-slate-800 border border-slate-700"></textarea>
                    </div>

                    @if ($series->is_paid)
                        <div class="bg-slate-800 p-6 rounded-2xl border border-green-600 space-y-4">
                            <h3 class="text-xl font-bold text-green-400">💳 Series Entry Payment</h3>
                            <p class="text-gray-300">Amount: <span class="font-bold text-yellow-300">₹{{ number_format((float) $series->entry_fee, 0) }}</span></p>
                            <p>UPI ID: <span class="font-bold text-cyan-400">{{ $series->upi_id }}</span></p>
                            @if ($series->upi_name)
                                <p>Payee Name: <span class="font-bold">{{ $series->upi_name }}</span></p>
                            @endif
                            @if ($series->upi_qr)
                                <img src="{{ asset('storage/' . $series->upi_qr) }}" class="w-48 rounded-xl" alt="UPI QR">
                            @endif

                            <div>
                                <label class="text-sm text-gray-400">Upload Payment Screenshot *</label>
                                <input type="file" name="payment_proof" required
                                    class="w-full mt-1 px-4 py-3 rounded bg-slate-900 border border-slate-700">
                            </div>
                        </div>
                    @endif

                    <button type="submit" class="w-full py-4 rounded-xl font-bold text-lg bg-gradient-to-r from-cyan-500 to-purple-600 hover:opacity-90">
                        ✅ Register For Series
                    </button>
                </form>
            </div>

            <div class="space-y-8">
                <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                    <h3 class="text-xl font-bold mb-4">🏆 Series Summary</h3>
                    <div class="space-y-3 text-gray-300">
                        <p>🎮 Game: <span class="text-white">{{ $series->game ?? 'CODM' }}</span></p>
                        <p>👥 Mode: <span class="text-white">{{ strtoupper($series->mode) }}</span></p>
                        <p>🔁 Substitutes: <span class="text-white">{{ $substituteCount }}</span></p>
                        <p>📦 Series Slots: <span class="text-white">{{ $seriesSlots }}</span></p>
                        <p>✅ Registered: <span class="text-cyan-300 font-semibold">{{ $registeredCount ?? 0 }}</span></p>
                        <p>💳 Entry: <span class="text-white">{{ $series->is_paid ? 'PAID' : 'FREE' }}</span></p>
                        <p>🧩 Open Matches: <span class="text-emerald-400 font-semibold">{{ $openMatchCount }}</span></p>
                        <p>🎟 Available Series Slots:
                            <span class="text-emerald-400 font-semibold">{{ $availableSeriesSlots ?? max(0, $seriesSlots - ($registeredCount ?? 0)) }}</span>
                        </p>
                    </div>
                </div>

                <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                    <h3 class="text-xl font-bold mb-4">📅 Will Join These Matches</h3>
                    <div class="space-y-3 text-sm text-gray-300">
                        @forelse ($joinableTournaments as $match)
                            <div class="border border-slate-700 rounded-lg px-3 py-2">
                                <p class="font-semibold text-white">{{ $match->title }}</p>
                                <p class="text-gray-400">{{ $match->start_time?->format('d M Y, h:i A') }}</p>
                                <p class="text-xs text-emerald-300 mt-1">
                                    Slots: {{ max(0, (int) $match->slots - (int) $match->filled_slots) }} available
                                </p>
                            </div>
                        @empty
                            <p class="text-amber-300">No open matches currently. Registration will be saved at series level.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function seriesJoinForm(maxMembers) {
            return {
                members: [],
                init() {
                    this.members = Array.from({
                        length: Math.max(1, parseInt(maxMembers || 1, 10))
                    }, () => ({}));
                }
            }
        }
    </script>
@endsection
