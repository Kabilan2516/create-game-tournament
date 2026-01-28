<?php

return [

    'enabled' => env('ADS_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | DEVELOPMENT MODE
    |--------------------------------------------------------------------------
    | If true → show placeholders instead of real ads
    */
    'use_placeholder' => env('ADS_PLACEHOLDER', false),

    'slots' => [
        [
            'name'      => 'Welcome Top Banner',
            'page'      => 'welcome',
            'position'  => 'header',
            'user'      => 'all',
            'device'    => 'all',
            'enabled'   => true,

            // REAL AD CODE (production only)
            'code' => <<<HTML
                <ins class="adsbygoogle"
                    style="display:block"
                    data-ad-client="ca-pub-XXXX"
                    data-ad-slot="111111"
                    data-ad-format="auto"
                    data-full-width-responsive="true"></ins>
                <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
            HTML,

            // PLACEHOLDER (development)
            'placeholder' => '
                <div class="border-2 border-dashed border-slate-600 rounded-xl p-6 text-center text-gray-400">
                    🧪 Ad Placeholder<br>
                    <span class="text-xs">Welcome · Header</span>
                </div>
            ',
        ],
    ],
];
