@component('mail::message')
# Payment Received

Hi {{ $user->name }},

We have successfully received your payment. Thank you!

@component('mail::panel')
**Transaction Details:**
* **Amount:** {{ $payment->amount }}
* **Date:** {{ $payment->date }}
* **Description:** {{ $payment->description }}
* **Transaction ID:** {{ $payment->transaction_id }}
@endcomponent

You can view your full transaction history in your account settings.

@component('mail::button', ['url' => $url])
View Receipt
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@slot('subcopy')
If you're having trouble clicking the "View Receipt" button, copy and paste the URL below into your web browser: [{{ $url }}]({{ $url }})
@endslot
@endcomponent
