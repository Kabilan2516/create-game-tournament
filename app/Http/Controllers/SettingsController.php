<?php

namespace App\Http\Controllers;

use App\Models\Organizer;
use App\Services\MediaUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    /* =========================
        SETTINGS PAGE
    ========================= */
    public function index()
    {
        $user = Auth::user();

        // 🔥 IMPORTANT FIX
        $organizer = Organizer::firstOrCreate(
            ['user_id' => $user->id],
            [
                'organization_name' => null,
                'contact_number'    => null,
                'discord_link'      => null,
                'verified'          => false,
            ]
        );

        return view('settings.index', compact('user', 'organizer'));
    }

    /* =========================
        PROFILE UPDATE
    ========================= */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $organizer = Organizer::firstOrCreate(
            ['user_id' => $user->id]
        );

        $request->validate([
            'display_name'   => 'required|string|max:255',
            'email'          => 'required|email',
            'contact_number' => 'nullable|string|max:20',
            'website'        => 'nullable|string|max:255',
            'bio'            => 'nullable|string|max:1000',
            'banner'         => 'nullable|image|max:2048',
            'avatar'         => 'nullable|image|max:2048',
        ]);

        // Update user
        $user->update([
            'name'  => $request->display_name,
            'email' => $request->email,
        ]);

        // Update organizer
        $organizer->update([
            'contact_number' => $request->contact_number,
            'discord_link'   => $request->website,
            'bio'            => $request->bio,
        ]);

        // Upload banner
        if ($request->hasFile('banner')) {
            MediaUploadService::upload(
                $request->file('banner'),
                $organizer,
                'banner',
                'organizers/banners'
            );
        }

        // Upload avatar
        if ($request->hasFile('avatar')) {
            MediaUploadService::upload(
                $request->file('avatar'),
                $organizer,
                'avatar',
                'organizers/avatars'
            );
        }

        return back()->with('success', '✅ Profile updated successfully');
    }

    /* =========================
        SECURITY
    ========================= */
    public function updateSecurity(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Incorrect password']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', '🔐 Password updated');
    }

    /* =========================
       NOTIFICATIONS
    ========================= */
    public function updateNotifications(Request $request)
    {
        $organizer = Organizer::firstOrCreate(['user_id' => Auth::id()]);

        $organizer->update([
            'email_notifications' => $request->boolean('email_notifications'),
            'sms_notifications' => $request->boolean('sms_notifications'),
            'push_notifications' => $request->boolean('push_notifications'),
            'weekly_summary' => $request->boolean('weekly_summary'),
        ]);

        return back()->with('success', '🔔 Notification settings saved');
    }
    /* =========================
       PRIVACY
    ========================= */
    public function updatePrivacy(Request $request)
    {
        $organizer = Organizer::firstOrCreate(['user_id' => Auth::id()]);

        $organizer->update([
            'show_earnings' => $request->boolean('show_earnings'),
            'allow_player_contact' => $request->boolean('allow_player_contact'),
        ]);

        return back()->with('success', '🌐 Privacy settings updated');
    }



    /* =========================
       SOCIAL LINKS
    ========================= */
    public function updateSocial(Request $request)
    {
        $request->validate([
            'social_links' => 'nullable|array',
            'social_links.*' => 'nullable|url',
        ]);

        $organizer = Organizer::firstOrCreate([
            'user_id' => Auth::id(),
        ]);

        $organizer->update([
            'social_links' => array_filter($request->social_links ?? []),
        ]);

        return back()->with('success', '🌐 Social links updated successfully');
    }




    /* =========================
        DEACTIVATE
    ========================= */
    public function deactivate()
    {
        Auth::user()->delete();
        Auth::logout();

        return redirect('/')->with('success', 'Account deactivated');
    }
}
