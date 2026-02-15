@extends('layouts.dashboard')

@section('title', 'Create CODM Series')

@section('dashboard-content')

<div class="bg-slate-900 border-b border-slate-800 px-8 py-6">
    <h1 class="text-3xl font-bold">🏆 Create CODM Tournament Series</h1>
    <p class="text-gray-400 mt-1">Series is the parent. All matches inherit these settings.</p>
</div>

<section class="px-8 py-10 max-w-6xl">

    <form method="POST" action="{{ route('series.instant.store') }}" enctype="multipart/form-data" class="space-y-12">
        @csrf

        <!-- BANNER -->
        <x-banner-upload name="banner" label="Series Banner / Poster" />

        <!-- BASIC DETAILS -->
        <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
            <h2 class="text-2xl font-bold mb-6">🏆 Series Basics</h2>

            <div class="grid md:grid-cols-2 gap-6">
                <input type="text" name="name" placeholder="Series Title" required
                    class="px-4 py-3 rounded bg-slate-800 border border-slate-700">

                <input type="text" name="subtitle" placeholder="Short Tagline (optional)"
                    class="px-4 py-3 rounded bg-slate-800 border border-slate-700">

                <input type="text" name="game" value="CODM" readonly
                    class="px-4 py-3 rounded bg-slate-800 border border-slate-700">

                <select name="mode" id="series-mode"
                    class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
                    <option value="solo">Solo (1v1)</option>
                    <option value="duo">Duo (2v2)</option>
                    <option value="squad" selected>Squad (4v4)</option>
                </select>

                <div>
                    <label class="block text-sm text-gray-400 mb-2">Substitutes Per Team</label>
                    <input type="number" name="substitute_count" min="0" max="10" value="0"
                        class="px-4 py-3 rounded bg-slate-800 border border-slate-700 w-full">
                </div>

                <div class="md:col-span-2 rounded-xl border border-cyan-600/30 bg-cyan-500/10 px-4 py-3">
                    <p class="text-sm text-cyan-200">
                        Series slots are fixed by mode:
                        <span class="font-semibold">Solo = 100</span>,
                        <span class="font-semibold">Duo = 50</span>,
                        <span class="font-semibold">Squad = 25</span>.
                    </p>
                </div>

                <select name="match_type"
                    class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
                    <option value="BR">Battle Royale</option>
                    <option value="MP">Multiplayer</option>
                    <option value="S&D">S&D</option>
                    <option value="TDM">TDM</option>
                </select>

                <input type="text" name="map" placeholder="Map (optional)"
                    class="px-4 py-3 rounded bg-slate-800 border border-slate-700">

                <select name="region" class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
                    <option value="India">India</option>
                    <option value="Asia">Asia</option>
                    <option value="Global">Global</option>
                </select>

                <div>
                    <label class="block text-sm text-gray-400 mb-2">Start Date</label>
                    <input type="date" name="start_date"
                        class="px-4 py-3 rounded bg-slate-800 border border-slate-700 w-full [color-scheme:dark]">
                </div>

                <div>
                    <label class="block text-sm text-gray-400 mb-2">End Date</label>
                    <input type="date" name="end_date"
                        class="px-4 py-3 rounded bg-slate-800 border border-slate-700 w-full [color-scheme:dark]">
                </div>
            </div>
        </div>

        <!-- REWARD & ENTRY -->
        <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
            <h2 class="text-2xl font-bold mb-6">🎁 Series Rewards & Entry</h2>

            <h3 class="text-lg font-semibold mb-4">🚪 Entry Type</h3>
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <label class="bg-slate-800 p-4 rounded-xl cursor-pointer flex gap-3">
                    <input type="radio" name="is_paid" value="0" checked>
                    <div>
                        <p class="font-semibold">🆓 Free Entry</p>
                        <p class="text-xs text-gray-400">No entry fee for the series</p>
                    </div>
                </label>

                <label class="bg-slate-800 p-4 rounded-xl cursor-pointer flex gap-3">
                    <input type="radio" name="is_paid" value="1">
                    <div>
                        <p class="font-semibold">💳 Paid Entry</p>
                        <p class="text-xs text-gray-400">Teams pay to join the series</p>
                    </div>
                </label>
            </div>

            <div id="entry-fee-section" class="mb-10 hidden">
                <h3 class="text-lg font-semibold mb-4">💳 Entry Fee</h3>
                <input type="number" name="entry_fee" placeholder="Entry Fee (₹)" min="0"
                    class="mb-6 px-4 py-3 rounded bg-slate-800 border border-slate-700 w-full">

                <div class="bg-slate-900 p-6 rounded-2xl border border-green-700">
                    <h4 class="font-bold mb-4">UPI Payment Details</h4>

                    <input type="text" name="upi_id" placeholder="UPI ID (example@upi)"
                        class="mb-4 px-4 py-3 rounded bg-slate-800 border border-slate-700 w-full">

                    <input type="text" name="upi_name" placeholder="Payee Name"
                        class="mb-4 px-4 py-3 rounded bg-slate-800 border border-slate-700 w-full">

                    <input type="file" name="upi_qr" accept="image/*"
                        class="px-4 py-3 rounded bg-slate-800 border border-slate-700 w-full">
                </div>
            </div>

            <h3 class="text-lg font-semibold mb-4">🏆 Reward Type</h3>
            <div class="grid md:grid-cols-3 gap-6 mb-10">
                <label class="bg-slate-800 p-4 rounded-xl cursor-pointer flex gap-3">
                    <input type="radio" name="reward_type" value="free" checked>
                    <div>
                        <p class="font-semibold">🆓 No Rewards</p>
                        <p class="text-xs text-gray-400">Practice / friendly series</p>
                    </div>
                </label>

                <label class="bg-slate-800 p-4 rounded-xl cursor-pointer flex gap-3">
                    <input type="radio" name="reward_type" value="organizer_prize">
                    <div>
                        <p class="font-semibold">💰 Organizer Prize</p>
                        <p class="text-xs text-gray-400">Cash prizes by organizer</p>
                    </div>
                </label>

                <label class="bg-slate-800/60 p-4 rounded-xl flex gap-3 opacity-70 cursor-not-allowed border border-dashed border-slate-600">
                    <input type="radio" name="reward_type" value="platform_points" disabled>
                    <div>
                        <p class="font-semibold">🎯 Platform Points</p>
                        <p class="text-xs text-amber-300">Coming Soon</p>
                    </div>
                </label>
            </div>

            <div id="prize-section" class="mb-4 hidden">
                <h3 class="text-xl font-bold mb-4">🏆 Prize Distribution</h3>
                <div class="bg-slate-900/70 border border-slate-700 rounded-2xl p-4">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-sm text-gray-400">Add as many prize positions as you want.</p>
                        <button type="button" id="add-prize-row"
                            class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 font-semibold">
                            + Add Position
                        </button>
                    </div>
                    <div id="prize-rows" class="space-y-3"></div>
                </div>
            </div>
        </div>

        <!-- POINTS SYSTEM -->
        <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
            <h2 class="text-2xl font-bold mb-6">🎯 Points System</h2>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Kill Point *</label>
                    <input type="number" name="kill_point" min="0" value="1"
                        class="w-full px-5 py-4 rounded-xl bg-slate-800 border border-slate-700">
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm text-gray-400 mb-2">Placement Points *</label>
                <div id="placement-points" class="grid grid-cols-2 md:grid-cols-3 gap-3"></div>
            </div>
        </div>

        <!-- RULES & DESCRIPTION -->
        <div class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                <div>
                    <h2 class="text-2xl font-bold">📜 Rules & Description</h2>
                    <p class="text-xs text-gray-400 mt-1">Quick template for organizers. You can edit after applying.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" id="fill-suggested-both"
                        class="px-3 py-2 rounded-lg bg-gradient-to-r from-cyan-500 to-blue-600 hover:opacity-90 text-sm font-semibold shadow shadow-cyan-500/30">
                        Use Suggested (Both)
                    </button>
                    <button type="button" id="append-suggested-rules"
                        class="px-3 py-2 rounded-lg bg-slate-800 border border-slate-600 hover:border-cyan-500 text-sm font-semibold">
                        Append Rules
                    </button>
                    <button type="button" id="clear-desc-rules"
                        class="px-3 py-2 rounded-lg bg-red-700/90 hover:bg-red-700 text-sm font-semibold">
                        Clear
                    </button>
                </div>
            </div>

            <div class="mb-4 rounded-xl border border-cyan-600/40 bg-cyan-500/10 px-4 py-3">
                <p class="text-sm text-cyan-200">
                    Includes fair-play, dispute window, and scoring policy text.
                </p>
                <p id="suggestion-feedback" class="text-xs text-cyan-300 mt-1 hidden"></p>
            </div>

            <textarea id="series-description" name="description" rows="5" placeholder="About this series..."
                class="w-full px-4 py-3 rounded bg-slate-800 border border-slate-700"></textarea>

            <textarea id="series-rules" name="rules" rows="6" placeholder="Series rules, bans, reporting rules..."
                class="w-full mt-6 px-4 py-3 rounded bg-slate-800 border border-slate-700"></textarea>
        </div>

        <div class="flex justify-between items-center">
            <a href="{{ route('instant.index') }}"
                class="px-6 py-3 rounded-xl bg-slate-700 hover:bg-slate-600 font-semibold">
                ← Back
            </a>

            <button type="submit"
                class="px-10 py-4 rounded-xl bg-gradient-to-r from-cyan-500 to-purple-600 font-bold text-lg">
                🚀 Create Series
            </button>
        </div>
    </form>

