@extends('layouts.app')

@section('title', 'Refund Policy – ' . config('app.name'))

@section('content')
<section class="max-w-4xl mx-auto px-6 py-16 space-y-6">

    <h1 class="text-3xl font-bold">Refund & Cancellation Policy</h1>

    <p class="text-gray-400">
        {{ config('app.name') }} provides tournament hosting services.
        Refunds depend on tournament status.
    </p>

    <ul class="list-disc list-inside text-gray-400">
        <li>No refunds after a tournament has started</li>
        <li>Refunds are processed only if an organizer cancels the tournament</li>
        <li>Processing time: 5–7 working days</li>
    </ul>

    <p class="text-gray-400">
        Entry fees are collected for tournament participation only.
    </p>

</section>
@endsection
