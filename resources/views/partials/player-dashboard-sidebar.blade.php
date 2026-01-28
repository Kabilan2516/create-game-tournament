{{-- ===============================
   FILE: resources/views/partials/player-dashboard-sidebar.blade.php
================================ --}}

<aside class="w-64 bg-slate-900 border-r border-slate-800 min-h-screen">

    <div class="p-6 border-b border-slate-800">
        <h2 class="text-xl font-extrabold text-cyan-400">
            🎮 Player Panel
        </h2>
        <p class="text-sm text-gray-400 mt-1">
            Welcome, {{ auth()->user()->name }}
        </p>
    </div>

    <nav class="p-4 space-y-2 text-gray-300">

        <!-- DASHBOARD -->
        <a href="{{ route('player.dashboard') }}"
           class="flex items-center px-4 py-3 rounded hover:bg-slate-800">
            📊 <span class="ml-3">Dashboard</span>
        </a>

        <!-- MY TOURNAMENTS -->
        <a href="{{ route('player.tournaments') }}"
           class="flex items-center px-4 py-3 rounded hover:bg-slate-800">
            🏆 <span class="ml-3">My Tournaments</span>
        </a>

        <!-- MATCH ROOMS -->
        <a href="{{ route('player.rooms') }}"
           class="flex items-center px-4 py-3 rounded hover:bg-slate-800">
            🔐 <span class="ml-3">Match Rooms</span>
        </a>

        <!-- NOTIFICATIONS -->
        <a href="{{ route('player.notifications') }}"
           class="flex items-center px-4 py-3 rounded hover:bg-slate-800">
            🔔 <span class="ml-3">Notifications</span>
        </a>

        <hr class="border-slate-700 my-4">

        <!-- PROFILE -->
        <a href="{{ route('player.profile') }}"
           class="flex items-center px-4 py-3 rounded hover:bg-slate-800">
            👤 <span class="ml-3">My Profile</span>
        </a>

        <!-- PAYMENT HISTORY -->
        <a href="{{ route('player.payments') }}"
           class="flex items-center px-4 py-3 rounded hover:bg-slate-800">
            💳 <span class="ml-3">Payments</span>
        </a>

        <!-- WALLET (DISABLED FOR NOW) -->
        <div class="flex items-center px-4 py-3 rounded text-gray-500 cursor-not-allowed">
            💰 <span class="ml-3">Wallet (Coming Soon)</span>
        </div>

        <hr class="border-slate-700 my-4">

        <!-- LOGOUT -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full text-left flex items-center px-4 py-3 rounded hover:bg-red-600/20 text-red-400">
                🚪 <span class="ml-3">Logout</span>
            </button>
        </form>

    </nav>

</aside>
