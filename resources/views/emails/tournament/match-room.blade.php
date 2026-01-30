@component('mail::message')
# Match Room Details

Hi {{ $user->name }},

Your match in "**{{ $tournament->name }}**" is ready!

Here are your room details:

@component('mail::panel')
**Room ID:** {{ $room->id }}
**Password:** {{ $room->password }}
@endcomponent

Please join the lobby immediately.

@component('mail::button', ['url' => $url])
Go to Match Page
@endcomponent

Good luck!

Thanks,<br>
{{ config('app.name') }}

@slot('subcopy')
If you're having trouble clicking the "Go to Match Page" button, copy and paste the URL below into your web browser: [{{ $url }}]({{ $url }})
@endslot
@endcomponent
