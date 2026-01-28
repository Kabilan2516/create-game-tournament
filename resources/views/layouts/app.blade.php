<!DOCTYPE html>
<html lang="en">

<head>
    <!-- BASIC -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @php
        $appName = config('app.name', 'GameConnect');
        $pageTitle = trim($__env->yieldContent('title')) ?: $appName . ' – Esports Hub';
        $metaDescription =
            trim($__env->yieldContent('meta_description')) ?:
            'Join CODM & PUBG tournaments, host scrims, manage teams and win prizes on ' .
                $appName .
                ' – India’s esports tournament platform.';
        $ogTitle = trim($__env->yieldContent('og_title')) ?: $pageTitle;
        $ogDescription = trim($__env->yieldContent('og_description')) ?: $metaDescription;
        $ogImage = trim($__env->yieldContent('og_image')) ?: asset('build/images/og-default.png');
    @endphp

    <title>{{ $pageTitle }}</title>

    <!-- PRIMARY SEO -->
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="keywords"
        content="CODM tournaments, PUBG tournaments, esports India, gaming rooms, online scrims, battle royale tournaments">
    <meta name="author" content="{{ $appName }}">
    <meta name="robots" content="index, follow">

    <!-- CANONICAL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- OPEN GRAPH -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ $appName }}">
    <meta property="og:image" content="{{ $ogImage }}">

    <!-- TWITTER CARD -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ $ogDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <!-- CSRF -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- FAVICON -->
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <!-- PERFORMANCE -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- TAILWIND + APP -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- ALPINE JS -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- EXTRA HEAD STACK -->
    @stack('head')

    <!-- CUSTOM ANIMATIONS + EFFECTS -->
    <style>
        /* Neon Glow Effects */
        .glow-cyan {
            box-shadow: 0 0 20px rgba(34, 211, 238, 0.6);
        }

        .glow-purple {
            box-shadow: 0 0 20px rgba(168, 85, 247, 0.6);
        }

        /* Fade + Slide Animations */
        .fade-up {
            animation: fadeUp 1s ease forwards;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Floating animation */
        .float {
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        @keyframes fadeInOut {
            0% {
                opacity: 0;
                transform: translateY(-10px);
            }

            10% {
                opacity: 1;
                transform: translateY(0);
            }

            90% {
                opacity: 1;
            }

            100% {
                opacity: 0;
                transform: translateY(-10px);
            }
        }

        .animate-fade-in {
            animation: fadeInOut 4s ease forwards;
        }
    </style>
</head>

<body class="bg-slate-950 text-gray-100">
    <x-pwa-install-prompt />
    @include('partials.navbar')
    @include('partials.flash-messages')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')



    <!-- COUNTDOWN SCRIPT (SAFE) -->
    <script>
        function startCountdowns() {
            document.querySelectorAll('[data-start]').forEach(el => {
                const start = new Date(el.getAttribute('data-start')).getTime();

                function update() {
                    const now = new Date().getTime();
                    const diff = start - now;

                    if (diff <= 0) {
                        el.innerHTML = 'Started';
                        return;
                    }

                    const h = Math.floor(diff / (1000 * 60 * 60));
                    const m = Math.floor((diff / (1000 * 60)) % 60);
                    const s = Math.floor((diff / 1000) % 60);

                    el.innerHTML = `${h}h ${m}m ${s}s`;
                }

                update();
                setInterval(update, 1000);
            });
        }

        document.addEventListener('DOMContentLoaded', startCountdowns);
    </script>

    <!-- SWIPER INIT (ONLY IF PRESENT) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Swiper !== 'undefined' && document.querySelector('.featuredSwiper')) {
                new Swiper('.featuredSwiper', {
                    slidesPerView: 3,
                    spaceBetween: 30,
                    loop: true,
                    autoplay: {
                        delay: 3000
                    },
                    breakpoints: {
                        0: {
                            slidesPerView: 1
                        },
                        768: {
                            slidesPerView: 2
                        },
                        1024: {
                            slidesPerView: 3
                        },
                    }
                });
            }
        });
    </script>

    @stack('scripts')

</body>

</html>
