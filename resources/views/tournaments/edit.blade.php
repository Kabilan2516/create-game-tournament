@extends('layouts.dashboard')

@section('title', 'Edit Tournament – ' . $tournament->title)

@section('dashboard-content')

<!-- HEADER -->
<div class="bg-slate-900 border-b border-slate-800 px-8 py-6">

    <h1 class="text-3xl font-bold">✏️ Edit Tournament</h1>
    <p class="text-gray-400">Update tournament details and manage room information</p>

    {{-- ⚠️ ROOM WARNING --}}
    @if(!$tournament->room_id || !$tournament->room_password)
        <div class="mt-4 px-6 py-3 rounded-xl bg-orange-500/20 border border-orange-500 text-orange-300 font-semibold">
            ⚠️ Room ID & Password not added yet. Players cannot receive room details until you add and release them.
        </div>
    @endif
</div>

<section class="px-8 py-12 max-w-6xl">

<form method="POST" action="{{ route('tournaments.update', $tournament) }}" enctype="multipart/form-data" class="space-y-12">
@csrf
@method('PUT')

{{-- ================= BASIC DETAILS (SAME AS CREATE, BUT PREFILLED) ================= --}}

<div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
    <h2 class="text-2xl font-bold mb-6">🏆 Tournament Basics</h2>

    <div class="grid md:grid-cols-2 gap-6">

        <input type="text" name="title" value="{{ $tournament->title }}" required
            class="px-4 py-3 rounded bg-slate-800 border border-slate-700">

        <input type="text" name="description" value="{{ $tournament->description }}"
            class="px-4 py-3 rounded bg-slate-800 border border-slate-700">

        <select name="mode" class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
            <option value="solo" {{ $tournament->mode=='solo'?'selected':'' }}>Solo</option>
            <option value="duo" {{ $tournament->mode=='duo'?'selected':'' }}>Duo</option>
            <option value="squad" {{ $tournament->mode=='squad'?'selected':'' }}>Squad</option>
        </select>

        <input type="number" name="slots" value="{{ $tournament->slots }}"
            class="px-4 py-3 rounded bg-slate-800 border border-slate-700">

        <input type="datetime-local" name="start_time"
            value="{{ $tournament->start_time->format('Y-m-d\TH:i') }}"
            class="px-4 py-3 rounded bg-slate-800 border border-slate-700">

        <input type="datetime-local" name="registration_close_time"
            value="{{ $tournament->registration_close_time->format('Y-m-d\TH:i') }}"
            class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
    </div>
</div>

{{-- ================= 🔐 ROOM MANAGEMENT (MAIN PART) ================= --}}

<div class="bg-slate-900 p-8 rounded-3xl border 
    {{ (!$tournament->room_id || !$tournament->room_password) ? 'border-orange-500' : 'border-green-600' }}">

    <h2 class="text-2xl font-bold mb-4">🔐 Room Details</h2>

    <p class="text-sm text-gray-400 mb-6">
        These details will be sent to approved teams through PWA / WhatsApp after you release them.
    </p>

    <div class="grid md:grid-cols-2 gap-6">

        <!-- ROOM ID -->
        <div>
            <label class="block text-sm text-gray-400 mb-2">Room ID</label>
            <input type="text" name="room_id" value="{{ $tournament->room_id }}"
                placeholder="Enter Room ID"
                class="px-4 py-3 rounded bg-slate-800 border border-slate-700 w-full">
        </div>

        <!-- ROOM PASSWORD -->
        <div>
            <label class="block text-sm text-gray-400 mb-2">Room Password</label>
            <input type="text" name="room_password" value="{{ $tournament->room_password }}"
                placeholder="Enter Room Password"
                class="px-4 py-3 rounded bg-slate-800 border border-slate-700 w-full">
        </div>

    </div>

    {{-- STATUS --}}
    <div class="mt-6">

        @if($tournament->room_released)
            <div class="px-5 py-3 rounded-xl bg-green-600/20 border border-green-600 text-green-400 font-bold">
                ✅ Room details already released to players
            </div>
        @else
            <div class="px-5 py-3 rounded-xl bg-yellow-500/20 border border-yellow-500 text-yellow-300">
                🔒 Room not released yet. Players cannot see it.
            </div>
        @endif

    </div>

    {{-- ACTION BUTTONS --}}
    <div class="flex flex-wrap gap-4 mt-8">

        <!-- SAVE ROOM -->
        <button type="submit"
            class="px-8 py-3 rounded-xl font-bold bg-cyan-600 hover:bg-cyan-700">
            💾 Save Changes
        </button>

        <!-- RELEASE ROOM -->
        @if(!$tournament->room_released)

            @if($tournament->room_id && $tournament->room_password)
                <a href="{{ route('tournaments.release-room', $tournament) }}"
                   onclick="return confirm('Release room details to all approved teams?')"
                   class="px-8 py-3 rounded-xl font-bold bg-green-600 hover:bg-green-700">
                    🔓 Release Room to Players
                </a>
            @else
                <button disabled
                    class="px-8 py-3 rounded-xl font-bold bg-gray-600 cursor-not-allowed">
                    🔓 Release Room (Add details first)
                </button>
            @endif

        @endif

    </div>

</div>

</form>
</section>

@endsection
