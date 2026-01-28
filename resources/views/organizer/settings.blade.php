@extends('layouts.dashboard')

@section('title', 'Settings – GameConnect')

@section('dashboard-content')

    <!-- HEADER -->
    <div class="bg-slate-900 border-b border-slate-800 px-8 py-6">
        <h1 class="text-3xl font-bold">⚙️ Account & Organizer Settings</h1>
        <p class="text-gray-400">Manage your account, security, notifications & tournament preferences</p>
    </div>

    <!-- 🔹 SETTINGS GRID -->
    <section class="px-8 py-12 grid md:grid-cols-3 gap-12" x-data="{ tab: 'profile' }">

        <!-- 🔹 LEFT MENU (REAL TABS NOW) -->
        <div class="space-y-4">

            <button @click="tab='profile'"
                :class="tab === 'profile' ? 'bg-slate-800 text-cyan-400' : 'bg-slate-900 hover:bg-slate-800'"
                class="w-full text-left px-6 py-4 rounded-xl">
                👤 Profile Settings
            </button>

            <button @click="tab='security'"
                :class="tab === 'security' ? 'bg-slate-800 text-cyan-400' : 'bg-slate-900 hover:bg-slate-800'"
                class="w-full text-left px-6 py-4 rounded-xl">
                🔐 Security
            </button>

            <button @click="tab='notifications'"
                :class="tab === 'notifications' ? 'bg-slate-800 text-cyan-400' : 'bg-slate-900 hover:bg-slate-800'"
                class="w-full text-left px-6 py-4 rounded-xl">
                🔔 Notifications
            </button>

            <button @click="tab='preferences'"
                :class="tab === 'preferences' ? 'bg-slate-800 text-cyan-400' : 'bg-slate-900 hover:bg-slate-800'"
                class="w-full text-left px-6 py-4 rounded-xl">
                🎮 Tournament Preferences
            </button>

            <button @click="tab='payment'"
                :class="tab === 'payment' ? 'bg-slate-800 text-cyan-400' : 'bg-slate-900 hover:bg-slate-800'"
                class="w-full text-left px-6 py-4 rounded-xl">
                💳 Payment & Payout
            </button>

            <button @click="tab='privacy'"
                :class="tab === 'privacy' ? 'bg-slate-800 text-cyan-400' : 'bg-slate-900 hover:bg-slate-800'"
                class="w-full text-left px-6 py-4 rounded-xl">
                🌐 Privacy
            </button>
            <button @click="tab='social'"
                :class="tab === 'social' ? 'bg-slate-800 text-cyan-400' : 'bg-slate-900 hover:bg-slate-800'"
                class="w-full text-left px-6 py-4 rounded-xl">
                🌐 Social & Creator Links
            </button>

        </div>

        <!-- 🔹 RIGHT CONTENT -->
        <div class="md:col-span-2 space-y-12">

            <!-- ================= PROFILE SETTINGS ================= -->
            <div x-show="tab==='profile'" x-transition class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                <h2 class="text-2xl font-bold mb-6">👤 Profile Settings</h2>

                <div class="grid md:grid-cols-2 gap-6">
                    <input type="text" placeholder="Organizer Name"
                        class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
                    <input type="email" placeholder="Email Address"
                        class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
                    <input type="text" placeholder="Phone Number"
                        class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
                    <input type="text" placeholder="Organization Name"
                        class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
                    <input type="url" placeholder="Website / Discord Link"
                        class="md:col-span-2 px-4 py-3 rounded bg-slate-800 border border-slate-700">
                </div>

                <div class="mt-6">
                    <button class="px-8 py-3 rounded-xl font-bold bg-gradient-to-r from-cyan-500 to-purple-600">
                        Save Profile
                    </button>
                </div>
            </div>

            <!-- ================= SECURITY ================= -->
            <div x-show="tab==='security'" x-transition class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                <h2 class="text-2xl font-bold mb-6">🔐 Security</h2>

                <div class="grid md:grid-cols-2 gap-6">
                    <input type="password" placeholder="Current Password"
                        class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
                    <input type="password" placeholder="New Password"
                        class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
                    <input type="password" placeholder="Confirm New Password"
                        class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
                </div>

                <div class="mt-6 flex items-center space-x-6">
                    <button class="px-8 py-3 rounded-xl font-bold bg-gradient-to-r from-cyan-500 to-purple-600">
                        Update Password
                    </button>

                    <label class="flex items-center space-x-3">
                        <input type="checkbox">
                        <span>Enable Two-Factor Authentication (2FA)</span>
                    </label>
                </div>
            </div>

            <!-- ================= NOTIFICATIONS ================= -->
            <div x-show="tab==='notifications'" x-transition class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                <h2 class="text-2xl font-bold mb-6">🔔 Notification Preferences</h2>

                <div class="space-y-4">
                    <label class="flex items-center justify-between">
                        <span>Email notifications for new join requests</span>
                        <input type="checkbox" checked>
                    </label>

                    <label class="flex items-center justify-between">
                        <span>SMS alerts for urgent updates</span>
                        <input type="checkbox">
                    </label>

                    <label class="flex items-center justify-between">
                        <span>Push notifications for tournament status</span>
                        <input type="checkbox" checked>
                    </label>

                    <label class="flex items-center justify-between">
                        <span>Weekly performance summary</span>
                        <input type="checkbox" checked>
                    </label>
                </div>
            </div>

            <!-- ================= TOURNAMENT PREFS ================= -->
            <div x-show="tab==='preferences'" x-transition class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                <h2 class="text-2xl font-bold mb-6">🎮 Tournament Preferences</h2>

                <div class="grid md:grid-cols-2 gap-6">
                    <select class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
                        <option>PUBG</option>
                        <option>CODM</option>
                        <option>Free Fire</option>
                    </select>

                    <select class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
                        <option>Squad</option>
                        <option>Solo</option>
                        <option>Duo</option>
                    </select>

                    <select class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
                        <option>India</option>
                        <option>Asia</option>
                        <option>Global</option>
                    </select>

                    <select class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
                        <option>Auto Approve: Off</option>
                        <option>Auto Approve: On</option>
                    </select>
                </div>
            </div>

            <!-- ================= PAYMENT ================= -->
            <div x-show="tab==='payment'" x-transition class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                <h2 class="text-2xl font-bold mb-6">💳 Payment & Payout Settings</h2>

                <div class="grid md:grid-cols-2 gap-6">
                    <input type="text" placeholder="Account Holder Name"
                        class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
                    <input type="text" placeholder="Account Number"
                        class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
                    <input type="text" placeholder="IFSC Code"
                        class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
                    <input type="text" placeholder="UPI ID"
                        class="px-4 py-3 rounded bg-slate-800 border border-slate-700">
                </div>
            </div>

            <!-- ================= PRIVACY ================= -->
            <div x-show="tab==='privacy'" x-transition class="bg-slate-900 p-8 rounded-3xl border border-red-700">
                <h2 class="text-2xl font-bold mb-6 text-red-400">🌐 Privacy & Account</h2>

                <div class="space-y-6">
                    <label class="flex items-center justify-between">
                        <span>Show my earnings publicly</span>
                        <input type="checkbox">
                    </label>

                    <label class="flex items-center justify-between">
                        <span>Allow players to contact me</span>
                        <input type="checkbox" checked>
                    </label>

                    <div class="pt-6 border-t border-slate-700">
                        <button class="px-8 py-3 rounded-xl bg-red-600 font-bold">
                            Deactivate Account
                        </button>
                    </div>
                </div>
            </div>
            <!-- ================= SOCIAL LINKS ================= -->
            <div x-show="tab==='social'" x-transition class="bg-slate-900 p-8 rounded-3xl border border-slate-700">
                <h2 class="text-2xl font-bold mb-6">🌐 Social & Creator Links</h2>
                <p class="text-gray-400 mb-8">Show your gaming presence and grow your audience</p>

                <div class="grid md:grid-cols-2 gap-6">

                    <!-- YouTube -->
                    <div class="flex items-center space-x-4 bg-slate-800 p-4 rounded-xl">
                        <span class="text-red-500 text-xl">▶️</span>
                        <input type="url" placeholder="YouTube Channel URL"
                            class="w-full bg-transparent border-none focus:ring-0">
                    </div>

                    <!-- Twitch -->
                    <div class="flex items-center space-x-4 bg-slate-800 p-4 rounded-xl">
                        <span class="text-purple-400 text-xl">🎮</span>
                        <input type="url" placeholder="Twitch Channel URL"
                            class="w-full bg-transparent border-none focus:ring-0">
                    </div>

                    <!-- Instagram -->
                    <div class="flex items-center space-x-4 bg-slate-800 p-4 rounded-xl">
                        <span class="text-pink-400 text-xl">📸</span>
                        <input type="url" placeholder="Instagram Profile"
                            class="w-full bg-transparent border-none focus:ring-0">
                    </div>

                    <!-- Twitter / X -->
                    <div class="flex items-center space-x-4 bg-slate-800 p-4 rounded-xl">
                        <span class="text-blue-400 text-xl">🐦</span>
                        <input type="url" placeholder="X (Twitter) Profile"
                            class="w-full bg-transparent border-none focus:ring-0">
                    </div>

                    <!-- Discord -->
                    <div class="flex items-center space-x-4 bg-slate-800 p-4 rounded-xl">
                        <span class="text-indigo-400 text-xl">💬</span>
                        <input type="url" placeholder="Discord Server Invite"
                            class="w-full bg-transparent border-none focus:ring-0">
                    </div>

                    <!-- Website -->
                    <div class="flex items-center space-x-4 bg-slate-800 p-4 rounded-xl">
                        <span class="text-green-400 text-xl">🔗</span>
                        <input type="url" placeholder="Personal / Team Website"
                            class="w-full bg-transparent border-none focus:ring-0">
                    </div>

                </div>

                <div class="mt-8">
                    <button class="px-10 py-3 rounded-xl font-bold bg-gradient-to-r from-cyan-500 to-purple-600">
                        Save Social Links
                    </button>
                </div>
            </div>


        </div>
    </section>

@endsection
