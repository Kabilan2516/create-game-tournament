import '../css/app.css';

window.addEventListener("load", async () => {

    if ("serviceWorker" in navigator) {
        try {
            const registration = await navigator.serviceWorker.register(
                "/firebase-messaging-sw.js"
            );

            console.log("✅ Service Worker registered", registration);
        } catch (err) {
            console.error("❌ SW registration failed:", err);
            return;
        }
    }

});
