@extends('layouts.app')

@section('title', 'Contact Us – ' . config('app.name'))

@section('content')
<section class="max-w-4xl mx-auto px-6 py-16 space-y-6">

    <h1 class="text-3xl font-bold">Contact Us</h1>

    <p class="text-gray-400">
        Need help? Reach out to our support team.
    </p>

    <div class="bg-slate-900 p-6 rounded-xl border border-slate-700 space-y-3">
        <p>📧 Email: support@{{ strtolower(config('app.name')) }}.com</p>
        <p>📞 Phone: +91 XXXXX XXXXX</p>
        <p>💬 WhatsApp support available</p>
    </div>

</section>
@endsection
