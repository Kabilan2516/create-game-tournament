

import { initializeApp } from "firebase/app";
import { getMessaging, getToken, onMessage } from "firebase/messaging";

// 🔥 Firebase config
const firebaseConfig = {
    apiKey: import.meta.env.VITE_FIREBASE_API_KEY,
    authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN,
    projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID,
    messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID,
    appId: import.meta.env.VITE_FIREBASE_APP_ID,
};

// Init Firebase
const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

// 🔔 Ask permission & get token
export async function initFirebaseMessaging() {
    try {
        const permission = await Notification.requestPermission();

        if (permission !== "granted") {
            console.warn("🔕 Notification permission denied");
            return null;
        }

        const token = await getToken(messaging, {
            vapidKey: import.meta.env.VITE_FIREBASE_VAPID_KEY
        });

        if (token) {
            console.log("✅ FCM Token:", token);
            return token;
        }

        console.warn("⚠ No token generated");
        return null;

    } catch (error) {
        console.error("❌ FCM error:", error);
        return null;
    }
}

// 🔔 Foreground messages
onMessage(messaging, payload => {
    console.log("📩 Foreground message:", payload);

    new Notification(payload.notification.title, {
        body: payload.notification.body,
        icon: "/favicon.ico",
    });
});
