@extends('layouts.dashboard')

@section('title', 'CODM Instant Result')

@section('dashboard-content')
    <div class="px-8 py-10 max-w-7xl">

        <!-- HEADER -->
        <div class="mb-10">
            <h1 class="text-3xl font-extrabold mb-2">⚡ CODM Instant Result Studio</h1>
            <p class="text-gray-400">
                Fast result entry with auto points, ready-to-share public results page.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 bg-red-900/40 border border-red-700 text-red-200 px-4 py-3 rounded-xl">
                {{ $errors->first() }}
            </div>
        @endif

        <div id="series-context-bar" class="mb-6 hidden">
            <div class="bg-slate-900 border border-cyan-500/40 rounded-2xl p-4">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <h3 id="series-context-title" class="text-sm font-semibold text-cyan-300">Series Context</h3>
                    <span class="text-xs text-gray-400">All linked matches</span>
                </div>
                <div id="series-context-tabs" class="flex flex-wrap gap-2"></div>
                <p id="series-context-note" class="text-xs text-cyan-100/80 mt-3 hidden"></p>
            </div>
        </div>

        <form id="instant-result-form" action="{{ route('organizer.results.instant.store') }}" method="POST">
            @csrf

            <input type="hidden" name="game" value="{{ $game ?? 'CODM' }}" />
            <input type="hidden" name="results" id="results-input" />
            <input type="hidden" name="rank_points" id="rank-points-input" />
            <input type="hidden" name="kill_point" id="kill-point-input" />

            <div class="grid lg:grid-cols-3 gap-6">
                <!-- MATCH DETAILS -->
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 lg:col-span-2">
                    <h2 class="text-xl font-bold mb-4">Match Details</h2>

                <div class="grid md:grid-cols-2 gap-4">
                    <div class="md:col-span-2 bg-slate-800/60 border border-cyan-500/40 rounded-2xl p-4">
                        @if (empty($series))
                            <label class="text-sm font-semibold text-cyan-300">Series Linking (Recommended)</label>
                            <select name="previous_tournament_id" id="previous-match-select"
                                class="w-full mt-2 px-4 py-3 bg-slate-900 rounded-xl border border-slate-700">
                                <option value="">No previous match</option>
                                @if (isset($previousMatches) && $previousMatches->isNotEmpty())
                                    @foreach ($previousMatches as $match)
                                        <option value="{{ $match->id }}" data-mode="{{ $match->mode }}" data-match-type="{{ $match->match_type }}">
                                            {{ $match->title }} — {{ $match->start_time->format('d M, h:i A') }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <p class="text-xs text-cyan-200/80 mt-2">
                                Use this when this match is part of an ongoing series. MP will increase and overall standings update in one series leaderboard.
                            </p>
                        @else
                            <label class="text-sm font-semibold text-cyan-300">Series</label>
                            <input type="text" value="{{ $series->title }}" readonly
                                class="w-full mt-2 px-4 py-3 bg-slate-900 rounded-xl border border-slate-700" />
                            <input type="hidden" name="series_id" value="{{ $series->id }}">
                            <p class="text-xs text-cyan-200/80 mt-2">
                                This match will be added to the selected series and calculated in the same leaderboard.
                            </p>
                        @endif
                    </div>

                    <div>
                        <label class="text-sm text-gray-400">Title</label>
                        <input name="title" required
                            class="w-full mt-1 px-4 py-3 bg-slate-800 rounded-xl border border-slate-700 focus:ring-2 focus:ring-cyan-500"
                            placeholder="CODM BR Scrim #12" />
                    </div>

                    <div>
                        <label class="text-sm text-gray-400">Mode</label>
                        <select name="mode" id="mode-select"
                            class="w-full mt-1 px-4 py-3 bg-slate-800 rounded-xl border border-slate-700"
                            @if(!empty($series)) disabled @endif>
                            <option value="solo" {{ (!empty($series) && $series->mode === 'solo') ? 'selected' : '' }}>Solo</option>
                            <option value="duo" {{ (!empty($series) && $series->mode === 'duo') ? 'selected' : '' }}>Duo</option>
                            <option value="squad" {{ (empty($series) ? 'selected' : '') }} {{ (!empty($series) && $series->mode === 'squad') ? 'selected' : '' }}>Squad</option>
                        </select>
                        @if(!empty($series))
                            <input type="hidden" name="mode" value="{{ $series->mode }}">
                        @endif
                        @if(empty($series))
                            <input type="hidden" id="locked-mode-input" />
                        @endif
                    </div>

                    <div>
                        <label class="text-sm text-gray-400">Match Type</label>
                        <select name="match_type" id="match-type-select"
                            class="w-full mt-1 px-4 py-3 bg-slate-800 rounded-xl border border-slate-700"
                            @if(!empty($series)) disabled @endif>
                            <option value="BR" {{ (!empty($series) && $series->match_type === 'BR') ? 'selected' : '' }}>BR</option>
                            <option value="MP" {{ (!empty($series) && $series->match_type === 'MP') ? 'selected' : '' }}>MP</option>
                            <option value="S&D" {{ (!empty($series) && $series->match_type === 'S&D') ? 'selected' : '' }}>S&D</option>
                            <option value="TDM" {{ (!empty($series) && $series->match_type === 'TDM') ? 'selected' : '' }}>TDM</option>
                        </select>
                        @if(!empty($series))
                            <input type="hidden" name="match_type" value="{{ $series->match_type }}">
                        @endif
                        @if(empty($series))
                            <input type="hidden" id="locked-match-type-input" />
                        @endif
                    </div>

                    <div>
                        <label class="text-sm text-gray-400">Map</label>
                        <input name="map"
                            class="w-full mt-1 px-4 py-3 bg-slate-800 rounded-xl border border-slate-700"
                            placeholder="Isolated / Blackout"
                            value="{{ $series->map ?? '' }}"
                            @if(!empty($series)) readonly @endif />
                    </div>

                    <div>
                        <label class="text-sm text-gray-400">Region</label>
                        <input name="region" value="India"
                            class="w-full mt-1 px-4 py-3 bg-slate-800 rounded-xl border border-slate-700" />
                    </div>

                    <div>
                        <label class="text-sm text-gray-400">Auto Players / Teams Count (100 Slots)</label>
                        <div class="mt-1">
                            <input id="player-count" type="number" min="2" value="10"
                                readonly
                                class="w-full px-4 py-3 bg-slate-800 text-white rounded-xl border border-slate-700 [color-scheme:dark]" />
                        </div>
                        <div class="mt-2">
                            <button type="button" id="generate-entries"
                                class="w-full md:w-auto px-4 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 font-semibold">
                                Generate
                            </button>
                        </div>
                        <p id="count-hint" class="text-xs text-gray-500 mt-1">
                            Uses fixed 100 slots. Solo = 100 players, Duo = 50 teams, Squad = 25 teams.
                        </p>
                    </div>

                    <div class="flex items-end">
                        <div class="text-xs text-gray-400">
                            Tip: After publish, you will get a public share link.
                        </div>
                    </div>
                    </div>
                </div>

                <!-- SCORING SETTINGS -->
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
                    <h2 class="text-xl font-bold mb-4">Scoring</h2>

                    <div class="mb-4">
                        <label class="text-sm text-gray-400">Points per Kill</label>
                    <input id="points-per-kill" type="number" value="{{ $series->kill_point ?? config('points.codm.kill_point', 1) }}"
                        min="0" step="1"
                        class="w-full mt-1 px-4 py-3 bg-slate-800 rounded-xl border border-slate-700"
                        @if(!empty($series)) readonly @endif />
                    </div>

                    <div>
                        <div class="text-sm text-gray-400 mb-2">Rank Points</div>
                        <div id="rank-points-grid" class="grid grid-cols-2 gap-2"></div>
                    </div>
                </div>
            </div>

        <!-- RESULTS TABLE -->
        <div class="mt-8 bg-slate-900 border border-slate-800 rounded-3xl p-6">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                <h2 class="text-xl font-bold">Results Entry</h2>
                <div class="flex flex-wrap gap-2">
                    <button type="button" id="add-row"
                        class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed font-semibold">
                        + Add Row
                    </button>
                    <button type="button" id="add-five"
                        class="px-4 py-2 rounded-lg bg-slate-700 hover:bg-slate-600 disabled:opacity-50 disabled:cursor-not-allowed font-semibold">
                        + Add 5
                    </button>
                    <button type="button" id="add-team"
                        class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed font-semibold hidden">
                        + Add Team
                    </button>
                    <button type="button" id="load-dummy"
                        class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 font-semibold">
                        Load Dummy Data
                    </button>
                    <button type="button" id="clear-all"
                        class="px-4 py-2 rounded-lg bg-red-700 hover:bg-red-800 font-semibold">
                        Clear All
                    </button>
                </div>
            </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-gray-300" id="results-table">
                        <thead class="bg-slate-800 text-gray-400">
                            <tr>
                                <th class="px-3 py-3">#</th>
                                <th class="px-3 py-3" id="team-header">Team</th>
                                <th class="px-3 py-3">Player IGN</th>
                                <th class="px-3 py-3">Game ID</th>
                                <th class="px-3 py-3 text-center">Rank</th>
                                <th class="px-3 py-3 text-center">Kills</th>
                                <th class="px-3 py-3 text-center">MP</th>
                                <th class="px-3 py-3 text-center">KP</th>
                                <th class="px-3 py-3 text-center">PP</th>
                                <th class="px-3 py-3 text-center">TT</th>
                                <th class="px-3 py-3 text-center">CD</th>
                            </tr>
                        </thead>
                        <tbody id="results-body"></tbody>
                    </table>
                </div>

            <div class="mt-4 text-sm text-gray-400">
                Player Slots: <span id="row-count">0</span>/100 • Teams: <span id="team-count">0</span>
            </div>
        </div>

        <div class="mt-8">
            <button type="submit"
                class="w-full md:w-auto px-8 py-3 rounded-xl bg-cyan-600 hover:bg-cyan-700 font-bold">
                Publish Results
            </button>
            </div>
        </form>
    </div>

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
            const defaultRankPoints = @json($series?->placement_points ?? $defaultRankPoints);
            const isSeries = @json(!empty($series));
            const TEAM_SIZE = {
                duo: 2,
                squad: 4
            };

            const rankPointsGrid = document.getElementById('rank-points-grid');
            const pointsPerKillInput = document.getElementById('points-per-kill');
            const resultsBody = document.getElementById('results-body');
            const resultsInput = document.getElementById('results-input');
            const rankPointsInput = document.getElementById('rank-points-input');
            const killPointInput = document.getElementById('kill-point-input');
            const modeSelect = document.getElementById('mode-select');
            const matchTypeSelect = document.getElementById('match-type-select');
            const teamHeader = document.getElementById('team-header');
            const rowCountEl = document.getElementById('row-count');
            const teamCountEl = document.getElementById('team-count');
            const addRowBtn = document.getElementById('add-row');
            const addFiveBtn = document.getElementById('add-five');
            const addTeamBtn = document.getElementById('add-team');
            const clearAllBtn = document.getElementById('clear-all');
            const loadDummyBtn = document.getElementById('load-dummy');
            const playerCountInput = document.getElementById('player-count');
            const generateEntriesBtn = document.getElementById('generate-entries');
            const countHint = document.getElementById('count-hint');
            const previousMatchSelect = document.getElementById('previous-match-select');
            const seriesContextBar = document.getElementById('series-context-bar');
            const seriesContextTitle = document.getElementById('series-context-title');
            const seriesContextTabs = document.getElementById('series-context-tabs');
            const seriesContextNote = document.getElementById('series-context-note');
            const lockedModeInput = document.getElementById('locked-mode-input');
            const lockedMatchTypeInput = document.getElementById('locked-match-type-input');
            const TOTAL_SLOTS = 100;

            function renderRankPoints() {
                rankPointsGrid.innerHTML = '';
                Object.keys(defaultRankPoints).sort((a, b) => Number(a) - Number(b)).forEach((rank) => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'flex items-center gap-2 bg-slate-800 border border-slate-700 rounded-lg px-2 py-2';
                    wrapper.innerHTML = `
                        <span class="text-xs text-gray-400">#${rank}</span>
                        <input type="number" min="0" step="1" value="${defaultRankPoints[rank]}"
                            data-rank="${rank}"
                            class="w-full bg-transparent text-right outline-none"
                            ${isSeries ? 'disabled' : ''} />
                    `;
                    rankPointsGrid.appendChild(wrapper);
                });
            }

            function getRankPoints(rank) {
                const input = rankPointsGrid.querySelector(`input[data-rank="${rank}"]`);
                if (!input) return 0;
                return parseInt(input.value || '0', 10);
            }

            function hideSeriesContext() {
                if (!seriesContextBar) return;
                seriesContextBar.classList.add('hidden');
                seriesContextTabs.innerHTML = '';
                seriesContextNote.textContent = '';
                seriesContextNote.classList.add('hidden');
            }

            function renderSeriesContext(payload) {
                if (!seriesContextBar) return;

                const matches = payload?.matches || [];
                if (!matches.length) {
                    hideSeriesContext();
                    return;
                }

                seriesContextTabs.innerHTML = '';
                const isLinkedSeries = !!payload?.series;
                seriesContextTitle.textContent = isLinkedSeries
                    ? `Series Timeline: ${payload.series.title}`
                    : 'Match Context';

                matches.forEach((m, idx) => {
                    const item = document.createElement('div');
                    item.className = 'px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-xs text-gray-200';
                    item.innerHTML = `
                        <span class="text-cyan-300 font-semibold">M${idx + 1}</span>
                        <span class="mx-1">•</span>
                        <span>${m.title}</span>
                        <span class="mx-1 text-gray-500">|</span>
                        <span class="text-gray-400">${m.date || '-'}</span>
                    `;
                    seriesContextTabs.appendChild(item);
                });

                if (payload?.message) {
                    seriesContextNote.textContent = payload.message;
                    seriesContextNote.classList.remove('hidden');
                } else {
                    seriesContextNote.textContent = '';
                    seriesContextNote.classList.add('hidden');
                }

                seriesContextBar.classList.remove('hidden');
            }

            async function loadSeriesContextFromPrevious() {
                if (!previousMatchSelect || !previousMatchSelect.value) {
                    hideSeriesContext();
                    return;
                }

                try {
                    const url = `{{ route('organizer.results.instant.series_context') }}?tournament_id=${encodeURIComponent(previousMatchSelect.value)}`;
                    const response = await fetch(url, { method: 'GET' });
                    if (!response.ok) {
                        hideSeriesContext();
                        return;
                    }
                    const payload = await response.json();
                    renderSeriesContext(payload);
                } catch (e) {
                    hideSeriesContext();
                }
            }

            function recalcRow(row) {
                const rank = parseInt(row.querySelector('[data-field="rank"]').value || '0', 10);
                const kills = parseInt(row.querySelector('[data-field="kills"]').value || '0', 10);
                const perKill = parseInt(pointsPerKillInput.value || '0', 10);
                const pp = getRankPoints(rank);
                const kp = kills * perKill;
                const tt = pp + kp;
                const cd = rank === 1 ? 1 : 0;

                row.querySelector('[data-field="mp"]').value = 1;
                row.querySelector('[data-field="kp"]').value = kp;
                row.querySelector('[data-field="pp"]').value = pp;
                row.querySelector('[data-field="tt"]').value = tt;
                row.querySelector('[data-field="cd"]').value = cd;
            }

            function updateCounts() {
                const rows = Array.from(resultsBody.querySelectorAll('tr')).filter(r => !r.dataset.spacer);
                rowCountEl.textContent = rows.length;

                if (modeSelect.value === 'solo') {
                    teamCountEl.textContent = rows.length;
                } else {
                    const teamIds = new Set(
                        rows.map(r => (r.dataset.teamId || '').trim()).filter(Boolean)
                    );
                    const teams = new Set(
                        rows.map(r => r.querySelector('[data-field="team_name"]').value.trim()).filter(Boolean)
                    );
                    teamCountEl.textContent = teamIds.size || teams.size;
                }

                const isFull = rows.length >= TOTAL_SLOTS;
                if (addRowBtn) addRowBtn.disabled = isFull;
                if (addFiveBtn) addFiveBtn.disabled = isFull;
                if (addTeamBtn) addTeamBtn.disabled = isFull;
            }

            function createRow(index, options = {}) {
                const tr = document.createElement('tr');
                tr.className = 'border-t border-slate-800';
                tr.dataset.teamId = options.teamId || '';
                tr.innerHTML = `
                    <td class="px-3 py-3 text-gray-500">${index}</td>
                    <td class="px-3 py-3 team-cell">
                        <input type="text" data-field="team_name" placeholder="Team Alpha"
                            value="${options.teamName || ''}"
                            class="${options.showTeamInput ? 'w-full' : 'hidden'} bg-slate-800 border border-slate-700 rounded-lg px-2 py-2" />
                        <span class="${options.showTeamInput ? 'hidden' : 'text-gray-400'}">${options.showTeamInput ? '' : (options.teamName || '')}</span>
                    </td>
                    <td class="px-3 py-3">
                        <input type="text" data-field="player_ign" placeholder="Player IGN"
                            value="${options.playerIgn || ''}"
                            class="w-full bg-slate-800 border border-slate-700 rounded-lg px-2 py-2" />
                    </td>
                    <td class="px-3 py-3">
                        <input type="text" data-field="player_game_id" placeholder="CODM ID (optional)"
                            value="${options.playerGameId || ''}"
                            class="w-full bg-slate-800 border border-slate-700 rounded-lg px-2 py-2" />
                    </td>
                    <td class="px-3 py-3 text-center">
                        <input type="number" min="1" data-field="rank" placeholder="1"
                            value="${options.rank || ''}"
                            class="w-20 text-center bg-slate-800 border border-slate-700 rounded-lg px-2 py-2" />
                    </td>
                    <td class="px-3 py-3 text-center">
                        <input type="number" min="0" data-field="kills" placeholder="0"
                            value="${options.kills || ''}"
                            class="w-20 text-center bg-slate-800 border border-slate-700 rounded-lg px-2 py-2" />
                    </td>
                    <td class="px-3 py-3 text-center">
                        <input type="number" data-field="mp" readonly
                            class="w-16 text-center bg-slate-900 border border-slate-700 rounded-lg px-2 py-2 text-gray-300" />
                    </td>
                    <td class="px-3 py-3 text-center">
                        <input type="number" data-field="kp" readonly
                            class="w-16 text-center bg-slate-900 border border-slate-700 rounded-lg px-2 py-2 text-gray-300" />
                    </td>
                    <td class="px-3 py-3 text-center">
                        <input type="number" data-field="pp" readonly
                            class="w-16 text-center bg-slate-900 border border-slate-700 rounded-lg px-2 py-2 text-gray-300" />
                    </td>
                    <td class="px-3 py-3 text-center">
                        <input type="number" data-field="tt" readonly
                            class="w-16 text-center bg-slate-900 border border-slate-700 rounded-lg px-2 py-2 text-yellow-300 font-bold" />
                    </td>
                    <td class="px-3 py-3 text-center">
                        <input type="number" data-field="cd" readonly
                            class="w-16 text-center bg-slate-900 border border-slate-700 rounded-lg px-2 py-2 text-gray-300" />
                    </td>
                `;

                tr.querySelectorAll('input').forEach((input) => {
                    input.addEventListener('input', () => {
                        if (input.dataset.field === 'team_name') {
                            syncTeamName(tr);
                        }
                        if (input.dataset.field === 'rank') {
                            syncTeamRank(tr);
                        }
                        recalcRow(tr);
                        updateCounts();
                    });
                });

                return tr;
            }

            function addSoloRow() {
                if (getPlayerRowCount() >= TOTAL_SLOTS) return;
                const index = getPlayerRowCount() + 1;
                const tr = createRow(index, { showTeamInput: true });
                resultsBody.appendChild(tr);
                recalcRow(tr);
                updateCounts();
                applyMode();
            }

            function addTeamGroup(team = {}) {
                const mode = modeSelect.value;
                const size = TEAM_SIZE[mode] || 2;
                if ((getPlayerRowCount() + size) > TOTAL_SLOTS) return;
                const teamId = `team_${Date.now()}_${Math.random().toString(36).slice(2, 6)}`;
                const startIndex = getPlayerRowCount() + 1;
                const players = team.players || [];

                for (let i = 0; i < size; i++) {
                    const player = players[i] || {};
                    const tr = createRow(startIndex + i, {
                        teamId,
                        teamName: team.name || '',
                        showTeamInput: i === 0,
                        rank: team.rank || '',
                        playerIgn: player.ign || '',
                        playerGameId: player.gameId || '',
                        kills: player.kills || '',
                    });
                    resultsBody.appendChild(tr);
                    recalcRow(tr);
                }

                updateCounts();
            }

            function getPlayerRowCount() {
                return Array.from(resultsBody.querySelectorAll('tr')).filter((r) => !r.dataset.spacer).length;
            }

            function syncTeamName(row) {
                const teamId = row.dataset.teamId;
                if (!teamId) return;
                const name = row.querySelector('[data-field="team_name"]').value;
                resultsBody.querySelectorAll(`tr[data-team-id="${teamId}"]`).forEach((r) => {
                    const input = r.querySelector('[data-field="team_name"]');
                    if (input) {
                        input.value = name;
                    }
                    const span = r.querySelector('td.team-cell span');
                    if (span && span.classList.contains('text-gray-400')) {
                        span.textContent = name;
                    }
                });
            }

            function syncTeamRank(row) {
                const teamId = row.dataset.teamId;
                if (!teamId) return;
                const rank = row.querySelector('[data-field="rank"]').value;
                resultsBody.querySelectorAll(`tr[data-team-id="${teamId}"]`).forEach((r) => {
                    const input = r.querySelector('[data-field="rank"]');
                    if (input) {
                        input.value = rank;
                        recalcRow(r);
                    }
                });
            }

            function applyMode() {
                const isSolo = modeSelect.value === 'solo';
                teamHeader.classList.toggle('hidden', isSolo);
                resultsBody.querySelectorAll('.team-cell').forEach((cell) => {
                    cell.classList.toggle('hidden', isSolo);
                });
                addRowBtn.classList.toggle('hidden', !isSolo);
                addFiveBtn.classList.toggle('hidden', !isSolo);
                addTeamBtn.classList.toggle('hidden', isSolo);
                const autoCount = getAutoCountByMode(modeSelect.value);
                playerCountInput.min = autoCount;
                playerCountInput.max = autoCount;
                playerCountInput.value = autoCount;

                if (isSolo) {
                    countHint.textContent = `Fixed slots: ${TOTAL_SLOTS} players for Solo.`;
                } else if (modeSelect.value === 'duo') {
                    countHint.textContent = `Fixed slots: ${TOTAL_SLOTS} players => ${autoCount} teams for Duo.`;
                } else {
                    countHint.textContent = `Fixed slots: ${TOTAL_SLOTS} players => ${autoCount} teams for Squad.`;
                }
            }

            function getAutoCountByMode(mode) {
                if (mode === 'solo') return TOTAL_SLOTS;
                if (mode === 'duo') return TOTAL_SLOTS / 2;
                if (mode === 'squad') return TOTAL_SLOTS / 4;
                return 0;
            }

            function syncModeLockFromPreviousMatch() {
                if (!previousMatchSelect || isSeries) return;

                const selectedOption = previousMatchSelect.options[previousMatchSelect.selectedIndex];
                const selectedMode = selectedOption ? selectedOption.getAttribute('data-mode') : null;
                const rawMatchType = selectedOption ? selectedOption.getAttribute('data-match-type') : null;
                const normalizeMatchType = (value) => {
                    if (!value) return '';
                    const upper = String(value).toUpperCase();
                    if (upper === 'BR') return 'BR';
                    if (upper === 'MP') return 'MP';
                    if (upper === 'S&D' || upper === 'SD' || upper === 'SEARCH & DESTROY') return 'S&D';
                    if (upper === 'TDM') return 'TDM';
                    return upper;
                };
                const selectedMatchType = normalizeMatchType(rawMatchType);

                if (selectedMode) {
                    const modeChanged = modeSelect.value !== selectedMode;
                    modeSelect.value = selectedMode;
                    modeSelect.setAttribute('disabled', 'disabled');
                    if (lockedModeInput) {
                        lockedModeInput.setAttribute('name', 'mode');
                        lockedModeInput.value = selectedMode;
                    }
                    if (matchTypeSelect && selectedMatchType) {
                        matchTypeSelect.value = selectedMatchType;
                        matchTypeSelect.setAttribute('disabled', 'disabled');
                        if (lockedMatchTypeInput) {
                            lockedMatchTypeInput.setAttribute('name', 'match_type');
                            lockedMatchTypeInput.value = selectedMatchType;
                        }
                    }
                    if (modeChanged) {
                        resultsBody.innerHTML = '';
                        if (modeSelect.value === 'solo') {
                            addSoloRow();
                        } else {
                            addTeamGroup();
                        }
                    }
                    applyMode();
                    updateCounts();
                    return;
                }

                modeSelect.removeAttribute('disabled');
                if (lockedModeInput) {
                    lockedModeInput.removeAttribute('name');
                    lockedModeInput.value = '';
                }
                if (matchTypeSelect) {
                    matchTypeSelect.removeAttribute('disabled');
                }
                if (lockedMatchTypeInput) {
                    lockedMatchTypeInput.removeAttribute('name');
                    lockedMatchTypeInput.value = '';
                }
            }

            function generateByCount() {
                const mode = modeSelect.value;
                const count = getAutoCountByMode(mode);
                playerCountInput.value = count;
                resultsBody.innerHTML = '';

                if (mode === 'solo') {
                    for (let i = 0; i < count; i++) {
                        addSoloRow();
                    }
                } else {
                    for (let i = 0; i < count; i++) {
                        addTeamGroup();
                    }
                }

                applyMode();
                updateCounts();
            }

            function serializeResults() {
                const rows = Array.from(resultsBody.querySelectorAll('tr')).filter(r => !r.dataset.spacer);
                const results = [];

                for (const row of rows) {
                    const playerIgn = row.querySelector('[data-field="player_ign"]').value.trim();
                    const teamName = row.querySelector('[data-field="team_name"]').value.trim();

                    if (playerIgn === '') {
                        continue;
                    }

                    if (modeSelect.value !== 'solo' && teamName === '') {
                        alert('Please enter team name for all players.');
                        return null;
                    }

                    const rank = parseInt(row.querySelector('[data-field="rank"]').value || '0', 10) || null;
                    if (!rank || rank < 1) {
                        alert('Each row must have a valid rank (1 or higher).');
                        return null;
                    }
                    const kills = parseInt(row.querySelector('[data-field="kills"]').value || '0', 10) || 0;
                    const points = parseInt(row.querySelector('[data-field="tt"]').value || '0', 10) || 0;
                    const winnerPosition = rank && [1, 2, 3].includes(rank) ? String(rank) : null;

                    results.push({
                        team_name: modeSelect.value === 'solo' ? null : teamName,
                        player_ign: playerIgn,
                        player_game_id: row.querySelector('[data-field="player_game_id"]').value.trim() || null,
                        rank,
                        kills,
                        points,
                        winner_position: winnerPosition,
                    });
                }

                if (modeSelect.value === 'duo' || modeSelect.value === 'squad') {
                    const requiredSize = modeSelect.value === 'duo' ? 2 : 4;
                    const grouped = results.reduce((acc, row) => {
                        const key = row.team_name || '__missing__';
                        if (!acc[key]) acc[key] = [];
                        acc[key].push(row);
                        return acc;
                    }, {});

                    for (const [teamName, members] of Object.entries(grouped)) {
                        if (!teamName || teamName === '__missing__') {
                            alert('Team name is required for duo/squad.');
                            return null;
                        }
                        if (members.length !== requiredSize) {
                            alert(`Team "${teamName}" must have exactly ${requiredSize} players.`);
                            return null;
                        }
                        const rankSet = [...new Set(members.map(m => m.rank))];
                        if (rankSet.length !== 1) {
                            alert(`Team "${teamName}" must use one team rank for all members.`);
                            return null;
                        }
                    }
                }

                return results;
            }

            addRowBtn.addEventListener('click', addSoloRow);
            addFiveBtn.addEventListener('click', () => {
                const remaining = Math.max(TOTAL_SLOTS - getPlayerRowCount(), 0);
                const batch = Math.min(5, remaining);
                for (let i = 0; i < batch; i++) addSoloRow();
            });
            addTeamBtn.addEventListener('click', () => addTeamGroup());
            generateEntriesBtn.addEventListener('click', generateByCount);

            loadDummyBtn.addEventListener('click', async () => {
                const rankPointsPayload = {};
                rankPointsGrid.querySelectorAll('input[data-rank]').forEach((input) => {
                    const rank = input.getAttribute('data-rank');
                    rankPointsPayload[rank] = parseInt(input.value || '0', 10);
                });

                const response = await fetch('{{ route('organizer.results.instant.dummy') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        mode: modeSelect.value,
                        rank_points: JSON.stringify(rankPointsPayload),
                        kill_point: pointsPerKillInput.value || 0,
                        limit: getAutoCountByMode(modeSelect.value),
                    }),
                });

                if (!response.ok) {
                    alert('Failed to load dummy data.');
                    return;
                }

                const data = await response.json();
                const results = data.results || [];
                resultsBody.innerHTML = '';

                if (modeSelect.value === 'solo') {
                    results.forEach((row) => {
                        addSoloRow();
                        const tr = resultsBody.querySelector('tr:last-child');
                        tr.querySelector('[data-field="team_name"]').value = row.team_name || '';
                        tr.querySelector('[data-field="player_ign"]').value = row.player_ign || '';
                        tr.querySelector('[data-field="player_game_id"]').value = row.player_game_id || '';
                        tr.querySelector('[data-field="rank"]').value = row.rank || '';
                        tr.querySelector('[data-field="kills"]').value = row.kills || 0;
                        recalcRow(tr);
                    });
                } else {
                    const grouped = {};
                    results.forEach((row) => {
                        const name = row.team_name || 'Team';
                        if (!grouped[name]) grouped[name] = { rank: row.rank, players: [] };
                        grouped[name].players.push({
                            ign: row.player_ign,
                            gameId: row.player_game_id,
                            kills: row.kills,
                        });
                    });
                    Object.keys(grouped).forEach((name) => {
                        addTeamGroup({
                            name,
                            rank: grouped[name].rank,
                            players: grouped[name].players,
                        });
                    });
                }

                applyMode();
                updateCounts();
            });

            clearAllBtn.addEventListener('click', () => {
                resultsBody.innerHTML = '';
                if (modeSelect.value === 'solo') {
                    addSoloRow();
                } else {
                    addTeamGroup();
                }
                updateCounts();
            });

            pointsPerKillInput.addEventListener('input', () => {
                resultsBody.querySelectorAll('tr').forEach(recalcRow);
            });

            rankPointsGrid.addEventListener('input', () => {
                resultsBody.querySelectorAll('tr').forEach(recalcRow);
            });

            modeSelect.addEventListener('change', () => {
                resultsBody.innerHTML = '';
                if (modeSelect.value === 'solo') {
                    addSoloRow();
                } else {
                    addTeamGroup();
                }
                applyMode();
                updateCounts();
            });

            if (previousMatchSelect) {
                previousMatchSelect.addEventListener('change', () => {
                    syncModeLockFromPreviousMatch();
                    loadSeriesContextFromPrevious();
                });
            }

            document.getElementById('instant-result-form').addEventListener('submit', (e) => {
                const results = serializeResults();
                if (!results || results.length === 0) {
                    e.preventDefault();
                    alert('Please add at least one result row.');
                    return;
                }
                const rankPointsPayload = {};
                rankPointsGrid.querySelectorAll('input[data-rank]').forEach((input) => {
                    const rank = input.getAttribute('data-rank');
                    rankPointsPayload[rank] = parseInt(input.value || '0', 10);
                });
                rankPointsInput.value = JSON.stringify(rankPointsPayload);
                killPointInput.value = pointsPerKillInput.value || '0';
                resultsInput.value = JSON.stringify(results);
            });

            renderRankPoints();
            if (modeSelect.value === 'solo') {
                addSoloRow();
            } else {
                addTeamGroup();
            }
            applyMode();
            updateCounts();
            syncModeLockFromPreviousMatch();
            loadSeriesContextFromPrevious();
        })();
    </script>
@endsection
