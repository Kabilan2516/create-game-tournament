@component('mail::message')
# Tournament Reminder

Hi {{ $user->name }},

This is a reminder that "**{{ $tournament->name }}**" is starting in **{{ $time_remaining }}**.

**Check-in Required:** {{ $tournament->check_in ? 'Yes' : 'No' }}

Please make sure you are ready and have checked in if required. Failure to check in may result in disqualification.

@component('mail::button', ['url' => $url])
Go to Tournament Lobby
@endcomponent

Good luck!

Thanks,<br>
{{ config('app.name') }}

@slot('subcopy')
If you're having trouble clicking the "Go to Tournament Lobby" button, copy and paste the URL below into your web browser: [{{ $url }}]({{ $url }})
@endslot
@endcomponent
