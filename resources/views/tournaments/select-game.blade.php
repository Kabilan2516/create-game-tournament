@extends('layouts.dashboard')

@section('title', 'Create Tournament')

@section('dashboard-content')

    <div class="px-8 py-10 max-w-4xl mx-auto">

        <h1 class="text-3xl font-bold mb-6 text-center">
            🎮 Choose Tournament Game
        </h1>

        <div class="grid md:grid-cols-2 gap-8">

            <!-- CODM -->
            <a href="{{ route('tournaments.create.form', 'CODM') }}"
                class="group bg-slate-900 border border-slate-700 rounded-3xl p-8
                  hover:border-cyan-500 hover:shadow-xl transition">

                <h2 class="text-2xl font-bold mb-2 group-hover:text-cyan-400">
                    Call of Duty Mobile
                </h2>

                <p class="text-gray-400">
                    Multiplayer & Battle Royale tournaments
                </p>
                <p class="text-xs text-cyan-400 mt-3">
                    Dynamic prizes • CODM points system ready
                </p>
            </a>

            <!-- PUBG (Coming Soon) -->
            <div
                class="relative bg-slate-900 border border-slate-700 rounded-3xl p-8
           opacity-60 cursor-not-allowed">

                <!-- BADGE -->
                <span
                    class="absolute top-4 right-4 text-xs px-3 py-1 rounded-full
                 bg-purple-500/20 text-purple-400 font-semibold">
                    Coming Soon
                </span>

                <h2 class="text-2xl font-bold mb-2 text-gray-300">
                    PUBG Mobile
                </h2>

                <p class="text-gray-400">
                    Classic BR & competitive matches
                </p>
            </div>


        </div>
    </div>

@endsection
