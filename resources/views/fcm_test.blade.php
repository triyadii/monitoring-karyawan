<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCM Token & Push Notification Tester</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: none; }
        .badge-step { font-size: 0.9rem; padding: 6px 12px; border-radius: 20px; }
    </style>
</head>
<body class="py-5">
<div class="container" style="max-width: 900px;">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-primary">🔔 Firebase Push Notification Tester</h2>
        <p class="text-muted">Gunakan browser ini sebagai pengganti HP untuk mendapatkan <strong>FCM Token Asli</strong> dan menguji kirim notifikasi secara langsung.</p>
    </div>

    <!-- LANGKAH 1 -->
    <div class="card mb-4">
        <div class="card-body p-4">
            <span class="badge bg-primary badge-step mb-2">Langkah 1</span>
            <h5 class="fw-bold">Dapatkan FCM Token dari Browser Ini</h5>
            <p class="text-muted small">Browser ini akan bertindak sebagai perangkat HP karyawan. Klik tombol di bawah untuk meminta izin notifikasi dan men-generate token.</p>
            
            <div class="mb-3">
                <label class="form-label small fw-semibold">Web Push VAPID Key (Opsional / Jika Diperlukan)</label>
                <input type="text" id="vapidKey" class="form-control form-control-sm" placeholder="Contoh: BB8mH... (dari Firebase Console > Cloud Messaging > Web configuration > Generate key pair)">
                <div class="form-text text-muted">Jika belum punya, kosongkan dulu atau klik tombol di bawah. Jika Firebase meminta VAPID key, ambil dari Firebase Console.</div>
            </div>

            <button id="btnGetToken" class="btn btn-primary" onclick="generateFcmToken()">
                🔑 Generate FCM Token Browser
            </button>

            <div class="mt-3" id="tokenContainer" style="display: none;">
                <label class="form-label small fw-semibold text-success">FCM Token Berhasil Didapatkan:</label>
                <div class="input-group">
                    <textarea id="fcmTokenResult" class="form-control font-monospace small" rows="3" readonly></textarea>
                    <button class="btn btn-outline-secondary" onclick="copyToken()">Salin</button>
                </div>
            </div>
            <div id="tokenError" class="alert alert-danger mt-3" style="display: none;"></div>
        </div>
    </div>

    <!-- LANGKAH 2 -->
    <div class="card mb-4">
        <div class="card-body p-4">
            <span class="badge bg-success badge-step mb-2">Langkah 2</span>
            <h5 class="fw-bold">Uji Coba Kirim Push Notification ke Browser Ini</h5>
            <p class="text-muted small">Kirim notifikasi langsung dari backend Laravel menggunakan token yang didapat dari Langkah 1.</p>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Judul Notifikasi</label>
                    <input type="text" id="notifTitle" class="form-control" value="Halo dari Laravel!">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Isi Pesan</label>
                    <input type="text" id="notifBody" class="form-control" value="Push notification berhasil diterima di perangkat Anda 🎉">
                </div>
            </div>

            <button id="btnSendNotif" class="btn btn-success" onclick="sendTestNotification()">
                🚀 Kirim Notifikasi Sekarang
            </button>
            <div id="notifResult" class="mt-3"></div>
        </div>
    </div>

    <!-- LANGKAH 3 -->
    <div class="card mb-4">
        <div class="card-body p-4">
            <span class="badge bg-warning text-dark badge-step mb-2">Langkah 3</span>
            <h5 class="fw-bold">Uji Coba Proteksi Login (Device Lock)</h5>
            <p class="text-muted small">Coba login dengan akun user dan token perangkat ini. Jika token berbeda dari yang terdaftar, login akan ditolak.</p>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Username</label>
                    <input type="text" id="loginUsername" class="form-control" placeholder="admin">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Password</label>
                    <input type="password" id="loginPassword" class="form-control" placeholder="password">
                </div>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" onclick="testLogin()">🔐 Test Login</button>
                <button class="btn btn-outline-danger" onclick="testResetToken()">🔄 Test Reset Token (Pindah HP)</button>
            </div>
            <div id="loginResult" class="mt-3"></div>
        </div>
    </div>
</div>

<!-- Firebase SDK -->
<script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging-compat.js"></script>

