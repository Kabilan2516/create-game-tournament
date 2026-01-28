@extends('layouts.dashboard')

@section('title', 'Join Requests – ' . $tournament->title)

@section('dashboard-content')

    <!-- HEADER -->
    <div class="bg-slate-900 border-b border-slate-800 px-8 py-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold">👥 Manage Tournament</h1>
            <p class="text-gray-400">
                Tournament: <span class="text-white font-semibold">{{ $tournament->title }}</span>
            </p>
        </div>

        <a href="{{ route('tournaments.my') }}" class="px-5 py-2 rounded bg-slate-700 hover:bg-slate-600">
            ← Back to My Tournaments
        </a>
    </div>

    <!-- 🔹 QUICK STATS -->
    <section class="px-8 py-8 grid md:grid-cols-4 gap-8">
        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
            <p class="text-gray-400">Pending</p>
            <h2 class="text-3xl font-bold text-yellow-400">{{ $pending }}</h2>
        </div>

        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
            <p class="text-gray-400">Approved</p>
            <h2 class="text-3xl font-bold text-green-400">{{ $approved }}</h2>
        </div>

        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
            <p class="text-gray-400">Rejected</p>
            <h2 class="text-3xl font-bold text-red-400">{{ $rejected }}</h2>
        </div>

        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700">
            <p class="text-gray-400">Total Joins</p>
            <h2 class="text-3xl font-bold text-cyan-400">{{ $joins->count() }}</h2>
        </div>
    </section>

    <!-- 🔹 FILTER BAR -->
    <form method="GET" class="px-8 py-6 grid md:grid-cols-5 gap-6 bg-slate-950">

        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search player / team..."
            class="px-4 py-3 rounded bg-slate-800 border border-slate-700">

        <select name="mode" class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
            <option value="">All Modes</option>
            <option value="solo" {{ request('mode') == 'solo' ? 'selected' : '' }}>Solo</option>
            <option value="duo" {{ request('mode') == 'duo' ? 'selected' : '' }}>Duo</option>
            <option value="squad" {{ request('mode') == 'squad' ? 'selected' : '' }}>Squad</option>
        </select>

        <select name="status" class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
            <option value="">All Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>

        <select name="sort" class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
            <option value="newest">Newest First</option>
            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
        </select>

        <button class="bg-gradient-to-r from-cyan-500 to-purple-600 rounded-xl font-bold">
            🔍 Filter
        </button>
    </form>

    <!-- 🔹 REQUESTS TABLE -->
    <section class="px-8 py-10">
      <form method="POST" id="bulkForm">

    @csrf

    <!-- 🔹 BULK ACTION BAR -->
    <div class="px-8 py-4 bg-slate-900 border-b border-slate-800 flex items-center">

        <span class="text-gray-300 font-semibold">
            Selected: <span id="selectedCount">0</span>
        </span>

        <div class="ml-auto flex space-x-4">

            <button type="submit" name="action" value="approve"
                    id="bulkApprove"
                    disabled
                    class="px-5 py-2 bg-green-600 hover:bg-green-700 rounded font-semibold disabled:opacity-40">
                ✅ Approve Selected
            </button>

            <button type="submit" name="action" value="reject"
                    id="bulkReject"
                    disabled
                    class="px-5 py-2 bg-red-600 hover:bg-red-700 rounded font-semibold disabled:opacity-40">
                ❌ Reject Selected
            </button>

            <button type="submit" name="action" value="send_mail"
                    id="bulkMail"
                    disabled
                    class="px-5 py-2 bg-purple-600 hover:bg-purple-700 rounded font-semibold disabled:opacity-40">
                📧 Send Room Details
            </button>

        </div>
    </div>

    <!-- 🔹 REQUESTS TABLE -->
    <section class="px-8 py-10">
        <div class="overflow-x-auto bg-slate-900 rounded-3xl border border-slate-700">
            <table class="w-full text-left">

                <thead class="bg-slate-800 text-gray-300">
                    <tr>
                        <!-- MASTER CHECKBOX -->
                        <th class="p-4">
                            <input type="checkbox" id="masterCheckbox" class="w-5 h-5 rounded">
                        </th>
                        <th class="p-4">Team / Player</th>
                        <th class="p-4">Mode</th>
                        <th class="p-4">Contact</th>
                        <th class="p-4">Payment</th>
                        <th class="p-4">Mail</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Actions</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($joins as $join)
                    <tr class="border-t border-slate-800 hover:bg-slate-800/40">

                        <!-- ROW CHECKBOX -->
                        <td class="p-4">
                            <input type="checkbox"
                                   name="selected_joins[]"
                                   value="{{ $join->id }}"
                                   class="rowCheckbox w-5 h-5 rounded">
                        </td>

                        <!-- PLAYER -->
                        <td class="p-4">
                            <p class="font-semibold">{{ $join->team_name ?? 'Solo Player' }}</p>
                            <p class="text-sm text-gray-400">Captain: {{ $join->captain_ign }}</p>
                            <p class="text-xs text-gray-500">Join ID: {{ $join->join_code }}</p>
                        </td>

                        <!-- MODE -->
                        <td class="p-4">{{ ucfirst($join->mode) }}</td>

                        <!-- CONTACT -->
                        <td class="p-4 text-sm">
                            <p>📧 {{ $join->email }}</p>
                            <p>📱 {{ $join->phone }}</p>
                        </td>

                        <!-- PAYMENT -->
                        <td class="p-4">
                            @if ($join->is_paid)
                                <span class="px-3 py-1 rounded-full bg-yellow-500 text-sm">
                                    {{ ucfirst($join->payment_status) }}
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full bg-green-600 text-sm">Free</span>
                            @endif
                        </td>

                        <!-- MAIL STATUS -->
                        <td class="p-4">
                            @if ($join->room_visible)
                                <span class="px-3 py-1 rounded-full bg-green-600 text-sm">Mail Sent</span>
                            @else
                                <span class="px-3 py-1 rounded-full bg-gray-600 text-sm">Not Sent</span>
                            @endif
                        </td>

                        <!-- STATUS -->
                        <td class="p-4">
                            @php
                                $colors = [
                                    'pending' => 'bg-yellow-500',
                                    'approved' => 'bg-green-600',
                                    'rejected' => 'bg-red-600',
                                ];
                            @endphp

                            <span class="px-3 py-1 rounded-full text-sm {{ $colors[$join->status] }}">
                                {{ ucfirst($join->status) }}
                            </span>
                        </td>

                        <!-- ACTIONS -->
                        <td class="p-4">
                            <div class="flex flex-wrap gap-2">

                                <a href="{{ route('organizer.joins.showdetials', $join) }}"
                                   class="px-3 py-2 bg-slate-700 hover:bg-slate-600 rounded">
                                    View
                                </a>

                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-10 text-center text-gray-400">
                            No join requests yet for this tournament.
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>
        </div>
    </section>

