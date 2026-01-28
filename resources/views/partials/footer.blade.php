


{{-- ===============================
   FILE 4: resources/views/partials/footer.blade.php
================================ --}}
<footer class="bg-black py-16">
    <div class="max-w-7xl mx-auto grid md:grid-cols-4 gap-10 px-6">

        <div>
            <h4 class="text-2xl font-bold text-cyan-400">GameConnect</h4>
            <p class="text-gray-400 mt-4">The ultimate esports tournament hub.</p>
        </div>

        <div>
            <h5 class="font-semibold mb-3">Platform</h5>
            <ul class="space-y-2 text-gray-400">
                <li>Browse Matches</li>
                <li>Host Tournament</li>
                <li>Pricing</li>
            </ul>
        </div>

        <div>
            <h5 class="font-semibold mb-3">Support</h5>
            <ul class="space-y-2 text-gray-400">
                <li>Help Center</li>
                <li>Terms</li>
                <li>Privacy</li>
            </ul>
        </div>

        <div>
            <h5 class="font-semibold mb-3">Ad Zone</h5>
            <div class="bg-slate-800 py-6 text-center rounded border border-dashed border-gray-600">
                <span class="text-gray-400">Footer Ad Slot</span>
            </div>
        </div>

    </div>

    <div class="text-center text-gray-500 mt-12">© {{ date('Y') }} GameConnect</div>
</footer>
