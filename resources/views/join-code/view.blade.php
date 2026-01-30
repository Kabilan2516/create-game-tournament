@extends('layouts.app')

@section('title', 'Join Code Details')

@section('content')
<section class="bg-slate-950 py-20">
    <div class="max-w-7xl mx-auto px-6">

        @php
            $canEdit = !$isMatchStarted && $join->status !== 'rejected';

            $maxPlayers = match($join->mode) {
                'solo' => 1,
                'duo' => 2,
                'squad' => 4,
            };

            $players = collect([
                [
                    'ign' => $join->captain_ign,
                    'game_id' => $join->captain_game_id,
                ],
                ...$join->members->map(fn($m) => [
                    'ign' => $m->ign,
                    'game_id' => $m->game_id,
                ])
            ]);

            $slotsLeft = $maxPlayers - $players->count();

            $statusColors = [
                'pending' => 'bg-yellow-500/20 text-yellow-400',
                'approved' => 'bg-green-500/20 text-green-400',
                'rejected' => 'bg-red-500/20 text-red-400',
            ];
        @endphp

        <div class="grid md:grid-cols-3 gap-10">

            <!-- LEFT -->
            <div class="md:col-span-2">

                <div class="bg-slate-900 p-8 rounded-3xl border border-slate-800">

                    <!-- HEADER -->
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <h2 class="text-2xl font-bold">🎫 Tournament Entry</h2>
                            <p class="text-sm text-gray-400">
                                {{ $tournament->title }} • {{ strtoupper($join->mode) }}
                            </p>
                        </div>

                        <span class="px-4 py-2 rounded-full bg-slate-800 font-mono text-cyan-400">
                            {{ $join->join_code }}
                        </span>
                    </div>

                    <!-- STATUS -->
                    <div class="mb-6 flex items-center gap-3">
                        <span class="px-4 py-1 rounded-full text-sm font-semibold {{ $statusColors[$join->status] }}">
                            {{ ucfirst($join->status) }}
                        </span>

                        @if ($isMatchStarted)
                            <span class="text-sm text-red-400 font-semibold">
                                ⛔ Match Started
                            </span>
                        @endif
                    </div>

                    <!-- FORM -->
                    <form method="POST"
                          action="{{ route('join.code.update', $join) }}"
                          class="space-y-8">
                        @csrf

                        <!-- TEAM NAME -->
                        @if ($join->mode !== 'solo')
                            <div>
                                <label class="block text-sm text-gray-400 mb-2">Team Name</label>
                                <input type="text"
                                       name="team_name"
                                       value="{{ old('team_name', $join->team_name) }}"
                                       {{ !$canEdit ? 'disabled' : '' }}
                                       class="w-full px-4 py-3 rounded-xl bg-slate-800 border border-slate-700">
                            </div>
                        @endif

                        <!-- CONTACT INFO -->
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm text-gray-400 mb-2">
                                    📧 Email
                                    @if($join->mode === 'solo')
                                        <span class="text-xs text-gray-500">(required)</span>
                                    @else
                                        <span class="text-xs text-gray-500">(team contact)</span>
                                    @endif
                                </label>
                                <input type="email"
                                       name="email"
                                       value="{{ old('email', $join->email) }}"
                                       {{ !$canEdit ? 'disabled' : '' }}
                                       class="w-full px-4 py-3 rounded-xl bg-slate-800 border border-slate-700">
                            </div>

                            <div>
                                <label class="block text-sm text-gray-400 mb-2">
                                    📞 Phone
                                    @if($join->mode === 'solo')
                                        <span class="text-xs text-gray-500">(required)</span>
                                    @else
                                        <span class="text-xs text-gray-500">(team contact)</span>
                                    @endif
                                </label>
                                <input type="text"
                                       name="phone"
                                       value="{{ old('phone', $join->phone) }}"
                                       {{ !$canEdit ? 'disabled' : '' }}
                                       class="w-full px-4 py-3 rounded-xl bg-slate-800 border border-slate-700">
                            </div>
                        </div>

                        <!-- PLAYERS -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-bold">
                                👥 Players ({{ $players->count() }} / {{ $maxPlayers }})
                            </h3>

                            @foreach ($players as $index => $player)
                                <div class="grid md:grid-cols-2 gap-4 bg-slate-800 p-4 rounded-xl">
                                    <input type="text"
                                           name="{{ $index === 0 ? 'captain_ign' : "members[$index][ign]" }}"
                                           value="{{ $player['ign'] }}"
                                           placeholder="Player {{ $index + 1 }} IGN"
                                           {{ !$canEdit ? 'disabled' : '' }}
                                           class="px-4 py-3 rounded bg-slate-900 border border-slate-700">

                                    <input type="text"
                                           name="{{ $index === 0 ? 'captain_game_id' : "members[$index][game_id]" }}"
                                           value="{{ $player['game_id'] }}"
                                           placeholder="Player {{ $index + 1 }} Game ID"
                                           {{ !$canEdit ? 'disabled' : '' }}
                                           class="px-4 py-3 rounded bg-slate-900 border border-slate-700">
                                </div>
                            @endforeach

                            <!-- ADD EMPTY PLAYER SLOTS -->
                            @if ($canEdit && $slotsLeft > 0)
                                @for ($i = 0; $i < $slotsLeft; $i++)
                                    @php $index = $players->count() + $i; @endphp
                                    <div class="grid md:grid-cols-2 gap-4 bg-slate-800/40 p-4 rounded-xl border border-dashed border-slate-700">
                                        <input type="text"
                                               name="members[{{ $index }}][ign]"
                                               placeholder="New Player IGN"
                                               class="px-4 py-3 rounded bg-slate-900 border border-slate-700">

                                        <input type="text"
                                               name="members[{{ $index }}][game_id]"
                                               placeholder="New Player Game ID"
                                               class="px-4 py-3 rounded bg-slate-900 border border-slate-700">
                                    </div>
                                @endfor
                            @endif
                        </div>

                        <!-- SAVE -->
                        @if ($canEdit)
                            <div class="pt-6 text-right">
                                <button
                                    class="px-10 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-purple-600 font-bold">
                                    💾 Save Changes
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- RIGHT -->
            <div>
                <div class="bg-slate-900 p-6 rounded-2xl border border-slate-800">
                    <h3 class="font-bold mb-4">ℹ️ Info</h3>
                    <ul class="text-sm text-gray-400 space-y-2">
                        <li>• Email & phone are required for communication</li>
                        <li>• One contact per team (duo / squad)</li>
                        <li>• You can add players until team is full</li>
                        <li>• Editing locks after match start</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
