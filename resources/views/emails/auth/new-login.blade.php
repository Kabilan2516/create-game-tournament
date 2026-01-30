@component('mail::message')
# New Login Detected

Hi {{ $user->name }},

We detected a new login to your {{ config('app.name') }} account from a new device or location.

**Login Details:**
* **Time:** {{ $time }}
* **IP Address:** {{ $ipAddress }}
* **Device:** {{ $device }}

If this was you, you can ignore this email.

@component('mail::panel')
**⚠️ Not you?**
If you don't recognize this activity, please change your password immediately to secure your account.
@endcomponent

@component('mail::button', ['url' => $url])
Secure My Account
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@slot('subcopy')
If you're having trouble clicking the "Secure My Account" button, copy and paste the URL below into your web browser: [{{ $url }}]({{ $url }})
@endslot
@endcomponent
