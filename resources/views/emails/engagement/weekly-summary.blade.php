@component('mail::message')
# Weekly Organizer Summary

Hi {{ $user->name }},

Here is your weekly summary for your tournaments on {{ config('app.name') }}.

@component('mail::panel')
**This Week's Stats:**
* **Tournaments Hosted:** {{ $stats->tournaments_hosted }}
* **New Participants:** {{ $stats->new_participants }}
* **Matches Completed:** {{ $stats->matches_completed }}
@endcomponent

Keep up the great work! Your community is growing.

@component('mail::button', ['url' => $url])
View Detailed Stats
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@slot('subcopy')
If you're having trouble clicking the "View Detailed Stats" button, copy and paste the URL below into your web browser: [{{ $url }}]({{ $url }})
@endslot
@endcomponent
