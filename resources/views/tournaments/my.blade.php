{{-- resources/views/tournaments/my.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'My Tournaments – GameConnect')

@section('dashboard-content')

    <!-- HEADER -->
    <div class="bg-slate-900 border-b border-slate-800 px-8 py-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold">🏆 My Tournaments</h1>
            <p class="text-gray-400">Manage, edit, promote and monitor all your tournaments</p>
        </div>
        <a href="{{ route('tournaments.create') }}"
            class="px-6 py-3 rounded-xl font-bold bg-gradient-to-r from-cyan-500 to-purple-600">
            ➕ Create New
        </a>
    </div>

    <!-- 🔹 FILTERS + SEARCH -->
    <section class="px-8 py-6 grid md:grid-cols-4 gap-6 bg-slate-950">
        <input type="text" placeholder="Search by title..." class="px-4 py-3 rounded bg-slate-800 border border-slate-700">

        <select class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
            <option>All Games</option>
            <option>PUBG</option>
            <option>CODM</option>
            <option>Free Fire</option>
            <option>Valorant</option>
        </select>

        <select class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
            <option>All Status</option>
            <option>Open</option>
            <option>Full</option>
            <option>Ongoing</option>
            <option>Completed</option>
            <option>Cancelled</option>
        </select>

        <select class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
            <option>Sort by Date</option>
            <option>Newest First</option>
            <option>Oldest First</option>
            <option>Highest Prize</option>
        </select>
    </section>

    <!-- 🔹 ADS SLOT (TOP BANNER) -->
    <x-ad-slot page="tournaments" position="header" />


    <!-- 🔹 TOURNAMENT LIST (CARD + TABLE HYBRID) -->
    <section class="px-8 py-10 grid md:grid-cols-2 gap-10">

        @forelse($tournaments as $tournament)
            @php
                $now = now();

                $isRegistrationClosed =
                    $tournament->registration_close_time &&
                    $now->greaterThanOrEqualTo($tournament->registration_close_time);

                $isMatchStarted = $tournament->start_time && $now->greaterThanOrEqualTo($tournament->start_time);
            @endphp
            <!-- TOURNAMENT CARD -->
            <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700 hover:shadow-2xl transition">
                {{-- ⚠️ ROOM DETAILS WARNING --}}
                @if (!$tournament->room_id || !$tournament->room_password)
                    <div
                        class="mb-4 px-4 py-2 rounded-xl bg-orange-500/20 border border-orange-500 text-orange-300 text-sm font-semibold flex items-center justify-between">
                        <span>⚠️ Room details not added yet</span>

                        <a href="{{ route('tournaments.edit', $tournament) }}"
                            class="px-3 py-1 rounded bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold">
                            Add Now
                        </a>
                    </div>
                @endif

                <!-- TOP INFO -->
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <span class="text-sm text-cyan-400">
                            {{ $tournament->game }} • {{ ucfirst($tournament->mode) }}
                        </span>
                        <h3 class="text-2xl font-bold mt-1">{{ $tournament->title }}</h3>
                        <p class="text-sm text-gray-400">
                            Starts: {{ $tournament->start_time->format('d M Y, h:i A') }}
                        </p>
                    </div>

                    <!-- STATUS BADGE -->
                    @php
                        $now = now();

                        if ($tournament->start_time && $now->greaterThanOrEqualTo($tournament->start_time)) {
                            $statusText = 'Match Started';
                            $statusColor = 'bg-blue-600';
                        } elseif (
                            $tournament->registration_close_time &&
                            $now->greaterThanOrEqualTo($tournament->registration_close_time)
                        ) {
                            $statusText = 'Registration Closed';
                            $statusColor = 'bg-yellow-500';
                        } else {
                            $statusText = 'Open';
                            $statusColor = 'bg-green-600';
                        }
                    @endphp

                    <span class="px-3 py-1 rounded-full text-sm {{ $statusColor }}">
                        {{ $statusText }}
                    </span>

                </div>

                <!-- STATS GRID -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="bg-slate-800 p-4 rounded-xl text-center">
                        <p class="text-sm text-gray-400">Slots</p>
                        <p class="font-bold">
                            {{ $tournament->filled_slots }} / {{ $tournament->slots }}
                        </p>
                    </div>

                    <div class="bg-slate-800 p-4 rounded-xl text-center">
                        <p class="text-sm text-gray-400">Prize Pool</p>
                        <p class="font-bold text-yellow-300">
                            ₹{{ number_format(
                                ($tournament->first_prize ?? 0) + ($tournament->second_prize ?? 0) + ($tournament->third_prize ?? 0),
                            ) }}
                        </p>
                    </div>

                    <div class="bg-slate-800 p-4 rounded-xl text-center">
                        <p class="text-sm text-gray-400">Earnings</p>
                        <p class="font-bold text-green-400">
                            ₹{{ number_format($tournament->filled_slots * $tournament->entry_fee) }}
                        </p>
                    </div>
                </div>

                <!-- PROGRESS BAR -->
                @php
                    $percent =
                        $tournament->slots > 0 ? round(($tournament->filled_slots / $tournament->slots) * 100) : 0;
                @endphp

                <div class="mb-6">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-400">Registration Progress</span>
                        <span class="text-cyan-400">{{ $percent }}%</span>
                    </div>
                    <div class="w-full bg-slate-800 rounded-full h-3">
                        <div class="bg-gradient-to-r from-cyan-500 to-purple-600 h-3 rounded-full"
                            style="width: {{ $percent }}%"></div>
                    </div>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="grid grid-cols-4 gap-4 text-sm font-semibold">

                    {{-- MANAGE JOINS (allowed until match starts) --}}
                    @if (!$isMatchStarted)
                        <a href="{{ route('organizer.requests', $tournament->id) }}"
                            class="text-center py-2 rounded bg-cyan-600 hover:bg-cyan-700">
                            Manage
                        </a>
                    @endif

                    {{-- EDIT (blocked after match starts) --}}
                    @if (!$isMatchStarted)
                        <a href="{{ route('tournaments.edit', $tournament) }}"
                            class="text-center py-2 rounded bg-slate-700 hover:bg-slate-600">
                            Edit
                        </a>
                    @endif

                    {{-- VIEW (always allowed) --}}
                    <a href="{{ route('tournaments.show', $tournament) }}"
                        class="text-center py-2 rounded bg-purple-600 hover:bg-purple-700">
                        View
                    </a>
                    {{--  Manuval organizer join --}}
                    <a href="{{ route('organizer.joins.create', $tournament) }}"
                        class="block text-center py-2 rounded bg-indigo-600 hover:bg-indigo-700 font-semibold">
                        ➕ Add Participants
                    </a>

                    {{-- DELETE (blocked after match starts) --}}
                    @if (!$isMatchStarted)
                        <button type="button" onclick="openDeleteModal({{ $tournament->id }})"
                            class="w-full py-2 rounded bg-red-600 hover:bg-red-700 font-semibold">
                            🗑️ Delete
                        </button>

                        <form id="delete-form-{{ $tournament->id }}"
                            action="{{ route('tournaments.destroy', $tournament) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif

                </div>


                <!-- EXTRA INFO -->
                <!-- EXTRA INFO -->
                <div class="mt-6 grid md:grid-cols-3 gap-4 text-sm text-gray-300">

                    <!-- Approved Teams -->
                    <div class="bg-slate-800 p-4 rounded-xl text-center">
                        <p class="text-sm text-gray-400">👥 Approved Teams</p>
                        <p class="font-bold text-white">{{ $tournament->approved_teams }}</p>
                    </div>

                    <!-- Type -->
                    <div class="bg-slate-800 p-4 rounded-xl text-center">
                        <p class="text-sm text-gray-400">💰 Type</p>
                        <p class="font-bold {{ $tournament->is_paid ? 'text-yellow-300' : 'text-green-400' }}">
                            {{ $tournament->is_paid ? 'Paid' : 'Free' }}
                        </p>
                    </div>

                    <!-- Room / Upload Results -->
                    <div class="bg-slate-800 p-4 rounded-xl text-center">

                        @if ($isMatchStarted)
                            {{-- Upload Results takes this slot --}}
                            <a href="{{ route('organizer.results.upload', $tournament) }}"
                                class="block w-full py-3 rounded-xl font-bold
                      bg-gradient-to-r from-emerald-500 to-green-600 hover:opacity-90">
                                📊 Upload Results
                            </a>
                        @else
                            {{-- Room status before match start --}}
                            @if ($tournament->room_released)
                                <p class="text-green-400 font-semibold">📤 Room Sent</p>
                            @elseif ($tournament->room_id && $tournament->room_password)
                                <p class="text-yellow-400 font-semibold">🔐 Room Ready</p>
                                <p class="text-xs text-gray-400">(Not Sent)</p>
                            @else
                                <p class="text-orange-400 font-semibold">🔐 Room Not Set</p>
                            @endif
                        @endif

                    </div>

                </div>

                {{-- 🔥 SEND ROOM DETAILS BUTTON --}}
                @if (!$isMatchStarted && $tournament->room_id && $tournament->room_password && !$tournament->room_released)
                    <form action="{{ route('organizer.tournaments.sendRoom', $tournament) }}" method="POST"
                        onsubmit="return confirm('Send room details to ALL approved teams?')" class="mt-6">
                        @csrf
                        <button
                            class="w-full py-3 rounded-xl font-bold
                   bg-gradient-to-r from-green-500 to-emerald-600 hover:opacity-90">
                            📤 Send Room Details to Approved Teams
                        </button>
                    </form>
                @endif


            </div>
        @empty
       
                <div
                    class="mb-4 px-4 py-2 rounded-xl bg-orange-500/20 border border-orange-500 text-orange-300 text-sm font-semibold">
                    ⚠️ NO rooms created
                </div>
        
        @endforelse


    </section>
    <!-- DELETE CONFIRM MODAL -->
    <div id="deleteModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-50">

        <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700 w-full max-w-md">

            <h2 class="text-2xl font-bold text-red-400 mb-4">
                ⚠️ Delete Tournament
            </h2>

            <p class="text-gray-300 mb-6">
                This action <span class="text-red-400 font-semibold">cannot be undone</span>.
                All joins, results, and data will be permanently removed.
            </p>

            <div class="flex justify-end gap-4">
                <button onclick="closeDeleteModal()" class="px-5 py-2 rounded bg-slate-700 hover:bg-slate-600">
                    Cancel
                </button>

                <button onclick="confirmDelete()" class="px-5 py-2 rounded bg-red-600 hover:bg-red-700 font-bold">
                    Yes, Delete
                </button>
            </div>
        </div>
    </div>
    <script>
        let deleteTournamentId = null;

        function openDeleteModal(id) {
            deleteTournamentId = id;
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('deleteModal').classList.add('flex');
        }

        function closeDeleteModal() {
            deleteTournamentId = null;
            document.getElementById('deleteModal').classList.add('hidden');
            document.getElementById('deleteModal').classList.remove('flex');
        }

        function confirmDelete() {
            if (!deleteTournamentId) return;

            document.getElementById(`delete-form-${deleteTournamentId}`).submit();
        }
    </script>


    <!-- 🔹 ADS SLOT (TOP BANNER) -->
    <x-ad-slot page="tournaments" position="header" />

@endsection
