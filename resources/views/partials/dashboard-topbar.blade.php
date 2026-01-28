<div class="bg-slate-900 border-b border-slate-800">

    {{-- 🔥 EMAIL NOT VERIFIED BANNER --}}
    @if(auth()->check() && !auth()->user()->hasVerifiedEmail())
        <div class="bg-yellow-600/20 border-b border-yellow-500 px-8 py-3 flex justify-between items-center">

            <div class="text-yellow-300 font-medium">
                ⚠️ Your email is not verified. Please verify to secure your account.
            </div>

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button class="px-4 py-2 rounded-lg bg-yellow-500 hover:bg-yellow-600 text-black font-semibold">
                    🔄 Resend Verification Email
                </button>
            </form>

        </div>
    @endif

    {{-- 🔹 NORMAL TOP BAR --}}
    <div class="px-8 py-4 flex justify-between items-center">

        <div>
            <h1 class="text-3xl font-bold">@yield('page-title','Dashboard')</h1>
            <p class="text-gray-400">Welcome back, {{ auth()->user()->name }}</p>
        </div>

        <div class="flex items-center space-x-6">

            {{-- Status message after resend --}}
            @if (session('status') == 'verification-link-sent')
                <span class="text-green-400 text-sm">
                    ✅ Verification email sent!
                </span>
            @endif

            <span class="relative">
                🔔
                <span class="absolute -top-1 -right-2 bg-red-500 text-xs rounded-full px-1">3</span>
            </span>

            <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=0F172A&color=22D3EE"
                 class="w-10 h-10 rounded-full">
        </div>

    </div>
</div>
