<template>
    <div v-if="ready" class="dashboard-wrapper">
        <!-- Sidebar Overlay (mobile) -->
        <div class="sidebar-overlay" :class="{ show: isMobileMenuOpen }" @click="isMobileMenuOpen = false"></div>

        <!-- Sidebar / Header -->
        <aside class="sidebar" :class="{ 'sidebar-open': isMobileMenuOpen }">
            <!-- Mobile close btn -->
            <div class="sidebar-close-row mobile-only">
                <div class="sidebar-brand-logo">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span>Attendance</span>
                </div>
                <button class="sidebar-close-btn" @click="isMobileMenuOpen = false">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Avatar Section -->
            <div class="sidebar-header">
                <div class="avatar-glow-ring">
                    <div class="avatar-wrapper">
                        <div class="avatar-gradient"></div>
                        <div class="avatar-text">{{ studentInitials }}</div>
                    </div>
                </div>
                <div class="sidebar-user-info">
                    <h2>{{ studentName }}</h2>
                    <p class="student-id">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c0 1.657 2.686 3 6 3s6-1.343 6-3"/></svg>
                        {{ currentStudent.student_id }}
                    </p>
                    <div class="badge-row">
                        <span class="badge course">{{ currentStudent.course }}</span>
                        <span class="badge year">{{ currentStudent.year_level }}</span>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="sidebar-divider"></div>

            <!-- Nav label -->
            <p class="sidebar-nav-label">NAVIGATION</p>

            <nav class="sidebar-nav">
                <a href="#" :class="{ active: activeTab === 'dashboard' }" @click.prevent="activeTab = 'dashboard'; isMobileMenuOpen = false">
                    <span class="nav-icon-wrap">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10"/></svg>
                    </span>
                    <span class="nav-label">Dashboard</span>
                    <span class="nav-arrow">›</span>
                </a>
                <a href="#" :class="{ active: activeTab === 'history' }" @click.prevent="activeTab = 'history'; isMobileMenuOpen = false">
                    <span class="nav-icon-wrap">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 12h6M9 16h6"/></svg>
                    </span>
                    <span class="nav-label">Attendance History</span>
                    <span class="nav-arrow">›</span>
                </a>
            </nav>

            <div style="flex: 1"></div>

            <!-- Logout -->
            <div class="sidebar-footer">
                <div class="sidebar-footer-divider"></div>
                <button class="logout-btn-premium" @click="logoutModalOpen = true">
                    <span class="nav-icon-wrap logout-icon">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </span>
                    <span>Logout</span>
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Mobile Header -->
            <header class="mobile-header">
                <button class="hamburger-btn" type="button" @click="isMobileMenuOpen = true">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <span class="brand" style="font-weight: 700; color: white;">Student Portal</span>
            </header>
            <div v-if="activeTab === 'dashboard'">
                <!-- 1. Dashboard Header -->
                <header class="content-header">
                    <h1>Student Dashboard</h1>
                    <p>Welcome back, <strong>{{ studentName }}</strong>! Here's your attendance summary.</p>
                </header>

                <!-- 2. Attendance Statistics — four cards in one row -->
                <div class="stats-grid-premium">
                    <div class="stat-card-premium">
                        <div class="stat-icon blue">
                            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="stat-content">
                            <span class="label">Total Events</span>
                            <span class="value">{{ totalEvents }}</span>
                        </div>
                    </div>
                    <div class="stat-card-premium">
                        <div class="stat-icon green">
                            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="stat-content">
                            <span class="label">Attended</span>
                            <span class="value">{{ eventsAttended }}</span>
                        </div>
                    </div>
                    <div class="stat-card-premium">
                        <div class="stat-icon red">
                            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 9l6 6m0-6l-6 6m12-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="stat-content">
                            <span class="label">Absent</span>
                            <span class="value">{{ eventsAbsent }}</span>
                        </div>
                    </div>
                    <div class="stat-card-premium">
                        <div class="stat-icon purple">
                            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                        <div class="stat-content">
                            <span class="label">Attendance Rate</span>
                            <span class="value">{{ attendanceRate }}</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Attendance Alert — directly below stats, conditional -->
                <div v-if="totalEvents > 0 && eventsAttended / totalEvents < 0.8" class="alert-banner">
                    <span class="alert-icon">⚠️</span>
                    <span><strong>Attendance Alert:</strong> Your current attendance rate is below the required 80% threshold. Please attend upcoming events.</span>
                </div>

                <!-- 4. Main Dashboard Section: Event Hub (left) + Attendance Progress (right) -->
                <div class="dashboard-two-col">
                    <!-- LEFT: Event Hub -->
                    <section class="section-card event-hub-card">
                        <div class="section-header">
                            <h2>Event Hub</h2>
                            <div class="hub-tabs">
                                <button :class="['hub-tab', { active: eventHubTab === 'active' }]" @click="eventHubTab = 'active'">
                                    Active <span class="hub-badge">{{ availableEvents.length }}</span>
                                </button>
                                <button :class="['hub-tab', { active: eventHubTab === 'upcoming' }]" @click="eventHubTab = 'upcoming'">
                                    Upcoming <span class="hub-badge neutral">{{ upcomingEvents.length }}</span>
                                </button>
                            </div>
                        </div>

                        <div v-if="eventHubTab === 'active'">
                            <div v-if="availableEvents.length === 0" class="hub-empty">
                                <svg width="36" height="36" fill="none" stroke="#93c5fd" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <p>You're all caught up! No active events right now.</p>
                                <button class="hub-action-btn" @click="activeTab = 'history'">View Past Events</button>
                            </div>
                            <div v-else>
                                <label class="test-mode-toggle"><input type="checkbox" v-model="testModeEnabled" /> <span>Test Mode</span></label>
                                <div v-for="ev in availableEvents" :key="ev.id" class="hub-event-row">
                                    <div class="hub-event-info">
                                        <strong>{{ ev.name }}</strong>
                                        <span>{{ formatEventDate(ev.date) }} · {{ formatEventTime(ev) }}</span>
                                    </div>
                                    <button class="hub-checkin-btn" @click="testModeEnabled ? openTestCheckin(ev) : openQrScanner(ev.id)" :disabled="loading">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                        {{ loading ? '...' : 'Check In' }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div v-if="eventHubTab === 'upcoming'">
                            <div v-if="upcomingEvents.length === 0" class="hub-empty">
                                <svg width="36" height="36" fill="none" stroke="#93c5fd" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <p>No upcoming events scheduled for your year level.</p>
                                <button class="hub-action-btn" @click="activeTab = 'history'">View Past Events</button>
                            </div>
                            <div v-else>
                                <div v-for="ev in upcomingEvents" :key="ev.id" class="hub-event-row">
                                    <div class="hub-event-info">
                                        <strong>{{ ev.name }}</strong>
                                        <span>{{ formatEventDate(ev.date) }} · {{ formatEventTime(ev) }}</span>
                                    </div>
                                    <button class="details-btn" @click="showEventDetails(ev)">Details</button>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- RIGHT: Attendance Progress -->
                    <section class="section-card progress-card">
                        <div class="rate-header">
                            <div>
                                <h2>Attendance Progress</h2>
                                <p class="section-caption">Your attendance at a glance</p>
                            </div>
                            <span class="rate-value" :style="{ color: isGoodAttendance ? '#10b981' : '#ef4444' }">{{ attendanceRate }}</span>
                        </div>
                        <div class="rate-bar-track">
                            <div class="rate-bar-fill" :style="{ width: attendanceRate, background: isGoodAttendance ? 'linear-gradient(90deg,#10b981,#34d399)' : 'linear-gradient(90deg,#ef4444,#f87171)' }"></div>
                        </div>
                        <div class="rate-legend">
                            <span><i class="legend-dot present-dot"></i> Present: {{ eventsAttended }}</span>
                            <span><i class="legend-dot absent-dot"></i> Absent: {{ eventsAbsent }}</span>
                        </div>
                    </section>
                </div>

                <!-- 5. Recent Attendance — full-width below main section -->
                <section class="section-card recent-section-full">
                    <div class="section-header">
                        <div>
                            <h2>Recent Attendance</h2>
                            <p class="section-caption">Your latest event records</p>
                        </div>
                        <button class="text-link" @click="activeTab = 'history'">View All →</button>
                    </div>
                    <div v-if="recentAttendance.length" class="recent-table-wrap">
                        <table class="history-table recent-table">
                            <thead>
                                <tr>
                                    <th>Event Name</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="row in recentAttendance" :key="row.id">
                                    <td class="font-bold" style="text-transform: capitalize;">{{ row.name }}</td>
                                    <td>{{ formatEventDate(row.date) }}</td>
                                    <td>{{ formatEventTime(row) }}</td>
                                    <td><span class="status-badge" :class="row.attendance?.status || 'absent'">{{ row.attendance?.status === 'present' ? '✓ Present' : '✗ Absent' }}</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="compact-empty">Attendance records will appear here after events.</div>
                </section>
            </div>

            <!-- History Tab -->
            <div v-if="activeTab === 'history'">
                <header class="content-header">
                    <h1>Attendance History</h1>
                    <p>View all your past event attendances.</p>
                </header>

                <div class="section-card">
                    <div v-if="historyEvents.length === 0" class="empty-state">
                        <p>No attendance records found yet.</p>
                    </div>

                    <div v-else class="history-table-wrapper">
                        <table class="history-table">
                            <thead>
                                <tr>
                                    <th>Event Name</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Checked In</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="row in historyEvents" :key="row.id">
                                    <td class="font-bold">{{ row.name }}</td>
                                    <td>{{ formatEventDate(row.date) }}</td>
                                    <td>
                                        <span class="status-badge" :class="row.attendance?.status || 'absent'">
                                            {{ row.attendance?.status || 'absent' }}
                                        </span>
                                    </td>
                                    <td>{{ formatDateTime(row.attendance?.checked_in_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <!-- Notification Toast -->
        <Transition name="toast">
            <div v-if="successMessage" class="toast-notification">
                {{ successMessage }}
            </div>
        </Transition>

        <!-- Logout Confirmation Modal -->
        <div v-if="logoutModalOpen" class="qr-modal" @click.self="logoutModalOpen = false">
            <div class="qr-modal-inner">
                <h3 style="margin-bottom: 1rem; color: var(--slate-900); font-weight: 700;">Confirm Logout</h3>
                <p style="margin-bottom: 1.5rem; color: var(--slate-500); font-size: 0.95rem;">Are you sure you want to logout? Your current student session will be ended.</p>
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <button class="btn" style="background: var(--slate-200); color: var(--slate-700); padding: 0.6rem 1.25rem; border-radius: 6px; font-weight: 600; border: none; cursor: pointer;" @click="logoutModalOpen = false">Cancel</button>
                    <button class="btn btn-primary" style="background: var(--red); color: white; border: none; padding: 0.6rem 1.25rem; border-radius: 6px; font-weight: 600; cursor: pointer;" @click="logoutStudent">Logout</button>
                </div>
            </div>
        </div>
        <!-- Alert Modal -->
        <div v-if="alertModal.isOpen" class="qr-modal">
            <div class="qr-modal-inner">
                <h3 style="margin-bottom: 1rem; color: var(--slate-900); font-weight: 700;">{{ alertModal.title }}</h3>
                <p style="margin-bottom: 1.5rem; color: var(--slate-500); font-size: 0.95rem;">{{ alertModal.message }}</p>
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <button class="btn btn-primary" style="background: var(--navy); color: white; border: none; min-width: 100px; padding: 0.75rem 1.5rem; border-radius: var(--radius-sm);" @click="alertModal.isOpen = false">OK</button>
                </div>
            </div>
        </div>

        <!-- QR Scanner Modal -->
        <div v-if="qrModalOpen" class="qr-modal">
            <div class="qr-modal-inner">
                <h3 style="margin-bottom: 1rem;">Scan Event QR Code</h3>
                <div class="scanner-container" style="background: black; position: relative; border-radius: 8px; overflow: hidden; aspect-ratio: 1; margin-bottom: 1rem;">
                    <video ref="qrVideo" style="width: 100%; height: 100%; object-fit: cover;" autoplay playsinline></video>
                    
                    <div v-if="qrError" style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.8); color: white; padding: 1rem; text-align: center;">
                        <p>Camera access denied or unavailable. Please check your browser permissions.</p>
                    </div>

                    <div v-if="isVerifying" style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.8); color: white; flex-direction: column; gap: 1rem;">
                        <div class="spinner"></div>
                        <p style="font-weight: 600;">Verifying GPS Location...</p>
                    </div>
                </div>
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <label class="qr-upload-btn">Upload QR Image<input type="file" accept="image/*" @change="handleQrImageUpload" hidden /></label>
                    <button class="btn btn-outline" style="border: 1px solid #cbd5e1; padding: 0.75rem 1.5rem; border-radius: 8px; background: white; cursor: pointer;" @click="closeQrScanner">Cancel</button>
                </div>
                <p class="qr-help">Use the active event QR code from the administrator. Student ID QR codes cannot be used for attendance.</p>
            </div>
        </div>
        <!-- Test Mode Check-in Modal -->
        <div v-if="testModalOpen" class="qr-modal">
            <div class="qr-modal-inner">
                <h3 style="margin-bottom: 1rem;">Developer Test Mode</h3>
                <p style="margin-bottom: 1rem; color: var(--text-muted); font-size: 0.9rem;">
                    Bypass the camera and GPS. Check the Admin Dashboard for the Event's active QR Token and paste it below.
                </p>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">QR Token String:</label>
                    <input type="text" v-model="testTokenInput" placeholder="Paste the long token string here" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; font-family: monospace;" />
                </div>
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <button class="btn btn-outline" style="border: 1px solid #cbd5e1; padding: 0.75rem 1.5rem; border-radius: 8px; background: white; cursor: pointer;" @click="testModalOpen = false">Cancel</button>
                    <button class="btn btn-primary" style="background: #10b981; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer; font-weight: 600;" @click="submitTestCheckin">Submit</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';

const ready = ref(false);
const loading = ref(false);
const activeTab = ref('dashboard');
const logoutModalOpen = ref(false);
const isMobileMenuOpen = ref(false);
const eventHubTab = ref('active');

const alertModal = ref({
    isOpen: false,
    title: 'Notice',
    message: ''
});

function showAlert(message, title = 'Notice') {
    alertModal.value.message = message;
    alertModal.value.title = title;
    alertModal.value.isOpen = true;
}

const currentStudent = ref(null);
const events = ref([]);
const attendances = ref([]);
const successMessage = ref('');

const qrModalOpen = ref(false);
const qrError = ref(false);
const qrVideo = ref(null);
const currentEventForQr = ref(null);
const isVerifying = ref(false);
let qrStream = null;
let qrAnimationId = null;

// Test Mode State
const testModeEnabled = ref(false);
const testModalOpen = ref(false);
const testTokenInput = ref('');
const testEvent = ref(null);

function ensureExternalScript(src) {
    return new Promise((resolve, reject) => {
        if (document.querySelector(`script[src="${src}"]`)) return resolve();
        const el = document.createElement('script');
        el.src = src;
        el.async = true;
        el.onload = resolve;
        el.onerror = () => reject(new Error(`Failed to load ${src}`));
        document.head.appendChild(el);
    });
}

async function apiRequest(path, { method = 'GET', body } = {}) {
    const res = await fetch(path, {
        method,
        headers: {
            Accept: 'application/json',
            ...(body ? { 'Content-Type': 'application/json' } : {}),
        },
        body: body ? JSON.stringify(body) : undefined,
    });

    if (res.status === 204) return null;
    const data = await res.json().catch(() => null);
    if (!res.ok) throw new Error(data?.message || `Error ${res.status}`);
    return data;
}

function logoutStudent() {
    localStorage.removeItem('studentId');
    window.location.href = '/admin?mode=student';
}

async function loadStudentData(studentId) {
    try {
        const [student, eventsData, attendancesData] = await Promise.all([
            apiRequest(`/api/students/${encodeURIComponent(studentId)}`),
            apiRequest('/api/events'),
            apiRequest(`/api/attendances?student_id=${encodeURIComponent(studentId)}`),
        ]);

        currentStudent.value = student;
        events.value = Array.isArray(eventsData) ? eventsData : [];
        attendances.value = Array.isArray(attendancesData) ? attendancesData : [];
        ready.value = true;
    } catch {
        logoutStudent();
    }
}

// Filtered events for the student's year level
const filteredEvents = computed(() => {
    const studentYear = currentStudent.value?.year_level || '';
    return [...events.value].filter(ev => {
        if (!ev.target_year_level || ev.target_year_level === 'All') return true;
        return ev.target_year_level === studentYear;
    });
});

// Currently open events for check-in
const availableEvents = computed(() => {
    return filteredEvents.value.filter(ev => {
        const { now, start, end } = eventWindow(ev);
        const attended = attendanceForEvent(ev.id);
        return now >= start && now <= end && attended?.status !== 'present';
    });
});

// History of all events for this student's year
const historyEvents = computed(() => {
    return filteredEvents.value.map(ev => ({
        ...ev,
        attendance: attendanceForEvent(ev.id)
    })).sort((a, b) => new Date(b.date) - new Date(a.date));
});

const completedEvents = computed(() => filteredEvents.value.filter(ev => eventWindow(ev).end < new Date()));
const totalEvents = computed(() => completedEvents.value.length);
const eventsAttended = computed(() => completedEvents.value.filter(ev => attendanceForEvent(ev.id)?.status === 'present').length);
const eventsAbsent = computed(() => Math.max(0, totalEvents.value - eventsAttended.value));
const recentAttendance = computed(() => historyEvents.value.filter(row => row.attendance).slice(0, 5));
const upcomingEvents = computed(() => filteredEvents.value.filter(ev => {
    const { now, start } = eventWindow(ev);
    return start > now;
}).sort((a, b) => new Date(a.date) - new Date(b.date)).slice(0, 5));
const attendanceChartStyle = computed(() => ({
    '--present-angle': `${totalEvents.value ? eventsAttended.value / totalEvents.value * 100 : 0}%`,
}));
const isGoodAttendance = computed(() => totalEvents.value > 0 && eventsAttended.value / totalEvents.value >= 0.8);
const attendanceRate = computed(() => {
    if (totalEvents.value === 0) return '0%';
    return `${Math.round((eventsAttended.value / totalEvents.value) * 100)}%`;
});

const studentName = computed(() => currentStudent.value?.name || '');
const studentInitials = computed(() => {
    return studentName.value.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2) || 'ST';
});

function formatEventDate(dateStr) { return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }); }
function formatDay(dateStr) { return new Date(dateStr).toLocaleDateString('en-US', { day: '2-digit' }); }
function formatMonth(dateStr) { return new Date(dateStr).toLocaleDateString('en-US', { month: 'short' }); }
function formatCheckInTime(isoStr) { return isoStr ? new Date(isoStr).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) : '---'; }
function showEventDetails(ev) {
    const details = [formatEventDate(ev.date), formatEventTime(ev), ev.venue || 'Location not specified'].join(' · ');
    showAlert(details, ev.name || 'Event Details');
}
function formatDateTime(isoStr) { if (!isoStr) return '---'; return new Date(isoStr).toLocaleString('en-US', { hour: '2-digit', minute: '2-digit', month: 'short', day: 'numeric' }); }
function formatEventTime(ev) {
    if (!ev.starts_at) return 'All Day';
    const s = new Date(ev.starts_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    const e = new Date(ev.ends_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    return `${s} - ${e}`;
}

function attendanceForEvent(eventId) { return attendances.value.find(a => a?.event?.id === eventId); }

function eventWindow(ev) {
    const now = new Date();
    const start = ev.starts_at ? new Date(ev.starts_at) : new Date(new Date(ev.date).setHours(0, 0, 0, 0));
    const end = ev.ends_at ? new Date(ev.ends_at) : new Date(new Date(ev.date).setHours(23, 59, 59, 999));
    return { now, start, end };
}

async function markPresent(eventId, qrToken, lat, lng) {
    const studentId = localStorage.getItem('studentId');
    loading.value = true;
    try {
        await apiRequest('/api/attendances/verify-checkin', { 
            method: 'POST', 
            body: { 
                student_id: studentId, 
                event_id: eventId,
                qr_token: qrToken,
                student_lat: lat,
                student_lng: lng
            } 
        });
        successMessage.value = 'Attendance recorded successfully.';
        await loadStudentData(studentId);
        setTimeout(() => successMessage.value = '', 3000);
    } catch (e) {
        showAlert(e.message);
    } finally {
        loading.value = false;
    }
}

async function openQrScanner(eventId) {
    currentEventForQr.value = eventId;
    qrModalOpen.value = true;
    qrError.value = false;
    isVerifying.value = false;

    try {
        await ensureExternalScript('https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js');
    } catch { }

    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
        qrStream = stream;
        const video = qrVideo.value;
        if (!video) return;
        video.srcObject = stream;
        video.setAttribute('playsinline', 'true');
        await video.play();
        startQrLoop();
    } catch {
        qrError.value = true;
    }
}

function closeQrScanner() {
    qrModalOpen.value = false;
    if (qrAnimationId) cancelAnimationFrame(qrAnimationId);
    if (qrStream) qrStream.getTracks().forEach((t) => t.stop());
    qrStream = null;
}

async function handleQrImageUpload(event) {
    const file = event.target.files?.[0];
    event.target.value = '';
    if (!file) return;
    try {
        await ensureExternalScript('https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js');
        const bitmap = await createImageBitmap(file);
        const scale = Math.min(1, 1600 / Math.max(bitmap.width, bitmap.height));
        const canvas = document.createElement('canvas');
        canvas.width = Math.max(1, Math.round(bitmap.width * scale));
        canvas.height = Math.max(1, Math.round(bitmap.height * scale));
        const ctx = canvas.getContext('2d', { willReadFrequently: true });
        ctx.drawImage(bitmap, 0, 0, canvas.width, canvas.height);
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = window.jsQR?.(imageData.data, canvas.width, canvas.height, { inversionAttempts: 'attemptBoth' });
        if (!code?.data) { showAlert('QR code could not be detected. Use a clear, straight image with the full QR code visible.'); return; }
        await handleQrCode(String(code.data).trim());
    } catch { showAlert('The selected image could not be read. Please try a clearer QR image.'); }
}

function startQrLoop() {
    const video = qrVideo.value;
    if (!video) return;

    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');

    const tick = () => {
        if (!qrModalOpen.value || isVerifying.value) return;
        if (!video.readyState || video.readyState < 2) {
            qrAnimationId = requestAnimationFrame(tick);
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = window.jsQR?.(imageData.data, canvas.width, canvas.height, { inversionAttempts: 'attemptBoth' });
        
        if (code && code.data) {
            const raw = String(code.data).trim();
            handleQrCode(raw);
            return;
        }

        qrAnimationId = requestAnimationFrame(tick);
    };

    tick();
}

async function handleQrCode(token) {
    isVerifying.value = true;
    
    if (qrStream) {
        qrStream.getTracks().forEach((t) => t.stop());
    }

    try {
        const position = await new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                reject(new Error("Geolocation is not supported by your browser"));
            } else {
                navigator.geolocation.getCurrentPosition(resolve, reject, { enableHighAccuracy: true, timeout: 10000 });
            }
        });

        const lat = position.coords.latitude;
        const lng = position.coords.longitude;

        await markPresent(currentEventForQr.value, token, lat, lng);
        closeQrScanner();
    } catch (error) {
        let msg = "Could not verify location.";
        if (error.code === 1) msg = "Location permission denied. Please enable GPS.";
        else if (error.code === 2) msg = "Position unavailable. Please ensure GPS is working.";
        else if (error.code === 3) msg = "Location request timed out.";
        showAlert(msg);
        closeQrScanner();
    }
}

