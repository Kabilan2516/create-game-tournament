{{-- ===============================
   FILE: resources/views/auth/forgot-password.blade.php
================================ --}}
@extends('layouts.app')
@section('title','Forgot Password – GameConnect')
@section('content')

<section class="min-h-screen flex items-center justify-center bg-slate-950">
    <div class="max-w-md w-full bg-slate-900 p-10 rounded-3xl border border-slate-700">

        <h2 class="text-2xl font-bold mb-6">🔑 Reset Password</h2>
        <p class="text-gray-400 mb-6">Enter your email to receive reset link</p>

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            <div>
                <label class="text-sm text-gray-400">Email</label>
                <input type="email" name="email" required
                       class="w-full mt-1 px-4 py-3 rounded bg-slate-800 border border-slate-700 focus:ring-2 focus:ring-cyan-400">
            </div>

            <button class="w-full py-3 rounded-xl font-bold bg-gradient-to-r from-cyan-500 to-purple-600">
                📩 Send Reset Link
            </button>
        </form>
    </div>
</section>
@endsection
