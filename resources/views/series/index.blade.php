@extends('layouts.dashboard')

@section('title', 'Tournament Series')

@section('dashboard-content')

    <div class="px-8 py-10">
        <div class="flex justify-between mb-8">
            <h1 class="text-3xl font-bold">🏆 Tournament Series</h1>
            <a href="{{ route('series.create') }}"
                class="px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-purple-600 font-bold">
                ➕ Create Series
            </a>
        </div>

        @forelse($series as $item)
            <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 mb-4 flex justify-between">
                <div>
                    <h2 class="font-bold text-xl">{{ $item->name }}</h2>
                    <p class="text-gray-400">
                        {{ $item->tournaments->count() }} tournaments
                    </p>
                </div>
                <a href="{{ route('series.show', $item) }}" class="px-6 py-3 rounded-xl bg-purple-600 hover:bg-purple-700 font-bold">
                    View data
                </a>

                <a href="{{ route('series.bracket', $series) }}"
                    class="px-6 py-3 rounded-xl bg-purple-600 hover:bg-purple-700 font-bold">
                    🌳 View Series Bracket
                </a>

            </div>
        @empty
            <p class="text-gray-400">No series created yet.</p>
        @endforelse
    </div>

@endsection
