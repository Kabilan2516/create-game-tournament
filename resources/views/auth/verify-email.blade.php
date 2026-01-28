@extends('layouts.app')

@section('title','Verify Email – GameConnect')

@section('content')
<section class="min-h-screen flex items-center justify-center bg-slate-950">
    <div class="max-w-md w-full bg-slate-900 p-10 rounded-3xl border border-slate-700 text-center">

        <h2 class="text-3xl font-bold mb-4">📧 Verify Your Email</h2>

        <p class="text-gray-400 mb-6">
            Thanks for signing up!  
            Please check your email and click the verification link to activate your account.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 text-green-400">
                ✅ A new verification link has been sent to your email.
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="w-full py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-purple-600 font-bold">
                🔄 Resend Verification Email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="mt-6">
            @csrf
            <button class="text-red-400 hover:text-red-500">🚪 Logout</button>
        </form>

    </div>
</section>
@endsection
