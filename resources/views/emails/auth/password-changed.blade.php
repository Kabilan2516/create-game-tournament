@component('mail::message')
# Password Changed Successfully

Hi {{ $user->name }},

This email is to confirm that the password for your {{ config('app.name') }} account has been successfully changed.

If you made this change, you can safely ignore this email.

@component('mail::panel')
**⚠️ Didn't change your password?**
If you did not make this change, please [contact support]({{ $supportUrl ?? '#' }}) immediately and secure your account.
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
