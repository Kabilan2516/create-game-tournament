@extends('layouts.dashboard')

@section('title', 'Upload Match Results')

@section('dashboard-content')

<div class="bg-slate-900 border-b border-slate-800 px-8 py-6">
    <h1 class="text-3xl font-bold">📊 Upload Match Results</h1>
    <p class="text-gray-400">
        Tournament: <span class="text-white font-semibold">{{ $tournament->title }}</span>
    </p>
</div>

<section class="px-8 py-10 max-w-4xl">

    <!-- INFO -->
    <div class="mb-8 bg-slate-800 border border-cyan-500/40 rounded-xl p-6">
        <p class="text-cyan-300 font-semibold mb-2">ℹ Result Upload Rules</p>
        <ul class="text-sm text-gray-300 space-y-1">
            <li>• Upload CSV file only</li>
            <li>• Must include Join Code for each player</li>
            <li>• Results can be uploaded only once</li>
        </ul>
    </div>

    <!-- FORM -->
    <form method="POST"
          action="{{ route('organizer.results.store', $tournament) }}"
          enctype="multipart/form-data"
          class="bg-slate-900 p-8 rounded-3xl border border-slate-700">

        @csrf

        <div class="mb-6">
            <label class="block text-sm text-gray-400 mb-2">
                Upload Results File (.csv)
            </label>

            <input type="file"
                   name="results_file"
                   accept=".csv"
                   required
                   class="w-full px-4 py-3 rounded bg-slate-800 border border-slate-700">
        </div>

        <div class="flex justify-between items-center">
            <a href="#"
               class="text-cyan-400 hover:underline text-sm">
               📥 Download CSV Template
            </a>

            <button type="submit"
                    class="px-8 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-purple-600 font-bold">
                🚀 Upload Results
            </button>
        </div>

    </form>
</section>

@endsection
