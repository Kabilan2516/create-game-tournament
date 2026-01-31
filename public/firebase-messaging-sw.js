importScripts("https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js");

// 🔥 Firebase config
firebase.initializeApp({
    apiKey: "AIzaSyARHTxn2c0HlSju2xxxpxQuLCE70vxHlzs",
    authDomain: "gameroom-c3271.firebaseapp.com",
    projectId: "gameroom-c3271",
    messagingSenderId: "106474026363",
    appId: "1:106474026363:web:d5f6b7f33b254c6295b9de",
});

// Initialize messaging
const messaging = firebase.messaging();

/* ===============================
   ✅ PWA LIFECYCLE (REQUIRED)
================================ */

self.addEventListener("install", event => {
    self.skipWaiting();
});

self.addEventListener("activate", event => {
    self.clients.claim();
});

/* ===============================
   🔔 BACKGROUND NOTIFICATIONS
================================ */

messaging.onBackgroundMessage(payload => {
    console.log("[FCM SW] Background message:", payload);

    const title = payload.notification?.title || "GameConnect";

    const options = {
        body: payload.notification?.body || "New update",
        icon: "/favicon/favicon-96x96.png",
        badge: "/favicon/favicon-96x96.png",
        data: {
            url: payload.data?.url || "/"
        }
    };

    self.registration.showNotification(title, options);
});

/* ===============================
   👉 NOTIFICATION CLICK HANDLER
================================ */

self.addEventListener("notificationclick", event => {
    event.notification.close();

    const targetUrl = event.notification.data?.url || "/";

    event.waitUntil(
        clients.matchAll({ type: "window", includeUncontrolled: true })
            .then(clientList => {
                for (const client of clientList) {
                    if (client.url === targetUrl && "focus" in client) {
                        return client.focus();
                    }
                }
                if (clients.openWindow) {
                    return clients.openWindow(targetUrl);
                }
            })
    );
});
