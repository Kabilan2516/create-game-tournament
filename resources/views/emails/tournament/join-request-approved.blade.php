@component('mail::message')
# Request Approved!

Hi {{ $user->name }},

Great news! Your request to join the tournament "**{{ $tournament->name }}**" has been approved by the organizer.

You are now officially a participant in this tournament. Get ready to compete!

@component('mail::panel')
**Tournament Details:**
* **Start Date:** {{ $tournament->start_date }}
* **Check-in Time:** {{ $tournament->check_in_time }}
@endcomponent

@component('mail::button', ['url' => $url])
Go to Tournament Lobby
@endcomponent

Good luck and have fun!

Thanks,<br>
{{ config('app.name') }}

@slot('subcopy')
If you're having trouble clicking the "Go to Tournament Lobby" button, copy and paste the URL below into your web browser: [{{ $url }}]({{ $url }})
@endslot
@endcomponent
