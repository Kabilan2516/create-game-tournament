@component('mail::message')
# Tournament Created Successfully!

Hi {{ $user->name }},

Your tournament "**{{ $tournament->name }}**" has been successfully created on {{ config('app.name') }}.

@component('mail::panel')
**Tournament Details:**
* **Game:** {{ $tournament->game }}
* **Start Date:** {{ $tournament->start_date }}
* **Format:** {{ $tournament->format }}
@endcomponent

Players can now start joining your tournament. Good luck hosting!

@component('mail::button', ['url' => $url])
Manage Tournament
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@slot('subcopy')
If you're having trouble clicking the "Manage Tournament" button, copy and paste the URL below into your web browser: [{{ $url }}]({{ $url }})
@endslot
@endcomponent