function openTestCheckin(ev) {
    testEvent.value = ev;
    testTokenInput.value = '';
    testModalOpen.value = true;
}

async function submitTestCheckin() {
    if (!testTokenInput.value.trim()) {
        showAlert('Please enter the token string from the Admin dashboard.');
        return;
    }
    testModalOpen.value = false;
    
    // Simulate GPS by sending the exact event coordinates so distance is 0
    const lat = testEvent.value.latitude || 0;
    const lng = testEvent.value.longitude || 0;
    
    await markPresent(testEvent.value.id, testTokenInput.value.trim(), lat, lng);
}

onMounted(() => {
    const studentId = localStorage.getItem('studentId');
    if (!studentId) { window.location.replace('/admin?mode=student'); return; }
    loadStudentData(studentId);
});
</script>

<style scoped>
/* ===========================================================
   DESIGN TOKENS
   =========================================================== */
:root {
    --navy:        #0f2044;
    --navy-mid:    #1e3a5f;
    --navy-light:  #2c5282;
    --blue:        #3b82f6;
    --blue-light:  #dbeafe;
    --green:       #10b981;
    --green-light: #d1fae5;
    --red:         #ef4444;
    --red-light:   #fee2e2;
    --amber:       #f59e0b;
    --amber-light: #fef3c7;
    --purple:      #8b5cf6;
    --purple-light:#ede9fe;
    --bg:          #f1f5f9;
    --surface:     #ffffff;
    --border:      #e2e8f0;
    --text:        #0f2044;
    --text-muted:  #64748b;
    --radius-sm:   10px;
    --radius-md:   14px;
    --radius-lg:   20px;
    --shadow-sm:   0 1px 4px rgba(15,32,68,.06);
    --shadow-md:   0 4px 16px rgba(15,32,68,.08);
    --shadow-lg:   0 8px 32px rgba(15,32,68,.10);
}

