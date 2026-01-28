{{-- ===============================
   FILE: resources/views/partials/player-dashboard-topbar.blade.php
================================ --}}

<header class="bg-slate-900 border-b border-slate-800 px-6 py-4 flex justify-between items-center">

    <!-- PAGE TITLE -->
    <div>
        <h1 class="text-2xl font-bold">
            @yield('player-page-title', 'Player Dashboard')
        </h1>
        <p class="text-sm text-gray-400">
            Track matches, rooms & updates
        </p>
    </div>

    <!-- ACTIONS -->
    <div class="flex items-center gap-4">

        <!-- TRACK ROOM -->
        <a href="{{ route('player.rooms') }}"
           class="px-4 py-2 rounded bg-purple-600 hover:bg-purple-700 font-semibold">
            🔍 Track Room
        </a>

        <!-- OPEN APP -->
        <a href="{{ config('services.pwa.pwa_url') }}"
           target="_blank"
           class="px-4 py-2 rounded bg-cyan-600 hover:bg-cyan-700 font-semibold">
            📲 Open App
        </a>

        <!-- USER -->
        <div class="flex items-center gap-2">
            <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}"
                 class="w-9 h-9 rounded-full border border-slate-700">
        </div>

    </div>

</header>