</section>

@php
    $defaultRankPoints = config('points.codm.placement_points', [
        1 => 30,
        2 => 24,
        3 => 20,
        4 => 14,
        5 => 12,
        6 => 10,
        7 => 8,
        8 => 6,
        9 => 4,
        10 => 2,
    ]);
@endphp

<script>
(() => {
    const grid = document.getElementById('placement-points');
    const defaults = @json($defaultRankPoints);

    Object.keys(defaults).sort((a, b) => Number(a) - Number(b)).forEach((rank) => {
        const wrap = document.createElement('div');
        wrap.className = 'flex items-center gap-2 bg-slate-800 border border-slate-700 rounded-lg px-3 py-3';
        wrap.innerHTML = `
            <input type="number" name="placement_points[${rank}][position]" value="${rank}" readonly
                class="w-16 bg-slate-900 border border-slate-700 rounded-lg px-2 py-2 text-center text-gray-300">
            <input type="number" name="placement_points[${rank}][points]" value="${defaults[rank]}" min="0"
                class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-2">
        `;
        grid.appendChild(wrap);
    });

    const prizeRows = document.getElementById('prize-rows');
    const addPrizeBtn = document.getElementById('add-prize-row');

    function normalizePrizePositions() {
        const positionInputs = prizeRows.querySelectorAll('input[name="prize_positions[]"]');
        positionInputs.forEach((input, index) => {
            input.value = index + 1;
        });
    }

    function addPrizeRow(position = '', amount = '') {
        const row = document.createElement('div');
        row.className = 'grid md:grid-cols-3 gap-3 items-center';
        row.innerHTML = `
            <input type="number" name="prize_positions[]" min="1" placeholder="Position (e.g. 1)"
                value="${position}"
                class="px-4 py-3 rounded bg-slate-800 border border-slate-700 w-full">
            <input type="number" name="prize_amounts[]" min="0" placeholder="Prize Amount (₹)"
                value="${amount}"
                class="px-4 py-3 rounded bg-slate-800 border border-slate-700 w-full">
            <button type="button"
                class="remove-prize-row px-4 py-3 rounded bg-red-700 hover:bg-red-800 font-semibold">
                Remove
            </button>
        `;
        row.querySelector('.remove-prize-row').addEventListener('click', () => {
            row.remove();
            normalizePrizePositions();
        });
        prizeRows.appendChild(row);
        normalizePrizePositions();
    }

    addPrizeBtn.addEventListener('click', () => addPrizeRow('', ''));
    addPrizeRow(1, '');
    addPrizeRow(2, '');
    addPrizeRow(3, '');

    const paidRadios = document.querySelectorAll('input[name="is_paid"]');
    const entryFeeSection = document.getElementById('entry-fee-section');
    const rewardRadios = document.querySelectorAll('input[name="reward_type"]');
    const prizeSection = document.getElementById('prize-section');
    const descriptionInput = document.getElementById('series-description');
    const rulesInput = document.getElementById('series-rules');
    const fillBothBtn = document.getElementById('fill-suggested-both');
    const appendRulesBtn = document.getElementById('append-suggested-rules');
    const clearDescRulesBtn = document.getElementById('clear-desc-rules');
    const modeInput = document.getElementById('series-mode');
    const feedbackEl = document.getElementById('suggestion-feedback');
    const suggestedRuleSignature = '1. Team / player name must match registration details.';

    function syncEntryFee() {
        const isPaid = document.querySelector('input[name="is_paid"]:checked')?.value === '1';
        entryFeeSection.classList.toggle('hidden', !isPaid);
    }

    function syncPrize() {
        const rewardType = document.querySelector('input[name="reward_type"]:checked')?.value;
        prizeSection.classList.toggle('hidden', rewardType !== 'organizer_prize');
    }

    paidRadios.forEach(r => r.addEventListener('change', syncEntryFee));
    rewardRadios.forEach(r => r.addEventListener('change', syncPrize));

    function buildSuggestedDescription() {
        const modeText = (modeInput?.value || 'squad').toUpperCase();
        return `Official CODM ${modeText} series with multiple matches.\\n\\nTeams play under fixed series settings and results are combined into one overall leaderboard (MP/KP/PP/TT/CD). Follow schedule updates and submit any dispute within organizer timeline.`;
    }

    function buildSuggestedRules() {
        return [
            '1. Team / player name must match registration details.',
            '2. All players must join only with approved IGN and Game ID.',
            '3. No emulator, hacks, exploit use, ghosting, or teaming.',
            '4. Organizer decision is final for penalties and disputes.',
            '5. Result disputes must be submitted with screenshot/video proof within 10 minutes.',
            '6. Late join, no-show, or incomplete team can lead to zero points for that match.',
            '7. Match points follow configured Kill Point + Placement Point rules.',
            '8. Any toxic behavior, abusive voice/text chat, or intentional griefing can lead to point deduction or disqualification.',
        ].join('\\n');
    }

    function showSuggestionFeedback(message) {
        if (!feedbackEl) return;
        feedbackEl.textContent = message;
        feedbackEl.classList.remove('hidden');
    }

    fillBothBtn?.addEventListener('click', () => {
        if (descriptionInput) descriptionInput.value = buildSuggestedDescription();
        if (rulesInput) rulesInput.value = buildSuggestedRules();
        showSuggestionFeedback('Suggested description and rules applied.');
    });

    appendRulesBtn?.addEventListener('click', () => {
        if (!rulesInput) return;
        const suggested = buildSuggestedRules();
        if (rulesInput.value.includes(suggestedRuleSignature)) {
            showSuggestionFeedback('Suggested rules already added. Duplicate add is blocked.');
            return;
        }
        rulesInput.value = rulesInput.value.trim()
            ? `${rulesInput.value.trim()}\\n\\n${suggested}`
            : suggested;
        showSuggestionFeedback('Suggested rules appended.');
    });

    clearDescRulesBtn?.addEventListener('click', () => {
        if (descriptionInput) descriptionInput.value = '';
        if (rulesInput) rulesInput.value = '';
        showSuggestionFeedback('Description and rules cleared.');
    });

    syncEntryFee();
    syncPrize();
})();
</script>

@endsection
