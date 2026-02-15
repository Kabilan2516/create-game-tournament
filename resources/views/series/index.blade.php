@extends('layouts.dashboard')

@section('title', 'Tournament Series')

@section('dashboard-content')

    <div class="px-8 py-10">
        <div class="flex justify-between mb-8">
            <h1 class="text-3xl font-bold">🏆 Tournament Series</h1>
            {{-- <a href="{{ route('series.create') }}" --}}
            <a href="{{ route('series.instant.create') }}"
                class="px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-purple-600 font-bold">
                ➕ Create Series
            </a>
        </div>

        @forelse($series as $item)
            <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 mb-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="font-bold text-xl">{{ $item->title }}</h2>
                        @if ($item->is_published)
                            <span class="text-xs px-2 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                                Published
                            </span>
                        @else
                            <span class="text-xs px-2 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/40">
                                Draft
                            </span>
                        @endif
                    </div>
                    <p class="text-gray-400">
                        {{ $item->tournaments->count() }} tournaments
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        @if ($item->is_published)
                            Players can register from the series page.
                        @else
                            Publish to enable player registration.
                        @endif
                    </p>
                </div>

                <div class="flex flex-wrap gap-2 mt-4">
                    <a href="{{ route('series.show', $item) }}"
                        class="px-4 py-2 rounded-lg bg-purple-600 hover:bg-purple-700 font-semibold">
                        View Data
                    </a>
                    <a href="{{ route('organizer.results.instant.codm', ['series_id' => $item->id]) }}"
                        class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 font-semibold">
                        Add Match
                    </a>
                    <a href="{{ route('series.bracket', $item) }}"
                        class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 font-semibold">
                        🌳 Bracket
                    </a>
                    <a href="{{ route('series.public.show', $item) }}"
                        class="px-4 py-2 rounded-lg bg-slate-700 hover:bg-slate-600 font-semibold">
                        Public Page
                    </a>

                    <form method="POST" action="{{ route('series.publish', $item) }}">
                        @csrf
                        <input type="hidden" name="is_published" value="{{ $item->is_published ? 0 : 1 }}">
                        <button type="submit"
                            class="px-4 py-2 rounded-lg font-semibold {{ $item->is_published ? 'bg-amber-600 hover:bg-amber-700' : 'bg-cyan-600 hover:bg-cyan-700' }}">
                            {{ $item->is_published ? 'Unpublish' : 'Publish' }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-gray-400">No series created yet.</p>
        @endforelse
    </div>

@endsection
