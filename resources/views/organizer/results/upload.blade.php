@extends('layouts.dashboard')

@section('title', 'Enter Match Results')

@section('dashboard-content')

    <!-- HEADER -->
    <div class="bg-slate-900 border-b border-slate-800 px-8 py-6">
        <h1 class="text-3xl font-bold">🏁 Match Results Entry</h1>
        <p class="text-gray-400 mt-1">
            Tournament:
            <span class="text-white font-semibold">{{ $tournament->title }}</span>
        </p>
    </div>

    <section class="px-8 py-10 max-w-7xl">

        <!-- SEARCH + INFO -->
        <div class="flex flex-col md:flex-row md:items-center gap-4 mb-6">
            <input type="text" id="searchInput" placeholder="🔍 Search player"
                class="w-full md:w-1/3 bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm">

            <div class="text-sm text-gray-400">
                💾 Draft auto-saved • Safe to refresh
            </div>
        </div>

        <!-- FORM -->
        <form method="POST" action="{{ route('organizer.results.store', $tournament) }}" id="resultsForm"
            class="bg-slate-900 rounded-3xl border border-slate-700 overflow-hidden">

            @csrf

            <input type="hidden" name="results" id="resultsInput">
            <input type="hidden" name="publish" id="publishInput" value="0">

            <div class="overflow-x-auto max-h-[70vh]">
                <table class="w-full text-sm text-gray-300 table-fixed">
                    <thead class="sticky top-0 bg-slate-800 text-gray-400 z-10">
                        <tr>
                            <th class="px-4 py-3 w-16 text-center">#</th>
                            <th class="px-4 py-3">Team / Player</th>
                            <th class="px-4 py-3 w-20 text-center">Rank</th>
                            <th class="px-4 py-3 w-24 text-center">Kills</th>
                            <th class="px-4 py-3 w-24 text-center">Points</th>
                            <th class="px-4 py-3 w-24 text-center">Winner</th>
                            <th class="px-4 py-3 w-20 text-center">Clear</th>
                        </tr>
                    </thead>

                    <tbody id="tableBody"></tbody>
                </table>
            </div>

            <!-- ACTIONS -->
            <div class="flex justify-end gap-4 p-6 border-t border-slate-800">
                <button type="button" onclick="submitResults(false)"
                    class="px-8 py-3 rounded-xl bg-slate-700 hover:bg-slate-600 font-bold">
                    💾 Save Draft
                </button>

                <button type="button" onclick="submitResults(true)"
                    class="px-10 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-green-600 font-bold">
                    🚀 Publish Results
                </button>
            </div>
        </form>

    </section>

    <script>
        /* =========================
       CONFIG
    ========================= */
        const STORAGE_KEY = 'match_results_draft_{{ $tournament->id }}';
        const participants = @json($joins);



        /* =========================
           BUILD GROUPS
        ========================= */
        function buildGroups(participants) {
            const groups = [];

            participants.forEach(join => {

                // SOLO
                if (join.mode === 'solo') {
                    groups.push({
                        team: null,
                        players: [{
                            key: `join_${join.id}_captain`,
                            join_id: join.id,
                            ign: join.captain_ign
                        }]
                    });
                    return;
                }

                // DUO / SQUAD
                const players = [];

                players.push({
                    key: `join_${join.id}_captain`,
                    join_id: join.id,
                    ign: join.captain_ign
                });

                (join.members || []).forEach((m, index) => {
                    players.push({
                        key: `join_${join.id}_member_${index}`,
                        join_id: join.id,
                        ign: m.ign
                    });
                });


                groups.push({
                    team: join.team_name || `Team ${join.id}`,
                    players
                });
            });

            return groups;
        }

        /* =========================
           RENDER TABLE
        ========================= */
        const groups = buildGroups(participants);
        const tableBody = document.getElementById('tableBody');
        let savedData = {};

        try {
            savedData = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
        } catch (e) {
            console.warn('Invalid draft data, resetting');
            localStorage.removeItem(STORAGE_KEY);
        }


        let rowIndex = 1;

        groups.forEach(group => {

            if (group.team) {
                tableBody.insertAdjacentHTML('beforeend', `
            <tr class="bg-slate-800/60 border-t border-slate-700">
                <td class="px-4 py-2 text-center font-bold">—</td>
                <td class="font-bold">${group.team}</td>
                <td colspan="4"></td>
                <td class="text-center">
                    <button type="button"
                            onclick="clearTeam('${group.team}')"
                            class="text-xs px-3 py-1 rounded bg-red-500/20 text-red-400">
                        Clear
                    </button>
                </td>
            </tr>
        `);
            }

            group.players.forEach(player => {
                const data = savedData[player.key] || {};

                tableBody.insertAdjacentHTML('beforeend', `
            <tr class="border-t border-slate-800 player-row"
                data-key="${player.key}"
                data-join="${player.join_id}">

                <td class="px-4 py-2 text-center">${rowIndex++}</td>

                <td class="pl-6 font-semibold player-name">${player.ign}</td>

                <td class="text-center">
                    <input type="number" data-field="rank" value="${data.rank || ''}"
                        class="w-20 bg-slate-800 border border-slate-700 rounded px-2 py-1">
                </td>

                <td class="text-center">
                    <input type="number" data-field="kills" value="${data.kills || ''}" min="0"
                        class="w-24 bg-slate-800 border border-slate-700 rounded px-2 py-1">
                </td>

                <td class="text-center">
                    <input type="number" data-field="points" value="${data.points || ''}" min="0"
                        class="w-24 bg-slate-800 border border-slate-700 rounded px-2 py-1">
                </td>

                <td class="text-center">
                    <select data-field="winner"
                        class="winner-select bg-slate-800 border border-slate-700 rounded px-2 py-1 text-sm">
                        <option value="">—</option>
                        <option value="1" ${data.winner === '1' ? 'selected' : ''}>🥇 1st</option>
                        <option value="2" ${data.winner === '2' ? 'selected' : ''}>🥈 2nd</option>
                        <option value="3" ${data.winner === '3' ? 'selected' : ''}>🥉 3rd</option>
                    </select>
                </td>

                <td class="text-center">
                    <button type="button"
                        onclick="clearPlayer('${player.key}')"
                        class="text-xs px-3 py-1 rounded bg-red-500/20 text-red-400">
                        Clear
                    </button>
                </td>
            </tr>
        `);
            });
        });

        /* =========================
           AUTOSAVE
        ========================= */
        document.addEventListener('input', e => {
            const row = e.target.closest('.player-row');
            if (!row) return;

            const key = row.dataset.key;
            const field = e.target.dataset.field;

            if (!savedData[key]) savedData[key] = {};
            savedData[key][field] = e.target.value;

            localStorage.setItem(STORAGE_KEY, JSON.stringify(savedData));
        });

        /* =========================
           PREVENT DUPLICATE WINNERS
        ========================= */
        document.addEventListener('change', e => {
            if (!e.target.classList.contains('winner-select')) return;

            const selected = e.target.value;
            if (!selected) return;

            document.querySelectorAll('.winner-select').forEach(sel => {
                if (sel !== e.target && sel.value === selected) {
                    sel.value = '';
                    alert('Only one 1st, 2nd, and 3rd winner is allowed');
                }
            });
        });

        /* =========================
           SUBMIT
        ========================= */
        function submitResults(publish = false) {

            const payload = [];

            document.querySelectorAll('.player-row').forEach(row => {
                const key = row.dataset.key;
                const joinId = row.dataset.join;
                const data = savedData[key];

                if (!data) return;

                payload.push({
                    tournament_join_id: joinId,
                    player_ign: row.querySelector('.player-name').innerText,
                    rank: data.rank || null,
                    kills: data.kills || 0,
                    points: data.points || 0,
                    winner_position: data.winner || null,
                });
            });

            if (!payload.length) {
                alert('No results entered.');
                return;
            }

            document.getElementById('resultsInput').value = JSON.stringify(payload);
            document.getElementById('publishInput').value = publish ? 1 : 0;

            document.getElementById('resultsForm').submit();
        }

        /* =========================
           CLEAR
        ========================= */
        function clearPlayer(key) {
            delete savedData[key];
            localStorage.setItem(STORAGE_KEY, JSON.stringify(savedData));
            document.querySelector(`[data-key="${key}"]`).querySelectorAll('input,select')
                .forEach(el => el.value = '');
        }

        function clearTeam(team) {
            document.querySelectorAll(`[data-team="${team}"]`).forEach(row => {
                delete savedData[row.dataset.key];
                row.querySelectorAll('input,select').forEach(el => el.value = '');
            });
            localStorage.setItem(STORAGE_KEY, JSON.stringify(savedData));
        }

        /* =========================
           SEARCH
        ========================= */
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.player-row').forEach(row => {
                const name = row.querySelector('.player-name').innerText.toLowerCase();
                row.style.display = name.includes(q) ? '' : 'none';
            });
        });
    </script>

@endsection
