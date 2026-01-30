@component('mail::message')
# Match Results Published

Hi {{ $user->name }},

New match results have been published for "**{{ $tournament->name }}**".

@component('mail::panel')
**Match Details:**
{{ $match->team_a }} vs {{ $match->team_b }}

**Result:**
{{ $match->score_a }} - {{ $match->score_b }}
@endcomponent

@component('mail::button', ['url' => $url])
View Match Details
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@slot('subcopy')
If you're having trouble clicking the "View Match Details" button, copy and paste the URL below into your web browser: [{{ $url }}]({{ $url }})
@endslot
@endcomponent
