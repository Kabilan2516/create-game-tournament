@props([
    'name' => 'banner',
    'label' => 'Tournament Banner / Poster',
    'hint' => '(1200×600 recommended)',
    'preview' => null
])

<div 
    class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 
           p-8 rounded-3xl border-2 border-dashed border-cyan-500/60 
           hover:border-purple-500 transition group">

    <!-- Title -->
    <label class="block text-lg font-semibold mb-4 text-cyan-400">
        🖼 {{ $label }}
        <span class="text-sm text-gray-400 ml-2">{{ $hint }}</span>
    </label>

    <!-- Upload Area -->
    <div class="grid md:grid-cols-2 gap-8 items-center">

        <!-- PREVIEW BOX -->
        <div class="relative rounded-2xl overflow-hidden border border-slate-700 bg-black/40">

            <!-- Image Preview -->
            <img 
                id="{{ $name }}Preview"
                src="{{ $preview ?? 'https://via.placeholder.com/800x400?text=Upload+Banner' }}"
                class="w-full h-48 object-cover transition">

            <!-- Hover Overlay -->
            <div 
                class="absolute inset-0 bg-black/50 flex items-center justify-center
                       opacity-0 group-hover:opacity-100 transition">

            </div>
        </div>

        <!-- CONTROLS -->
        <div class="space-y-4">

            <!-- Hidden File Input -->
            <input 
                type="file" 
                name="{{ $name }}" 
                accept="image/*"
                class="hidden"
                id="{{ $name }}Input"
                onchange="previewImage(event, '{{ $name }}Preview')">

            <!-- Upload Button -->
            <label for="{{ $name }}Input"
                   class="inline-flex items-center space-x-3 px-8 py-4 rounded-xl 
                          font-bold cursor-pointer shadow-lg
                          bg-gradient-to-r from-cyan-500 to-purple-600
                          hover:from-purple-600 hover:to-cyan-500 transition transform hover:scale-105">

                <span>📤 Upload Banner</span>
            </label>

            <!-- Info -->
            <div class="text-sm text-gray-400 space-y-1">
                <p>✔ JPG / PNG only</p>
                <p>✔ Max size: 2MB</p>
                <p>✔ Landscape images work best</p>
            </div>

            <!-- Error -->
            @error($name)
                <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
