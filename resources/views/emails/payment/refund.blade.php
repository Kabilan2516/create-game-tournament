@component('mail::message')
# Refund Issued

Hi {{ $user->name }},

A refund has been issued for your recent transaction.

@component('mail::panel')
**Refund Details:**
* **Original Transaction:** {{ $refund->original_transaction_id }}
* **Amount Refunded:** {{ $refund->amount }}
* **Reason:** {{ $refund->reason }}
@endcomponent

The funds should return to your original payment method within 5-10 business days, depending on your bank's processing times.

@component('mail::button', ['url' => $url])
View Transaction
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@slot('subcopy')
If you're having trouble clicking the "View Transaction" button, copy and paste the URL below into your web browser: [{{ $url }}]({{ $url }})
@endslot
@endcomponent
