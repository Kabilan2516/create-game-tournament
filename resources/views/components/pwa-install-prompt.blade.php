@if(session('show_pwa_prompt'))
<div id="pwa-install-banner"
     class="fixed bottom-4 left-4 right-4 z-50">

    <div class="bg-slate-900 border border-cyan-500 rounded-2xl p-4
                flex flex-col sm:flex-row gap-4 items-center justify-between shadow-xl">

        <div>
            <p class="font-bold text-white">📲 Install GameConnect App</p>
            <p class="text-sm text-gray-300">
                Get instant room updates & match alerts without WhatsApp spam.
            </p>
        </div>

        <div class="flex gap-3 w-full sm:w-auto">
            <a href="{{ config('services.pwa.pwa_url') }}?code={{ session('join_code') }}"
               target="_blank"
               class="flex-1 text-center py-2 px-4 rounded-lg
                      bg-gradient-to-r from-cyan-500 to-purple-600 font-semibold">
                Open App
            </a>

            <button onclick="hidePwaPrompt()"
                    class="px-4 py-2 rounded-lg bg-slate-700">
                Later
            </button>
        </div>
    </div>
</div>

<script>
function hidePwaPrompt() {
    const el = document.getElementById('pwa-install-banner');
    if (el) el.remove();
}
</script>
@endif
