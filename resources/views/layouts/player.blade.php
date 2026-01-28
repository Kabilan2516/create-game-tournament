@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-950 flex">

        {{-- Sidebar --}}
        @include('partials.player-dashboard-sidebar')

        {{-- Main --}}
        <main class="flex-1">

            {{-- Topbar --}}
            @include('partials.player-dashboard-topbar')

            <div class="dashboard-content">
                @yield('player-dashboard-content')
            </div>

        </main>

    </div>
@endsection
