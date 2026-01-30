{{-- ========================================================= --}}
{{-- SIDEBAR PARTIAL FILE --}}
{{-- resources/views/partials/dashboard-sidebar.blade.php --}}

<aside class="w-64 bg-black border-r border-slate-800 hidden md:flex flex-col">
    <div class="p-6 text-2xl font-extrabold text-cyan-400">GameConnect</div>

    <nav class="flex-1 px-4 space-y-2">

        <a href="{{ route('dashboard') }}"
            class="block px-4 py-3 rounded {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-cyan-400' : 'hover:bg-slate-800' }}">
            🏠 Dashboard
        </a>

        <a href="{{ route('tournaments.create') }}"
            class="block px-4 py-3 rounded {{ request()->routeIs('tournaments.create') ? 'bg-slate-800 text-cyan-400' : 'hover:bg-slate-800' }}">
            ➕ Create Tournament
        </a>
             <a href="{{ route('series.index') }}"
            class="block px-4 py-3 rounded {{ request()->routeIs('tournaments.create') ? 'bg-slate-800 text-cyan-400' : 'hover:bg-slate-800' }}">
            🌳 Tournament Series
        </a>

        <a href="{{ route('tournaments.my') }}"
            class="block px-4 py-3 rounded {{ request()->routeIs('tournaments.my') ? 'bg-slate-800 text-cyan-400' : 'hover:bg-slate-800' }}">
            🏆 My Tournaments
        </a>

        <a href="{{ route('tournaments.requests') }}"
            class="block px-4 py-3 rounded {{ request()->routeIs('tournaments.requests') ? 'bg-slate-800 text-cyan-400' : 'hover:bg-slate-800' }}">
            👥 Join Requests
        </a>

        <a href="{{ route('analytics.index') }}"
            class="block px-4 py-3 rounded {{ request()->routeIs('analytics.index') ? 'bg-slate-800 text-cyan-400' : 'hover:bg-slate-800' }}">
            📊 Analytics
        </a>

        <a href="{{ route('organizer.profile') }}"
            class="block px-4 py-3 rounded {{ request()->routeIs('organizer.profile') ? 'bg-slate-800 text-cyan-400' : 'hover:bg-slate-800' }}">
            👤 Organizer Profile
        </a>

        <a href="{{ route('organizer.settings') }}"
            class="block px-4 py-3 rounded {{ request()->routeIs('organizer.settings') ? 'bg-slate-800 text-cyan-400' : 'hover:bg-slate-800' }}">
            ⚙️ Settings
        </a>
        <div class="p-6 border-t border-slate-800">
            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                @csrf

                <button type="submit" class="w-full py-2 rounded bg-red-500 hover:bg-red-600 font-semibold transition">
                    🚪 Logout
                </button>
            </form>
        </div>
    </nav>



</aside>