.mobile-only { display: none; }

/* ===========================================================
   LAYOUT
   =========================================================== */
.dashboard-wrapper {
    display: flex;
    min-height: 100vh;
    background-color: var(--bg);
    font-family: 'Inter', 'Outfit', ui-sans-serif, system-ui, sans-serif;
    color: var(--text);
}

/* ===========================================================
   SIDEBAR
   =========================================================== */
.sidebar {
    width: 270px;
    flex-shrink: 0;
    background: linear-gradient(165deg, #0f2044 0%, #1a3560 100%);
    color: white;
    display: flex;
    flex-direction: column;
    padding: 1.5rem 1.25rem;
    position: sticky;
    top: 0;
    height: 100vh;
    overflow-y: auto;
    border-right: 1px solid rgba(255,255,255,0.07);
    box-shadow: 4px 0 24px rgba(0,0,0,0.12);
}
.sidebar-close-row {
    display: none;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
}
.sidebar-brand-logo {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    font-weight: 700;
    color: rgba(255,255,255,0.85);
}
.sidebar-close-btn {
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 10px;
    color: white;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s;
}
.sidebar-close-btn:hover { background: rgba(255,255,255,0.15); }

.sidebar-header {
    text-align: center;
    margin-bottom: 1.5rem;
    padding-top: 0.5rem;
}
.avatar-glow-ring {
    position: relative;
    display: inline-block;
    margin-bottom: 1rem;
}
.avatar-glow-ring::before {
    content: '';
    position: absolute;
    inset: -4px;
    border-radius: 30px;
    background: linear-gradient(135deg, #3b82f6, #6366f1, #a855f7);
    z-index: 0;
    animation: ringPulse 3s ease-in-out infinite;
}
@keyframes ringPulse {
    0%, 100% { opacity: 0.6; transform: scale(1); }
    50%       { opacity: 1;   transform: scale(1.03); }
}
.avatar-wrapper {
    position: relative;
    width: 80px;
    height: 80px;
    z-index: 1;
}
.avatar-gradient {
    width: 100%;
    height: 100%;
    border-radius: 20px;
    background: linear-gradient(135deg, #3b82f6 0%, #6366f1 50%, #a855f7 100%);
    transform: rotate(6deg);
    box-shadow: 0 8px 20px rgba(59,130,246,0.3);
}
.avatar-text {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: 800;
    color: white;
    text-shadow: 0 2px 8px rgba(0,0,0,0.3);
}
.sidebar-user-info h2 {
    font-size: 1.05rem;
    font-weight: 700;
    margin-bottom: 0.3rem;
    color: white;
}
.student-id {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    font-size: 0.75rem;
    color: rgba(255,255,255,0.45);
    margin-bottom: 0.75rem;
    font-family: 'Courier New', monospace;
    letter-spacing: 0.05em;
}
.badge-row { display: flex; gap: 0.4rem; justify-content: center; flex-wrap: wrap; }
.badge {
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.03em;
    text-transform: uppercase;
}
.badge.course { background: rgba(99,102,241,0.25); color: #a5b4fc; border: 1px solid rgba(99,102,241,0.3); }
.badge.year   { background: rgba(59,130,246,0.2);  color: #93c5fd; border: 1px solid rgba(59,130,246,0.25); }

.sidebar-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
    margin: 0 0 1.25rem;
}
.sidebar-nav-label {
    font-size: 0.62rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    color: rgba(255,255,255,0.3);
    margin: 0 0.5rem 0.75rem;
    text-transform: uppercase;
}
.sidebar-nav { display: flex; flex-direction: column; gap: 0.35rem; }
.sidebar-nav a {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding: 0.75rem 0.9rem;
    border-radius: 12px;
    color: rgba(255,255,255,0.5);
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.2s;
    border: 1px solid transparent;
}
.sidebar-nav a:hover { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.85); }
.sidebar-nav a.active {
    background: linear-gradient(100deg, rgba(59,130,246,0.28), rgba(59,130,246,0.12));
    color: white;
    border-color: rgba(147,197,253,0.18);
    box-shadow: inset 3px 0 0 #60a5fa;
}
.sidebar-nav a.active .nav-icon-wrap { background: rgba(59,130,246,0.3); color: #93c5fd; }
.nav-icon-wrap {
    width: 34px; height: 34px;
    display: flex; align-items: center; justify-content: center;
    border-radius: 10px;
    background: rgba(255,255,255,0.07);
    flex-shrink: 0;
    transition: all 0.2s;
}
.nav-label { flex: 1; }
.nav-arrow { font-size: 1rem; color: rgba(255,255,255,0.2); transition: all 0.2s; }
.sidebar-nav a.active .nav-arrow { color: rgba(255,255,255,0.55); }
.sidebar-nav a:hover .nav-arrow  { color: rgba(255,255,255,0.4); transform: translateX(2px); }

.sidebar-footer { margin-top: 0.5rem; }
.sidebar-footer-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.08), transparent);
    margin-bottom: 1rem;
}
.logout-btn-premium {
    width: 100%;
    background: rgba(239,68,68,0.1);
    color: rgba(248,113,113,0.9);
    border: 1px solid rgba(239,68,68,0.2);
    padding: 0.75rem 0.9rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    text-align: left;
    display: flex;
    align-items: center;
    gap: 0.85rem;
    transition: all 0.2s;
}
.logout-btn-premium:hover {
    background: rgba(239,68,68,0.18);
    color: #fca5a5;
    border-color: rgba(239,68,68,0.35);
}
.logout-icon { background: rgba(239,68,68,0.15); }

/* ===========================================================
   MAIN CONTENT
   =========================================================== */
.main-content {
    flex: 1;
    min-width: 0;
    padding: clamp(1.25rem, 3vw, 2.5rem);
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
    overflow-y: auto;
    height: 100vh;
    box-sizing: border-box;
}
.mobile-header {
    display: none;
    align-items: center;
    gap: 0.75rem;
    background: #0f2044;
    padding: 0.9rem 1.25rem;
    color: white;
    margin: -1.25rem -1.25rem 1.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.08);
    position: sticky;
    top: 0;
    z-index: 20;
}
.mobile-header .brand { font-size: 1rem; font-weight: 700; color: white; }
.hamburger-btn { background: none; border: none; color: white; cursor: pointer; padding: 2px; border-radius: 4px; }
.sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 999; }
.sidebar-overlay.show { display: block; }

