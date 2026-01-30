{{-- ===============================
   FILE: resources/views/auth/register.blade.php
================================ --}}
@extends('layouts.app')
@section('title', 'Register – GameConnect')

@section('content')

    {{-- prevent alpine flicker --}}
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <section class="min-h-screen flex items-center justify-center bg-gradient-to-br from-black via-slate-900 to-purple-950">

        {{-- ✅ SINGLE ALPINE STATE --}}
        <div x-data="{ role: 'organizer' }" x-cloak
            class="max-w-md w-full bg-slate-900 p-10 rounded-3xl shadow-2xl border border-slate-700 fade-up">

            {{-- HEADER --}}
            <h2 class="text-3xl font-extrabold text-center mb-2">🚀 Create Account</h2>
            <p class="text-gray-400 text-center mb-6">
                Join GameConnect as a
                <span x-text="role" class="text-cyan-400 capitalize"></span>
            </p>

            {{-- ROLE SWITCH – PREMIUM (FIXED) --}}
            <div class="relative mb-10">

                {{-- glow (background only) --}}
                <div
                    class="pointer-events-none absolute inset-0 rounded-2xl
                       bg-gradient-to-r from-purple-600/20 to-cyan-500/20
                       blur-xl z-0">
                </div>

                {{-- content --}}
                <div
                    class="relative z-10 bg-slate-800/80 backdrop-blur-xl
                       rounded-2xl p-2 grid grid-cols-2 gap-2">

                    {{-- PLAYER --}}
                    {{-- <button
                    type="button"
                    @click="role = 'player'"
                    class="relative rounded-xl px-4 py-4 text-left transition-all duration-300"
                    :class="role === 'player'
                        ? 'bg-gradient-to-br from-purple-600 to-cyan-500 shadow-xl scale-[1.03]'
                        : 'bg-slate-900 hover:bg-slate-800'"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="text-2xl transition-transform duration-300"
                            :class="role === 'player' ? 'scale-110 rotate-[-6deg]' : ''"
                        >
                            🎮
                        </div>
                        <div>
                            <p class="font-bold text-white">Player</p>
                            <p class="text-xs text-gray-300">Join & compete</p>
                        </div>
                    </div>
                </button> --}}

                    {{-- ORGANIZER --}}
                    <button type="button" @click="role = 'organizer'"
                        class="relative rounded-xl px-4 py-4 text-left transition-all duration-300"
                        :class="role === 'organizer'
                            ?
                            'bg-gradient-to-br from-purple-600 to-cyan-500 shadow-xl scale-[1.03]' :
                            'bg-slate-900 hover:bg-slate-800'">
                        <div class="flex items-center gap-3">
                            <div class="text-2xl transition-transform duration-300"
                                :class="role === 'organizer' ? 'scale-110 rotate-[6deg]' : ''">
                                🏆
                            </div>
                            <div>
                                <p class="font-bold text-white">Organizer</p>
                                <p class="text-xs text-gray-300">Host tournaments</p>
                            </div>
                        </div>
                    </button>

                </div>
            </div>

            {{-- FORM --}}
            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                {{-- ✅ role payload --}}
                <input type="hidden" name="role" :value="role">

                <div>
                    <label class="text-sm text-gray-400">Name</label>
                    <input type="text" name="name" required
                        class="w-full mt-1 px-4 py-3 rounded-xl bg-slate-800
                           border border-slate-700 focus:ring-2 focus:ring-cyan-400">
                </div>

                <div>
                    <label class="text-sm text-gray-400">Email</label>
                    <input type="email" name="email" required
                        class="w-full mt-1 px-4 py-3 rounded-xl bg-slate-800
                           border border-slate-700 focus:ring-2 focus:ring-purple-400">
                </div>

                <div>
                    <label class="text-sm text-gray-400">Password</label>

                    <input type="password" name="password" required
                        class="w-full mt-1 px-4 py-3 rounded-xl bg-slate-800
                           border border-slate-700 focus:ring-2 focus:ring-cyan-400">
                    <p class="mt-1 text-xs text-gray-500">
                        Minimum 8 characters
                    </p>
                </div>

                <div>
                    <label class="text-sm text-gray-400">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full mt-1 px-4 py-3 rounded-xl bg-slate-800
                           border border-slate-700 focus:ring-2 focus:ring-purple-400">
                </div>

                <button
                    class="w-full py-3 rounded-xl font-bold
                       bg-gradient-to-r from-purple-600 to-cyan-500
                       hover:opacity-90 transition">
                    ✨ Register & Verify Email
                </button>
            </form>

        </div>
    </section>
@endsection
