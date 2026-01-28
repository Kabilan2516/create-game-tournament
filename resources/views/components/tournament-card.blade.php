<div class="relative group bg-gradient-to-br from-slate-800 via-slate-900 to-black
    rounded-3xl p-6 border
    {{ $tournament->almost_full ? 'border-red-500 shadow-[0_0_30px_rgba(239,68,68,0.4)]' : 'border-slate-700' }}
    hover:border-cyan-400 transition-all duration-500">

    {{-- FEATURED --}}
    @if($tournament->is_featured)
        <div class="absolute top-4 right-4 bg-gradient-to-r from-yellow-400 to-orange-500
                    text-black text-xs font-bold px-3 py-1 rounded-full">
            ⭐ FEATURED
        </div>
    @endif

    {{-- STARTS SOON --}}
    @if($tournament->starts_soon)
        <div class="absolute top-4 left-4 bg-red-600 text-white text-xs px-3 py-1 rounded-full animate-pulse">
            🔥 Starts Soon
        </div>
    @endif

    {{-- GAME + MODE --}}
    <div class="flex flex-wrap gap-2 mb-4">
        <span class="px-3 py-1 text-xs rounded-full bg-cyan-500/20 text-cyan-400">
            {{ $tournament->game }}
        </span>
        <span class="px-3 py-1 text-xs rounded-full bg-purple-500/20 text-purple-400">
            {{ strtoupper($tournament->mode) }}
        </span>

        {{-- REWARD TYPE BADGE --}}
        @if($tournament->reward_type === 'platform_points')
            <span class="px-3 py-1 text-xs rounded-full bg-emerald-500/20 text-emerald-400">
                🎯 Points Reward
            </span>
        @elseif($tournament->reward_type === 'organizer_prize')
            <span class="px-3 py-1 text-xs rounded-full bg-yellow-500/20 text-yellow-400">
                💰 Cash Prize
            </span>
        @else
            <span class="px-3 py-1 text-xs rounded-full bg-slate-500/20 text-slate-300">
                🆓 Free Match
            </span>
        @endif
    </div>

    {{-- TITLE --}}
    <h4 class="text-2xl font-bold mb-2 group-hover:text-cyan-400 transition">
        {{ $tournament->title }}
    </h4>

    {{-- ORGANIZER --}}
    <div class="flex items-center space-x-3 mb-4">
        <img src="https://ui-avatars.com/api/?name={{ urlencode($tournament->organizer->name) }}&background=0F172A&color=22D3EE"
             class="w-8 h-8 rounded-full" alt="Organizer">
        <span class="text-sm text-gray-400">
            By <span class="text-white">{{ $tournament->organizer->name }}</span>
        </span>
    </div>

    {{-- PRIZE / REWARD DISPLAY --}}
    @if($tournament->reward_type === 'organizer_prize')
        <div class="bg-gradient-to-r from-yellow-400/20 to-orange-500/20
                    text-yellow-300 px-4 py-2 rounded-xl mb-4 font-semibold">
            💰 Prize Pool:
            <span class="text-white">
                ₹{{ number_format(
                    ($tournament->first_prize ?? 0)
                  + ($tournament->second_prize ?? 0)
                  + ($tournament->third_prize ?? 0)
                ) }}
            </span>
        </div>
    @elseif($tournament->reward_type === 'platform_points')
        <div class="bg-gradient-to-r from-emerald-400/20 to-green-500/20
                    text-emerald-300 px-4 py-2 rounded-xl mb-4 font-semibold">
            🎯 Earn Platform Points
        </div>
    @else
        <div class="bg-slate-700/40 text-slate-300 px-4 py-2 rounded-xl mb-4 font-semibold">
            🆓 Free to Play
        </div>
    @endif

    {{-- INFO --}}
    <div class="space-y-2 text-gray-300 text-sm mb-6">
        <p>
            ⏰ Starts:
            <span class="text-white font-semibold countdown"
                  data-start="{{ $tournament->start_time }}"></span>
        </p>

        {{-- ENTRY FEE --}}
        @if($tournament->is_paid)
            <p>
                🎟 Entry Fee:
                <span class="text-white font-semibold">₹{{ $tournament->entry_fee }}</span>
            </p>
        @else
            <p>
                🎟 Entry Fee:
                <span class="text-green-400 font-bold">FREE</span>
            </p>
        @endif

        <p>
            👥 Slots:
            <span class="text-white font-semibold">
                {{ $tournament->filled_slots }} / {{ $tournament->slots }}
            </span>
        </p>
    </div>

    {{-- PROGRESS BAR --}}
    <div class="mb-6">
        <div class="w-full bg-slate-700 rounded-full h-2 overflow-hidden">
            <div class="bg-gradient-to-r from-cyan-400 to-purple-500 h-2"
                 style="width: {{ ($tournament->filled_slots / max(1, $tournament->slots)) * 100 }}%">
            </div>
        </div>
    </div>

    {{-- ACTION BUTTONS --}}
    <div class="grid grid-cols-2 gap-4">
        <a href="{{ route('tournaments.show', $tournament) }}"
           class="py-2 rounded-xl border border-cyan-400 text-cyan-400 text-center
                  hover:bg-cyan-400 hover:text-black transition">
            Details
        </a>

        @if(!$tournament->join_closed)
            <a href="{{ route('tournaments.join.form', $tournament) }}"
               class="py-2 rounded-xl font-bold bg-gradient-to-r from-cyan-500 to-purple-600
                      text-center hover:opacity-90">
                Join
            </a>
        @else
            <span class="py-2 rounded-xl text-center bg-gray-700 text-gray-400 cursor-not-allowed">
                Closed
            </span>
        @endif
    </div>
</div>
