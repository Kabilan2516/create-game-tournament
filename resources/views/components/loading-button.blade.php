<button
    type="submit"
    x-bind:disabled="loading"
    {{ $attributes->merge([
        'class' => '
            w-full py-3 rounded-xl font-bold
            bg-gradient-to-r from-cyan-500 to-purple-600
            hover:opacity-90 active:scale-95 transition
            flex items-center justify-center space-x-2
            disabled:opacity-70 disabled:cursor-not-allowed
        '
    ]) }}>

    {{-- Normal --}}
    <span x-show="!loading">
        {{ $slot }}
    </span>

    {{-- Loading --}}
    <span x-show="loading" class="flex items-center space-x-2">
        <svg class="w-5 h-5 animate-spin text-white"
             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                  d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8z"/>
        </svg>
        <span>Processing...</span>
    </span>

</button>
