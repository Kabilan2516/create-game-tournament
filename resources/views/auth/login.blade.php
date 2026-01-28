{{-- ===============================
   AUTH UI SET FOR LARAVEL BREEZE (API MODE)
   Stunning Gaming-Themed Pages
   Place files inside resources/views/auth/
================================ --}}

{{-- ===============================
   FILE: resources/views/auth/login.blade.php
================================ --}}
@extends('layouts.app')
@section('title', 'Login – GameConnect')
@section('content')

    <section class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-950 via-purple-950 to-black">
        <div class="max-w-md w-full bg-slate-900 p-10 rounded-3xl shadow-2xl border border-slate-700 fade-up">

            <h2 class="text-3xl font-extrabold text-center mb-6">🎮 Welcome Back</h2>
            <p class="text-gray-400 text-center mb-8">Login to manage tournaments & dashboard</p>

            <form method="POST" action="{{ route('login') }}" class="space-y-6" x-data="{ loading: false }"
                @submit="loading = true">
                @csrf

                <div>
                    <label class="text-sm text-gray-400">Email</label>
                    <input type="email" name="email" required
                        class="w-full mt-1 px-4 py-3 rounded bg-slate-800 border border-slate-700 focus:ring-2 focus:ring-cyan-400">
                </div>

                <div>
                    <label class="text-sm text-gray-400">Password</label>
                    <input type="password" name="password" required
                        class="w-full mt-1 px-4 py-3 rounded bg-slate-800 border border-slate-700 focus:ring-2 focus:ring-purple-400">
                </div>

                <button
                    class="w-full py-3 rounded-xl font-bold bg-gradient-to-r from-cyan-500 to-purple-600 hover:opacity-90">
                    🔐 Login
                </button>

                <div class="flex justify-between text-sm text-gray-400 mt-4">
                    <a href="{{ route('password.email') }}" class="hover:text-cyan-400">Forgot Password?</a>
                    <a href="{{ route('register') }}" class="hover:text-purple-400">Create Account</a>
                </div>
            </form>
        </div>
    </section>
@endsection
