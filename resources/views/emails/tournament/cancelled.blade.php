@component('mail::message')
# Tournament Update: {{ $status }}

Hi {{ $user->name }},

We're writing to inform you that the tournament "**{{ $tournament->name }}**" has been **{{ strtolower($status) }}**.

@if($status == 'Cancelled')
We apologize for the inconvenience. If you paid an entry fee, a refund will be processed shortly.
@elseif($status == 'Rescheduled')
**New Start Time:** {{ $tournament->new_start_date }}
Please mark your calendar!
@endif

@component('mail::button', ['url' => $url])
View Tournament Details
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@slot('subcopy')
If you're having trouble clicking the "View Tournament Details" button, copy and paste the URL below into your web browser: [{{ $url }}]({{ $url }})
@endslot
@endcomponent
