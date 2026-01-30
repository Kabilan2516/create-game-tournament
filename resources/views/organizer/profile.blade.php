@extends('layouts.dashboard')

@section('title', 'Organizer Profile – GameConnect')

@section('dashboard-content')

{{-- ================= HEADER / COVER ================= --}}
<div class="relative h-60 bg-gradient-to-r from-purple-900 via-slate-900 to-cyan-900 rounded-2xl overflow-hidden">

    @if($banner)
        <img src="{{ asset('storage/'.$banner) }}"
             class="absolute inset-0 w-full h-full object-cover opacity-40">
    @endif

    <div class="absolute inset-0 bg-black/50"></div>

    <div class="relative max-w-7xl mx-auto px-8 h-full flex items-end pb-6">
        <div class="flex items-center space-x-6">

            {{-- Avatar --}}
            <img src="{{ $avatar ? asset('storage/'.$avatar) : 'https://ui-avatars.com/api/?name='.$organizer->display_name }}"
                 class="w-28 h-28 rounded-full border-4 border-cyan-400 shadow-xl object-cover">

            {{-- Identity --}}
            <div>
                <h1 class="text-3xl font-extrabold">
                    {{ $organizer->display_name ?? $user->name }}
                </h1>

                <p class="text-gray-300">
                    {{ ucfirst($organizer->organizer_type ?? 'Organizer') }}
                </p>

                <div class="flex items-center space-x-4 mt-2">
                    @if($organizer->verification_status === 'verified')
                        <span class="px-3 py-1 rounded-full bg-green-600 text-sm">
                            ✔ Verified
                        </span>
                    @endif

                    <span class="text-yellow-300">
                        ⭐ {{ number_format($organizer->rating,1) }} Rating
                    </span>
                </div>
            </div>
        </div>

    
    </div>
</div>

{{-- ================= STATS ================= --}}
<section class="px-8 py-10 grid md:grid-cols-4 gap-8">
    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 text-center">
        <p class="text-gray-400">Tournaments Hosted</p>
        <h2 class="text-3xl font-bold">{{ $tournamentsCount }}</h2>
    </div>

    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 text-center">
        <p class="text-gray-400">Series Created</p>
        <h2 class="text-3xl font-bold">{{ $seriesCount }}</h2>
    </div>

    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 text-center">
        <p class="text-gray-400">Total Participants</p>
        <h2 class="text-3xl font-bold">{{ $totalParticipants }}</h2>
    </div>

    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 text-center">
        <p class="text-gray-400">Region</p>
        <h2 class="text-xl font-bold">{{ $organizer->region ?? 'Global' }}</h2>
    </div>
</section>

{{-- ================= ABOUT ================= --}}
<section class="px-8 py-10 max-w-5xl">
    <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
        <h2 class="text-2xl font-bold mb-4">👤 About Organizer</h2>
        <p class="text-gray-300 leading-relaxed">
            {{ $organizer->bio ?? 'No bio added yet.' }}
        </p>
    </div>
</section>

{{-- ================= SOCIAL LINKS ================= --}}
@if($organizer->social_links)
<section class="px-8 pb-10 max-w-5xl">
    <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
        <h2 class="text-2xl font-bold mb-6">🌐 Community & Links</h2>

        <div class="grid md:grid-cols-2 gap-4">
            @foreach($organizer->social_links as $key => $url)
                @if($url)
                    <a href="{{ $url }}" target="_blank"
                       class="bg-slate-800 p-4 rounded-xl hover:bg-slate-700 transition">
                        {{ ucfirst($key) }}
                    </a>
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
