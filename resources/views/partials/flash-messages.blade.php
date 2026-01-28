{{-- 🔔 GLOBAL FLASH / ALERT MESSAGES --}}

<div class="fixed top-6 right-6 space-y-4 z-50">

    {{-- Success Messages --}}
    @if (session('success'))
        <div class="bg-green-600 text-white px-6 py-3 rounded-xl shadow-lg animate-fade-in">
            ✅ {{ session('success') }}
        </div>
    @endif

    {{-- Auth Login Success --}}
    @if (session('auth.login.success'))
        <div class="bg-green-600 text-white px-6 py-3 rounded-xl shadow-lg animate-fade-in">
            {{ session('auth.login.success') }}
        </div>
    @endif

    {{-- Logout Success --}}
    @if (session('auth.logout.success'))
        <div class="bg-blue-600 text-white px-6 py-3 rounded-xl shadow-lg animate-fade-in">
            {{ session('auth.logout.success') }}
        </div>
    @endif

    {{-- Password Reset Success --}}
    @if (session('auth.reset.success'))
        <div class="bg-green-600 text-white px-6 py-3 rounded-xl shadow-lg animate-fade-in">
            {{ session('auth.reset.success') }}
        </div>
    @endif

    {{-- Password Update Success (Settings) --}}
    @if (session('password.success'))
        <div class="bg-green-600 text-white px-6 py-3 rounded-xl shadow-lg animate-fade-in">
            {{ session('password.success') }}
        </div>
    @endif

    {{-- Email Verified --}}
    @if (session('status') == 'verified')
        <div class="bg-green-600 text-white px-6 py-3 rounded-xl shadow-lg animate-fade-in">
            🎉 Your email has been verified successfully!
        </div>
    @endif

    {{-- Verification Link Sent --}}
    @if (session('status') == 'verification-link-sent')
        <div class="bg-yellow-600 text-black px-6 py-3 rounded-xl shadow-lg animate-fade-in">
            📧 Verification email has been sent again.
        </div>
    @endif

    {{-- Generic Error --}}
    @if (session('error'))
        <div class="bg-red-600 text-white px-6 py-3 rounded-xl shadow-lg animate-fade-in">
            ❌ {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-900 text-white p-4 rounded-xl mb-6">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

</div>