<script>
const firebaseConfig = {
    apiKey: "AIzaSyBdoAaFK4obS82aO9D-oqWn5dYgYLFJKRM",
    authDomain: "mdtnotification.firebaseapp.com",
    projectId: "mdtnotification",
    storageBucket: "mdtnotification.firebasestorage.app",
    messagingSenderId: "330219223368",
    appId: "1:330219223368:web:1a318d1fb0ae5ac1511b7d",
    measurementId: "G-0YDHT2GN43"
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

let currentFcmToken = "";
let authToken = "";

// Handle foreground notification
messaging.onMessage((payload) => {
    console.log("Foreground message received:", payload);
    const title = payload.notification?.title || 'Notifikasi Baru';
    const body = payload.notification?.body || '';
    
    if (Notification.permission === 'granted') {
        new Notification(title, { body: body, icon: '/favicon.ico' });
    }
});

const baseUrl = "{{ rtrim(url('/'), '/') }}";
const swUrl = "{{ url('/firebase-messaging-sw.js') }}";

async function generateFcmToken() {
    const errorEl = document.getElementById('tokenError');
    const containerEl = document.getElementById('tokenContainer');
    const resultEl = document.getElementById('fcmTokenResult');
    const btn = document.getElementById('btnGetToken');
    const vapidKey = document.getElementById('vapidKey').value.trim();

    errorEl.style.display = 'none';
    btn.disabled = true;
    btn.innerText = 'Meminta izin notifikasi...';

    if (!('serviceWorker' in navigator) || !('Notification' in window)) {
        errorEl.innerText = 'Browser ini tidak mendukung Service Worker atau Web Notification. Pastikan Anda membuka halaman ini melalui HTTPS (bukan HTTP biasa).';
        errorEl.style.display = 'block';
        btn.disabled = false;
        btn.innerText = '🔑 Coba Lagi';
        return;
    }

    try {
        const permission = await Notification.requestPermission();
        if (permission !== 'granted') {
            throw new Error('Izin notifikasi ditolak oleh browser. Silakan klik icon gembok di sebelah URL dan ubah Notifications ke Allow.');
        }

        btn.innerText = 'Mengambil token dari Firebase...';

        const swRegistration = await navigator.serviceWorker.register(swUrl, { scope: './' });

        const options = { serviceWorkerRegistration: swRegistration };
        if (vapidKey) {
            options.vapidKey = vapidKey;
        }

        const token = await messaging.getToken(options);
        if (!token) {
            throw new Error('Gagal mendapatkan token dari Firebase. Pastikan project Firebase Anda memiliki Web Push Certificate.');
        }

        currentFcmToken = token;
        resultEl.value = token;
        containerEl.style.display = 'block';
        btn.innerText = '✅ FCM Token Berhasil Diambil!';
        btn.classList.replace('btn-primary', 'btn-success');
    } catch (err) {
        console.error(err);
        errorEl.innerText = err.message || err;
        errorEl.style.display = 'block';
        btn.disabled = false;
        btn.innerText = '🔑 Coba Lagi';
    }
}

function copyToken() {
    const textarea = document.getElementById('fcmTokenResult');
    textarea.select();
    document.execCommand('copy');
    alert('FCM Token berhasil disalin ke clipboard!');
}

async function sendTestNotification() {
    const resultEl = document.getElementById('notifResult');
    const title = document.getElementById('notifTitle').value;
    const body = document.getElementById('notifBody').value;
    const token = currentFcmToken || document.getElementById('fcmTokenResult').value;

    if (!token) {
        alert('Silakan dapatkan FCM Token di Langkah 1 terlebih dahulu!');
        return;
    }

    resultEl.innerHTML = '<div class="spinner-border spinner-border-sm text-success"></div> Mengirim notifikasi melalui backend...';

    try {
        const res = await fetch(`${baseUrl}/api/notifications/send-to-token`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...(authToken ? { 'Authorization': `Bearer ${authToken}` } : {})
            },
            body: JSON.stringify({
                fcm_token: token,
                title: title,
                body: body
            })
        });

        const data = await res.json();
        if (res.ok && data.success) {
            resultEl.innerHTML = `<div class="alert alert-success">✅ Notifikasi berhasil dikirim oleh server! Periksa notifikasi di ponsel / layar Anda.</div>`;
        } else {
            resultEl.innerHTML = `<div class="alert alert-danger">❌ Gagal mengirim: ${data.message || JSON.stringify(data)}</div>`;
        }
    } catch (err) {
        resultEl.innerHTML = `<div class="alert alert-danger">Error: ${err.message}</div>`;
    }
}

async function testLogin() {
    const resultEl = document.getElementById('loginResult');
    const username = document.getElementById('loginUsername').value;
    const password = document.getElementById('loginPassword').value;
    const token = currentFcmToken || document.getElementById('fcmTokenResult').value || 'dummy_token_123';

    resultEl.innerHTML = '<div class="spinner-border spinner-border-sm text-primary"></div> Menghubungkan ke API Login...';

    try {
        const res = await fetch(`${baseUrl}/api/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({
                username: username,
                password: password,
                fcm_token: token
            })
        });

        const data = await res.json();
        if (res.ok) {
            authToken = data.authorization?.token;
            resultEl.innerHTML = `<div class="alert alert-success">✅ Login Berhasil (HTTP 200)! Token JWT tersimpan. FCM Token terikat ke user ini.</div>`;
        } else if (res.status === 403) {
            resultEl.innerHTML = `<div class="alert alert-warning">⚠️ <strong>Terkunci di HP Lain (HTTP 403):</strong> ${data.message}</div>`;
        } else {
            resultEl.innerHTML = `<div class="alert alert-danger">❌ Error ${res.status}: ${data.message || data.error || JSON.stringify(data)}</div>`;
        }
    } catch (err) {
        resultEl.innerHTML = `<div class="alert alert-danger">Error: ${err.message}</div>`;
    }
}

async function testResetToken() {
    const resultEl = document.getElementById('loginResult');
    const username = document.getElementById('loginUsername').value;
    const password = document.getElementById('loginPassword').value;

    resultEl.innerHTML = '<div class="spinner-border spinner-border-sm text-danger"></div> Mereset FCM token...';

    try {
        const res = await fetch(`${baseUrl}/api/reset-fcm-token`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({
                username: username,
                password: password
            })
        });

        const data = await res.json();
        if (res.ok) {
            resultEl.innerHTML = `<div class="alert alert-info">🔄 <strong>${data.message}</strong> Sekarang Anda bisa login dengan perangkat baru.</div>`;
        } else {
            resultEl.innerHTML = `<div class="alert alert-danger">❌ Error ${res.status}: ${data.message || data.error || JSON.stringify(data)}</div>`;
        }
    } catch (err) {
        resultEl.innerHTML = `<div class="alert alert-danger">Error: ${err.message}</div>`;
    }
}
</script>
</body>
</html>
