@extends('layouts.app')

@section('title','Reset Password – GameConnect')

@section('content')
<section class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-950 via-purple-950 to-black">

    <div class="max-w-md w-full bg-slate-900 p-10 rounded-3xl shadow-2xl border border-slate-700">

        <h2 class="text-3xl font-extrabold text-center mb-6">🔑 Reset Password</h2>
        <p class="text-gray-400 text-center mb-8">Enter your new password to secure your account</p>

        {{-- Errors --}}
        @if($errors->any())
            <div class="mb-4 text-red-400">
                @foreach($errors->all() as $error)
                    <div>❌ {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
            @csrf

            {{-- Token --}}
            <input type="hidden" name="token" value="{{ $token }}">

            {{-- Email --}}
            <input type="hidden" name="email" value="{{ $email }}">

            <div>
                <label class="text-sm text-gray-400">New Password</label>
                <input type="password" name="password" required
                       class="w-full mt-1 px-4 py-3 rounded bg-slate-800 border border-slate-700 focus:ring-2 focus:ring-cyan-400">
            </div>

            <div>
                <label class="text-sm text-gray-400">Confirm New Password</label>
                <input type="password" name="password_confirmation" required
                       class="w-full mt-1 px-4 py-3 rounded bg-slate-800 border border-slate-700 focus:ring-2 focus:ring-purple-400">
            </div>

            <button class="w-full py-3 rounded-xl font-bold bg-gradient-to-r from-cyan-500 to-purple-600 hover:opacity-90">
                🔄 Reset Password
            </button>
        </form>

    </div>
</section>
@endsection