/* ===========================================================
   CONTENT HEADER
   =========================================================== */
.content-header {
    margin-bottom: 1.5rem;
    padding-bottom: 1.25rem;
    border-bottom: 1px solid var(--border);
}
.content-header h1 {
    font-size: clamp(1.5rem, 2.5vw, 2rem);
    font-weight: 800;
    color: var(--navy);
    letter-spacing: -0.03em;
    line-height: 1.15;
    margin-bottom: 0.35rem;
}
.content-header p { color: var(--text-muted); font-size: 0.9rem; }
.content-header p strong { color: var(--navy-mid); font-weight: 600; }

/* ===========================================================
   STAT CARDS
   =========================================================== */
.stats-grid-premium {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 1rem;
    margin-bottom: 1.25rem;
}
.stat-card-premium {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1.25rem 1.4rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: var(--shadow-sm);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    min-width: 0;
}
.stat-card-premium:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
.stat-icon {
    width: 46px; height: 46px;
    flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    border-radius: var(--radius-md);
}
.stat-icon.blue   { background: #eff6ff; color: #2563eb; }
.stat-icon.green  { background: #f0fdf4; color: #16a34a; }
.stat-icon.red    { background: #fff1f2; color: #c24141; }
.stat-icon.purple { background: #f5f3ff; color: #7c3aed; }
.stat-content { min-width: 0; }
.stat-content .label {
    display: block;
    font-size: 0.74rem;
    color: var(--text-muted);
    font-weight: 500;
    margin-bottom: 0.15rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.stat-content .value {
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--navy);
    letter-spacing: -0.03em;
    line-height: 1;
}

/* ===========================================================
   ATTENDANCE ALERT
   =========================================================== */
.alert-banner {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #fffbeb;
    border: 1px solid #fcd34d;
    border-left: 4px solid var(--amber);
    border-radius: var(--radius-md);
    padding: 0.85rem 1.1rem;
    margin-bottom: 1.25rem;
    font-size: 0.875rem;
    color: #78350f;
}
.alert-icon { font-size: 1.1rem; flex-shrink: 0; }

/* ===========================================================
   TWO-COLUMN MAIN SECTION
   =========================================================== */
.dashboard-two-col {
    display: grid;
    grid-template-columns: minmax(0, 1.2fr) minmax(280px, 0.8fr);
    gap: 1rem;
    align-items: start;
    margin-bottom: 1rem;
}

/* ===========================================================
   SECTION CARD (shared)
   =========================================================== */
.section-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1.4rem 1.5rem;
    box-shadow: var(--shadow-sm);
    min-width: 0;
    overflow: hidden;
}
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1rem;
}
.section-header h2 {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--navy);
    letter-spacing: -0.02em;
}
.section-caption { margin: 0.2rem 0 0; font-size: 0.78rem; color: var(--text-muted); }

/* ===========================================================
   EVENT HUB
   =========================================================== */
.event-hub-card { display: flex; flex-direction: column; }
.hub-tabs { display: flex; gap: 0.4rem; flex-wrap: wrap; }
.hub-tab {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.8rem;
    border: 1px solid var(--border);
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    background: var(--surface);
    color: var(--text-muted);
    transition: all 0.2s;
}
.hub-tab:hover { border-color: #93c5fd; color: var(--navy); }
.hub-tab.active { background: var(--blue); color: white; border-color: transparent; box-shadow: 0 2px 8px rgba(59,130,246,0.3); }
.hub-badge {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 18px; height: 18px; padding: 0 5px;
    border-radius: 10px; font-size: 0.68rem; font-weight: 700;
    background: rgba(255,255,255,0.25);
}
.hub-badge.neutral { background: rgba(100,116,139,0.12); color: var(--text-muted); }
.hub-tab.active .hub-badge { background: rgba(255,255,255,0.3); color: white; }

.hub-empty {
    display: flex; flex-direction: column; align-items: center; gap: 0.5rem;
    padding: 2rem 1rem; text-align: center; color: var(--text-muted); font-size: 0.85rem;
}
.hub-action-btn {
    margin-top: 0.25rem;
    padding: 0.45rem 1.1rem;
    border: none; border-radius: 20px;
    background: var(--blue); color: white;
    font-size: 0.78rem; font-weight: 600; cursor: pointer;
    transition: opacity 0.2s;
}
.hub-action-btn:hover { opacity: 0.85; }

.hub-event-row {
    display: flex; align-items: center; justify-content: space-between;
    gap: 0.75rem; padding: 0.65rem 0;
    border-bottom: 1px solid var(--border);
}
.hub-event-row:last-child { border-bottom: none; }
.hub-event-info { display: flex; flex-direction: column; gap: 0.15rem; min-width: 0; flex: 1 1 0; }
.hub-event-info strong {
    font-size: 0.875rem; color: var(--navy); font-weight: 600;
    text-transform: capitalize; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.hub-event-info span { font-size: 0.72rem; color: var(--text-muted); }

.hub-checkin-btn {
    display: inline-flex; align-items: center; gap: 0.4rem;
    flex-shrink: 0; padding: 0.45rem 0.9rem;
    border: none; border-radius: 20px;
    background: #059669; color: white;
    font-size: 0.75rem; font-weight: 700; cursor: pointer;
    box-shadow: 0 2px 8px rgba(5,150,105,0.25);
    transition: all 0.2s;
}
.hub-checkin-btn:hover { background: #047857; transform: translateY(-1px); }
.hub-checkin-btn:disabled { opacity: 0.6; cursor: wait; }

.details-btn {
    flex-shrink: 0; padding: 0.4rem 0.8rem;
    border: 1px solid var(--border); border-radius: 8px;
    background: var(--surface); color: var(--navy-mid);
    font-size: 0.76rem; font-weight: 600; cursor: pointer;
    transition: all 0.2s;
}
.details-btn:hover { background: #f8fafc; border-color: #93c5fd; }

.test-mode-toggle {
    display: flex; align-items: center; gap: 0.4rem;
    font-size: 0.74rem; color: var(--text-muted);
    cursor: pointer; margin-bottom: 0.6rem;
}

/* ===========================================================
   ATTENDANCE PROGRESS CARD
   =========================================================== */
.progress-card {}
.rate-header {
    display: flex; justify-content: space-between; align-items: flex-start;
    flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1rem;
}
.rate-value { font-size: 1.75rem; font-weight: 800; line-height: 1; letter-spacing: -0.04em; }
.rate-bar-track {
    height: 10px; background: var(--border);
    border-radius: 10px; overflow: hidden; margin-bottom: 0.75rem;
}
.rate-bar-fill {
    height: 100%; border-radius: 10px;
    transition: width 0.8s cubic-bezier(0.4,0,0.2,1);
    min-width: 4px;
}
.rate-legend { display: flex; gap: 1.25rem; font-size: 0.8rem; color: var(--text-muted); flex-wrap: wrap; }
.rate-legend span { display: flex; align-items: center; gap: 0.4rem; }
.legend-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.present-dot { background: #10b981; }
.absent-dot  { background: #d1d5db; }

/* ===========================================================
   RECENT ATTENDANCE - full width
   =========================================================== */
.recent-section-full {}

.text-link {
    border: 0; padding: 0.35rem 0; background: transparent;
    color: var(--blue); font-size: 0.82rem; font-weight: 600;
    cursor: pointer; white-space: nowrap; transition: color 0.2s;
}
.text-link:hover { color: #1d4ed8; }

.compact-empty {
    padding: 1.5rem 1rem; border-radius: var(--radius-sm);
    background: #f8fafc; color: var(--text-muted);
    font-size: 0.85rem; text-align: center;
    border: 1px dashed var(--border);
}

/* ===========================================================
   TABLES
   =========================================================== */
.recent-table-wrap,
.history-table-wrapper {
    overflow-x: auto;
    max-width: 100%;
    -webkit-overflow-scrolling: touch;
}
.history-table { width: 100%; border-collapse: collapse; min-width: 520px; }
.history-table th {
    text-align: left;
    padding: 0.75rem 1rem;
    font-size: 0.72rem; font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase; letter-spacing: 0.07em;
    background: #f8fafc;
    border-bottom: 1px solid var(--border);
}
.history-table th:first-child { border-radius: 8px 0 0 8px; }
.history-table th:last-child  { border-radius: 0 8px 8px 0; }
.history-table td {
    padding: 0.9rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    color: var(--text-muted);
    font-size: 0.875rem;
}
.history-table tbody tr:last-child td { border-bottom: none; }
.history-table tbody tr:hover { background: #f8fafc; }
.font-bold { font-weight: 700; color: var(--navy); }

.status-badge {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 80px; padding: 0.3rem 0.75rem;
    border-radius: 20px; font-size: 0.74rem; font-weight: 700;
    white-space: nowrap;
}
.status-badge.present { background: var(--green-light); color: #065f46; }
.status-badge.absent  { background: var(--red-light);   color: #991b1b; }

/* ===========================================================
   HISTORY TAB
   =========================================================== */
.empty-state { text-align: center; padding: 3rem 1rem; color: var(--text-muted); }

/* ===========================================================
   TOAST & MODALS
   =========================================================== */
.toast-notification {
    position: fixed; bottom: 2rem; right: 2rem;
    background: var(--navy); color: white;
    padding: 0.9rem 1.5rem; border-radius: var(--radius-md);
    box-shadow: var(--shadow-lg); z-index: 2000;
    font-weight: 600; font-size: 0.9rem;
}
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(12px); }

.qr-modal {
    position: fixed; inset: 0;
    background: rgba(15,32,68,0.55);
    backdrop-filter: blur(4px);
    display: flex; align-items: center; justify-content: center;
    z-index: 1000; padding: 1rem;
}
.qr-modal-inner {
    background: white; padding: 2rem;
    border-radius: var(--radius-lg);
    max-width: 400px; width: 100%;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border);
}
.spinner {
    width: 36px; height: 36px;
    border: 3px solid rgba(255,255,255,0.2);
    border-top: 3px solid #10b981;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
.qr-upload-btn {
    display: inline-flex; align-items: center; justify-content: center;
    padding: 0.7rem 1rem; border-radius: 8px;
    background: #059669; color: white;
    font-weight: 600; font-size: 0.875rem; cursor: pointer;
}
.qr-upload-btn:hover { background: #047857; }
.qr-help { margin: 0.9rem 0 0; color: var(--text-muted); font-size: 0.78rem; text-align: center; }

/* ===========================================================
   RESPONSIVE BREAKPOINTS
   =========================================================== */
@media (max-width: 1100px) {
    .sidebar { width: 245px; }
    .stats-grid-premium { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .dashboard-two-col { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .sidebar {
        position: fixed;
        top: 0; bottom: 0; left: 0;
        height: 100vh; height: 100dvh;
        width: min(84vw, 300px);
        z-index: 1000;
        transform: translateX(-100%);
        transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
        padding: 1rem;
        overflow-y: auto;
        overscroll-behavior: contain;
    }
    .sidebar.sidebar-open { transform: translateX(0); box-shadow: 6px 0 24px rgba(0,0,0,0.2); }
    .sidebar-close-row { display: flex; }
    .mobile-only { display: flex !important; }
    .sidebar-header { display: flex; align-items: center; text-align: left; gap: 0.85rem; padding: 0.75rem 0 1rem; margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
    .avatar-glow-ring { margin-bottom: 0; }
    .avatar-wrapper { width: 52px; height: 52px; }
    .avatar-text { font-size: 1.3rem; }
    .sidebar-user-info h2 { font-size: 0.9rem; }
    .student-id { justify-content: flex-start; }
    .badge-row { justify-content: flex-start; }
    .mobile-header { display: flex; }
    .main-content {
        height: auto; min-height: 100vh;
        max-width: none; width: 100%;
        overflow: visible;
        padding: 1.25rem 1rem;
    }
    .mobile-header { margin: -1.25rem -1rem 1.25rem; }
    .stats-grid-premium { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.65rem; }
    .stat-card-premium { padding: 1rem; }
    .stat-content .value { font-size: 1.35rem; }
    .dashboard-two-col { grid-template-columns: 1fr; gap: 0.75rem; }
    .history-table-wrapper, .recent-table-wrap { width: 100%; overflow-x: auto; }
    .recent-section-full { overflow: hidden; }
}

@media (max-width: 480px) {
    .main-content { padding: 1rem 0.75rem; }
    .mobile-header { margin: -1rem -0.75rem 1rem; padding: 0.75rem 1rem; }
    .content-header { margin-bottom: 1rem; padding-bottom: 1rem; }
    .content-header h1 { font-size: 1.35rem; }
    .stats-grid-premium { gap: 0.5rem; }
    .stat-card-premium { padding: 0.85rem 0.9rem; gap: 0.7rem; }
    .stat-icon { width: 38px; height: 38px; }
    .stat-icon svg { width: 18px; height: 18px; }
    .stat-content .label { font-size: 0.7rem; }
    .stat-content .value { font-size: 1.15rem; }
    .section-card { padding: 1rem; }
    .section-header h2 { font-size: 0.95rem; }
    .hub-tabs { width: 100%; }
    .hub-tab { flex: 1; justify-content: center; padding: 0.4rem 0.55rem; }
    .hub-event-row { flex-wrap: wrap; }
    .hub-event-info { flex-basis: 100%; }
    .hub-checkin-btn, .details-btn { min-height: 38px; }
    .rate-value { font-size: 1.4rem; }
    .alert-banner { align-items: flex-start; padding: 0.7rem; line-height: 1.45; font-size: 0.82rem; }
    .toast-notification { left: 0.75rem; right: 0.75rem; bottom: 0.75rem; padding: 0.75rem 1rem; font-size: 0.85rem; }
    .qr-modal { padding: 0.75rem; overflow-y: auto; }
    .qr-modal-inner { max-height: calc(100dvh - 1.5rem); overflow-y: auto; padding: 1.25rem; }
}

@media (max-width: 360px) {
    .stats-grid-premium { grid-template-columns: 1fr; }
    .stat-card-premium { flex-direction: row; align-items: center; }
}
</style>
