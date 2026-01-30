@component('mail::message')
# 🏆 We Have a Winner!

Hi {{ $user->name }},

The "**{{ $series->name }}**" series has officially concluded.

**Congratulations to the Champion: {{ $winner->name }}!**

Thank you to everyone who participated and made this series an exciting competition.

@component('mail::button', ['url' => $url])
View Full Results
@endcomponent

Stay tuned for upcoming tournaments and series!

Thanks,<br>
{{ config('app.name') }}

@slot('subcopy')
If you're having trouble clicking the "View Full Results" button, copy and paste the URL below into your web browser: [{{ $url }}]({{ $url }})
@endslot
@endcomponent
