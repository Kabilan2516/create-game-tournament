@extends('layouts.dashboard')

@section('title','Series Bracket')

@section('dashboard-content')

<div class="px-8 py-10">

    <h1 class="text-3xl font-bold mb-6">
        🌳 {{ $series->title }} – Progression Bracket
    </h1>

    <div class="relative overflow-x-auto bg-slate-900 p-10 rounded-3xl border border-slate-700">

        <svg width="1400" height="900" viewBox="0 0 1400 900">

            <!-- CENTRAL LINE -->
            <line x1="700" y1="50" x2="700" y2="850"
                  stroke="#64748b" stroke-width="2"/>

            @php
                $leftX = 200;
                $rightX = 1200;
                $startY = 150;
                $gap = 120;
                $i = 0;
            @endphp

            @foreach($globalScores as $key => $player)
                @php
                    $y = $startY + ($i * $gap);
                    $isChampion = $key === $champion;
                @endphp

                <!-- LEFT NODE -->
                <rect x="{{ $leftX - 160 }}" y="{{ $y - 25 }}"
                      width="150" height="50"
                      rx="12"
                      fill="{{ $isChampion ? '#16a34a' : '#0f172a' }}"
                      stroke="#38bdf8" stroke-width="2"/>

                <text x="{{ $leftX - 85 }}" y="{{ $y + 5 }}"
                      text-anchor="middle"
                      fill="white"
                      font-size="14">
                    {{ $player['ign'] }}
                </text>

                <!-- CURVE -->
                <path d="M {{ $leftX }} {{ $y }}
                         C 400 {{ $y }},
                           500 {{ $y }},
                           700 {{ $y }}"
                      fill="none"
                      stroke="#38bdf8"
                      stroke-width="2"/>

                @if($isChampion)
                    <!-- TROPHY -->
                    <text x="720" y="{{ $y - 10 }}"
                          fill="#facc15"
                          font-size="24">🏆</text>
                @endif

                @php $i++; @endphp
            @endforeach

        </svg>

    </div>

    <!-- LEGEND -->
    <div class="mt-8 grid md:grid-cols-3 gap-6 text-sm">
        <div class="bg-slate-900 p-4 rounded-xl border border-slate-700">
            🟦 Player progression path
        </div>
        <div class="bg-slate-900 p-4 rounded-xl border border-slate-700">
            🏆 Final Series Champion
        </div>
        <div class="bg-slate-900 p-4 rounded-xl border border-slate-700">
            Calculated across all tournaments
        </div>
    </div>

</div>

@endsection
