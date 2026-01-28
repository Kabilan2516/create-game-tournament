@php
    if (!config('ads.enabled')) return;

    $page ??= 'all';
    $position ??= 'in_content';

    $isDev = app()->environment('local') || config('ads.use_placeholder');

    $userType = auth()->check()
        ? (auth()->user()->is_organizer ?? false ? 'organizer' : 'user')
        : 'guest';

    $device = request()->header('User-Agent') &&
              Str::contains(request()->header('User-Agent'), ['Mobile', 'Android', 'iPhone'])
                ? 'mobile'
                : 'desktop';

    $ads = collect(config('ads.slots'))->filter(function ($ad) use ($page, $position, $userType, $device) {
        return
            $ad['enabled'] &&
            $ad['position'] === $position &&
            in_array($ad['page'], [$page, 'all']) &&
            in_array($ad['user'], [$userType, 'all']) &&
            in_array($ad['device'], [$device, 'all']);
    });
@endphp

@foreach ($ads as $ad)
    <div class="my-6 ad-slot">
        {!! $isDev
            ? ($ad['placeholder'] ?? '<div class="border border-dashed p-4 text-center">Ad Placeholder</div>')
            : $ad['code']
        !!}
    </div>
@endforeach
