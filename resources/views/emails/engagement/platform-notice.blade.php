@component('mail::message')
# Important Notice

Hi {{ $user->name }},

We are writing to inform you about an important update regarding the {{ config('app.name') }} platform.

**{{ $notice->subject }}**

{{ $notice->content }}

@if($notice->action_required)
**Action Required:**
{{ $notice->action_instructions }}
@endif

@component('mail::button', ['url' => $url])
Read Full Notice
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@slot('subcopy')
If you're having trouble clicking the "Read Full Notice" button, copy and paste the URL below into your web browser: [{{ $url }}]({{ $url }})
@endslot
@endcomponent
