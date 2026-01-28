@extends('layouts.app')

@section('title', 'Blog – GameConnect')

@section('content')
    <section class="bg-slate-950 py-24">
        <div class="max-w-7xl mx-auto px-6">

            <!-- HEADER -->
            <div class="text-center mb-16 max-w-3xl mx-auto">

                <h1 class="text-4xl md:text-5xl font-extrabold mb-6 leading-tight">
                    Esports & Gaming Blog for
                    <span class="text-cyan-400">CODM</span>,
                    <span class="text-purple-400">PUBG</span> & Tournament Players
                </h1>

                <p class="text-gray-300 text-lg leading-relaxed mb-10">
                    Explore expert gaming guides, esports tournament strategies, room setup tips,
                    and competitive insights curated from
                    <span class="text-white font-semibold">GameSnag</span>.
                    Perfect for players, organizers, and esports communities using
                    <span class="text-white font-semibold">GameConnect</span>.
                </p>

            </div>

            <!-- BLOG GRID -->
            <div class="grid md:grid-cols-3 gap-10">

                @forelse($posts as $post)
                    @php
                        $image = $post['_embedded']['wp:featuredmedia'][0]['source_url'] ?? null;
                        $author = $post['_embedded']['author'][0]['name'] ?? 'GameSnag';
                        $date = \Carbon\Carbon::parse($post['date'])->format('M d, Y');
                    @endphp

                    <article
                        class="group bg-slate-900 rounded-3xl overflow-hidden border border-slate-800 hover:border-cyan-400 transition">

                        <!-- IMAGE -->
                        @if ($image)
                            <img src="{{ $image }}" alt="{{ $post['title']['rendered'] }}"
                                class="h-56 w-full object-cover group-hover:scale-105 transition duration-500">
                        @endif

                        <!-- CONTENT -->
                        <div class="p-6">

                            <!-- META -->
                            <div class="text-xs text-gray-400 mb-3 flex justify-between">
                                <span>{{ $date }}</span>
                                <span>By {{ $author }}</span>
                            </div>

                            <!-- TITLE -->
                            <h2 class="text-xl font-bold mb-3 group-hover:text-cyan-400 transition">
                                {!! $post['title']['rendered'] !!}
                            </h2>

                            <!-- EXCERPT -->
                            <p class="text-gray-400 text-sm line-clamp-3 mb-6">
                                {!! strip_tags($post['excerpt']['rendered']) !!}
                            </p>

                            <!-- READ MORE -->
                            <a href="{{ $post['link'] }}" target="_blank"
                                class="inline-flex items-center text-cyan-400 font-semibold hover:text-purple-400 transition">
                                Read more →
                            </a>

                        </div>
                    </article>

                @empty
                    <p class="text-gray-400">No blog posts available.</p>
                @endforelse

            </div>
        </div>
    </section>
@endsection
