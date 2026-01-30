@extends('layouts.dashboard')

@section('title', 'Organizer Dashboard – GameConnect')
@section('page-title', '🎮 Organizer Dashboard')

@section('dashboard-content')

@php
    $organizer = auth()->user();
@endphp

<!-- 🔹 QUICK STATS -->
<section class="py-10 px-8 grid md:grid-cols-4 gap-8">
    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
        <p class="text-gray-400">Tournaments Hosted</p>
        <h2 class="text-3xl font-bold">{{ $stats['tournaments'] ?? 0 }}</h2>
    </div>


</section>

<!-- 🔹 QUICK ACTIONS -->
<section class="px-8 grid md:grid-cols-4 gap-6 mb-12">
    <a href="{{ route('tournaments.create') }}"
       class="bg-gradient-to-r from-cyan-500 to-purple-600 p-6 rounded-2xl font-bold text-center">
       ➕ Create Tournament
    </a>



</section>

<!-- 🔹 MAIN GRID -->
<section class="px-8 grid md:grid-cols-3 gap-10 pb-20">

    <!-- LEFT -->
    {{-- <div class="md:col-span-2 space-y-10">

        <!-- Tournaments -->
        <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
            <div class="flex justify-between mb-6">
                <h2 class="text-2xl font-bold">🔥 Live & Upcoming</h2>
                <a href="{{ route('tournaments.my') }}" class="text-cyan-400">View All</a>
            </div>

            @forelse ($tournaments as $tournament)
                <div class="flex justify-between bg-slate-800 p-4 rounded-xl mb-3">
                    <div>
                        <h4 class="font-bold">{{ $tournament->title }}</h4>
                        <p class="text-sm text-gray-400">
                            {{ $tournament->game }} • {{ ucfirst($tournament->type) }} •
                            {{ $tournament->start_time->format('d M, h:i A') }}
                        </p>
                    </div>
                    <div class="text-cyan-400">
                        {{ $tournament->participants_count }} / {{ $tournament->max_players }}
                    </div>
                </div>
            @empty
                <p class="text-gray-400">
                    No tournaments yet.  
                    <a href="{{ route('tournaments.create') }}" class="text-cyan-400 underline">
                        Create your first tournament
                    </a>
                </p>
            @endforelse
        </div>


    </div> --}}


</section>

@endsection
