<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TournamentJoin;
use Illuminate\Support\Facades\Auth;

class PlayerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 🔹 All joins by this player (logged-in user)
        $joinsQuery = TournamentJoin::with('tournament')
            ->where('user_id', $user->id);

        // 🔹 Stats
        $stats = [
            'joined'   => (clone $joinsQuery)->count(),
            'approved' => (clone $joinsQuery)->where('status', 'approved')->count(),
            'pending'  => (clone $joinsQuery)->where('status', 'pending')->count(),
            'played'   => (clone $joinsQuery)
                ->whereHas('tournament', function ($q) {
                    $q->where('status', 'completed');
                })
                ->count(),
        ];

        // 🔹 Upcoming / Live matches
        $upcomingMatches = (clone $joinsQuery)
            ->where('status', 'approved')
            ->whereHas('tournament', function ($q) {
                $q->whereIn('status', ['open', 'ongoing'])
                  ->orderBy('start_time');
            })
            ->limit(5)
            ->get();

        // 🔹 Recent activity (simple for now, scalable later)
        $activities = [];

        foreach (
            (clone $joinsQuery)
                ->latest()
                ->limit(5)
                ->get()
            as $join
        ) {
            $activities[] =
                "🏆 Joined {$join->tournament->title} ({$join->status})";
        }

        return view('player.dashboard', [
            'stats'      => $stats,
            'upcoming'   => $upcomingMatches,
            'activities' => $activities,
        ]);
    }
}
