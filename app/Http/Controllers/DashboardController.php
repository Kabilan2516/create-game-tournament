<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Wallet;
use App\Models\Activity;
use App\Models\Tournament;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    public function index()
    {
        $organizer = Auth::user();

        // 🔹 Tournaments hosted
        $tournamentsCount = Tournament::where('organizer_id', $organizer->id)->count();



        // 🔹 Latest tournaments
        $tournaments = Tournament::where('organizer_id', $organizer->id)
            ->withCount('participants')
            ->orderBy('start_time')
            ->limit(5)
            ->get();



        return view('dashboard', [
            'stats' => [
                'tournaments' => $tournamentsCount,
          

            ],

            'tournaments'   => $tournaments,

        ]);
    }
}
