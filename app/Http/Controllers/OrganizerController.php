<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tournament;
use Illuminate\Http\Request;
use App\Models\TournamentJoin;

class OrganizerController extends Controller
{
    public function profile()
    {
        return view('organizer.profile');
    }
    public function settings()
    {
        return view('organizer.settings');
    }
    public function publicProfile(User $user)
    {
        // Organizer basic info
        $organizer = $user;

        // Stats
        $totalTournaments = Tournament::where('organizer_id', $user->id)->count();
        $totalPlayers = TournamentJoin::where('organizer_id', $user->id)->count();
        $totalPrize = Tournament::where('organizer_id', $user->id)->sum('prize_pool');

        // Upcoming tournaments
        $upcoming = Tournament::where('organizer_id', $user->id)
            ->whereIn('status', ['open', 'ongoing'])
            ->orderBy('start_time')
            ->take(6)
            ->get();

        // Completed tournaments
        $completed = Tournament::where('organizer_id', $user->id)
            ->where('status', 'completed')
            ->latest()
            ->take(5)
            ->get();

        return view('organizer.public-profile', compact(
            'organizer',
            'totalTournaments',
            'totalPlayers',
            'totalPrize',
            'upcoming',
            'completed'
        ));
    }
}
