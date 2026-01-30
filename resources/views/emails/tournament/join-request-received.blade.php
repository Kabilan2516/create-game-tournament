@component('mail::message')
# New Join Request

Hi {{ $user->name }},

A new player/team has requested to join your tournament "**{{ $tournament->name }}**".

**Requester:** {{ $requester->name }}

Please review their request to approve or reject their participation.

@component('mail::button', ['url' => $url])
Review Request
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@slot('subcopy')
If you're having trouble clicking the "Review Request" button, copy and paste the URL below into your web browser: [{{ $url }}]({{ $url }})
@endslot
@endcomponent
