<template>
    <div class="login-wrapper">
        <div class="login-card">
            <h1 class="login-title">Student Login</h1>
            <p class="login-subtitle">Scan your QR code or enter your ID to access your dashboard.</p>

            <div class="form-group">
                <label>Student ID</label>
                <input 
                    v-model="studentId" 
                    type="text" 
                    placeholder="e.g. 2023-00500"
                    class="blue-input"
                />
            </div>

            <div class="form-group">
                <label>Password</label>
                <input 
                    v-model="password" 
                    type="password" 
                    placeholder="••••••••"
                    class="blue-input"
                    @keyup.enter="loginWithCredentials"
                />
            </div>

            <button class="btn-primary" @click="loginWithCredentials" :disabled="loading">
                {{ loading ? '...' : 'Login to Dashboard' }}
            </button>

            <div class="divider">
                <div class="line"></div>
                <span class="or-text">OR</span>
                <div class="line"></div>
            </div>

            <button class="btn-emerald" @click="startScanner" :disabled="scanning || loading">
                <span class="icon">📷</span> Scan QR Code to Login
            </button>
        </div>

        <!-- Scanner Overlay -->
        <div v-if="scanning" class="scanner-overlay" @click.self="stopScanner">
            <div class="scanner-content">
                <h3>Point camera at your ID's QR code</h3>
                <p class="scanner-hint">Hold the QR code steady inside the frame</p>
                <div class="video-container">
                    <video ref="video" playsinline></video>
                    <div class="scan-frame">
                        <div class="scan-line"></div>
                        <span class="corner tl"></span>
                        <span class="corner tr"></span>
                        <span class="corner bl"></span>
                        <span class="corner br"></span>
                    </div>
                </div>
                <button class="close-btn" @click="stopScanner">✕ Close Camera</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onBeforeUnmount } from 'vue';

const studentId = ref('');
const password = ref('');
const scanning = ref(false);
const loading = ref(false);
const video = ref(null);

let stream = null;
let animationId = null;

async function apiRequest(path, { method = 'GET', body } = {}) {
    const res = await fetch(path, {
        method,
        headers: { Accept: 'application/json', ...(body ? { 'Content-Type': 'application/json' } : {}) },
        body: body ? JSON.stringify(body) : undefined,
    });
    if (res.status === 204) return null;
    const data = await res.json().catch(() => null);
    if (!res.ok) throw new Error(data?.message || `Error ${res.status}`);
    return data;
}

async function loginWithCredentials() {
    if (!studentId.value || !password.value) { alert('Please enter both ID and Password.'); return; }
    loading.value = true;
    try {
        const response = await apiRequest('/api/student/login', {
            method: 'POST',
            body: { student_id: studentId.value, password: password.value },
        });
        localStorage.setItem('studentId', response.student.student_id);
        window.location.href = '/student';
    } catch (e) { alert(e.message); } finally { loading.value = false; }
}

async function startScanner() {
    scanning.value = true;
    try {
        // Load jsQR library if not already present
        if (!window.jsQR) {
            await new Promise((resolve, reject) => {
                const s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js';
                s.onload = resolve;
                s.onerror = reject;
                document.head.appendChild(s);
            });
        }

        // Try rear camera first (phones), then fall back to any camera (laptop webcam)
        let mediaStream = null;
        try {
            mediaStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 }, height: { ideal: 720 } }
            });
        } catch {
            // Fallback: accept any camera — covers laptop front webcams
            mediaStream = await navigator.mediaDevices.getUserMedia({
                video: { width: { ideal: 1280 }, height: { ideal: 720 } }
            });
        }

        stream = mediaStream;
        if (video.value) {
            video.value.srcObject = stream;
            video.value.setAttribute('playsinline', 'true');
            await video.value.play();
            tick();
        }
    } catch (err) {
        alert('Camera error: ' + err.message + '\n\nMake sure you allow camera access in your browser.');
        stopScanner();
    }
}

function stopScanner() {
    scanning.value = false;
    if (animationId) { cancelAnimationFrame(animationId); animationId = null; }
    if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
}

// Reuse a single offscreen canvas for performance (avoid creating a new one every frame)
const _scanCanvas = document.createElement('canvas');
const _scanCtx = _scanCanvas.getContext('2d', { willReadFrequently: true });

function tick() {
    if (!scanning.value || !video.value) return;
    const v = video.value;

    if (v.readyState >= 2 && v.videoWidth > 0) {
        _scanCanvas.width = v.videoWidth;
        _scanCanvas.height = v.videoHeight;

        // Boost contrast + brightness — helps with dark ID card backgrounds and low light
        _scanCtx.filter = 'contrast(1.5) brightness(1.15)';
        _scanCtx.drawImage(v, 0, 0, _scanCanvas.width, _scanCanvas.height);
        _scanCtx.filter = 'none';

        const imageData = _scanCtx.getImageData(0, 0, _scanCanvas.width, _scanCanvas.height);

        // 'attemptBoth' handles light-on-dark AND dark-on-light QR codes (school IDs vary)
        const code = window.jsQR(imageData.data, imageData.width, imageData.height, {
            inversionAttempts: 'attemptBoth',
        });

        if (code) {
            handleScannedCode(code.data);
            return;
        }
    }
    animationId = requestAnimationFrame(tick);
}

