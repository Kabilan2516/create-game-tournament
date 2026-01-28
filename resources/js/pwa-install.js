let deferredPrompt = null;

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
});

document.addEventListener('DOMContentLoaded', () => {

    const banner = document.getElementById('pwa-install-banner');
    if (!banner) return;

    // Prevent repeated annoyance
    if (localStorage.getItem('pwaPromptDismissed')) {
        banner.remove();
        return;
    }

    banner.classList.remove('hidden');

    const installBtn = document.getElementById('pwa-install-btn');
    const dismissBtn = document.getElementById('pwa-dismiss-btn');

    dismissBtn?.addEventListener('click', () => {
        localStorage.setItem('pwaPromptDismissed', '1');
        banner.remove();
    });

    installBtn?.addEventListener('click', async () => {

        // iOS fallback
        if (!deferredPrompt) {
            showIOSInstallHint();
            return;
        }

        deferredPrompt.prompt();
        const choice = await deferredPrompt.userChoice;

        if (choice.outcome === 'accepted') {
            localStorage.setItem('pwaPromptDismissed', '1');
        }

        banner.remove();
        deferredPrompt = null;
    });
});

function showIOSInstallHint() {
    alert(
        "📲 Install GameConnect:\n\n" +
        "1. Tap the Share button ⬆️\n" +
        "2. Select 'Add to Home Screen'\n\n" +
        "Get instant room alerts!"
    );
}
