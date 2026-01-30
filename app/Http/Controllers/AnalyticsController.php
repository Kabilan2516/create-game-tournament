<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentJoin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $organizerId = Auth::id();

        /* =========================
           CORE METRICS
        ========================= */

        $totalTournaments = Tournament::where('organizer_id', $organizerId)->count();

        $totalPlayers = TournamentJoin::whereHas('tournament', function ($q) use ($organizerId) {
            $q->where('organizer_id', $organizerId);
        })->count();

        $totalRevenue = TournamentJoin::whereHas('tournament', function ($q) use ($organizerId) {
            $q->where('organizer_id', $organizerId);
        })->where('payment_status', 'approved')->sum('entry_fee');

        $approvedRate = TournamentJoin::whereHas('tournament', function ($q) use ($organizerId) {
            $q->where('organizer_id', $organizerId);
        })->where('status', 'approved')->count();

        /* =========================
           PERFORMANCE BY GAME
        ========================= */
        $byGame = Tournament::where('organizer_id', $organizerId)
            ->select(
                'game',
                DB::raw('COUNT(*) as tournaments'),
                DB::raw('SUM(filled_slots) as players'),
                DB::raw('SUM(entry_fee * filled_slots) as revenue')
            )
            ->groupBy('game')
            ->get();

        /* =========================
           TOP TOURNAMENTS
        ========================= */
        $topTournaments = Tournament::where('organizer_id', $organizerId)
            ->orderByDesc('filled_slots')
            ->limit(5)
            ->get();

        return view('analytics.index', compact(
            'totalTournaments',
            'totalPlayers',
            'totalRevenue',
            'approvedRate',
            'byGame',
            'topTournaments'
        ));
    }
}
