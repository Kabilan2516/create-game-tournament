<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Already verified → go to dashboard
        if ($user->hasVerifiedEmail()) {
            return redirect()
                ->route('dashboard')
                ->with('success', '✅ Your email is already verified.');
        }

        // Send verification email
        $user->sendEmailVerificationNotification();

        // Redirect to friendly page
        return redirect()
            ->route('verification.notice')
            ->with('success', '📩 Verification link sent! Please check your email.');
    }
}
