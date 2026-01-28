@extends('layouts.dashboard')

@section('title', 'Join Requests – GameConnect')

@section('dashboard-content')

    <!-- HEADER -->
    <div class="bg-slate-900 border-b border-slate-800 px-8 py-6">
        <h1 class="text-3xl font-bold">👥 Join Requests</h1>
        <p class="text-gray-400">Approve players, manage teams & send room details</p>
    </div>

    <!-- 🔹 FILTER BAR -->
    <form method="GET" class="px-8 py-6 grid md:grid-cols-5 gap-6">

        <!-- Search -->
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search team / captain..."
            class="px-4 py-3 rounded bg-slate-800 border border-slate-700">

        <!-- Tournament Filter -->
        <select name="tournament_id" class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
            <option value="">All Tournaments</option>
            @foreach ($tournaments as $t)
                <option value="{{ $t->id }}" @selected(request('tournament_id') == $t->id)>
                    {{ $t->title }}
                </option>
            @endforeach
        </select>

        <!-- Mode -->
        <select name="mode" class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
            <option value="">All Modes</option>
            <option value="solo" @selected(request('mode') == 'solo')>Solo</option>
            <option value="duo" @selected(request('mode') == 'duo')>Duo</option>
            <option value="squad" @selected(request('mode') == 'squad')>Squad</option>
        </select>

        <!-- Status -->
        <select name="status" class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
            <option value="">All Status</option>
            <option value="pending" @selected(request('status') == 'pending')>Pending</option>
            <option value="approved" @selected(request('status') == 'approved')>Approved</option>
            <option value="rejected" @selected(request('status') == 'rejected')>Rejected</option>
        </select>

        <!-- Sort -->
        <select name="sort" class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
            <option value="newest">Newest First</option>
            <option value="oldest" @selected(request('sort') == 'oldest')>Oldest First</option>
        </select>

        <!-- Submit -->
        <div class="md:col-span-5">
            <button class="w-full bg-gradient-to-r from-cyan-500 to-purple-600 py-3 rounded-xl font-bold">
                🔍 Apply Filters
            </button>
        </div>
    </form>


    <!-- 🔹 REQUEST TABLE -->
    <section class="px-8 py-10">
        <div class="overflow-x-auto bg-slate-900 rounded-3xl border border-slate-700">
            <table class="w-full">

                <thead class="bg-slate-800 text-gray-300">
                    <tr>
                        <th class="p-4">Player / Team</th>
                        <th class="p-4">Tournament</th>
                        <th class="p-4">Mode</th>
                        <th class="p-4">Contact</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($joins as $join)
                        <tr class="border-t border-slate-800 hover:bg-slate-800/40">

                            <!-- PLAYER INFO -->
                            <td class="p-4">
                                <p class="font-semibold">{{ $join->team_name ?? 'Solo Player' }}</p>
                                <p class="text-sm text-gray-400">Captain: {{ $join->captain_ign }}</p>
                                <p class="text-xs text-gray-500">Join Code: {{ $join->join_code }}</p>
                            </td>

                            <!-- TOURNAMENT -->
                            <td class="p-4">
                                <p class="font-semibold">{{ $join->tournament->title }}</p>
                                <p class="text-sm text-gray-400">
                                    {{ $join->tournament->start_time->format('d M Y, h:i A') }}
                                </p>
                            </td>

                            <!-- MODE -->
                            <td class="p-4">{{ ucfirst($join->mode) }}</td>

                            <!-- CONTACT -->
                            <td class="p-4">
                                <p class="text-sm">📧 {{ $join->email }}</p>
                                <p class="text-sm">📱 {{ $join->phone }}</p>
                            </td>

                            <!-- STATUS -->
                            <td class="p-4">
                                @if ($join->status == 'pending')
                                    <span class="px-3 py-1 rounded-full bg-yellow-500 text-sm">Pending</span>
                                @elseif($join->status == 'approved')
                                    <span class="px-3 py-1 rounded-full bg-green-600 text-sm">Approved</span>
                                @else
                                    <span class="px-3 py-1 rounded-full bg-red-600 text-sm">Rejected</span>
                                @endif
                            </td>

                            <!-- ACTIONS -->
                            <td class="p-4">
                                <div class="flex space-x-3">

                                    @if ($join->status === 'pending')
                                        <!-- APPROVE -->
                                        <form method="POST" action="{{ route('organizer.requests.approve', $join) }}">
                                            @csrf
                                            <button class="px-4 py-2 bg-green-600 hover:bg-green-700 rounded">
                                                Approve
                                            </button>
                                        </form>

                                        <!-- REJECT -->
                                        <form method="POST" action="{{ route('organizer.requests.reject', $join) }}">
                                            @csrf
                                            <input type="hidden" name="reject_reason" value="Not eligible">
                                            <button class="px-4 py-2 bg-red-600 hover:bg-red-700 rounded">
                                                Reject
                                            </button>
                                        </form>
                                    @elseif($join->status === 'approved')
                                        <span class="px-3 py-1 rounded-full bg-green-600 text-sm">Approved</span>
                                    @elseif($join->status === 'rejected')
                                        <span class="px-3 py-1 rounded-full bg-red-600 text-sm">Rejected</span>
                                    @endif

                                    <!-- VIEW -->
                                    <a href="{{ route('organizer.joins.showdetials', $join) }}"
                                        class="px-4 py-2 bg-slate-700 hover:bg-slate-600 rounded">
                                        View
                                    </a>
                                </div>
                            </td>


                        </tr>
                    @endforeach

                </tbody>

            </table>
        </div>

        <!-- PAGINATION -->
        <div class="mt-8">
            {{ $joins->links() }}
        </div>
    </section>

    <!-- 🔹 QUICK STATS PANEL -->
    <section class="px-8 py-10 grid md:grid-cols-4 gap-8">

        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
            <p class="text-gray-400">Pending Requests</p>
            <h2 class="text-3xl font-bold">{{ $stats['pending'] }}</h2>
        </div>

        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
            <p class="text-gray-400">Approved Today</p>
            <h2 class="text-3xl font-bold text-green-400">
                {{ $stats['approved_today'] }}
            </h2>
        </div>

        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
            <p class="text-gray-400">Rejected</p>
            <h2 class="text-3xl font-bold text-red-400">
                {{ $stats['rejected'] }}
            </h2>
        </div>

        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
            <p class="text-gray-400">Total Teams</p>
            <h2 class="text-3xl font-bold text-cyan-400">
                {{ $stats['total'] }}
            </h2>
        </div>

    </section>


@endsection
