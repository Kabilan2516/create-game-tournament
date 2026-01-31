{{-- ===============================
   FILE: resources/views/partials/footer.blade.php
================================ --}}

<footer class="bg-black border-t border-slate-800 py-16">
    <div class="max-w-7xl mx-auto px-6 grid gap-12 md:grid-cols-5">

        {{-- BRAND --}}
        <div class="md:col-span-2">
            <h4 class="text-2xl font-extrabold text-cyan-400">
                {{ config('app.name') }}
            </h4>

            <p class="text-gray-400 mt-4 leading-relaxed max-w-md">
                {{ config('app.name') }} is a competitive esports platform where organizers host
                tournaments and players compete in games like CODM, PUBG, Free Fire and more.
                Fair play, fast approvals, and trusted organizers.
            </p>
        </div>

        {{-- PLATFORM --}}
        <div>
            <h5 class="font-semibold mb-4 text-white">Platform</h5>
            <ul class="space-y-2 text-gray-400">
                <li><a href="{{ route('tournaments.index') }}" class="hover:text-cyan-400">Browse Tournaments</a></li>
                <li><a href="{{ route('tournaments.create') }}" class="hover:text-cyan-400">Host Tournament</a></li>
                <li><a href="{{ route('series.index') }}" class="hover:text-cyan-400">Tournament Series</a></li>
                <li><a href="{{ route('dashboard') }}" class="hover:text-cyan-400">Organizer Dashboard</a></li>
            </ul>
        </div>

        {{-- GAMES --}}
        <div>
            <h5 class="font-semibold mb-4 text-white">Supported Games</h5>
            <ul class="space-y-2 text-gray-400">
                <li>Call of Duty: Mobile</li>
                <li>PUBG Mobile</li>
                <li>Free Fire</li>
                <li>Valorant (Coming Soon)</li>
                <li>More esports titles</li>
            </ul>
        </div>

        {{-- SUPPORT & LEGAL --}}
        <div>
            <h5 class="font-semibold mb-4 text-white">Support & Legal</h5>
            <ul class="space-y-2 text-gray-400">
                <li><a href="{{ route('help.center') }}" class="hover:text-cyan-400">Help Center</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-cyan-400">Contact Us</a></li>
                <li><a href="{{ route('terms') }}" class="hover:text-cyan-400">Terms & Conditions</a></li>
                <li><a href="{{ route('privacy') }}" class="hover:text-cyan-400">Privacy Policy</a></li>
                <li><a href="{{ route('refund.policy') }}" class="hover:text-cyan-400">Refund Policy</a></li>
            </ul>
        </div>

    </div>

    {{-- OPTIONAL FOOTER AD (SAFE PLACEHOLDER) --}}
    <div class="max-w-7xl mx-auto px-6 mt-12">
        <div class="bg-slate-900 py-6 text-center rounded-xl border border-dashed border-slate-700">
            <span class="text-gray-500 text-sm">
                Sponsored Content / Footer Ad Slot
            </span>
        </div>
    </div>

    {{-- COPYRIGHT --}}
    <div class="text-center text-gray-500 mt-12 text-sm">
        © {{ date('Y') }} {{ config('app.name') }}.
        All rights reserved.
    </div>
</footer>
