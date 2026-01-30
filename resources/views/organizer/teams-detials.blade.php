@extends('layouts.dashboard')

@section('title','Join Request Details – GameConnect')

@section('dashboard-content')

<!-- HEADER -->
<div class="bg-slate-900 border-b border-slate-800 px-8 py-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold">👥 Join Request Details</h1>
        <p class="text-gray-400">Review team, payment & approve or reject</p>
    </div>

    <a href="{{ route('tournaments.requests') }}"
       class="px-6 py-3 rounded-xl bg-slate-700 hover:bg-slate-600">
        ← Back to Requests
    </a>
</div>

<section class="px-8 py-10 grid md:grid-cols-3 gap-12">

    <!-- 🔹 LEFT: MAIN DETAILS -->
    <div class="md:col-span-2 space-y-10">

        <!-- BASIC INFO -->
        <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
            <h2 class="text-2xl font-bold mb-6">🎯 Team / Player Info</h2>

            <div class="grid md:grid-cols-2 gap-6 text-gray-300">
                <p><b>Team Name:</b> {{ $join->team_name ?? 'Solo Player' }}</p>
                <p><b>Mode:</b> {{ strtoupper($join->mode) }}</p>

                <p><b>Email:</b> {{ $join->email }}</p>
                <p><b>Phone:</b> {{ $join->phone }}</p>

                <p>
                    <b>Join Code:</b>
                    <span class="text-cyan-400 font-bold">{{ $join->join_code }}</span>
                </p>

                <p>
                    <b>Status:</b>
                    @php
                        $statusColors = [
                            'pending' => 'text-yellow-400',
                            'approved' => 'text-green-400',
                            'rejected' => 'text-red-400',
                        ];
                    @endphp
                    <span class="{{ $statusColors[$join->status] }}">
                        {{ ucfirst($join->status) }}
                    </span>
                </p>
            </div>
        </div>

        <!-- TEAM MEMBERS -->
        <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
            <h2 class="text-2xl font-bold mb-2">👥 Team Members</h2>
            <p class="text-sm text-gray-400 mb-6">
                Total Players: {{ 1 + $join->members->count() }}
            </p>

            <div class="space-y-4">

                <!-- MEMBER 1 (Captain treated as member) -->
                <div class="flex justify-between bg-slate-800 p-4 rounded-xl">
                    <span class="font-semibold">Member 1</span>
                    <span>
                        {{ $join->captain_ign }}
                        <span class="text-gray-400 text-sm">
                            ({{ $join->captain_game_id }})
                        </span>
                    </span>
                </div>

                <!-- OTHER MEMBERS -->
                @foreach($join->members as $index => $member)
                    <div class="flex justify-between bg-slate-800 p-4 rounded-xl">
                        <span class="font-semibold">Member {{ $index + 2 }}</span>
                        <span>
                            {{ $member->ign }}
                            <span class="text-gray-400 text-sm">
                                ({{ $member->game_id }})
                            </span>
                        </span>
                    </div>
                @endforeach

            </div>
        </div>

        <!-- NOTES -->
        @if($join->notes)
            <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                <h2 class="text-2xl font-bold mb-4">📝 Player Notes</h2>
                <p class="text-gray-300">{{ $join->notes }}</p>
            </div>
        @endif

        <!-- PAYMENT PROOF -->
        @if($join->is_paid)
            <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                <h2 class="text-2xl font-bold mb-6">💳 Payment Proof</h2>

                <p class="mb-4">
                    Entry Fee:
                    <span class="text-yellow-300 font-bold">₹{{ $join->entry_fee }}</span>
                </p>

                @if($paymentProof)
                    <img src="{{ asset('storage/'.$paymentProof->file_path) }}"
                         class="w-80 rounded-xl border border-slate-700">
                @else
                    <p class="text-red-400">No payment proof uploaded</p>
                @endif
            </div>
        @endif

    </div>

    <!-- 🔹 RIGHT: TOURNAMENT + ACTIONS -->
    <div class="space-y-10">

        <!-- TOURNAMENT INFO -->
        <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
            <h3 class="text-xl font-bold mb-4">🏆 Tournament</h3>

            <p class="font-semibold">{{ $join->tournament->title }}</p>
            <p class="text-gray-400">
                {{ $join->tournament->game }} • {{ strtoupper($join->tournament->mode) }}
            </p>
            <p class="text-gray-400">
                Starts: {{ $join->tournament->start_time->format('d M Y, h:i A') }}
            </p>
        </div>

        <!-- ACTIONS -->
        @if($join->status === 'pending')
            <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700 space-y-6">

                <h3 class="text-xl font-bold">⚡ Actions</h3>

                <!-- APPROVE -->
                <form method="POST" action="{{ route('organizer.requests.approve', $join) }}">
                    @csrf
                    <button class="w-full py-3 rounded-xl bg-green-600 hover:bg-green-700 font-bold">
                        ✅ Approve Entry
                    </button>
                </form>

                <!-- REJECT -->
                <form method="POST" action="{{ route('organizer.requests.reject', $join) }}">
                    @csrf
                    <textarea name="reject_reason"
                              placeholder="Reason for rejection (optional)"
                              class="w-full px-4 py-3 rounded bg-slate-800 border border-slate-700 mb-3"></textarea>

                    <button class="w-full py-3 rounded-xl bg-red-600 hover:bg-red-700 font-bold">
                        ❌ Reject Entry
                    </button>
                </form>

            </div>
        @endif

    </div>

</section>

@endsection
