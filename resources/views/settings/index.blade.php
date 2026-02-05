@extends('layouts.dashboard')

@section('title', 'Settings – GameConnect')

@section('dashboard-content')

    <!-- HEADER -->
    <div class="bg-slate-900 border-b border-slate-800 px-8 py-6">
        <h1 class="text-3xl font-bold">⚙️ Account & Organizer Settings</h1>
        <p class="text-gray-400">
            Manage your account, security, notifications & organizer profile
        </p>
    </div>

    <section class="px-8 py-12 grid md:grid-cols-3 gap-12" x-data="{ tab: 'profile' }">

        <!-- LEFT MENU -->
        <div class="space-y-4">
            <button @click="tab='profile'"
                :class="tab === 'profile' ? 'bg-slate-800 text-cyan-400' : 'bg-slate-900 hover:bg-slate-800'"
                class="w-full px-6 py-4 rounded-xl text-left">
                👤 Profile Settings
            </button>

            <button @click="tab='security'"
                :class="tab === 'security' ? 'bg-slate-800 text-cyan-400' : 'bg-slate-900 hover:bg-slate-800'"
                class="w-full px-6 py-4 rounded-xl text-left">
                🔐 Security
            </button>

            <button @click="tab='notifications'"
                :class="tab === 'notifications' ? 'bg-slate-800 text-cyan-400' : 'bg-slate-900 hover:bg-slate-800'"
                class="w-full px-6 py-4 rounded-xl text-left">
                🔔 Notifications
            </button>

            <button @click="tab='privacy'"
                :class="tab === 'privacy' ? 'bg-slate-800 text-cyan-400' : 'bg-slate-900 hover:bg-slate-800'"
                class="w-full px-6 py-4 rounded-xl text-left">
                🌐 Privacy
            </button>

            <button @click="tab='social'"
                :class="tab === 'social' ? 'bg-slate-800 text-cyan-400' : 'bg-slate-900 hover:bg-slate-800'"
                class="w-full px-6 py-4 rounded-xl text-left">
                🌐 Social Links
            </button>
        </div>

        <!-- RIGHT CONTENT -->
        <div class="md:col-span-2 space-y-12">

            {{-- ================================================= --}}
            {{-- PROFILE --}}
            {{-- ================================================= --}}
            <form x-show="tab==='profile'" x-transition method="POST" action="{{ route('settings.profile.update') }}"
                enctype="multipart/form-data" x-data="{
                    bannerPreview: '{{ optional($organizer->media->where('collection', 'banner')->last())->url }}',
                    avatarPreview: '{{ optional($organizer->media->where('collection', 'avatar')->last())->url }}'
                }"
                class="bg-slate-900 p-8 rounded-3xl border border-slate-700 space-y-10">
                @csrf

                <h2 class="text-2xl font-bold">👤 Profile Settings</h2>

                <!-- Banner -->
                <div>
                    <label class="text-sm text-gray-400">Profile Banner</label>
                    <div
                        class="relative h-72 md:h-80 rounded-2xl overflow-hidden
            bg-slate-800 border border-dashed border-slate-600
            flex items-center justify-center">
                        <template x-if="bannerPreview">
                            <img :src="bannerPreview" class="absolute inset-0 w-full h-full object-cover">
                        </template>
                        <label class="cursor-pointer px-4 py-2 bg-black/60 rounded">
                            Upload Banner
                            <input type="file" name="banner" hidden
                                @change="bannerPreview = URL.createObjectURL($event.target.files[0])">
                        </label>
                    </div>
                </div>

                <!-- Avatar -->
                <div class="flex items-center gap-6">
                    <div
                        class="relative w-28 h-28 rounded-full overflow-hidden bg-slate-800 border border-dashed border-slate-600 flex items-center justify-center">
                        <template x-if="avatarPreview">
                            <img :src="avatarPreview" class="absolute inset-0 w-full h-full object-cover">
                        </template>
                        <label class="cursor-pointer text-xs text-center">
                            Upload Avatar
                            <input type="file" name="avatar" hidden
                                @change="avatarPreview = URL.createObjectURL($event.target.files[0])">
                        </label>
                    </div>
                    <p class="text-sm text-gray-400">Used across tournaments & leaderboards</p>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <input name="display_name" value="{{ $organizer->display_name ?? $user->name }}" class="input"
                        placeholder="Display Name">
                    <input name="email" value="{{ $user->email }}" class="input" placeholder="Email">
                    <input name="contact_number" value="{{ $organizer->contact_number }}" class="input"
                        placeholder="Phone">
                    <input name="website" value="{{ $organizer->discord_link }}" class="input"
                        placeholder="Website / Discord">
                </div>

                <textarea name="bio" rows="4" class="input" placeholder="Organizer bio">{{ $organizer->bio }}</textarea>

                <button class="btn-primary">Save Profile</button>
            </form>

            {{-- ================================================= --}}
            {{-- SECURITY --}}
            {{-- ================================================= --}}
            <form x-show="tab==='security'" x-transition method="POST" action="{{ route('settings.security.update') }}"
                class="bg-slate-900 p-8 rounded-3xl border border-slate-700 space-y-6">
                @csrf

                <h2 class="text-2xl font-bold">🔐 Security</h2>

                <input type="password" name="current_password" class="input" placeholder="Current Password">
                <input type="password" name="password" class="input" placeholder="New Password">
                <input type="password" name="password_confirmation" class="input" placeholder="Confirm Password">

                <button class="btn-primary">Update Password</button>
            </form>

            {{-- ================================================= --}}
            {{-- NOTIFICATIONS --}}
            {{-- ================================================= --}}
            <form x-show="tab==='notifications'" x-transition method="POST"
                action="{{ route('settings.notifications.update') }}"
                class="bg-slate-900 p-8 rounded-3xl border border-slate-700 space-y-6">

                @csrf

                <h2 class="text-2xl font-bold">🔔 Notification Preferences</h2>

                <!-- Email Notifications -->
                <label class="flex items-center justify-between gap-4">
                    <div>
                        <p class="font-semibold">Email notifications</p>
                        <p class="text-sm text-gray-400">
                            Receive emails for join requests & important updates
                        </p>
                    </div>
                    <input type="checkbox" name="email_notifications" value="1"
                        {{ $organizer->email_notifications ? 'checked' : '' }} class="h-5 w-5 accent-cyan-500">
                </label>

                <!-- SMS Notifications -->
                <label class="flex items-center justify-between gap-4">
                    <div>
                        <p class="font-semibold">SMS alerts</p>
                        <p class="text-sm text-gray-400">
                            Get SMS for urgent tournament actions
                        </p>
                    </div>
                    <input type="checkbox" name="sms_notifications" value="1"
                        {{ $organizer->sms_notifications ? 'checked' : '' }} class="h-5 w-5 accent-cyan-500">
                </label>

                <!-- Push Notifications -->
                <label class="flex items-center justify-between gap-4">
                    <div>
                        <p class="font-semibold">Push notifications</p>
                        <p class="text-sm text-gray-400">
                            Browser & device notifications
                        </p>
                    </div>
                    <input type="checkbox" name="push_notifications" value="1"
                        {{ $organizer->push_notifications ? 'checked' : '' }} class="h-5 w-5 accent-cyan-500">
                </label>

                <!-- Weekly Summary -->
                <label class="flex items-center justify-between gap-4">
                    <div>
                        <p class="font-semibold">Weekly summary</p>
                        <p class="text-sm text-gray-400">
                            Performance & earnings summary every week
                        </p>
                    </div>
                    <input type="checkbox" name="weekly_summary" value="1"
                        {{ $organizer->weekly_summary ? 'checked' : '' }} class="h-5 w-5 accent-cyan-500">
                </label>

                <!-- SAVE -->
                <div class="pt-4">
                    <button type="submit"
                        class="px-10 py-3 rounded-xl font-bold
                       bg-gradient-to-r from-cyan-500 to-purple-600">
                        Save Notification Settings
                    </button>
                </div>
            </form>


            {{-- ================================================= --}}
            {{-- PRIVACY --}}
            {{-- ================================================= --}}
            <form x-show="tab==='privacy'" x-transition method="POST" action="{{ route('settings.privacy.update') }}"
                class="bg-slate-900 p-8 rounded-3xl border border-red-700 space-y-6">

                @csrf

                <h2 class="text-2xl font-bold text-red-400">🌐 Privacy & Visibility</h2>

                <!-- Show Earnings -->
                <label class="flex items-center justify-between gap-4">
                    <div>
                        <p class="font-semibold">Show earnings publicly</p>
                        <p class="text-sm text-gray-400">
                            Display your total winnings on public profile & leaderboards
                        </p>
                    </div>
                    <input type="checkbox" name="show_earnings" value="1"
                        {{ $organizer->show_earnings ? 'checked' : '' }} class="h-5 w-5 accent-red-500">
                </label>

                <!-- Allow Contact -->
                <label class="flex items-center justify-between gap-4">
                    <div>
                        <p class="font-semibold">Allow players to contact you</p>
                        <p class="text-sm text-gray-400">
                            Let players reach you via email / Discord
                        </p>
                    </div>
                    <input type="checkbox" name="allow_player_contact" value="1"
                        {{ $organizer->allow_player_contact ? 'checked' : '' }} class="h-5 w-5 accent-red-500">
                </label>

                <!-- SAVE -->
                <div class="pt-4">
                    <button type="submit" class="px-10 py-3 rounded-xl font-bold bg-red-600 hover:bg-red-700">
                        Save Privacy Settings
                    </button>
                </div>
            </form>


            {{-- ================================================= --}}
            {{-- SOCIAL LINKS --}}
            {{-- ================================================= --}}
            <form x-show="tab==='social'" x-transition method="POST" action="{{ route('settings.social.update') }}"
                class="bg-slate-900 p-8 rounded-3xl border border-slate-700 space-y-6">

                @csrf

                <h2 class="text-2xl font-bold">🌐 Social & Creator Links</h2>
                <p class="text-gray-400 text-sm">
                    These links appear on your public organizer profile
                </p>

                @php
                    $social = $organizer->social_links ?? [];
                @endphp

                <!-- YouTube -->
                <input name="social_links[youtube]" value="{{ old('social_links.youtube', $social['youtube'] ?? '') }}"
                    class="input" placeholder="YouTube Channel URL">

                <!-- Twitch -->
                <input name="social_links[twitch]" value="{{ old('social_links.twitch', $social['twitch'] ?? '') }}"
                    class="input" placeholder="Twitch Channel URL">

                <!-- Instagram -->
                <input name="social_links[instagram]"
                    value="{{ old('social_links.instagram', $social['instagram'] ?? '') }}" class="input"
                    placeholder="Instagram Profile URL">

                <!-- Twitter / X -->
                <input name="social_links[twitter]" value="{{ old('social_links.twitter', $social['twitter'] ?? '') }}"
                    class="input" placeholder="X (Twitter) Profile URL">

                <!-- Discord -->
                <input name="social_links[discord]" value="{{ old('social_links.discord', $social['discord'] ?? '') }}"
                    class="input" placeholder="Discord Server / Profile Link">

                <!-- Website -->
                <input name="social_links[website]" value="{{ old('social_links.website', $social['website'] ?? '') }}"
                    class="input" placeholder="Website / Linktree">

                <!-- SAVE -->
                <div class="pt-4">
                    <button type="submit" class="btn-primary">
                        Save Social Links
                    </button>
                </div>

            </form>


        </div>
    </section>

    <style>
        .input {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            background-color: rgb(30 41 59);
            border: 1px solid rgb(51 65 85);
        }

        .btn-primary {
            padding: 0.75rem 2rem;
            border-radius: 0.75rem;
            font-weight: 700;
            background: linear-gradient(to right, rgb(6 182 212), rgb(168 85 247));
        }
    </style>

@endsection
