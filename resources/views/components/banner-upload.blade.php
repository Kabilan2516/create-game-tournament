@props([
    'name' => 'banner',
    'label' => 'Tournament Banner / Poster',
    'preview' => null
])

<div
    x-data="{
        type: 'landscape',
        ratios: {
            landscape: 'aspect-[16/9]',
            portrait: 'aspect-[3/4]',
            square: 'aspect-[1/1]'
        },
        hints: {
            landscape: '1200×600 recommended (Landscape)',
            portrait: '800×1200 recommended (Poster)',
            square: '800×800 recommended (Square)'
        }
    }"
    class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900
           p-8 rounded-3xl border-2 border-dashed border-cyan-500/60
           hover:border-purple-500 transition group"
>

    <!-- TITLE -->
    <label class="block text-lg font-semibold mb-4 text-cyan-400">
        🖼 {{ $label }}
        <span class="text-sm text-gray-400 ml-2" x-text="hints[type]"></span>
    </label>

    <!-- BANNER TYPE SELECT -->
    <div class="flex gap-3 mb-6">
        <template x-for="option in ['landscape','portrait','square']" :key="option">
            <button
                type="button"
                @click="type = option"
                class="px-4 py-2 rounded-xl font-semibold text-sm transition"
                :class="type === option
                    ? 'bg-cyan-500/20 text-cyan-400 border border-cyan-400'
                    : 'bg-slate-800 text-gray-400 hover:bg-slate-700'"
            >
                <span x-text="option.charAt(0).toUpperCase() + option.slice(1)"></span>
            </button>
        </template>
    </div>

    <!-- UPLOAD AREA -->
    <div class="grid md:grid-cols-2 gap-8 items-center">

        <!-- PREVIEW BOX -->
        <div
            class="relative w-full overflow-hidden rounded-2xl border border-slate-700 bg-black/40"
            :class="ratios[type]"
        >
            <img
                id="{{ $name }}Preview"
                src="{{ $preview ?? 'https://via.placeholder.com/800x400?text=Upload+Banner' }}"
                class="absolute inset-0 w-full h-full object-cover transition"
            >
        </div>

        <!-- CONTROLS -->
        <div class="space-y-4">

            <!-- FORMAT VALUE -->
            <input type="hidden" name="banner_format" x-model="type">

            <!-- FILE INPUT -->
            <input
                type="file"
                name="{{ $name }}"
                accept="image/*"
                class="hidden"
                id="{{ $name }}Input"
                onchange="previewImage(event, '{{ $name }}Preview')"
            >

            <!-- UPLOAD BUTTON -->
            <label
                for="{{ $name }}Input"
                class="inline-flex items-center space-x-3 px-8 py-4 rounded-xl
                       font-bold cursor-pointer shadow-lg
                       bg-gradient-to-r from-cyan-500 to-purple-600
                       hover:from-purple-600 hover:to-cyan-500
                       transition transform hover:scale-105"
            >
                📤 Upload Banner
            </label>

            <!-- INFO -->
            <div class="text-sm text-gray-400 space-y-1">
                <p>✔ JPG / PNG</p>
                <p>✔ Max 4MB</p>
                <p x-text="hints[type]"></p>
            </div>

            @error($name)
                <p class="text-red-400 text-sm">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

{{-- ✅ GLOBAL SAFE PREVIEW FUNCTION --}}
<script>
function previewImage(event, targetId) {
    const file = event.target.files[0];
    if (!file) return;

    const img = document.getElementById(targetId);
    if (!img) return;

    img.src = URL.createObjectURL(file);
}
</script>
