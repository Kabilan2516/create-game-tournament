@component('mail::message')
# Payout Processed

Hi {{ $user->name }},

Good news! Your payout request has been processed and sent to your account.

@component('mail::panel')
**Payout Details:**
* **Amount:** {{ $payout->amount }}
* **Method:** {{ $payout->method }}
* **Reference ID:** {{ $payout->reference_id }}
@endcomponent

Please allow up to {{ $payout->estimated_days }} business days for the funds to appear in your account.

@component('mail::button', ['url' => $url])
View Payout History
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@slot('subcopy')
If you're having trouble clicking the "View Payout History" button, copy and paste the URL below into your web browser: [{{ $url }}]({{ $url }})
@endslot
@endcomponent
