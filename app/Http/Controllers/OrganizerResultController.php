<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizerResultController extends Controller
{
public function create(Tournament $tournament)
{
    // 🔐 Security
    abort_if($tournament->organizer_id !== Auth::id(), 403);

    // 🔒 Match must be started
    abort_if(now()->lt($tournament->start_time), 403);

    // ✅ LOAD APPROVED JOINS + MEMBERS
    $joins = $tournament->joins()
        ->where('status', 'approved')
        ->with('members')
        ->orderBy('id')
        ->get();

    return view('organizer.results.upload', [
        'tournament' => $tournament,
        'joins'      => $joins,
    ]);
}





    public function store(Request $request, Tournament $tournament)
    {
        abort_if($tournament->organizer_id !== Auth::id(), 403);

        $request->validate([
            'results_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        // File handling logic will come in STEP 3
        // For now: parse + preview

        return back()->with('success', 'Results uploaded successfully. Pending processing.');
    }
}
