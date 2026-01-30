@component('mail::message')
# New Feature Alert: {{ $feature->name }}

Hi {{ $user->name }},

We're excited to announce a new feature that will take your {{ config('app.name') }} experience to the next level!

**Introducing: {{ $feature->name }}**

{{ $feature->description }}

@component('mail::panel')
**Why you'll love it:**
@foreach($feature->highlights as $highlight)
* {{ $highlight }}
@endforeach
@endcomponent

@component('mail::button', ['url' => $url])
Try It Now
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@slot('subcopy')
If you're having trouble clicking the "Try It Now" button, copy and paste the URL below into your web browser: [{{ $url }}]({{ $url }})
@endslot
@endcomponent
