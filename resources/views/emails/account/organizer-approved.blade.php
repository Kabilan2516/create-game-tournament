@component('mail::message')
# You're Verify as an Organizer!

Hi {{ $user->name }},

Great news! Your application to become a verified tournament organizer on {{ config('app.name') }} has been approved.

You now have access to advanced tournament creation tools, verified badges, and priority support.

@component('mail::button', ['url' => $url])
Go to Organizer Dashboard
@endcomponent

We look forward to seeing the amazing tournaments you'll host!

Thanks,<br>
{{ config('app.name') }}

@slot('subcopy')
If you're having trouble clicking the "Go to Organizer Dashboard" button, copy and paste the URL below into your web browser: [{{ $url }}]({{ $url }})
@endslot
@endcomponent
