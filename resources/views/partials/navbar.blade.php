{{-- ===============================
   FILE: resources/views/partials/navbar.blade.php
================================ --}}
<header class="bg-black/80 backdrop-blur sticky top-0 z-50 border-b border-slate-800">
    <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-4">

        {{-- LOGO --}}
        <a href="/">
            <h1 class="text-2xl font-extrabold text-cyan-400 tracking-wide">
                {{ config('app.name') }}
            </h1>
        </a>

        {{-- MAIN NAV --}}
        <nav class="hidden md:flex space-x-8 text-gray-300">
            <a href="/" class="hover:text-cyan-400">Home</a>
            <a href="{{ route('tournaments.index') }}" class="hover:text-purple-400">Tournaments</a>
            <a href="{{ route('join.code.index') }}" class="hover:text-purple-400">Join-Room</a>
            <a href="{{ route('blog.index') }}" class="hover:text-purple-400">Blog</a>
        </nav>

        {{-- AUTH ACTIONS --}}
        <div class="space-x-3 flex items-center">

            @guest
                {{-- GUEST --}}
                <a href="{{ route('login') }}"
                    class="px-4 py-2 border border-cyan-400 rounded hover:bg-cyan-400 hover:text-black">
                    Login
                </a>

                <a href="{{ route('register') }}" class="px-4 py-2 bg-purple-600 rounded glow-purple">
                    Sign Up
                </a>
            @else
                {{-- AUTHENTICATED --}}

                {{-- PLAYER DASHBOARD (ALWAYS AVAILABLE) --}}
                <a href="{{ route('player.dashboard') }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 rounded">
                    🎮 Player Dashboard
                </a>

                {{-- ORGANIZER DASHBOARD (ONLY IF ORGANIZER) --}}
                @if (auth()->user()->role === 'organizer')
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-cyan-500 rounded glow-cyan">
                        🏆 Organizer Dashboard
                    </a>
                @endif

            @endguest
        </div>

    </div>
</header>
