<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organizer;
use App\Models\Tournament;
use Illuminate\Http\Request;
use App\Models\TournamentJoin;
use Illuminate\Support\Facades\Auth;

class OrganizerController extends Controller
{
    public function profile()
    {
        $user = Auth::user();

        $organizer = Organizer::with('media')
            ->where('user_id', $user->id)
            ->firstOrFail();

        // ✅ PASS FULL MEDIA OBJECTS
        $banner = $organizer->media
            ->where('collection', 'banner')
            ->last();

        $avatar = $organizer->media
            ->where('collection', 'avatar')
            ->last();

        return view('organizer.profile', [
            'user' => $user,
            'organizer' => $organizer,

            // Media
            'banner' => $banner,
            'avatar' => $avatar,

            // Stats
            'tournamentsCount' => $organizer->tournaments()->count(),
            'seriesCount' => $organizer->series()->count(),
            'totalParticipants' => $organizer->tournaments()->sum('filled_slots'),
        ]);
    }

    public function settings()
    {
        return view('organizer.settings');
    }
    
    public function publicProfile(User $user)
    {
        // ✅ Load organizer profile with media
        $organizer = Organizer::with('media')
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Stats
        $totalTournaments = Tournament::where('organizer_id', $organizer->id)->count();

        $totalPlayers = TournamentJoin::whereHas('tournament', function ($q) use ($organizer) {
            $q->where('organizer_id', $organizer->id);
        })->count();

        $totalPrize = Tournament::where('organizer_id', $organizer->id)
            ->sum('prize_pool');

        // Upcoming tournaments
        $upcoming = Tournament::where('organizer_id', $organizer->id)
            ->whereIn('status', ['open', 'ongoing'])
            ->orderBy('start_time')
            ->take(6)
            ->get();

        // Completed tournaments
        $completed = Tournament::where('organizer_id', $organizer->id)
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
