import '../css/app.css';
import './pwa-install';
import { initFirebaseMessaging } from "./firebase";

window.addEventListener("load", async () => {

    // ✅ Register Service Worker first
    if ("serviceWorker" in navigator) {
        try {
            await navigator.serviceWorker.register("/firebase-messaging-sw.js");
            console.log("✅ Firebase Service Worker registered");
        } catch (err) {
            console.error("❌ SW registration failed:", err);
            return;
        }
    }

    // 🔔 Then init Firebase Messaging
    const token = await initFirebaseMessaging();

    if (token) {
        console.log("🔥 Ready to save token:", token);
    }
});
