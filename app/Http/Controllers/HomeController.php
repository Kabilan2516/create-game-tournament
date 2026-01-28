<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use Illuminate\Http\Request;

class HomeController extends Controller
{
public function index()
{
    // First try to get featured tournaments
    $featured = Tournament::with('organizer')
        ->where('is_featured', true)
        ->where('status', 'open')
        ->orderBy('start_time', 'asc')
        ->take(6)
        ->get();

    // 🔹 If no featured tournaments found, fallback to normal open tournaments
    if ($featured->count() === 0) {
        $featured = Tournament::with('organizer')
            ->where('status', 'open')
            ->orderBy('start_time', 'asc')
            ->take(6)
            ->get();
    }

    return view('welcome', compact('featured'));
}

}
