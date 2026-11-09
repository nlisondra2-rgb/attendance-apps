<template>
    <div v-if="ready" class="dashboard-wrapper">
        <!-- Sidebar Overlay (mobile) -->
        <div class="sidebar-overlay" :class="{ show: isMobileMenuOpen }" @click="isMobileMenuOpen = false"></div>

        <!-- Sidebar -->
        <aside class="sidebar" :class="{ 'sidebar-open': isMobileMenuOpen }">
            <!-- Mobile close button -->
            <div class="sidebar-mobile-close mobile-only">
                <span class="mobile-brand-title">SNSU Portal</span>
                <button class="sidebar-close-btn" @click="isMobileMenuOpen = false" aria-label="Close menu">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- University Brand -->
            <div class="sidebar-brand">
                <div class="brand-icon">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                </div>
                <div class="brand-text">
                    <span class="brand-name">SNSU</span>
                    <span class="brand-sub">Student Portal</span>
                </div>
            </div>

            <!-- Compact Student Profile -->
            <div class="student-profile-strip">
                <div class="student-avatar">{{ studentInitials }}</div>
                <div class="student-meta">
                    <span class="student-name" :title="studentName">{{ studentName }}</span>
                    <span class="student-id-text">{{ currentStudent?.student_id || '---' }}</span>
                    <span class="student-course-text">{{ currentStudent?.course }} <template v-if="currentStudent?.year_level">• {{ currentStudent?.year_level }}</template></span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="sidebar-nav">
                <a href="#" class="nav-link" :class="{ active: activeTab === 'dashboard' }" @click.prevent="activeTab = 'dashboard'; isMobileMenuOpen = false">
                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10"/></svg>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="nav-link" :class="{ active: activeTab === 'history' }" @click.prevent="activeTab = 'history'; isMobileMenuOpen = false">
                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 12h6M9 16h6"/></svg>
                    <span>Attendance History</span>
                </a>
                <a href="#" class="nav-link" :class="{ active: activeTab === 'fines' }" @click.prevent="activeTab = 'fines'; isMobileMenuOpen = false">
                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>My Fines</span>
                    <span v-if="outstandingBalance > 0" class="fine-badge">₱{{ Math.round(outstandingBalance) }}</span>
                </a>
            </nav>

            <!-- Bottom Sign Out (Anchored, fits viewport without scrolling) -->
            <div class="sidebar-bottom">
                <button class="logout-link" @click="logoutModalOpen = true">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Sign Out</span>
                </button>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <!-- Mobile Header Bar -->
            <header class="mobile-header">
                <button class="hamburger-btn" type="button" @click="isMobileMenuOpen = true" aria-label="Open menu">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <span class="mobile-header-title">SNSU Student Portal</span>
                <span class="mobile-student-initial">{{ studentInitials }}</span>
            </header>

            <!-- ========================================================
                 TAB 1: CLEAN HUMAN-STYLE DASHBOARD
                 ======================================================== -->
            <div v-if="activeTab === 'dashboard'" class="dashboard-body">
                <!-- Page Header -->
                <div class="dashboard-header">
                    <div>
                        <h1 class="page-title">Student Dashboard</h1>
                        <p class="page-subtitle">Welcome back, <strong>{{ studentName }}</strong>. Here is your official attendance standing and event schedule.</p>
                    </div>
                </div>

                <!-- 4 Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon-box blue">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="stat-details">
                            <span class="stat-label">Total Events</span>
                            <span class="stat-value">{{ summaryStats.total_events }}</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon-box green">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="stat-details">
                            <span class="stat-label">Attended</span>
                            <span class="stat-value">{{ summaryStats.events_attended }}</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon-box red">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="stat-details">
                            <span class="stat-label">Absent</span>
                            <span class="stat-value">{{ summaryStats.events_absent }}</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon-box purple">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                        <div class="stat-details">
                            <span class="stat-label">Attendance Rate</span>
                            <div class="stat-value-row">
                                <span class="stat-value" :class="isGoodStanding ? 'text-green' : 'text-red'">{{ numericAttendanceRate }}%</span>
                                <span class="stat-rate-sub" :class="isGoodStanding ? 'tag-green' : 'tag-amber'">
                                    {{ isGoodStanding ? 'Passing' : 'Below 80%' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Clean Official University Alerts (Only when applicable) -->
                <!-- Attendance Rate Warning -->
                <div v-if="summaryStats.is_rate_warning" class="clean-alert alert-warning">
                    <div class="alert-icon">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div class="alert-text">
                        <strong>Attendance Warning:</strong> Your current attendance rate is <strong>{{ numericAttendanceRate }}%</strong>, which is below the required 80% threshold. Please attend upcoming university events to maintain good standing.
                    </div>
                </div>

                <!-- Outstanding Fines Alert -->
                <div v-if="outstandingBalance > 0" class="clean-alert alert-danger">
                    <div class="alert-icon">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="alert-text">
                        <strong>Outstanding Balance:</strong> You have an unsettled penalty balance of <strong>₱{{ outstandingBalance.toFixed(2) }}</strong>. Settle at the University Cashier.
                        <a href="#" @click.prevent="activeTab = 'fines'" class="alert-link">View Fines →</a>
                    </div>
                </div>

                <!-- Two-Column Layout: Events (Left) + Attendance Overview (Right) -->
                <div class="dashboard-grid">
                    <!-- Left Column: Event Hub -->
                    <div class="content-box event-hub-box">
                        <div class="box-header">
                            <div>
                                <h2 class="box-title">University Events</h2>
                                <p class="box-subtitle">Official schedule & attendance verification</p>
                            </div>
                            <div class="tab-pill-group">
                                <button :class="['tab-pill', { active: eventHubTab === 'ongoing' }]" @click="eventHubTab = 'ongoing'">
                                    Ongoing ({{ categorizedEvents.ongoing.length }})
                                </button>
                                <button :class="['tab-pill', { active: eventHubTab === 'upcoming' }]" @click="eventHubTab = 'upcoming'">
                                    Upcoming ({{ categorizedEvents.upcoming.length }})
                                </button>
                                <button :class="['tab-pill', { active: eventHubTab === 'past' }]" @click="eventHubTab = 'past'">
                                    Past ({{ categorizedEvents.past.length }})
                                </button>
                            </div>
                        </div>

                        <!-- ONGOING TAB -->
                        <div v-if="eventHubTab === 'ongoing'">
                            <div v-if="categorizedEvents.ongoing.length === 0" class="empty-state-clean">
                                <svg width="32" height="32" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <p>No events are currently taking place right now.</p>
                                <button class="btn-clean-link" @click="eventHubTab = 'upcoming'">Check Upcoming Events →</button>
                            </div>

                            <div v-else class="event-rows">
                                <div v-for="ev in categorizedEvents.ongoing" :key="ev.id" class="event-row ongoing-row">
                                    <div class="event-row-main">
                                        <div class="event-name-line">
                                            <strong class="event-title">{{ ev.name }}</strong>
                                            <span class="badge-live">In Session</span>
                                        </div>
                                        <div class="event-meta-line">
                                            <span>{{ formatEventDate(ev.date) }} · {{ formatEventTime(ev) }}</span>
                                            <span v-if="ev.venue">· 📍 {{ ev.venue }}</span>
                                            <span v-if="ev.deadline" class="deadline-text">· Deadline: {{ formatTime(ev.deadline) }}</span>
                                        </div>
                                    </div>
                                    <div class="event-row-actions">
                                        <span v-if="ev.my_attendance?.status === 'present'" class="badge-status present">✓ Checked In</span>
                                        <button v-else-if="ev.is_checkin_open" class="btn-checkin" @click="testModeEnabled ? openTestCheckin(ev) : openQrScanner(ev.id)" :disabled="loading">
                                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                            Check In
                                        </button>
                                        <span v-else-if="ev.checkin_status === 'deadline_passed'" class="badge-status closed">Deadline Passed</span>
                                        <button class="btn-outline-sm" @click="showEventDetails(ev)">Details</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- UPCOMING TAB -->
                        <div v-if="eventHubTab === 'upcoming'">
                            <div v-if="categorizedEvents.upcoming.length === 0" class="empty-state-clean">
                                <p>No upcoming events scheduled for your program and year level.</p>
                            </div>
                            <div v-else class="event-rows">
                                <div v-for="ev in categorizedEvents.upcoming" :key="ev.id" class="event-row">
                                    <div class="event-row-main">
                                        <strong class="event-title">{{ ev.name }}</strong>
                                        <div class="event-meta-line">
                                            <span>{{ formatEventDate(ev.date) }} · {{ formatEventTime(ev) }}</span>
                                            <span v-if="ev.venue">· 📍 {{ ev.venue }}</span>
                                            <span v-if="ev.fine_amount > 0" class="fine-note">· Absence Fine: ₱{{ ev.fine_amount.toFixed(2) }}</span>
                                        </div>
                                    </div>
                                    <div class="event-row-actions">
                                        <span class="badge-status neutral">Scheduled</span>
                                        <button class="btn-outline-sm" @click="showEventDetails(ev)">Details</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PAST TAB -->
                        <div v-if="eventHubTab === 'past'">
                            <div v-if="categorizedEvents.past.length === 0" class="empty-state-clean">
                                <p>No past events recorded yet.</p>
                            </div>
                            <div v-else class="event-rows">
                                <div v-for="ev in categorizedEvents.past" :key="ev.id" class="event-row">
                                    <div class="event-row-main">
                                        <strong class="event-title">{{ ev.name }}</strong>
                                        <div class="event-meta-line">
                                            <span>{{ formatEventDate(ev.date) }} · {{ formatEventTime(ev) }}</span>
                                            <span v-if="ev.venue">· {{ ev.venue }}</span>
                                        </div>
                                    </div>
                                    <div class="event-row-actions">
                                        <span class="badge-status" :class="ev.my_attendance?.status || 'absent'">
                                            {{ ev.my_attendance?.status === 'present' ? '✓ Present' : (ev.my_attendance?.status === 'excused' ? 'Excused' : '✗ Absent') }}
                                        </span>
                                        <button class="btn-outline-sm" @click="showEventDetails(ev)">Details</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Attendance Overview -->
                    <div class="content-box attendance-overview-box">
                        <div class="box-header">
                            <div>
                                <h2 class="box-title">Attendance Overview</h2>
                                <p class="box-subtitle">Real-time completion metric</p>
                            </div>
                        </div>

                        <div class="rate-display-row">
                            <span class="rate-large" :class="isGoodStanding ? 'text-green' : 'text-red'">{{ numericAttendanceRate }}%</span>
                            <span class="rate-target-label">Requirement: <strong>80.0%</strong></span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="progress-bar-track">
                            <div class="progress-bar-fill" :class="isGoodStanding ? 'fill-green' : 'fill-red'" :style="{ width: attendanceProgressWidth + '%' }"></div>
                        </div>

                        <!-- Attendance Statistics Breakdown Table -->
                        <div class="breakdown-list">
                            <div class="breakdown-row">
                                <span class="breakdown-label"><i class="dot dot-green"></i> Attended Events</span>
                                <span class="breakdown-value">{{ summaryStats.events_attended }}</span>
                            </div>
                            <div class="breakdown-row">
                                <span class="breakdown-label"><i class="dot dot-red"></i> Absent Sessions</span>
                                <span class="breakdown-value">{{ summaryStats.events_absent }}</span>
                            </div>
                            <div class="breakdown-row" v-if="summaryStats.events_excused > 0">
                                <span class="breakdown-label"><i class="dot dot-purple"></i> Excused Absences</span>
                                <span class="breakdown-value">{{ summaryStats.events_excused }}</span>
                            </div>
                            <div class="breakdown-row total-row">
                                <span class="breakdown-label">Total Required Events</span>
                                <span class="breakdown-value">{{ summaryStats.total_events }}</span>
                            </div>
                        </div>

                        <div class="policy-note">
                            Students must maintain at least 80% attendance to secure academic clearance and avoid institutional fines.
                        </div>
                    </div>
                </div>

                <!-- Bottom Full-Width: Recent Attendance Records -->
                <div class="content-box">
                    <div class="box-header">
                        <div>
                            <h2 class="box-title">Recent Attendance Records</h2>
                            <p class="box-subtitle">Your latest logged event attendances</p>
                        </div>
                        <button class="btn-text-link" @click="activeTab = 'history'">View Full History →</button>
                    </div>

                    <div v-if="attendances.length > 0" class="table-container">
                        <table class="standard-table">
                            <thead>
                                <tr>
                                    <th>REF</th>
                                    <th>EVENT</th>
                                    <th>AMOUNT</th>
                                    <th>DATE</th>
                                    <th>STATUS</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="fine in studentFines" :key="fine.id">
                                    <td style="font-family: monospace; font-size: 0.82rem;">{{ fine.reference_no }}</td>
                                    <td><strong>{{ fine.event?.name || 'General Event' }}</strong></td>
                                    <td>₱{{ Number(fine.amount).toFixed(2) }}</td>
                                    <td>{{ formatEventDate(fine.issue_date) }}</td>
                                    <td>
                                        <span class="badge-status" :class="fine.payment_status === 'paid' ? 'present' : 'absent'">
                                            {{ fine.payment_status === 'paid' ? 'PAID' : 'UNPAID' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button v-if="fine.payment_status === 'paid'" class="btn-delete-sm" @click="deletePaidFine(fine)">
                                            Delete
                                        </button>
                                        <span v-else class="text-muted">—</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="empty-state-clean">
                        <p>No verified attendance records have been registered yet.</p>
                    </div>
                </div>
            </div>

            <!-- ========================================================
                 TAB 2: ATTENDANCE HISTORY
                 ======================================================== -->
            <div v-if="activeTab === 'history'" class="dashboard-body">
                <div class="dashboard-header">
                    <div>
                        <h1 class="page-title">Attendance History</h1>
                        <p class="page-subtitle">Historical archive of all scheduled events and your attendance status.</p>
                    </div>
                </div>

                <div class="content-box">
                    <div v-if="historyEvents.length === 0" class="empty-state-clean">
                        <p>No attendance records found yet.</p>
                    </div>
                    <div v-else class="table-container">
                        <table class="standard-table">
                            <thead>
                                <tr>
                                    <th>Event Name</th>
                                    <th>Date</th>
                                    <th>Venue</th>
                                    <th>Status</th>
                                    <th>Check-in Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="row in historyEvents" :key="row.id">
                                    <td><strong>{{ row.name }}</strong></td>
                                    <td>{{ formatEventDate(row.date) }}</td>
                                    <td>{{ row.venue || 'Campus Main' }}</td>
                                    <td>
                                        <span class="badge-status" :class="row.attendance?.status || 'absent'">
                                            {{ row.attendance?.status === 'present' ? '✓ Present' : (row.attendance?.status === 'excused' ? 'Excused' : '✗ Absent') }}
                                        </span>
                                    </td>
                                    <td>{{ formatDateTime(row.attendance?.checked_in_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ========================================================
                 TAB 3: MY FINES
                 ======================================================== -->
            <div v-if="activeTab === 'fines'" class="dashboard-body">
                <div class="dashboard-header">
                    <div>
                        <h1 class="page-title">My Fines</h1>
                        <p class="page-subtitle">Official assessment records for missed events.</p>
                    </div>
                </div>

                <!-- Clean Fine Summary Cards -->
                <div class="fine-summary-grid">
                    <div class="content-box fine-total-box">
                        <span class="fine-box-label">Outstanding Balance</span>
                        <div class="fine-box-amount">₱{{ outstandingBalance.toFixed(2) }}</div>
                        <p class="fine-box-sub">Payable amount for unexcused absences.</p>
                    </div>
                    <div class="content-box fine-guide-box">
                        <strong class="guide-title">Cashier Payment Instructions</strong>
                        <p class="guide-text">
                            Settle payments directly at the <strong>University Cashier</strong>. Present your <strong>Student ID</strong> and the corresponding <strong>Reference Number</strong>.
                        </p>
                    </div>
                </div>

                <div class="content-box" style="margin-top: 1rem;">
                    <div class="box-header">
                        <div>
                            <h2 class="box-title">Fine Assessments</h2>
                            <p class="box-subtitle">Official penalty records</p>
                        </div>
                    </div>

                    <div v-if="studentFines.length === 0" class="empty-state-clean">
                        <svg width="32" height="32" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p style="margin-top:0.5rem;font-weight:600;color:#0f172a;">No Fines on Record</p>
                        <p style="color:#64748b;font-size:0.85rem;">You have zero outstanding penalties.</p>
                    </div>

                    <div v-else class="table-container">
                        <table class="standard-table">
                            <thead>
                                <tr>
                                    <th>REF</th>
                                    <th>EVENT</th>
                                    <th>AMOUNT</th>
                                    <th>DATE</th>
                                    <th>STATUS</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="fine in studentFines" :key="fine.id">
                                    <td style="font-family: monospace; font-size: 0.82rem;">{{ fine.reference_no }}</td>
                                    <td><strong>{{ fine.event?.name || 'General Event' }}</strong></td>
                                    <td>₱{{ Number(fine.amount).toFixed(2) }}</td>
                                    <td>{{ formatEventDate(fine.issue_date) }}</td>
                                    <td>
                                        <span class="badge-status" :class="fine.payment_status === 'paid' ? 'present' : 'absent'">
                                            {{ fine.payment_status === 'paid' ? 'PAID' : 'UNPAID' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button v-if="fine.payment_status === 'paid'" class="btn-delete-sm" @click="deletePaidFine(fine)">
                                            Delete
                                        </button>
                                        <span v-else class="text-muted">—</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <!-- Notification Toast -->
        <Transition name="toast">
            <div v-if="successMessage" class="toast-box">
                {{ successMessage }}
            </div>
        </Transition>

        <!-- Event Details Modal -->
        <div v-if="eventDetailsModal.isOpen" class="clean-modal-backdrop" @click.self="eventDetailsModal.isOpen = false">
            <div class="clean-modal-dialog">
                <div class="modal-title-row">
                    <h3>{{ eventDetailsModal.event?.name }}</h3>
                    <button class="btn-close" @click="eventDetailsModal.isOpen = false">✕</button>
                </div>
                <p class="event-desc">{{ eventDetailsModal.event?.description || 'No additional description provided.' }}</p>
                <div class="event-details-grid">
                    <div><strong>Date:</strong> {{ formatEventDate(eventDetailsModal.event?.date) }}</div>
                    <div><strong>Time:</strong> {{ formatEventTime(eventDetailsModal.event) }}</div>
                    <div><strong>Venue:</strong> {{ eventDetailsModal.event?.venue || 'Campus Main' }}</div>
                    <div><strong>Check-in Deadline:</strong> {{ formatTime(eventDetailsModal.event?.deadline) }} ({{ eventDetailsModal.event?.grace_period_minutes || 0 }} min grace period)</div>
                    <div v-if="eventDetailsModal.event?.fine_amount > 0"><strong>Absence Fine:</strong> ₱{{ eventDetailsModal.event?.fine_amount.toFixed(2) }}</div>
                </div>
                <div class="modal-btn-row" style="margin-top: 1.25rem;">
                    <button class="btn-submit" @click="eventDetailsModal.isOpen = false">Close</button>
                </div>
            </div>
        </div>

        <!-- Logout Confirmation Modal -->
        <div v-if="logoutModalOpen" class="clean-modal-backdrop" @click.self="logoutModalOpen = false">
            <div class="clean-modal-dialog" style="max-width: 380px; text-align: center;">
                <h3 style="margin-bottom: 0.5rem; color: #0f172a;">Confirm Sign Out</h3>
                <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 1.25rem;">Are you sure you want to end your current session?</p>
                <div class="modal-btn-row" style="justify-content: center;">
                    <button class="btn-cancel" @click="logoutModalOpen = false">Cancel</button>
                    <button class="btn-danger" @click="logoutStudent">Sign Out</button>
                </div>
            </div>
        </div>

        <!-- Alert Modal -->
        <div v-if="alertModal.isOpen" class="clean-modal-backdrop">
            <div class="clean-modal-dialog" style="max-width: 400px; text-align: center;">
                <h3 style="margin-bottom: 0.5rem; color: #0f172a;">{{ alertModal.title }}</h3>
                <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 1.25rem;">{{ alertModal.message }}</p>
                <button class="btn-submit" style="width: 100%;" @click="alertModal.isOpen = false">OK</button>
            </div>
        </div>

        <!-- QR Scanner Modal -->
        <div v-if="qrModalOpen" class="clean-modal-backdrop">
            <div class="clean-modal-dialog" style="max-width: 440px;">
                <div class="modal-title-row">
                    <h3>Scan Event QR Code</h3>
                    <button class="btn-close" @click="closeQrScanner">✕</button>
                </div>
                <div class="scanner-frame">
                    <video ref="qrVideo" class="scanner-video" autoplay playsinline></video>
                    <div v-if="qrError" class="scanner-status-overlay">Camera unavailable. Please check permissions or upload an image.</div>
                    <div v-if="isVerifying" class="scanner-status-overlay">Verifying attendance and GPS location...</div>
                </div>
                <div class="scanner-btn-row">
                    <label class="btn-upload">Upload QR Image<input type="file" accept="image/*" @change="handleQrImageUpload" hidden /></label>
                    <button class="btn-cancel" @click="closeQrScanner">Cancel</button>
                </div>
                <p class="scanner-help-text">Scan the official event QR code displayed by the event coordinator.</p>
            </div>
        </div>

        <!-- Test Mode Check-in Modal -->
        <div v-if="testModalOpen" class="clean-modal-backdrop">
            <div class="clean-modal-dialog" style="max-width: 440px;">
                <div class="modal-title-row">
                    <h3>Developer Test Mode</h3>
                    <button class="btn-close" @click="testModalOpen = false">✕</button>
                </div>
                <div class="form-group">
                    <label>QR Token</label>
                    <input type="text" v-model="testTokenInput" class="form-input" placeholder="Paste event QR token string" />
                </div>
                <div class="modal-btn-row">
                    <button class="btn-cancel" @click="testModalOpen = false">Cancel</button>
                    <button class="btn-submit" @click="submitTestCheckin">Submit</button>
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
const eventHubTab = ref('ongoing');

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
const studentFines = ref([]);
const outstandingBalance = ref(0);
const successMessage = ref('');

const summaryStats = ref({
    total_events: 0,
    events_attended: 0,
    events_absent: 0,
    events_excused: 0,
    attendance_rate: '0%',
    is_rate_warning: false
});

const categorizedEvents = ref({
    upcoming: [],
    ongoing: [],
    past: [],
    all: []
});

const qrModalOpen = ref(false);
const qrError = ref(false);
const qrVideo = ref(null);
const currentEventForQr = ref(null);
const isVerifying = ref(false);
let qrStream = null;
let qrAnimationId = null;

// Developer Test Mode
const testModeEnabled = ref(false);
const testModalOpen = ref(false);
const testTokenInput = ref('');
const testEvent = ref(null);



// Event Details Modal State
const eventDetailsModal = ref({
    isOpen: false,
    event: null
});

// Computed properties
const numericAttendanceRate = computed(() => {
    const raw = summaryStats.value.attendance_rate;
    if (typeof raw === 'number') return Math.round(raw);
    const parsed = parseFloat(String(raw).replace('%', ''));
    return isNaN(parsed) ? 0 : Math.round(parsed);
});

const attendanceProgressWidth = computed(() => {
    return Math.min(100, Math.max(0, numericAttendanceRate.value));
});

const isGoodStanding = computed(() => {
    return numericAttendanceRate.value >= 80;
});

const isGoodAttendance = computed(() => {
    return !summaryStats.value.is_rate_warning;
});

const studentName = computed(() => currentStudent.value?.name || 'Student');
const studentInitials = computed(() => {
    return studentName.value.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2) || 'ST';
});

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
        const [student, attendancesData, summaryData, finesData, eventsCategorized] = await Promise.all([
            apiRequest(`/api/students/${encodeURIComponent(studentId)}`),
            apiRequest(`/api/attendances?student_id=${encodeURIComponent(studentId)}`),
            apiRequest(`/api/students/${encodeURIComponent(studentId)}/attendance-summary`),
            apiRequest(`/api/students/${encodeURIComponent(studentId)}/fines`),
            apiRequest(`/api/students/${encodeURIComponent(studentId)}/events`)
        ]);

        currentStudent.value = student;
        attendances.value = Array.isArray(attendancesData) ? attendancesData : [];

        if (summaryData) {
            summaryStats.value = summaryData;
        }

        if (finesData) {
            studentFines.value = finesData.fines || [];
            outstandingBalance.value = finesData.outstanding_balance || 0;
        }

        if (eventsCategorized) {
            categorizedEvents.value = {
                upcoming: eventsCategorized.upcoming || [],
                ongoing: eventsCategorized.ongoing || [],
                past: eventsCategorized.past || [],
                all: eventsCategorized.all || []
            };
        }

        ready.value = true;
    } catch {
        logoutStudent();
    }
}

// History of all events for this student
const historyEvents = computed(() => {
    return categorizedEvents.value.all.map(ev => ({
        ...ev,
        attendance: attendances.value.find(a => a?.event?.id === ev.id)
    })).sort((a, b) => new Date(b.date) - new Date(a.date));
});

function formatEventDate(dateStr) {
    if (!dateStr) return '---';
    return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}
function formatTime(isoStr) {
    if (!isoStr) return '---';
    return new Date(isoStr).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
}
function formatCheckInTime(isoStr) {
    return isoStr ? new Date(isoStr).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) : '---';
}
function formatDateTime(isoStr) {
    if (!isoStr) return '---';
    return new Date(isoStr).toLocaleString('en-US', { hour: '2-digit', minute: '2-digit', month: 'short', day: 'numeric' });
}
function formatEventTime(ev) {
    if (!ev || !ev.starts_at) return 'All Day';
    const s = new Date(ev.starts_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    const e = ev.ends_at ? new Date(ev.ends_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
    return e ? `${s} - ${e}` : s;
}

function showEventDetails(ev) {
    eventDetailsModal.value.event = ev;
    eventDetailsModal.value.isOpen = true;
}

async function deletePaidFine(fine) {
    if (!confirm(`Are you sure you want to remove the record for paid fine ${fine.reference_no}?`)) return;
    loading.value = true;
    try {
        const studentId = localStorage.getItem('studentId');
        await apiRequest(`/api/students/${encodeURIComponent(studentId)}/fines/${fine.id}`, {
            method: 'DELETE'
        });
        studentFines.value = studentFines.value.filter(f => f.id !== fine.id);
        successMessage.value = 'Paid fine record deleted successfully.';
        setTimeout(() => successMessage.value = '', 3000);
    } catch (e) {
        showAlert(e.message || 'Failed to delete paid fine record.');
    } finally {
        loading.value = false;
    }
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
        successMessage.value = 'Attendance successfully recorded.';
        await loadStudentData(studentId);
        setTimeout(() => successMessage.value = '', 4000);
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
        if (!code?.data) { showAlert('QR code could not be detected. Use a clear image with the full QR code visible.'); return; }
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
        const position = await new Promise((resolve) => {
            if (!navigator.geolocation) {
                resolve({ coords: { latitude: null, longitude: null } });
            } else {
                navigator.geolocation.getCurrentPosition(resolve, () => {
                    resolve({ coords: { latitude: null, longitude: null } });
                }, { enableHighAccuracy: true, timeout: 6000 });
            }
        });

        const lat = position.coords.latitude;
        const lng = position.coords.longitude;

        await markPresent(currentEventForQr.value, token, lat, lng);
        closeQrScanner();
    } catch (error) {
        showAlert(error.message || 'Could not process check-in.');
        closeQrScanner();
    }
}

function openTestCheckin(ev) {
    testEvent.value = ev;
    testTokenInput.value = ev.session_token || '';
    testModalOpen.value = true;
}

async function submitTestCheckin() {
    if (!testTokenInput.value.trim()) {
        showAlert('Please enter the token string from the Admin dashboard.');
        return;
    }
    testModalOpen.value = false;
    const lat = testEvent.value?.latitude || null;
    const lng = testEvent.value?.longitude || null;
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
   ROOT LAYOUT
   =========================================================== */
.dashboard-wrapper {
    display: flex;
    min-height: 100vh;
    background: #f8fafc;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    color: #1e293b;
}

/* ===========================================================
   SIDEBAR (Clean, Compact, ZERO SCROLLBAR)
   =========================================================== */
.sidebar {
    width: 240px;
    flex-shrink: 0;
    background: #0f172a; /* Solid deep professional slate */
    color: #f8fafc;
    display: flex;
    flex-direction: column;
    padding: 1rem 0.85rem;
    height: 100vh;
    position: sticky;
    top: 0;
    box-sizing: border-box;
    overflow-y: auto; /* Scrollable if viewport is small, ZERO visible scrollbar */
    scrollbar-width: none;
    -ms-overflow-style: none;
    border-right: 1px solid #1e293b;
}
.sidebar::-webkit-scrollbar {
    display: none;
}

.sidebar-mobile-close {
    display: none;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.75rem;
}
.mobile-brand-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #ffffff;
}
.sidebar-close-btn {
    background: transparent;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 4px;
}

/* University Brand */
.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    padding: 0.35rem 0.5rem 0.85rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}
.brand-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #2563eb;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.brand-text {
    display: flex;
    flex-direction: column;
    line-height: 1.2;
}
.brand-name {
    font-size: 1rem;
    font-weight: 800;
    letter-spacing: -0.02em;
    color: #ffffff;
}
.brand-sub {
    font-size: 0.7rem;
    color: #94a3b8;
}

/* Compact Student Profile Strip */
.student-profile-strip {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    padding: 0.75rem 0.5rem;
    margin: 0.65rem 0;
    background: rgba(255, 255, 255, 0.04);
    border-radius: 8px;
}
.student-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #1e293b;
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: #93c5fd;
    font-weight: 700;
    font-size: 0.82rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.student-meta {
    display: flex;
    flex-direction: column;
    min-width: 0;
    line-height: 1.25;
}
.student-name {
    font-size: 0.85rem;
    font-weight: 600;
    color: #ffffff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.student-id-text {
    font-size: 0.72rem;
    color: #94a3b8;
    font-family: ui-monospace, monospace;
}
.student-course-text {
    font-size: 0.68rem;
    color: #64748b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Navigation Links */
.sidebar-nav {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    margin-top: 0.25rem;
}
.nav-link {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    padding: 0.6rem 0.75rem;
    border-radius: 8px;
    color: #94a3b8;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.15s ease;
}
.nav-link:hover {
    background: rgba(255, 255, 255, 0.05);
    color: #f8fafc;
}
.nav-link.active {
    background: #1e293b;
    color: #ffffff;
    font-weight: 600;
}
.nav-link.active svg {
    color: #38bdf8;
}
.nav-link svg {
    flex-shrink: 0;
}
.fine-badge {
    margin-left: auto;
    background: #ef4444;
    color: #ffffff;
    font-size: 0.68rem;
    font-weight: 700;
    padding: 0.1rem 0.45rem;
    border-radius: 10px;
}

/* Sidebar Bottom (Sign Out) */
.sidebar-bottom {
    margin-top: auto;
    padding-top: 0.75rem;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
}
.logout-link {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    width: 100%;
    padding: 0.55rem 0.75rem;
    background: transparent;
    border: none;
    border-radius: 8px;
    color: #94a3b8;
    font-size: 0.82rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
}
.logout-link:hover {
    background: rgba(239, 68, 68, 0.12);
    color: #fca5a5;
}

/* ===========================================================
   MAIN CONTENT AREA
   =========================================================== */
.main-content {
    flex: 1;
    min-width: 0;
    padding: 1.5rem 2rem;
    max-width: 1300px;
    margin: 0 auto;
    box-sizing: border-box;
}

.dashboard-body {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

/* Mobile Header */
.mobile-header {
    display: none;
    align-items: center;
    justify-content: space-between;
    background: #0f172a;
    color: white;
    padding: 0.75rem 1rem;
    margin: -1.5rem -2rem 1.25rem;
}
.hamburger-btn {
    background: transparent;
    border: none;
    color: white;
    cursor: pointer;
    padding: 2px;
}
.mobile-header-title {
    font-weight: 700;
    font-size: 0.95rem;
}
.mobile-student-initial {
    width: 30px;
    height: 30px;
    background: #2563eb;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.8rem;
}

.sidebar-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 99;
}
.sidebar-overlay.show { display: block; }

/* ===========================================================
   DASHBOARD HEADER
   =========================================================== */
.dashboard-header {
    margin-bottom: 0.25rem;
}
.page-title {
    font-size: 1.45rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 0.25rem;
    letter-spacing: -0.02em;
}
.page-subtitle {
    font-size: 0.88rem;
    color: #64748b;
    margin: 0;
}

/* ===========================================================
   STATISTICS CARDS (Clean, Human Design)
   =========================================================== */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 1rem;
}
.stat-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 1.15rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}
.stat-icon-box {
    width: 42px;
    height: 42px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.stat-icon-box.blue   { background: #eff6ff; color: #2563eb; }
.stat-icon-box.green  { background: #f0fdf4; color: #16a34a; }
.stat-icon-box.red    { background: #fef2f2; color: #dc2626; }
.stat-icon-box.purple { background: #f5f3ff; color: #7c3aed; }

.stat-details {
    display: flex;
    flex-direction: column;
    min-width: 0;
}
.stat-label {
    font-size: 0.78rem;
    font-weight: 500;
    color: #64748b;
    margin-bottom: 0.2rem;
}
.stat-value {
    font-size: 1.65rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.1;
    letter-spacing: -0.02em;
}
.stat-value-row {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
}
.stat-rate-sub {
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.1rem 0.4rem;
    border-radius: 4px;
}
.tag-green { background: #dcfce7; color: #15803d; }
.tag-amber { background: #fef3c7; color: #b45309; }

.text-green { color: #16a34a; }
.text-red   { color: #dc2626; }

/* ===========================================================
   CLEAN OFFICIAL ALERTS
   =========================================================== */
.clean-alert {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.85rem 1rem;
    border-radius: 8px;
    font-size: 0.86rem;
    line-height: 1.45;
}
.clean-alert.alert-warning {
    background: #fffbeb;
    border: 1px solid #fde68a;
    color: #92400e;
}
.clean-alert.alert-danger {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
}
.alert-icon {
    flex-shrink: 0;
    margin-top: 1px;
}
.alert-text {
    flex: 1;
}
.alert-link {
    color: #b91c1c;
    font-weight: 600;
    text-decoration: underline;
    margin-left: 0.4rem;
}

/* ===========================================================
   TWO-COLUMN CONTENT BOXES
   =========================================================== */
.dashboard-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.4fr) minmax(320px, 0.85fr);
    gap: 1.25rem;
    align-items: start;
}

.content-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 1.25rem 1.4rem;
}

.box-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.65rem;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #f1f5f9;
}
.box-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}
.box-subtitle {
    font-size: 0.78rem;
    color: #64748b;
    margin: 0.15rem 0 0;
}

/* Tab Pills in Event Hub */
.tab-pill-group {
    display: flex;
    background: #f1f5f9;
    padding: 2px;
    border-radius: 6px;
    gap: 2px;
}
.tab-pill {
    padding: 0.35rem 0.7rem;
    border: none;
    background: transparent;
    border-radius: 5px;
    font-size: 0.76rem;
    font-weight: 500;
    color: #64748b;
    cursor: pointer;
    transition: all 0.15s;
}
.tab-pill:hover { color: #0f172a; }
.tab-pill.active {
    background: #ffffff;
    color: #0f172a;
    font-weight: 600;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

/* Event Rows */
.event-rows {
    display: flex;
    flex-direction: column;
    gap: 0.65rem;
}
.event-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 0.85rem;
    padding: 0.75rem 0.85rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
}
.event-row.ongoing-row {
    background: #ffffff;
    border-color: #cbd5e1;
}
.event-row-main {
    flex: 1;
    min-width: 0;
}
.event-name-line {
    display: flex;
    align-items: center;
    gap: 0.45rem;
}
.event-title {
    font-size: 0.88rem;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.badge-live {
    font-size: 0.65rem;
    font-weight: 700;
    background: #fee2e2;
    color: #b91c1c;
    padding: 0.1rem 0.4rem;
    border-radius: 4px;
}
.event-meta-line {
    font-size: 0.75rem;
    color: #64748b;
    margin-top: 0.2rem;
}
.deadline-text { color: #d97706; font-weight: 600; }
.fine-note { color: #dc2626; font-weight: 600; }

.event-row-actions {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    flex-shrink: 0;
}
.btn-checkin {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: #16a34a;
    color: white;
    border: none;
    padding: 0.45rem 0.85rem;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 600;
    cursor: pointer;
}
.btn-checkin:hover { background: #15803d; }
.btn-outline-sm {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #334155;
    padding: 0.4rem 0.7rem;
    border-radius: 6px;
    font-size: 0.76rem;
    font-weight: 500;
    cursor: pointer;
}
.btn-outline-sm:hover { background: #f8fafc; }

/* Status Badges */
.badge-status {
    font-size: 0.72rem;
    font-weight: 600;
    padding: 0.25rem 0.55rem;
    border-radius: 4px;
}
.badge-status.present { background: #dcfce7; color: #15803d; }
.badge-status.absent  { background: #fee2e2; color: #b91c1c; }
.badge-status.excused { background: #ede9fe; color: #6d28d9; }
.badge-status.closed  { background: #f1f5f9; color: #64748b; }
.badge-status.neutral { background: #f1f5f9; color: #475569; }
.badge-status.unpaid  { background: #fee2e2; color: #b91c1c; }
.badge-status.paid    { background: #dcfce7; color: #15803d; }

/* Clean Empty State */
.empty-state-clean {
    text-align: center;
    padding: 2rem 1rem;
    color: #64748b;
    font-size: 0.85rem;
}
.empty-state-clean p { margin: 0.4rem 0 0; }
.btn-clean-link {
    margin-top: 0.5rem;
    background: transparent;
    border: none;
    color: #2563eb;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
}

/* ===========================================================
   ATTENDANCE OVERVIEW BOX
   =========================================================== */
.rate-display-row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin-bottom: 0.5rem;
}
.rate-large {
    font-size: 2.25rem;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -0.03em;
}
.rate-target-label {
    font-size: 0.8rem;
    color: #64748b;
}

.progress-bar-track {
    height: 8px;
    background: #e2e8f0;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 1.15rem;
}
.progress-bar-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 0.4s ease;
}
.fill-green { background: #16a34a; }
.fill-red   { background: #dc2626; }

.breakdown-list {
    display: flex;
    flex-direction: column;
    gap: 0.45rem;
    font-size: 0.82rem;
}
.breakdown-row {
    display: flex;
    justify-content: space-between;
    color: #475569;
    padding-bottom: 0.35rem;
    border-bottom: 1px solid #f1f5f9;
}
.breakdown-row.total-row {
    border-bottom: none;
    font-weight: 600;
    color: #0f172a;
    padding-top: 0.25rem;
}
.breakdown-label {
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    display: inline-block;
}
.dot-green  { background: #16a34a; }
.dot-red    { background: #dc2626; }
.dot-purple { background: #7c3aed; }
.breakdown-value {
    font-weight: 600;
    color: #0f172a;
}
.policy-note {
    margin-top: 0.85rem;
    padding: 0.6rem 0.75rem;
    background: #f8fafc;
    border-radius: 6px;
    font-size: 0.74rem;
    color: #64748b;
    line-height: 1.4;
}

/* ===========================================================
   STANDARD TABLES
   =========================================================== */
.btn-text-link {
    background: transparent;
    border: none;
    color: #2563eb;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
}
.btn-text-link:hover { text-decoration: underline; }

.table-container {
    width: 100%;
    overflow-x: auto;
}
.standard-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.84rem;
    text-align: left;
}
.standard-table th {
    background: #f8fafc;
    color: #475569;
    font-weight: 600;
    padding: 0.65rem 0.85rem;
    border-bottom: 1px solid #e2e8f0;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}
.standard-table td {
    padding: 0.75rem 0.85rem;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
    vertical-align: middle;
}
.standard-table tr:hover td {
    background: #f8fafc;
}
.standard-table tr:last-child td {
    border-bottom: none;
}

/* ===========================================================
   FINES SECTION
   =========================================================== */
.fine-summary-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 2fr);
    gap: 1rem;
}
.fine-total-box {
    display: flex;
    flex-direction: column;
}
.fine-box-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
}
.fine-box-amount {
    font-size: 2rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0.25rem 0;
}
.fine-box-sub {
    font-size: 0.78rem;
    color: #64748b;
    margin: 0;
}
.fine-guide-box .guide-title {
    display: block;
    font-size: 0.9rem;
    color: #0f172a;
    margin-bottom: 0.35rem;
}
.fine-guide-box .guide-text {
    font-size: 0.82rem;
    color: #475569;
    line-height: 1.5;
    margin: 0;
}

/* ===========================================================
   MODALS
   =========================================================== */
.clean-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    z-index: 1000;
}
.clean-modal-dialog {
    background: #ffffff;
    border-radius: 10px;
    width: 100%;
    max-width: 480px;
    padding: 1.25rem 1.4rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}
.modal-title-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}
.modal-title-row h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 700;
    color: #0f172a;
}
.btn-close {
    background: transparent;
    border: none;
    color: #94a3b8;
    font-size: 1rem;
    cursor: pointer;
}
.modal-sub {
    font-size: 0.85rem;
    color: #64748b;
    margin-bottom: 1rem;
}
.form-group label {
    display: block;
    font-size: 0.8rem;
    font-weight: 600;
    color: #334155;
    margin-bottom: 0.35rem;
}
.form-textarea, .form-input {
    width: 100%;
    padding: 0.65rem;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 0.85rem;
    font-family: inherit;
    box-sizing: border-box;
}
.modal-btn-row {
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
    margin-top: 1rem;
}
.btn-delete-sm {
    background: #fee2e2;
    border: 1px solid #fca5a5;
    color: #b91c1c;
    padding: 0.25rem 0.65rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
}
.btn-delete-sm:hover {
    background: #ef4444;
    color: white;
}
.btn-cancel {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #475569;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.82rem;
    cursor: pointer;
}
.btn-submit {
    background: #2563eb;
    border: none;
    color: white;
    padding: 0.5rem 1.15rem;
    border-radius: 6px;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
}
.btn-danger {
    background: #dc2626;
    border: none;
    color: white;
    padding: 0.5rem 1.15rem;
    border-radius: 6px;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
}

/* Event Specs */
.event-desc { font-size: 0.85rem; color: #475569; margin-bottom: 0.75rem; }
.event-details-grid {
    background: #f8fafc;
    border-radius: 6px;
    padding: 0.75rem;
    font-size: 0.82rem;
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    color: #334155;
}

/* Scanner Frame */
.scanner-frame {
    width: 100%;
    aspect-ratio: 1;
    background: black;
    border-radius: 8px;
    overflow: hidden;
    position: relative;
    margin-bottom: 0.75rem;
}
.scanner-video { width: 100%; height: 100%; object-fit: cover; }
.scanner-status-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    text-align: center;
    font-size: 0.85rem;
}
.scanner-btn-row {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}
.btn-upload {
    background: #16a34a;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
}
.scanner-help-text {
    font-size: 0.75rem;
    color: #64748b;
    text-align: center;
    margin: 0;
}

/* Toast */
.toast-box {
    position: fixed;
    bottom: 1.5rem;
    right: 1.5rem;
    background: #0f172a;
    color: white;
    padding: 0.75rem 1.15rem;
    border-radius: 6px;
    font-size: 0.84rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 2000;
}

/* ===========================================================
   RESPONSIVE QUERIES
   =========================================================== */
@media (max-width: 1024px) {
    .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .dashboard-grid { grid-template-columns: 1fr; }
    .fine-summary-grid { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .sidebar {
        position: fixed;
        top: 0; bottom: 0; left: 0;
        height: 100vh;
        width: 250px;
        z-index: 1000;
        transform: translateX(-100%);
        transition: transform 0.25s ease;
    }
    .sidebar.sidebar-open { transform: translateX(0); }
    .sidebar-mobile-close { display: flex; }
    .mobile-header { display: flex; }
    .main-content { padding: 1rem; }
    .stats-grid { grid-template-columns: 1fr; }
    .event-row { flex-direction: column; align-items: flex-start; }
    .event-row-actions { width: 100%; justify-content: space-between; }
}
</style>
