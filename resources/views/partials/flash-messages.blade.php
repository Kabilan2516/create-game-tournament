{{-- 🔔 GLOBAL FLASH / ALERT MESSAGES --}}
<div class="fixed top-6 right-4 left-4 sm:left-auto sm:right-6
            space-y-4 z-50
            sm:max-w-sm">


    {{-- Success --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show"
            class="bg-green-600 text-white px-5 py-4 rounded-xl shadow-lg flex justify-between items-start animate-fade-in">
            <span>✅ {{ session('success') }}</span>
            <button @click="show = false" class="ml-4 text-white/80 hover:text-white">✕</button>
        </div>
    @endif

    {{-- Auth Login Success --}}
    @if (session('auth.login.success'))
        <div x-data="{ show: true }" x-show="show"
            class="bg-green-600 text-white px-5 py-4 rounded-xl shadow-lg flex justify-between items-start animate-fade-in">
            <span>{{ session('auth.login.success') }}</span>
            <button @click="show = false" class="ml-4 text-white/80 hover:text-white">✕</button>
        </div>
    @endif

    {{-- Logout Success --}}
    @if (session('auth.logout.success'))
        <div x-data="{ show: true }" x-show="show"
            class="bg-blue-600 text-white px-5 py-4 rounded-xl shadow-lg flex justify-between items-start animate-fade-in">
            <span>{{ session('auth.logout.success') }}</span>
            <button @click="show = false" class="ml-4 text-white/80 hover:text-white">✕</button>
        </div>
    @endif

    {{-- Password Reset Success --}}
    @if (session('auth.reset.success'))
        <div x-data="{ show: true }" x-show="show"
            class="bg-green-600 text-white px-5 py-4 rounded-xl shadow-lg flex justify-between items-start animate-fade-in">
            <span>{{ session('auth.reset.success') }}</span>
            <button @click="show = false" class="ml-4 text-white/80 hover:text-white">✕</button>
        </div>
    @endif

    {{-- Password Update --}}
    @if (session('password.success'))
        <div x-data="{ show: true }" x-show="show"
            class="bg-green-600 text-white px-5 py-4 rounded-xl shadow-lg flex justify-between items-start animate-fade-in">
            <span>{{ session('password.success') }}</span>
            <button @click="show = false" class="ml-4 text-white/80 hover:text-white">✕</button>
        </div>
    @endif

    {{-- Email Verified --}}
    @if (session('status') === 'verified')
        <div x-data="{ show: true }" x-show="show"
            class="bg-green-600 text-white px-5 py-4 rounded-xl shadow-lg flex justify-between items-start animate-fade-in">
            <span>🎉 Your email has been verified successfully!</span>
            <button @click="show = false" class="ml-4 text-white/80 hover:text-white">✕</button>
        </div>
    @endif

    {{-- Verification Link Sent --}}
    @if (session('status') === 'verification-link-sent')
        <div x-data="{ show: true }" x-show="show"
            class="bg-yellow-500 text-black px-5 py-4 rounded-xl shadow-lg flex justify-between items-start animate-fade-in">
            <span>📧 Verification email has been sent again.</span>
            <button @click="show = false" class="ml-4 text-black/70 hover:text-black">✕</button>
        </div>
    @endif

    {{-- Generic Error --}}
    @if (session('error'))
        <div x-data="{ show: true }" x-show="show"
            class="bg-red-600 text-white px-5 py-4 rounded-xl shadow-lg flex justify-between items-start animate-fade-in">
            <span>❌ {{ session('error') }}</span>
            <button @click="show = false" class="ml-4 text-white/80 hover:text-white">✕</button>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div x-data="{ show: true }" x-show="show"
            class="bg-red-900 text-white px-5 py-4 rounded-xl shadow-lg animate-fade-in">
            <div class="flex justify-between items-start">
                <strong>❌ Please fix the following:</strong>
                <button @click="show = false" class="text-white/80 hover:text-white">✕</button>
            </div>
            <ul class="mt-2 list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 📱 Device Warning --}}
@if ($isMobileOrTablet && !session('hide.device.warning'))
    <div x-data="{ show: true }" x-show="show"
         class="bg-yellow-500 text-black border-l-4 border-yellow-800
                px-4 py-3 rounded-xl shadow-xl
                flex gap-3 items-start
                w-full break-words">
        <span class="text-sm leading-relaxed">
            ⚠️ <strong>Desktop Recommended.</strong>
            This site is not optimized for mobile or tablet.
            Please use a <strong>PC / Laptop</strong> or enable
            <strong>Desktop View</strong>.
        </span>

        <div class="flex gap-2 ml-auto shrink-0">

            <button @click="show = false"
                    class="text-lg font-bold leading-none">
                ✕
            </button>
        </div>
    </div>
@endif




</div>
