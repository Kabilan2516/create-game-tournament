@extends('layouts.app')

@section('title', 'Privacy Policy – ' . config('app.name'))

@section('content')
<section class="max-w-4xl mx-auto px-6 py-16 space-y-6">

    <h1 class="text-3xl font-bold">Privacy Policy</h1>

    <p class="text-gray-400">
        {{ config('app.name') }} values your privacy. This policy explains how we collect,
        use, and protect your personal information while using our esports platform.
    </p>

    <h2 class="text-xl font-semibold mt-6">Information We Collect</h2>
    <ul class="list-disc list-inside text-gray-400">
        <li>Name, email address, phone number</li>
        <li>Game-related details for tournament participation</li>
        <li>Payment information (processed securely via third-party providers)</li>
    </ul>

    <h2 class="text-xl font-semibold mt-6">How We Use Information</h2>
    <ul class="list-disc list-inside text-gray-400">
        <li>To manage tournaments and series</li>
        <li>To communicate match updates and results</li>
        <li>To improve platform security and experience</li>
    </ul>

    <h2 class="text-xl font-semibold mt-6">Data Protection</h2>
    <p class="text-gray-400">
        We use industry-standard security practices. We never sell user data.
    </p>

    <p class="text-gray-400 mt-8">
        By using {{ config('app.name') }}, you agree to this policy.
    </p>

</section>
@endsection
