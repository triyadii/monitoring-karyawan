importScripts('https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging-compat.js');

firebase.initializeApp({
  apiKey: "AIzaSyBdoAaFK4obS82aO9D-oqWn5dYgYLFJKRM",
  authDomain: "mdtnotification.firebaseapp.com",
  projectId: "mdtnotification",
  storageBucket: "mdtnotification.firebasestorage.app",
  messagingSenderId: "330219223368",
  appId: "1:330219223368:web:1a318d1fb0ae5ac1511b7d",
  measurementId: "G-0YDHT2GN43"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
  const notificationTitle = payload.notification?.title || 'Notifikasi Baru';
  const notificationOptions = {
    body: payload.notification?.body || '',
    icon: '/favicon.ico',
    data: payload.data
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});
