@extends('layouts.app')

@section('title', 'Terms & Conditions – ' . config('app.name'))

@section('content')
<section class="max-w-4xl mx-auto px-6 py-16 space-y-6">

    <h1 class="text-3xl font-bold">Terms & Conditions</h1>

    <p class="text-gray-400">
        By accessing {{ config('app.name') }}, you agree to follow these terms.
    </p>

    <h2 class="text-xl font-semibold mt-6">Platform Usage</h2>
    <ul class="list-disc list-inside text-gray-400">
        <li>Users must provide accurate information</li>
        <li>Cheating, abuse, or fraud leads to account suspension</li>
        <li>Organizers are responsible for fair tournament conduct</li>
    </ul>

    <h2 class="text-xl font-semibold mt-6">Tournaments</h2>
    <p class="text-gray-400">
        {{ config('app.name') }} acts as a platform. Match results, prizes, and rules
        are controlled by organizers.
    </p>

    <h2 class="text-xl font-semibold mt-6">Account Termination</h2>
    <p class="text-gray-400">
        We reserve the right to suspend or terminate accounts violating our policies.
    </p>

</section>
@endsection
