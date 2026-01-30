@extends('layouts.dashboard')

@section('title', 'Add Participants')

@section('dashboard-content')

    <div class="bg-slate-900 border-b border-slate-800 px-8 py-6">
        <h1 class="text-3xl font-bold">➕ Add Participants</h1>
        <p class="text-gray-400 mt-1">
            Tournament:
            <span class="text-white font-semibold">{{ $tournament->title }}</span>
            • Mode:
            <span class="text-cyan-400 font-semibold">{{ strtoupper($tournament->mode) }}</span>
        </p>
        <div class="flex gap-4 mb-6">
            <a href="{{ route('organizer.joins.template', $tournament) }}"
                class="px-4 py-2 rounded bg-slate-700 hover:bg-slate-600 text-sm">
                ⬇ Download Excel Template
            </a>

            <label class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-sm cursor-pointer">
                ⬆ Upload Excel
                <input type="file" hidden accept=".csv" onchange="uploadExcel(this)">
            </label>
        </div>

    </div>

    <section class="px-8 py-10 max-w-6xl">

        <form method="POST" action="{{ route('organizer.joins.store', $tournament) }}" id="manualJoinForm">

            @csrf

            <div id="teamsWrapper" class="space-y-8"></div>

            <div class="flex justify-between mt-10">
                <button type="button" onclick="addTeam()"
                    class="px-6 py-3 rounded-xl bg-slate-700 hover:bg-slate-600 font-semibold">
                    ➕ Add {{ $tournament->mode === 'solo' ? 'Player' : 'Team' }}
                </button>

                <button type="submit" class="px-10 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-purple-600 font-bold">
                    💾 Save Participants
                </button>
            </div>

        </form>
    </section>

    <script>
        /* =========================
     CONFIG
    ========================= */
        const MODE = "{{ $tournament->mode }}";
        const MAX_MEMBERS = MODE === 'solo' ? 1 : (MODE === 'duo' ? 2 : 4);
        const STORAGE_KEY = 'manual_join_draft_{{ $tournament->id }}';

        let teamIndex = 0;

        /* =========================
         LOAD DRAFT
        ========================= */
        const draft = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');

        /* =========================
         SAVE DRAFT
        ========================= */
        function saveDraft() {
            const data = [];

            document.querySelectorAll('.team-block').forEach(team => {
                const teamId = team.dataset.team;
                const teamData = {
                    team_name: team.querySelector('[data-team-name]')?.value || '',
                    members: []
                };

                team.querySelectorAll('[data-member]').forEach(member => {
                    teamData.members.push({
                        ign: member.querySelector('[data-ign]').value,
                        game_id: member.querySelector('[data-game]').value,
                    });
                });

                data.push(teamData);
            });

            localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
        }

        /* =========================
         ADD TEAM
        ========================= */
        function addTeam(prefill = null) {
            const wrapper = document.getElementById('teamsWrapper');
            const teamId = teamIndex++;

            wrapper.insertAdjacentHTML('beforeend', `
        <div class="bg-slate-900 border border-slate-700 rounded-3xl p-6 team-block"
             data-team="${teamId}">

            ${MODE !== 'solo' ? `
                <div class="mb-4">
                    <label class="text-sm text-gray-400">Team Name</label>
                    <input type="text"
                           data-team-name
                           name="teams[${teamId}][team_name]"
                           class="w-full mt-1 px-4 py-3 rounded-xl bg-slate-800 border border-slate-700"
                           placeholder="Team ${teamId + 1}">
                </div>` : ''}

            <div class="space-y-4 members"></div>

            <div class="flex justify-end mt-6">
                <button type="button"
                        onclick="removeTeam(${teamId})"
                        class="text-sm px-4 py-2 rounded bg-red-600/20 text-red-400 hover:bg-red-600/30">
                    ❌ Remove
                </button>
            </div>
        </div>
    `);

            // Auto-create full team
            for (let i = 0; i < MAX_MEMBERS; i++) {
                addMember(teamId, prefill?.members?.[i] || null);
            }

            if (prefill?.team_name) {
                document
                    .querySelector(`[data-team="${teamId}"] [data-team-name]`)
                    .value = prefill.team_name;
            }

            saveDraft();
        }

        /* =========================
         ADD MEMBER
        ========================= */
        function addMember(teamId, prefill = null) {
            const team = document.querySelector(`[data-team="${teamId}"]`);
            const members = team.querySelector('.members');
            const index = members.children.length;

            members.insertAdjacentHTML('beforeend', `
        <div class="grid md:grid-cols-2 gap-4 bg-slate-800 p-4 rounded-xl"
             data-member>

            <input type="text"
                   data-ign
                   name="teams[${teamId}][members][${index}][ign]"
                   placeholder="Player IGN"
                   class="px-4 py-3 rounded bg-slate-900 border border-slate-700">

            <input type="text"
                   data-game
                   name="teams[${teamId}][members][${index}][game_id]"
                   placeholder="Game ID"
                   class="px-4 py-3 rounded bg-slate-900 border border-slate-700">
        </div>
    `);

            if (prefill) {
                const block = members.lastElementChild;
                block.querySelector('[data-ign]').value = prefill.ign || '';
                block.querySelector('[data-game]').value = prefill.game_id || '';
            }
        }

        /* =========================
         REMOVE TEAM
        ========================= */
        function removeTeam(teamId) {
            document.querySelector(`[data-team="${teamId}"]`)?.remove();
            saveDraft();
        }

        /* =========================
         AUTOSAVE
        ========================= */
        document.addEventListener('input', saveDraft);

        /* =========================
         INIT
        ========================= */
        if (draft.length) {
            draft.forEach(team => addTeam(team));
        } else {
            addTeam();
        }
    </script>
    <script>
        async function uploadExcel(input) {
            const file = input.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');

            try {
                const res = await fetch(
                    "{{ route('organizer.joins.importPreview', $tournament) }}", {
                        method: 'POST',
                        body: formData,
                    }
                );

                if (!res.ok) {
                    alert('Failed to read file');
                    return;
                }

                const data = await res.json();

                if (!data.teams || !data.teams.length) {
                    alert('No valid rows found in file');
                    return;
                }

                // 🔥 Clear existing UI + draft
                document.getElementById('teamsWrapper').innerHTML = '';
                localStorage.removeItem(STORAGE_KEY);
                teamIndex = 0;

                // 🔥 Fill UI from Excel
                data.teams.forEach(team => {
                    addTeam(team);
                });

            } catch (e) {
                console.error(e);
                alert('Upload failed');
            }

            // reset input so re-upload works
            input.value = '';
        }
    </script>

@endsection
