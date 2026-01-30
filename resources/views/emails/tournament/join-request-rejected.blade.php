@component('mail::message')
# Request Status Update

Hi {{ $user->name }},

We wanted to update you on your request to join "**{{ $tournament->name }}**".

Unfortunately, the organizer has declined your request at this time.

**Reason:**
{{ $reason }}

Don't worry, there are plenty of other tournaments happening on {{ config('app.name') }}!

@component('mail::button', ['url' => $url])
Browse Other Tournaments
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@slot('subcopy')
If you're having trouble clicking the "Browse Other Tournaments" button, copy and paste the URL below into your web browser: [{{ $url }}]({{ $url }})
@endslot
@endcomponent
