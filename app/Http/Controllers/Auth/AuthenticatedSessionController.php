<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();

        if ($user->role === 'organizer') {
            return redirect()->intended(route('dashboard'))
                ->with('auth.login.success', '✅ Login successful. Welcome back!');
        }

        if ($user->role === 'player') {
            return redirect()->intended(route('player.dashboard'))
                ->with('auth.login.success', '✅ Login successful. Welcome back!');
        }

        // Fallback (safety)
        return redirect('/')
            ->with('auth.login.success', '✅ Login successful.');
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();


        return redirect('/')->with('auth.logout.success', '👋 You have been logged out successfully.');
    }
}
