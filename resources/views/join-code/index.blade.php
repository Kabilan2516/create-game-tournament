@extends('layouts.app')

@section('title', 'Join Tournament Using Join Code | GameConnect')

@section('content')
<section class="bg-slate-950 min-h-screen py-24">
    <div class="max-w-7xl mx-auto px-6">

        <!-- SEO HEADER -->
        <header class="text-center max-w-2xl mx-auto mb-14">
            <h1 class="text-4xl md:text-5xl font-extrabold mb-6 leading-tight">
                Join Your Tournament Team
            </h1>

            <p class="text-gray-300 text-lg leading-relaxed">
                Enter your unique <span class="text-cyan-400 font-semibold">Join Code</span> to view
                your tournament registration, team details, and update player information before the match starts.
            </p>
        </header>

        <!-- JOIN CODE CARD -->
        <div class="max-w-md mx-auto bg-slate-900 p-8 rounded-3xl border border-slate-700">

            <h2 class="text-2xl font-bold mb-4 text-center">
                🔐 Enter Join Code
            </h2>

            <p class="text-sm text-gray-400 text-center mb-6">
                This code was provided after you joined a tournament.
                Keep it safe to manage your team.
            </p>

            <form method="POST"
                  action="{{ route('join.code.lookup') }}"
                  class="space-y-6">
                @csrf

                <input type="text"
                       name="join_code"
                       required
                       minlength="6"
                       placeholder="E.g. AB3D9KXQ"
                       class="w-full px-4 py-4 rounded-xl bg-slate-800 border border-slate-700
                              text-center text-lg uppercase tracking-widest
                              focus:outline-none focus:ring-2 focus:ring-cyan-500">

                @error('join_code')
                    <p class="text-sm text-red-400 text-center">
                        {{ $message }}
                    </p>
                @enderror

                <button
                    class="w-full py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-purple-600
                           font-bold text-lg hover:opacity-90 transition">
                    View Team Details
                </button>
            </form>
        </div>

        <!-- INFO SECTION (SEO VALUE) -->
        <section class="max-w-3xl mx-auto mt-20 text-gray-300">
            <h3 class="text-2xl font-bold mb-6 text-center">
                Why use a Join Code?
            </h3>

            <div class="grid md:grid-cols-3 gap-8 text-sm">

                <div class="bg-slate-900 p-6 rounded-2xl border border-slate-800">
                    <h4 class="font-semibold mb-2 text-cyan-400">👥 Manage Team</h4>
                    <p>
                        View your solo, duo, or squad team details and update player IGN or Game IDs
                        before the match starts.
                    </p>
                </div>

                <div class="bg-slate-900 p-6 rounded-2xl border border-slate-800">
                    <h4 class="font-semibold mb-2 text-purple-400">⏰ Edit Before Match</h4>
                    <p>
                        Team editing is allowed only before the match start time to ensure fair play
                        for all participants.
                    </p>
                </div>

                <div class="bg-slate-900 p-6 rounded-2xl border border-slate-800">
                    <h4 class="font-semibold mb-2 text-green-400">🔒 Secure Access</h4>
                    <p>
                        Each Join Code is unique and securely linked to your tournament entry.
                        No login required.
                    </p>
                </div>

            </div>
        </section>

    </div>
</section>
@endsection
