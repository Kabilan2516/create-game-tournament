importScripts("https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js");

// 🔥 Firebase config (same project)
firebase.initializeApp({
    apiKey: "AIzaSyARHTxn2c0HlSju2xxxpxQuLCE70vxHlzs",
    authDomain: "gameroom-c3271.firebaseapp.com",
    projectId: "gameroom-c3271",
    messagingSenderId: "106474026363",
    appId: "1:106474026363:web:d5f6b7f33b254c6295b9de",
});

// Initialize messaging
const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage(function (payload) {
    console.log("[FCM SW] Background message:", payload);

    const title = payload.notification?.title || "GameConnect";
    const options = {
        body: payload.notification?.body || "New update",
        icon: "/favicon.ico",
    };

    self.registration.showNotification(title, options);
});
