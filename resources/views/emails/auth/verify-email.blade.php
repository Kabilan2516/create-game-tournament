@component('mail::message')
# Verify Your Email Address

Hi {{ $user->name }},

Welcome to **{{ config('app.name') }}**! 🎮  
To get started and compete in tournaments, please verify your email address by clicking the button below.

@component('mail::button', ['url' => $url, 'color' => 'success'])
Verify Email Address
@endcomponent

If you didn't create an account, you can safely ignore this email.

Thanks,  
**{{ config('app.name') }} Team**

@component('mail::subcopy')
If you're having trouble clicking the **"Verify Email Address"** button, copy and paste the URL below into your web browser:

{{ $url }}
@endcomponent
@endcomponent
