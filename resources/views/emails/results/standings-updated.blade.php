@component('mail::message')
# Standings Updated

Hi {{ $user->name }},

The series standings for "**{{ $series->name }}**" have been updated following the recent matches.

@component('mail::panel')
**Your Current Rank:** #{{ $user->rank }}
**Points:** {{ $user->points }}
@endcomponent

Check out the full leaderboard to see where you stand!

@component('mail::button', ['url' => $url])
View Leaderboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@slot('subcopy')
If you're having trouble clicking the "View Leaderboard" button, copy and paste the URL below into your web browser: [{{ $url }}]({{ $url }})
@endslot
@endcomponent