</form>

        <!-- 🔹 REJECT / REMOVE MODAL -->
        <div id="reasonModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-50">

            <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700 w-full max-w-md">

                <h2 class="text-2xl font-bold mb-4">❗ Reason Required</h2>

                <textarea id="reasonText" rows="4" placeholder="Enter reason for rejection / removal..."
                    class="w-full px-4 py-3 rounded bg-slate-800 border border-slate-700"></textarea>

                <div class="flex justify-end space-x-4 mt-6">
                    <button id="closeModal" class="px-5 py-2 bg-slate-700 hover:bg-slate-600 rounded">
                        Cancel
                    </button>

                    <button class="px-5 py-2 bg-red-600 hover:bg-red-700 rounded font-bold">
                        Confirm
                    </button>
                </div>

            </div>
        </div>

    </section>
<script>
const masterCheckbox = document.getElementById('masterCheckbox');
const rowCheckboxes  = document.querySelectorAll('.rowCheckbox');

const bulkApprove = document.getElementById('bulkApprove');
const bulkReject  = document.getElementById('bulkReject');
const bulkMail    = document.getElementById('bulkMail');

const selectedCountEl = document.getElementById('selectedCount');

// Update UI state
function updateBulkUI() {
    const checked = document.querySelectorAll('.rowCheckbox:checked').length;

    selectedCountEl.textContent = checked;

    const enable = checked > 0;
    bulkApprove.disabled = !enable;
    bulkReject.disabled  = !enable;
    bulkMail.disabled    = !enable;

    // Sync master checkbox
    if (checked === rowCheckboxes.length && rowCheckboxes.length > 0) {
        masterCheckbox.checked = true;
        masterCheckbox.indeterminate = false;
    } else if (checked > 0) {
        masterCheckbox.checked = false;
        masterCheckbox.indeterminate = true;
    } else {
        masterCheckbox.checked = false;
        masterCheckbox.indeterminate = false;
    }
}

// Master checkbox click
masterCheckbox.addEventListener('change', function () {
    rowCheckboxes.forEach(cb => cb.checked = this.checked);
    updateBulkUI();
});

// Row checkbox click
rowCheckboxes.forEach(cb => {
    cb.addEventListener('change', updateBulkUI);
});
</script>


@endsection