async function handleScannedCode(raw) {
    stopScanner();
    loading.value = true;
    try {
        const student = await apiRequest(`/api/student-by-qr?raw=${encodeURIComponent(raw)}`);
        localStorage.setItem('studentId', student.student_id);
        window.location.href = '/student';
    } catch (err) {
        alert('QR not recognized. Make sure you are scanning your registered school ID QR code.');
    } finally { loading.value = false; }
}

onBeforeUnmount(stopScanner);
</script>

<style scoped>


.login-wrapper {
    min-height: 100vh;
    background-color: #f1f3f5;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 1.5rem;
    font-family: 'Outfit', sans-serif;
}

.login-card {
    background: white;
    width: 100%;
    max-width: 480px;
    padding: 3rem;
    border-radius: 2rem;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06);
    text-align: center;
}

.login-title {
    color: #005d45; /* Specific Dark Green from image */
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
}

.login-subtitle {
    color: #64748b;
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 2.5rem;
    padding: 0 1rem;
}

.form-group {
    text-align: left;
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    font-size: 0.95rem;
    font-weight: 600;
    color: #334155;
    margin-bottom: 0.65rem;
    padding-left: 0.25rem;
}

.blue-input {
    width: 100%;
    background-color: #e3efff; /* Specific Light Blue from image */
    border: none;
    padding: 1.15rem 1.25rem;
    border-radius: 0.85rem;
    font-size: 1.05rem;
    color: #1e293b;
    outline: none;
    transition: box-shadow 0.2s;
    font-family: 'Outfit', sans-serif;
}

.blue-input:focus {
    box-shadow: 0 0 0 3px rgba(227, 239, 255, 0.5);
}

.btn-primary {
    width: 100%;
    background-color: #005d45; /* Dark Green */
    color: white;
    border: none;
    padding: 1.25rem;
    border-radius: 0.85rem;
    font-size: 1.15rem;
    font-weight: 700;
    cursor: pointer;
    margin-top: 1rem;
    transition: transform 0.1s, background 0.2s;
}

.btn-primary:hover {
    background-color: #004b38;
}

.btn-primary:active {
    transform: scale(0.99);
}

.btn-primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.divider {
    display: flex;
    align-items: center;
    margin: 2.25rem 0;
}

.line {
    flex: 1;
    height: 1px;
    background-color: #e2e8f0;
}

.or-text {
    padding: 0 1.25rem;
    color: #94a3b8;
    font-size: 0.85rem;
    font-weight: 600;
}

.btn-emerald {
    width: 100%;
    background-color: #00b884; /* Emerald Green from image */
    color: white;
    border: none;
    padding: 1.25rem;
    border-radius: 0.85rem;
    font-size: 1.15rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    transition: background 0.2s;
}

.btn-emerald:hover {
    background-color: #00a375;
}

/* Scanner Overlay */
.scanner-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.85);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
}

.scanner-content {
    background: #1e293b;
    padding: 2.5rem;
    border-radius: 2rem;
    width: 100%;
    max-width: 440px;
    text-align: center;
    color: white;
}

.video-container {
    width: 100%;
    aspect-ratio: 1;
    background: black;
    border-radius: 1.5rem;
    margin: 2rem 0;
    overflow: hidden;
    position: relative;
}

video { width: 100%; height: 100%; object-fit: cover; }

.scanner-content h3 {
    font-size: 1.15rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.scanner-hint {
    color: #94a3b8;
    font-size: 0.85rem;
    margin: 0;
}

.scan-frame {
    position: absolute;
    inset: 15%;
    border: none;
}

/* Animated scanning line */
.scan-line {
    position: absolute;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, #00b884, transparent);
    animation: scanMove 2s linear infinite;
    box-shadow: 0 0 8px #00b884;
}

@keyframes scanMove {
    0%   { top: 0; }
    50%  { top: 100%; }
    100% { top: 0; }
}

/* Corner bracket guides */
.corner {
    position: absolute;
    width: 22px;
    height: 22px;
    border-color: #00b884;
    border-style: solid;
}
.corner.tl { top: 0; left: 0;  border-width: 3px 0 0 3px; }
.corner.tr { top: 0; right: 0; border-width: 3px 3px 0 0; }
.corner.bl { bottom: 0; left: 0;  border-width: 0 0 3px 3px; }
.corner.br { bottom: 0; right: 0; border-width: 0 3px 3px 0; }

/* Dark vignette around scan area */
.video-container::after {
    content: '';
    position: absolute;
    inset: 0;
    box-shadow: inset 0 0 0 9999px rgba(0,0,0,0.35);
    clip-path: polygon(
        0% 0%, 100% 0%, 100% 100%, 0% 100%,
        0% 15%, 15% 15%, 15% 85%, 85% 85%, 85% 15%, 0% 15%
    );
    pointer-events: none;
}

.close-btn {
    background: transparent;
    border: 1px solid #475569;
    color: #94a3b8;
    padding: 0.75rem 2.5rem;
    border-radius: 0.75rem;
    cursor: pointer;
    font-weight: 600;
    transition: border-color 0.2s, color 0.2s;
}

.close-btn:hover {
    border-color: #ef4444;
    color: #ef4444;
}

@media (max-width: 480px) {
    .login-card { padding: 2rem 1.5rem; border-radius: 1.5rem; }
    .login-title { font-size: 2rem; }
}
</style>
