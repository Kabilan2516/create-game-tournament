@component('mail::message')
# Update on Your Verification Request

Hi {{ $user->name }},

Thank you for your interest in becoming a verified organizer on {{ config('app.name') }}.

After reviewing your application, we are unable to verify your account at this time.

**Reason:**
{{ $reason }}

You can re-apply once the issues mentioned above have been addressed. If you believe this is a mistake, please contact our support team.

@component('mail::button', ['url' => $url])
Review Application Status
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@slot('subcopy')
If you're having trouble clicking the "Review Application Status" button, copy and paste the URL below into your web browser: [{{ $url }}]({{ $url }})
@endslot
@endcomponent
