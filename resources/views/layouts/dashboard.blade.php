@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-950 flex">

    {{-- Sidebar --}}
    @include('partials.dashboard-sidebar')

    {{-- Main --}}
    <main class="flex-1">

        {{-- Topbar --}}
        @include('partials.dashboard-topbar')

        <div class="dashboard-content">
            @yield('dashboard-content')
        </div>

    </main>
        <script>
        function previewImage(event, previewId) {
            const input = event.target;

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewId).src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</div>
@endsection
