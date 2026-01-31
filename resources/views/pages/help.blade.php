@extends('layouts.app')

@section('title', 'Help Center – ' . config('app.name'))

@section('content')

<section class="max-w-5xl mx-auto px-6 py-16">

    <!-- HEADER -->
    <div class="text-center mb-14">
        <h1 class="text-4xl font-extrabold mb-3">❓ Help Center</h1>
        <p class="text-gray-400">
            Everything you need to know about tournaments, payments, and accounts on {{ config('app.name') }}.
        </p>
    </div>

    <!-- FAQ GRID -->
    <div x-data="{ open: null }" class="space-y-6">

        {{-- ================= TOURNAMENTS ================= --}}
        <div class="bg-slate-900 rounded-2xl border border-slate-700 p-6">
            <h2 class="text-2xl font-bold mb-6">🏆 Tournaments</h2>

            @php
                $tournamentFaqs = [
                    [
                        'q' => 'How do I join a tournament?',
                        'a' => 'Browse available tournaments, open one, fill the join form, and submit. You will receive a join code after submission.'
                    ],
                    [
                        'q' => 'Can I join without an account?',
                        'a' => 'Yes. Some tournaments allow guest joins using email and phone number.'
                    ],
                    [
                        'q' => 'What games are supported?',
                        'a' => 'Currently CODM, PUBG, Free Fire, and more esports titles are supported.'
                    ],
                    [
                        'q' => 'What happens if slots are full?',
                        'a' => 'Once slots are filled, the tournament is closed for new entries.'
                    ],
                ];
            @endphp

            @foreach ($tournamentFaqs as $i => $faq)
                <div class="border-t border-slate-700 pt-4">
                    <button
                        @click="open === 't{{ $i }}' ? open = null : open = 't{{ $i }}'"
                        class="w-full flex justify-between items-center text-left font-semibold"
                    >
                        {{ $faq['q'] }}
                        <span x-text="open === 't{{ $i }}' ? '−' : '+'"></span>
                    </button>

                    <div x-show="open === 't{{ $i }}'" x-transition class="mt-3 text-gray-400">
                        {{ $faq['a'] }}
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ================= PAYMENTS ================= --}}
        <div class="bg-slate-900 rounded-2xl border border-slate-700 p-6">
            <h2 class="text-2xl font-bold mb-6">💰 Payments & Fees</h2>

            @php
                $paymentFaqs = [
                    [
                        'q' => 'Is payment mandatory to join tournaments?',
                        'a' => 'No. Many tournaments are free. Paid tournaments will clearly display the entry fee.'
                    ],
                    [
                        'q' => 'How do I pay the entry fee?',
                        'a' => 'Payments are done via UPI or methods specified by the organizer.'
                    ],
                    [
                        'q' => 'Will I get a refund?',
                        'a' => 'Refunds are only processed if a tournament is cancelled by the organizer.'
                    ],
                    [
                        'q' => 'How long do refunds take?',
                        'a' => 'Refunds are usually processed within 5–7 working days.'
                    ],
                ];
            @endphp

            @foreach ($paymentFaqs as $i => $faq)
                <div class="border-t border-slate-700 pt-4">
                    <button
                        @click="open === 'p{{ $i }}' ? open = null : open = 'p{{ $i }}'"
                        class="w-full flex justify-between items-center text-left font-semibold"
                    >
                        {{ $faq['q'] }}
                        <span x-text="open === 'p{{ $i }}' ? '−' : '+'"></span>
                    </button>

                    <div x-show="open === 'p{{ $i }}'" x-transition class="mt-3 text-gray-400">
                        {{ $faq['a'] }}
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ================= ORGANIZERS ================= --}}
        <div class="bg-slate-900 rounded-2xl border border-slate-700 p-6">
            <h2 class="text-2xl font-bold mb-6">🎮 Organizers</h2>

            @php
                $organizerFaqs = [
                    [
                        'q' => 'How do I host a tournament?',
                        'a' => 'Register as an organizer, go to dashboard, and create a tournament.'
                    ],
                    [
                        'q' => 'Can I auto-approve teams?',
                        'a' => 'Yes. Organizers can enable auto-approval while creating tournaments.'
                    ],
                    [
                        'q' => 'Who manages prize payouts?',
                        'a' => 'Organizers are responsible for distributing prizes to winners.'
                    ],
                ];
            @endphp

            @foreach ($organizerFaqs as $i => $faq)
                <div class="border-t border-slate-700 pt-4">
                    <button
                        @click="open === 'o{{ $i }}' ? open = null : open = 'o{{ $i }}'"
                        class="w-full flex justify-between items-center text-left font-semibold"
                    >
                        {{ $faq['q'] }}
                        <span x-text="open === 'o{{ $i }}' ? '−' : '+'"></span>
                    </button>

                    <div x-show="open === 'o{{ $i }}'" x-transition class="mt-3 text-gray-400">
                        {{ $faq['a'] }}
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ================= ACCOUNT ================= --}}
        <div class="bg-slate-900 rounded-2xl border border-slate-700 p-6">
            <h2 class="text-2xl font-bold mb-6">👤 Accounts & Security</h2>

            @php
                $accountFaqs = [
                    [
                        'q' => 'Do I need email verification?',
                        'a' => 'Yes. Email verification is required to secure your account.'
                    ],
                    [
                        'q' => 'What if I forget my password?',
                        'a' => 'Use the "Forgot Password" option on the login page.'
                    ],
                    [
                        'q' => 'Can my account be suspended?',
                        'a' => 'Yes, for cheating, fraud, or policy violations.'
                    ],
                ];
            @endphp

            @foreach ($accountFaqs as $i => $faq)
                <div class="border-t border-slate-700 pt-4">
                    <button
                        @click="open === 'a{{ $i }}' ? open = null : open = 'a{{ $i }}'"
                        class="w-full flex justify-between items-center text-left font-semibold"
                    >
                        {{ $faq['q'] }}
                        <span x-text="open === 'a{{ $i }}' ? '−' : '+'"></span>
                    </button>

                    <div x-show="open === 'a{{ $i }}'" x-transition class="mt-3 text-gray-400">
                        {{ $faq['a'] }}
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    <!-- CONTACT CTA -->
    <div class="mt-16 text-center">
        <p class="text-gray-400 mb-4">Still need help?</p>
        <a href="{{ route('contact') }}"
           class="inline-block px-8 py-3 rounded-xl font-bold
                  bg-gradient-to-r from-cyan-500 to-purple-600">
            📩 Contact Support
        </a>
    </div>

</section>

@endsection
