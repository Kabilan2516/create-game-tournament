@component('mail::message')
# Account Status Update

Hi {{ $user->name }},

This email is to inform you that your {{ config('app.name') }} account status has changed.

**New Status:** {{ $status }}

@if($status == 'Deactivated')
Your account has been deactivated. If you believe this is an error, please contact support immediately.
@elseif($status == 'Reactivated')
Your account has been reactivated. You can now log in and access all features.
@endif

@component('mail::button', ['url' => $url])
View Account
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@slot('subcopy')
If you're having trouble clicking the "View Account" button, copy and paste the URL below into your web browser: [{{ $url }}]({{ $url }})
@endslot
@endcomponent
