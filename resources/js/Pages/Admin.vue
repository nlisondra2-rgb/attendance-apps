<template>
    <div>
        <!-- ===================== LOGIN PAGE ===================== -->
        <div v-if="!isAdminLoggedIn" id="loginPage">
            <TopBar />
            <div style="flex:1; display:flex; align-items:center; justify-content:center; padding: 2rem; min-height:calc(100vh - 56px);">
                <div class="login-box" :style="loginMode === 'student-register' ? 'max-width: 820px;' : ''">
                    <div v-if="loginMode !== 'student-register'" style="text-align:center; margin-bottom:1.5rem;">
                        <div style="width:52px;height:52px;background:#2563eb;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                            <svg width="26" height="26" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <h1>Attendance System</h1>
                        <p class="login-subtitle" v-if="loginMode === 'admin'">Sign in to your admin account</p>
                    </div>

                    <div v-if="loginMode === 'admin'" id="adminLoginForm">
                        <div style="display: flex; gap: 1rem; align-items: flex-end;">
                            <div style="flex: 1;">
                                <label class="form-label" style="display: block; margin-bottom: 0.25rem;">Email Address</label>
                                <input v-model.trim="adminEmail" type="email" placeholder="admin@school.edu" required @keydown.enter.prevent="login" style="margin-bottom: 0;" />
                            </div>
                            <div style="flex: 1;">
                                <label class="form-label" style="display: block; margin-bottom: 0.25rem;">Password</label>
                                <input v-model.trim="adminPassword" type="password" placeholder="••••••••" required @keydown.enter.prevent="login" style="margin-bottom: 0;" />
                            </div>
                        </div>
                        <button class="btn btn-primary" type="button" style="width:100%;justify-content:center;padding:.8rem;margin-top:1rem;" @click="login">Sign In</button>
                        <div style="margin-top:1.25rem;text-align:center;font-size:0.875rem;color:#64748b;">
                            Student? <a href="#" style="color:#2563eb;font-weight:600;" @click.prevent="setLoginMode('student')">Login as student</a>
                        </div>
                    </div>

                    <div v-else-if="loginMode === 'student'" id="studentLoginForm" class="student-login-admin">
                        <h1 class="student-login-title">Student Login</h1>
                        <p class="student-login-subtitle">Scan your QR code or enter your ID to access your dashboard.</p>
                        <div style="display: flex; gap: 1rem; margin-bottom: 0.75rem;">
                            <div class="form-group-admin" style="flex: 1; margin-bottom: 0;">
                                <label>Student ID</label>
                                <input v-model.trim="studentIdInput" type="text" placeholder="e.g. 2023-00500" class="blue-input-admin" style="margin-bottom: 0;" required />
                            </div>
                            <div class="form-group-admin" style="flex: 1; margin-bottom: 0;">
                                <label>Password</label>
                                <input v-model.trim="studentPasswordInput" type="password" placeholder="••••••••" class="blue-input-admin" style="margin-bottom: 0;" required @keydown.enter.prevent="loginStudentFromAdmin" />
                            </div>
                        </div>
                        <button class="btn-primary-admin" type="button" @click="loginStudentFromAdmin">Login to Dashboard</button>
                        <div class="divider-admin"><div class="line"></div><span>OR</span><div class="line"></div></div>
                        <button class="btn-emerald-admin" type="button" @click="openStudentQrScannerFromAdmin">
                            <span>📷</span> Scan QR Code to Login
                        </button>
                        <div class="back-link-admin" style="margin-top:0.5rem; margin-bottom: 0.25rem;">
                            <a href="#" @click.prevent="setLoginMode('student-register')" style="color:var(--blue); font-weight: 600;">Don't have an account? Register here</a>
                        </div>
                        <div class="back-link-admin">
                            <a href="#" @click.prevent="setLoginMode('admin')">Admin? Back to admin login</a>
                        </div>
                    </div>

                    <div v-else-if="loginMode === 'student-register'" id="publicRegisterForm" style="width:100%; max-width:800px; margin:auto;">
                        <div class="page-banner" style="margin-bottom: 1.5rem;">
                            <div class="page-banner-icon"><span>📝</span></div>
                            <div class="page-banner-text">
                                <h1>Register Student</h1>
                                <p>Add a new student to the system.</p>
                            </div>
                            <button type="button" @click="setLoginMode('student')" style="margin-left:auto; background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.3); color:white; padding:0.5rem 1rem; border-radius:var(--radius-sm); cursor:pointer; font-size:0.875rem; display:flex; align-items:center; gap:0.4rem; flex-shrink:0;">
                                ← Back to Login
                            </button>
                        </div>
                        <div class="card" style="margin-top:0;">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label" for="pubRegId">Student ID</label>
                                    <input id="pubRegId" v-model.trim="publicReg.student_id" type="text" class="form-input" placeholder="e.g., 2023-00500" required />
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="pubRegName">Full Name</label>
                                    <input id="pubRegName" v-model.trim="publicReg.name" type="text" class="form-input" placeholder="Juan Dela Cruz" required />
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="pubRegEmail">Email</label>
                                    <input id="pubRegEmail" v-model.trim="publicReg.email" type="email" class="form-input" placeholder="student@email.com" autocomplete="off" required />
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="pubRegCourse">Course</label>
                                    <select id="pubRegCourse" v-model="publicReg.course" class="form-select" required>
                                        <option value="">Select course</option>
                                        <option value="BSIS">BSIS</option>
                                        <option value="BSIT">BSIT</option>
                                        <option value="BSCS">BSCS</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="pubRegYear">Year Level</label>
                                    <select id="pubRegYear" v-model="publicReg.year_level" class="form-select" required>
                                        <option value="">Select year</option>
                                        <option value="1st Year">1st Year</option>
                                        <option value="2nd Year">2nd Year</option>
                                        <option value="3rd Year">3rd Year</option>
                                        <option value="4th Year">4th Year</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="pubRegPassword">Password</label>
                                    <input id="pubRegPassword" v-model.trim="publicReg.password" type="password" class="form-input" placeholder="Min 6 characters" autocomplete="new-password" required />
                                </div>
                                <div class="form-group" style="grid-column:1/-1;">
                                    <label class="form-label" for="pubRegQrImage">Upload ID Image with QR (optional)</label>
                                    <input id="pubRegQrImage" ref="publicRegQrInput" type="file" accept="image/*" class="form-input" />
                                    <small style="color:#94a3b8;font-size:0.8rem;margin-top:0.25rem;">We will read the QR from this image and store it for check-in.</small>
                                </div>
                            </div>
                            <div class="form-actions" style="margin-top: 1.5rem; justify-content: space-between;">
                                <a href="#" @click.prevent="setLoginMode('student')" style="color: var(--slate-500); text-decoration: none; font-size: 0.9rem;">&larr; Back to Login</a>
                                <button class="btn btn-primary" type="button" @click="publicRegisterStudent">
                                    + Register Student
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===================== CONFIRM MODAL ===================== -->
        <div v-if="confirmModal.isOpen" class="qr-modal">
            <div class="qr-modal-inner">
                <h3 style="margin-bottom: 1rem; color: var(--slate-900); font-weight: 700;">{{ confirmModal.title }}</h3>
                <p style="margin-bottom: 1.5rem; color: var(--slate-500); font-size: 0.95rem;">{{ confirmModal.message }}</p>
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <button class="btn" style="background: var(--slate-200); color: var(--slate-700);" @click="confirmModal.isOpen = false">Cancel</button>
                    <button class="btn btn-primary" style="background: var(--red); color: white; border: none;" @click="handleConfirm">OK</button>
                </div>
            </div>
        </div>

        <!-- ===================== ALERT MODAL ===================== -->
        <div v-if="alertModal.isOpen" class="qr-modal">
            <div class="qr-modal-inner">
                <h3 style="margin-bottom: 1rem; color: var(--slate-900); font-weight: 700;">{{ alertModal.title }}</h3>
                <p style="margin-bottom: 1.5rem; color: var(--slate-500); font-size: 0.95rem;">{{ alertModal.message }}</p>
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <button class="btn btn-primary" style="background: var(--navy); color: white; border: none; min-width: 100px;" @click="alertModal.isOpen = false">OK</button>
                </div>
            </div>
        </div>

        <!-- ===================== EVENT QR MODAL ===================== -->
        <div v-if="eventQrModal.isOpen" class="qr-modal">
            <div class="qr-modal-inner" style="max-width: 450px; text-align: center;">
                <h3 style="margin-bottom: 0.5rem; color: var(--slate-900); font-weight: 800; font-size: 1.5rem;">Attendance Open</h3>
                <p style="margin-bottom: 1.5rem; color: var(--slate-500);">Students can scan this QR code to check in.</p>
                
                <div style="background: white; padding: 1rem; border-radius: 12px; display: inline-block; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 1.5rem;">
                    <canvas ref="eventQrCanvas"></canvas>
                </div>

                <div style="margin-bottom: 1.5rem; font-family: monospace; font-size: 0.85rem; background: #f1f5f9; padding: 0.75rem; border-radius: 8px; color: #475569; word-break: break-all;">
                    {{ eventQrModal.token }}
                </div>
                
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <button class="btn btn-primary" style="width: 100%; justify-content: center;" @click="eventQrModal.isOpen = false">Close</button>
                </div>
            </div>
        </div>

        <!-- ===================== QR MODAL ===================== -->
        <div v-if="studentQrModalOpen" id="studentQrModal" class="qr-modal">
            <div class="qr-modal-inner">
                <h3 style="margin-bottom:1rem;font-weight:700;">Scan QR Code</h3>
                <video ref="studentQrVideo" autoplay playsinline class="qr-video"></video>
                <div v-if="studentQrError" class="qr-error">
                    Unable to access camera. Use http://localhost or HTTPS and allow camera access.
                </div>
                <button type="button" class="btn btn-outline" style="margin-top:1rem;width:100%;justify-content:center;" @click="closeStudentQrScanner">Close</button>
            </div>
        </div>

        <!-- ===================== ADMIN APP ===================== -->
        <div v-if="isAdminLoggedIn" id="mainApp" class="admin-layout">

            <!-- Sidebar Overlay (mobile) -->
            <div class="sidebar-overlay" :class="{ show: isMobileMenuOpen }" @click="isMobileMenuOpen = false"></div>

            <!-- ===== SIDEBAR ===== -->
            <aside class="admin-sidebar" :class="{ 'sidebar-open': isMobileMenuOpen }">
                <div class="sidebar-header">
                    <div class="sidebar-brand">
                        <div class="sidebar-brand-icon">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div class="sidebar-brand-text">
                            Attendance<span>Management System</span>
                        </div>
                    </div>
                    <button class="sidebar-close-btn" type="button" @click="isMobileMenuOpen = false">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <nav class="sidebar-nav">
                    <div class="sidebar-nav-label">Main Menu</div>
                    <a href="#" :class="{ active: activePage === 'dashboard' }" @click.prevent="showPage('dashboard'); isMobileMenuOpen = false">
                        <span class="nav-icon">📊</span> Dashboard
                    </a>
                    <a href="#" :class="{ active: activePage === 'analytics' }" @click.prevent="showPage('analytics'); isMobileMenuOpen = false">
                        <span class="nav-icon">📈</span> Analytics
                    </a>
                    <a href="#" :class="{ active: activePage === 'checkin' }" @click.prevent="showPage('checkin'); isMobileMenuOpen = false">
                        <span class="nav-icon">✅</span> Check-in
                    </a>
                    <div class="sidebar-nav-label" style="margin-top:0.5rem;">Students</div>
                    <a href="#" :class="{ active: activePage === 'participants' }" @click.prevent="showPage('participants'); isMobileMenuOpen = false">
                        <span class="nav-icon">👥</span> Participants
                    </a>
                    <a href="#" :class="{ active: activePage === 'students' }" @click.prevent="showPage('students'); isMobileMenuOpen = false">
                        <span class="nav-icon">🎓</span> Student List
                    </a>
                    <a href="#" :class="{ active: activePage === 'register' }" @click.prevent="showPage('register'); isMobileMenuOpen = false">
                        <span class="nav-icon">📝</span> Register
                    </a>
                    <div class="sidebar-nav-label" style="margin-top:0.5rem;">Schedule</div>
                    <a href="#" :class="{ active: activePage === 'events' }" @click.prevent="showPage('events'); isMobileMenuOpen = false">
                        <span class="nav-icon">📅</span> Events
                    </a>
                </nav>

                <div class="sidebar-footer">
                    <button id="logoutBtn" class="sidebar-logout" type="button" @click="confirmAction('Confirm Logout', 'Are you sure you want to log out?', logout)">
                        <span style="font-size:1rem;">🚪</span> Logout
                    </button>
                </div>
            </aside>

            <!-- ===== MAIN CONTENT ===== -->
            <div class="main-content">

                <!-- Mobile Header -->
                <header class="mobile-header">
                    <button class="hamburger-btn" type="button" @click="isMobileMenuOpen = true">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <span class="brand">Attendance System</span>
                </header>

                <div class="page-content">
                    <div class="container">

                        <!-- ============ DASHBOARD ============ -->
                        <div id="dashboard" class="page" :class="{ active: activePage === 'dashboard' }">
                            <div class="welcome-card">
                                <div>
                                    <h2>Welcome back, Admin! 👋</h2>
                                    <p>Here's what's happening with your attendance system today.</p>
                                </div>
                                <button class="btn btn-primary" type="button" style="flex-shrink:0;" @click="showPage('checkin')">✅ Start Check-in</button>
                            </div>

                            <div class="stats-grid">
                                <div class="stat-card">
                                    <div class="stat-icon blue">
                                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 14v6.5" /></svg>
                                    </div>
                                    <div class="stat-info"><h3>Total Students</h3><div class="number">{{ totalStudents }}</div></div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon green">
                                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </div>
                                    <div class="stat-info"><h3>Checked In</h3><div class="number">{{ checkedInCount }}</div></div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon purple">
                                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                                    </div>
                                    <div class="stat-info"><h3>BSIS</h3><div class="number">{{ bsisCount }}</div></div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon orange">
                                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                    </div>
                                    <div class="stat-info"><h3>Others</h3><div class="number">{{ otherCount }}</div></div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title"><span class="card-title-icon">📅</span> Upcoming Events</div>
                                    <button class="btn btn-primary btn-sm" type="button" @click="showPage('events')">Manage Events</button>
                                </div>
                                <div class="table-container">
                                    <table class="event-table">
                                        <thead><tr><th>Event Name</th><th>Date</th><th>Start Time</th><th>End Time</th><th>Description</th></tr></thead>
                                        <tbody>
                                            <tr v-for="ev in dashboardEvents" :key="ev.id">
                                                <td style="font-weight:600;color:#0f172a;">{{ ev.name }}</td>
                                                <td>{{ formatDate(ev.date) }}</td>
                                                <td>{{ formatTime(ev.starts_at) }}</td>
                                                <td>{{ formatTime(ev.ends_at) }}</td>
                                                <td>{{ ev.description || '—' }}</td>
                                            </tr>
                                            <tr v-if="!dashboardEvents.length">
                                                <td colspan="5" style="text-align:center;color:#94a3b8;padding:2rem;">No upcoming events.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title"><span class="card-title-icon">⚡</span> Quick Actions</div>
                                </div>
                                <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
                                    <button class="btn btn-primary" type="button" @click="showPage('checkin')">✅ Start Check-in</button>
                                    <button class="btn btn-outline" type="button" @click="showPage('register')">📝 Register Student</button>
                                    <button class="btn btn-outline" type="button" @click="showPage('events')">📅 Add Event</button>
                                </div>
                            </div>
                        </div>

                        <!-- ============ ANALYTICS ============ -->
                        <div id="analytics" class="page" :class="{ active: activePage === 'analytics' }">
                            <div class="page-banner">
                                <div class="page-banner-icon"><span>📈</span></div>
                                <div class="page-banner-text"><h1>Analytics Dashboard</h1><p>Track attendance trends and student engagement.</p></div>
                            </div>
                            <div class="stats-grid" style="margin-bottom:1.5rem;">
                                <div v-for="card in analytics.cards" :key="card.label" class="stat-card">
                                    <div class="stat-icon blue">📊</div>
                                    <div class="stat-info"><h3>{{ card.label }}</h3><div class="number">{{ card.value }}</div></div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header"><div class="card-title"><span class="card-title-icon">📊</span> Attendance by Event</div></div>
                                <apexchart type="bar" height="350" :options="eventChartOptions" :series="eventChartSeries"></apexchart>
                            </div>
                            <div class="card">
                                <div class="card-header"><div class="card-title"><span class="card-title-icon">🥧</span> Attendance by Year Level</div></div>
                                <apexchart type="pie" height="350" :options="yearChartOptions" :series="yearChartSeries"></apexchart>
                            </div>
                        </div>

                        <!-- ============ CHECK-IN ============ -->
                        <div id="checkin" class="page" :class="{ active: activePage === 'checkin' }">
                            <div class="page-banner">
                                <div class="page-banner-icon"><span>✅</span></div>
                                <div class="page-banner-text"><h1>Check-in Station</h1><p>Scan QR codes or enter a student ID to record attendance.</p></div>
                            </div>
                            <div class="card" style="max-width:600px;margin:0 auto;text-align:center;">
                                <div class="card-header" style="justify-content:center;"><div class="card-title"><span class="card-title-icon">📷</span> Scan / Enter Student ID</div></div>
                                <div id="reader"></div>
                                <div id="cameraError" style="color:#ef4444;margin:1rem 0;display:none;">Camera blocked. Use <strong>http://localhost</strong> or HTTPS.</div>
                                <div class="search-wrapper" style="margin-bottom:1rem;">
                                    <span class="search-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/></svg></span>
                                    <input v-model.trim="manualId" type="text" placeholder="Enter Student ID" class="search-box" />
                                </div>
                                <div style="display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap;">
                                    <button class="btn btn-primary" type="button" @click="manualCheckIn">✅ Check-in ID</button>
                                    <label for="uploadQR" style="cursor:pointer;" class="btn btn-outline">📁 Upload QR Image</label>
                                    <input id="uploadQR" type="file" accept="image/*" style="display:none;" @change="handleUploadQR" />
                                    <button class="btn btn-outline" type="button" @click="resetScanner">🔄 Reset</button>
                                </div>
                                <div id="result"></div>
                            </div>
                        </div>

                        <!-- ============ PARTICIPANTS ============ -->
                        <div id="participants" class="page" :class="{ active: activePage === 'participants' }">
                            <div class="page-banner">
                                <div class="page-banner-icon"><span>👥</span></div>
                                <div class="page-banner-text"><h1>Event Attendance</h1><p>Manage participants and track event attendance.</p></div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title"><span class="card-title-icon">👥</span> Participants</div>
                                    <div style="display:flex;gap:.75rem;align-items:center;flex-wrap:wrap;">
                                        <button class="btn btn-outline btn-sm" type="button" @click="printParticipants">🖨️ Print</button>
                                        <select class="filter-select"><option>All Participants</option></select>
                                    </div>
                                </div>
                                <div class="search-wrapper" style="margin-bottom:1rem;">
                                    <span class="search-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/></svg></span>
                                    <input v-model.trim="searchParticipant" type="text" placeholder="Search participant..." class="search-box" />
                                </div>
                                <div class="table-container">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>👤 Name</th>
                                                <th>✉️ Email</th>
                                                <th>🎓 Course</th>
                                                <th>🕐 Time</th>
                                                <th>⚙️ Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(row, index) in filteredParticipants" :key="row.attendanceId">
                                                <td style="color:#94a3b8;font-weight:600;">{{ index + 1 }}</td>
                                                <td>
                                                    <div class="td-name">
                                                        <div class="student-avatar" :class="row.name === 'Unknown' ? 'grey' : ''">
                                                            {{ row.name && row.name !== 'Unknown' ? row.name.slice(0,2).toUpperCase() : '?' }}
                                                        </div>
                                                        <span class="td-name-text">{{ row.name }}</span>
                                                    </div>
                                                </td>
                                                <td style="color:#64748b;">{{ row.email || '—' }}</td>
                                                <td><span v-if="row.course" class="badge badge-blue">{{ row.course }}</span><span v-else style="color:#94a3b8;">—</span></td>
                                                <td style="color:#64748b;font-size:0.82rem;">{{ row.time || '—' }}</td>
                                                <td>
                                                    <button class="table-btn delete" type="button" @click="deleteParticipant(row.attendanceId)">🗑️ Remove</button>
                                                </td>
                                            </tr>
                                            <tr v-if="!filteredParticipants.length">
                                                <td colspan="6" style="text-align:center;color:#94a3b8;padding:2.5rem;">No participants found.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="pagination-bar" v-if="filteredParticipants.length">
                                    <span class="pagination-info">Showing 1 – {{ filteredParticipants.length }} of {{ filteredParticipants.length }} participants</span>
                                    <div class="pagination-buttons">
                                        <button class="page-btn" disabled>‹</button>
                                        <button class="page-btn active">1</button>
                                        <button class="page-btn" disabled>›</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ============ STUDENT LIST ============ -->
                        <div id="students" class="page" :class="{ active: activePage === 'students' }">
                            <div class="page-banner">
                                <div class="page-banner-icon"><span>🎓</span></div>
                                <div class="page-banner-text"><h1>Student List</h1><p>View and manage all registered students.</p></div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title"><span class="card-title-icon">🎓</span> Registered Students</div>
                                    <button class="btn btn-primary btn-sm" type="button" @click="showPage('register')">+ Register Student</button>
                                </div>
                                <div class="search-wrapper" style="margin-bottom:1rem;">
                                    <span class="search-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/></svg></span>
                                    <input v-model.trim="searchStudent" type="text" placeholder="Search student..." class="search-box" />
                                </div>
                                <div class="table-container">
                                    <table>
                                        <thead>
                                            <tr><th>Student ID</th><th>Name</th><th>Email</th><th>Course</th><th>QR Code</th><th>Actions</th></tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="s in filteredStudents" :key="s.student_id">
                                                <td style="font-family:monospace;font-size:0.82rem;color:#64748b;">{{ s.student_id }}</td>
                                                <td>
                                                    <div class="td-name">
                                                        <div class="student-avatar">{{ (s.name || '?').slice(0,2).toUpperCase() }}</div>
                                                        <span class="td-name-text">{{ s.name }}</span>
                                                    </div>
                                                </td>
                                                <td style="color:#64748b;">{{ s.email }}</td>
                                                <td><span v-if="s.course" class="badge badge-blue">{{ s.course }}</span><span v-else style="color:#94a3b8;">—</span></td>
                                                <td>
                                                    <canvas v-if="s.school_qr_raw" class="qr-canvas" :ref="(el) => registerStudentQrCanvas(el, s.school_qr_raw)"></canvas>
                                                    <span v-else style="color:#94a3b8;font-size:0.8rem;">—</span>
                                                </td>
                                                <td>
                                                    <div class="table-actions">
                                                        <button class="table-btn edit" type="button" @click="editStudent(s.student_id)">✏️ Edit</button>
                                                        <button class="table-btn delete" type="button" @click="deleteStudent(s.student_id)">🗑️ Delete</button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr v-if="!filteredStudents.length">
                                                <td colspan="6" style="text-align:center;color:#94a3b8;padding:2.5rem;">No students found.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- ============ REGISTER ============ -->
                        <div id="register" class="page" :class="{ active: activePage === 'register' }">
                            <div class="page-banner">
                                <div class="page-banner-icon"><span>📝</span></div>
                                <div class="page-banner-text">
                                    <h1>{{ editingStudentId === null ? 'Register Student' : 'Edit Student' }}</h1>
                                    <p>{{ editingStudentId === null ? 'Add a new student to the system.' : 'Update the student record below.' }}</p>
                                </div>
                            </div>
                            <div class="card">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label class="form-label" for="regId">Student ID</label>
                                        <input id="regId" v-model.trim="reg.student_id" type="text" class="form-input" placeholder="e.g., 2023-00500" required :disabled="editingStudentId !== null" />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="regName">Full Name</label>
                                        <input id="regName" v-model.trim="reg.name" type="text" class="form-input" placeholder="Juan Dela Cruz" required />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="regEmail">Email</label>
                                        <input id="regEmail" v-model.trim="reg.email" type="email" class="form-input" placeholder="student@email.com" required />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="regCourse">Course</label>
                                        <select id="regCourse" v-model="reg.course" class="form-select" required>
                                            <option value="">Select course</option>
                                            <option value="BSIS">BSIS</option>
                                            <option value="BSIT">BSIT</option>
                                            <option value="BSCS">BSCS</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="regYear">Year Level</label>
                                        <select id="regYear" v-model="reg.year_level" class="form-select" required>
                                            <option value="">Select year</option>
                                            <option value="1st Year">1st Year</option>
                                            <option value="2nd Year">2nd Year</option>
                                            <option value="3rd Year">3rd Year</option>
                                            <option value="4th Year">4th Year</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="regPassword">Password {{ editingStudentId ? '(leave blank to keep current)' : '' }}</label>
                                        <input id="regPassword" v-model.trim="reg.password" type="password" class="form-input" placeholder="Min 6 characters" :required="!editingStudentId" />
                                    </div>
                                    <div class="form-group" style="grid-column:1/-1;">
                                        <label class="form-label" for="regQrImage">Upload ID Image with QR (optional)</label>
                                        <input id="regQrImage" ref="regQrInput" type="file" accept="image/*" class="form-input" />
                                        <small style="color:#94a3b8;font-size:0.8rem;margin-top:0.25rem;">We will read the QR from this image and store it for check-in.</small>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <button class="btn btn-outline" type="button" v-if="editingStudentId" @click="editingStudentId = null; reg.student_id=''; reg.name=''; reg.email=''; reg.course=''; reg.year_level=''; reg.password='';">Cancel</button>
                                    <button class="btn btn-primary" type="button" @click="registerStudent">
                                        {{ editingStudentId === null ? '+ Register Student' : '💾 Save Changes' }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- ============ EVENTS ============ -->
                        <div id="events" class="page" :class="{ active: activePage === 'events' }">
                            <div class="page-banner">
                                <div class="page-banner-icon"><span>📅</span></div>
                                <div class="page-banner-text"><h1>Events</h1><p>Create and manage attendance events for your students.</p></div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">
                                        <span class="card-title-icon">{{ editingEventId === null ? '➕' : '✏️' }}</span>
                                        {{ editingEventId === null ? 'Create Event' : 'Edit Event' }}
                                    </div>
                                </div>
                                <div class="form-grid" style="grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));">
                                    <div class="form-group">
                                        <label class="form-label">Event Name</label>
                                        <input v-model.trim="eventForm.name" type="text" class="form-input" placeholder="Event Name" required />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Date</label>
                                        <input v-model="eventForm.date" type="date" class="form-input" required />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Start Time</label>
                                        <input v-model="eventForm.starts_time" type="time" class="form-input" />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">End Time</label>
                                        <input v-model="eventForm.ends_time" type="time" class="form-input" />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Description</label>
                                        <input v-model.trim="eventForm.description" type="text" class="form-input" placeholder="Event description" />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Target Year Level</label>
                                        <select v-model="eventForm.target_year_level" class="form-select">
                                            <option value="All">All Years</option>
                                            <option value="1st Year">1st Year Only</option>
                                            <option value="2nd Year">2nd Year Only</option>
                                            <option value="3rd Year">3rd Year Only</option>
                                            <option value="4th Year">4th Year Only</option>
                                        </select>
                                    </div>
                                    <div class="form-group" style="grid-column: 1 / -1; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                                        <h4 style="font-size: 0.95rem; font-weight: 700; color: #0f1c3f; margin-bottom: 0.75rem;">Location & GPS (Optional)</h4>
                                    </div>
                                    <div class="form-group" style="grid-column: 1 / -1;">
                                        <label class="form-label">Venue Name</label>
                                        <input v-model.trim="eventForm.venue" type="text" class="form-input" placeholder="e.g. School Gymnasium" />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Latitude</label>
                                        <input v-model.number="eventForm.latitude" type="number" step="any" class="form-input" placeholder="e.g. 8.9475" />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Longitude</label>
                                        <input v-model.number="eventForm.longitude" type="number" step="any" class="form-input" placeholder="e.g. 125.5406" />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Allowed Radius (meters)</label>
                                        <input v-model.number="eventForm.radius_meters" type="number" class="form-input" placeholder="100" />
                                    </div>
                                    <div class="form-group" style="align-self: flex-end;">
                                        <a v-if="eventForm.latitude && eventForm.longitude" :href="`https://www.google.com/maps/search/?api=1&query=${eventForm.latitude},${eventForm.longitude}`" target="_blank" class="btn btn-outline btn-sm" style="width: 100%; justify-content: center;">🗺️ View on Map</a>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <button id="eventSubmitBtn" class="btn btn-primary" type="button" @click="onEventSubmit">
                                        {{ editingEventId === null ? '+ Create Event' : '💾 Update Event' }}
                                    </button>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title"><span class="card-title-icon">📋</span> Event List</div>
                                </div>
                                <div class="table-container">
                                    <table class="event-table">
                                        <thead><tr><th>Name</th><th>Date</th><th>Start Time</th><th>End Time</th><th>Actions</th></tr></thead>
                                        <tbody>
                                            <tr v-for="ev in events" :key="ev.id">
                                                <td style="font-weight:600;color:#0f172a;">
                                                    {{ ev.name }}
                                                    <div v-if="ev.venue" style="font-size: 0.75rem; color: #64748b; font-weight: 400;">📍 {{ ev.venue }}</div>
                                                </td>
                                                <td>{{ formatDate(ev.date) }}</td>
                                                <td>{{ formatTime(ev.starts_at) }}</td>
                                                <td>{{ formatTime(ev.ends_at) }}</td>
                                                <td>
                                                    <div class="table-actions" style="flex-wrap: wrap;">
                                                        <button v-if="activeSessions[ev.id]" class="btn btn-sm" style="background: #fee2e2; color: #991b1b; border: 1px solid #f87171;" type="button" @click="stopAttendanceSession(ev.id, activeSessions[ev.id].id)">⏹ Stop Attendance</button>
                                                        <button v-else class="btn btn-primary btn-sm" type="button" @click="startAttendanceSession(ev.id)">▶ Start Attendance</button>
                                                        
                                                        <button v-if="activeSessions[ev.id]" class="table-btn" type="button" @click="showSessionQr(ev.id)">📱 Show QR</button>
                                                        
                                                        <button class="table-btn edit" type="button" @click="editEvent(ev.id)">✏️ Edit</button>
                                                        <button class="table-btn delete" type="button" @click="deleteEvent(ev.id)">🗑️ Delete</button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr v-if="!events.length">
                                                <td colspan="6" style="text-align:center;color:#94a3b8;padding:2.5rem;">No events yet. Create one above.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div><!-- end container -->
                </div><!-- end page-content -->
            </div><!-- end main-content -->
        </div><!-- end admin-layout -->
    </div>
</template>
<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue';
import TopBar from '../Components/TopBar.vue';

const loginMode = ref('admin');
const adminEmail = ref('');
const adminPassword = ref('');
const studentIdInput = ref('');
const studentPasswordInput = ref('');

const isAdminLoggedIn = ref(false);
const activePage = ref('dashboard');
const isMobileMenuOpen = ref(false);

const confirmModal = reactive({
    isOpen: false,
    title: '',
    message: '',
    onConfirm: null
});

function confirmAction(title, message, callback) {
    confirmModal.title = title;
    confirmModal.message = message;
    confirmModal.onConfirm = callback;
    confirmModal.isOpen = true;
}

function handleConfirm() {
    if (confirmModal.onConfirm) confirmModal.onConfirm();
    confirmModal.isOpen = false;
}

const alertModal = reactive({
    isOpen: false,
    title: 'Notice',
    message: ''
});

function showAlert(message, title = 'Notice') {
    alertModal.message = message;
    alertModal.title = title;
    alertModal.isOpen = true;
}

const students = ref([]);
const events = ref([]);
const attendances = ref([]);

const editingStudentId = ref(null);
const reg = reactive({ student_id: '', name: '', email: '', course: '', year_level: '', password: '' });
const regQrInput = ref(null);

const publicReg = reactive({ student_id: '', name: '', email: '', course: '', year_level: '', password: '' });
const publicRegQrInput = ref(null);

const manualId = ref('');

const searchStudent = ref('');
const searchParticipant = ref('');

const editingEventId = ref(null);
const eventForm = reactive({ name: '', date: '', starts_time: '', ends_time: '', description: '', target_year_level: 'All', venue: '', latitude: null, longitude: null, radius_meters: 100 });

const activeSessions = ref({});
const eventQrCanvas = ref(null);
const eventQrModal = reactive({
    isOpen: false,
    token: ''
});

const analytics = reactive({
    cards: [],
    attendanceByEvent: [],
    attendanceByYearLevel: []
});

const eventChartOptions = computed(() => ({
    chart: { id: 'attendance-by-event' },
    xaxis: { categories: analytics.attendanceByEvent.map(e => e.name) },
    colors: ['#10b981']
}));

const eventChartSeries = computed(() => [{
    name: 'Participants',
    data: analytics.attendanceByEvent.map(e => e.count)
}]);

const yearChartOptions = computed(() => ({
    labels: analytics.attendanceByYearLevel.map(y => y.label),
    colors: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6']
}));

const yearChartSeries = computed(() => analytics.attendanceByYearLevel.map(y => y.count));

// Student QR scanner modal state
const studentQrModalOpen = ref(false);
const studentQrError = ref(false);
const studentQrVideo = ref(null);
let studentQrStream = null;
let studentQrAnimationId = null;

function ensureExternalScript(src) {
    return new Promise((resolve, reject) => {
        const existing = document.querySelector(`script[src="${src}"]`);
        if (existing) {
            if (existing.getAttribute('data-loaded') === '1') return resolve();
            existing.addEventListener('load', () => resolve());
            existing.addEventListener('error', () => reject(new Error(`Failed to load ${src}`)));
            return;
        }

        const el = document.createElement('script');
        el.src = src;
        el.async = true;
        el.addEventListener('load', () => {
            el.setAttribute('data-loaded', '1');
            resolve();
        });
        el.addEventListener('error', () => reject(new Error(`Failed to load ${src}`)));
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
    if (!res.ok) {
        const message = data?.message || `Request failed: ${res.status}`;
        throw new Error(message);
    }
    return data;
}

async function loadAll() {
    const [s, e, a, stats] = await Promise.all([
        apiRequest('/api/students'),
        apiRequest('/api/events'),
        apiRequest('/api/attendances'),
        apiRequest('/api/analytics')
    ]);
    students.value = Array.isArray(s) ? s : [];
    events.value = Array.isArray(e) ? e : [];
    attendances.value = Array.isArray(a) ? a : [];
    if (stats) {
        analytics.cards = stats.cards || [];
        analytics.attendanceByEvent = stats.attendanceByEvent || [];
        analytics.attendanceByYearLevel = stats.attendanceByYearLevel || [];
    }
    
    // Load active sessions for events
    activeSessions.value = {};
    if (events.value.length > 0) {
        await Promise.all(events.value.map(async (ev) => {
            try {
                const res = await apiRequest(`/api/attendance-sessions/active/${ev.id}`);
                if (res && res.active) {
                    activeSessions.value[ev.id] = res.session;
                }
            } catch (err) {
                console.error("Failed to fetch session for event", ev.id, err);
            }
        }));
    }
}

function setLoginMode(mode) {
    if (mode === 'student' || mode === 'student-register') {
        loginMode.value = mode;
        // Clear admin credentials when leaving admin mode
        adminEmail.value = '';
        adminPassword.value = '';
        // Reset public registration form when entering register mode
        if (mode === 'student-register') {
            publicReg.student_id = '';
            publicReg.name = '';
            publicReg.email = '';
            publicReg.course = '';
            publicReg.year_level = '';
            publicReg.password = '';
        }
    } else {
        loginMode.value = 'admin';
        // Clear student credentials when going back to admin
        studentIdInput.value = '';
        studentPasswordInput.value = '';
    }
}

async function login() {
    try {
        await apiRequest('/api/admin/login', {
            method: 'POST',
            body: { email: adminEmail.value, password: adminPassword.value },
        });
        localStorage.setItem('isAdminLoggedIn', '1');
        isAdminLoggedIn.value = true;
        await initAdminApp();
    } catch (e) {
        showAlert(e?.message || 'Invalid admin credentials.');
    }
}

function logout() {
    localStorage.removeItem('isAdminLoggedIn');
    isAdminLoggedIn.value = false;
    activePage.value = 'dashboard';
    adminEmail.value = '';
    adminPassword.value = '';
    studentIdInput.value = '';
    setLoginMode('admin');
}

async function loginStudentFromAdmin() {
    const studentId = studentIdInput.value.trim();
    const password = studentPasswordInput.value.trim();
    if (!studentId || !password) {
        showAlert('Please enter your Student ID and Password.');
        return;
    }

    try {
        const response = await apiRequest('/api/student/login', {
            method: 'POST',
            body: { student_id: studentId, password: password },
        });
        localStorage.setItem('studentId', response.student.student_id);
        window.location.href = '/student';
    } catch (e) {
        showAlert(e.message || 'Login failed.');
    }
}

function showPage(pageId) {
    activePage.value = pageId;
}

const totalStudents = computed(() => students.value.length);
const checkedInCount = computed(() => attendances.value.length);
const bsisCount = computed(() => students.value.filter((s) => s.course === 'BSIS').length);
const otherCount = computed(() => totalStudents.value - bsisCount.value);

const dashboardEvents = computed(() => {
    return [...events.value].sort((a, b) => new Date(a.date) - new Date(b.date));
});

function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString();
}

function formatTime(iso) {
    if (!iso) return '';
    const normalized = String(iso).replace(' ', 'T');
    const dt = new Date(normalized);
    if (Number.isNaN(dt.getTime())) return '';
    return dt.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

const filteredStudents = computed(() => {
    const search = searchStudent.value.toLowerCase();
    return students.value.filter((s) => {
        if (!search) return true;
        return (
            (s.student_id || '').toLowerCase().includes(search) ||
            (s.name || '').toLowerCase().includes(search) ||
            (s.email || '').toLowerCase().includes(search) ||
            (s.course || '').toLowerCase().includes(search)
        );
    });
});

const filteredParticipants = computed(() => {
    const search = searchParticipant.value.toLowerCase();
    const rows = attendances.value
        .map((a) => {
            const student = a.student || { student_id: '', name: 'Unknown', email: '', course: '' };
            const event = a.event || { name: '' };
            return {
                attendanceId: a.id,
                student_id: student.student_id,
                name: student.name,
                email: student.email,
                course: student.course,
                time: a.checked_in_at ? new Date(a.checked_in_at).toLocaleString() : '',
                event: event.name,
            };
        })
        .filter((r) => {
            if (!search) return true;
            return (
                (r.student_id || '').toLowerCase().includes(search) ||
                (r.name || '').toLowerCase().includes(search) ||
                (r.email || '').toLowerCase().includes(search) ||
                (r.course || '').toLowerCase().includes(search)
            );
        });
    return rows;
});

function registerStudentQrCanvas(el, raw) {
    if (!el || !raw) return;
    if (typeof window.QRious !== 'function') return;

    // Draw once
    // eslint-disable-next-line no-new
    new window.QRious({
        element: el,
        value: raw,
        size: 64,
    });
}

async function registerStudent() {
    const student_id = reg.student_id.trim();
    const name = reg.name.trim();
    const email = reg.email.trim();
    const course = reg.course;
    const year_level = reg.year_level;
    const password = reg.password;

    const qrFile = regQrInput.value?.files?.[0] || null;

    if (!student_id || !name || !email || !course || !year_level || (!editingStudentId.value && !password)) {
        showAlert('Please fill in all required fields (including password for new students).');
        return;
    }

    try {
        let school_qr_raw = null;
        if (qrFile) {
            school_qr_raw = await decodeQrFromFile(qrFile);
            if (!school_qr_raw) {
                showAlert('No QR code detected in the uploaded ID image. You can still register without it.');
            }
        }

        const basePayload = { student_id, name, email, course, year_level, password };
        const payload = school_qr_raw ? { ...basePayload, school_qr_raw } : basePayload;

        if (editingStudentId.value === null) {
            await apiRequest('/api/students', { method: 'POST', body: payload });
        } else {
            const updatePayload = { name, email, course, year_level };
            if (password) updatePayload.password = password;
            if (school_qr_raw) updatePayload.school_qr_raw = school_qr_raw;
            
            await apiRequest(`/api/students/${encodeURIComponent(editingStudentId.value)}`, {
                method: 'PUT',
                body: updatePayload,
            });
        }

        // reset form
        reg.student_id = '';
        reg.name = '';
        reg.email = '';
        reg.course = '';
        reg.year_level = '';
        reg.password = '';
        if (regQrInput.value) regQrInput.value.value = '';
        
        const wasEditing = editingStudentId.value !== null;
        editingStudentId.value = null;

        await refreshAllViews();
        showAlert('Saved successfully.');
        
        if (wasEditing) {
            showPage('students');
        }
    } catch (e) {
        showAlert(e.message);
    }
}

async function publicRegisterStudent() {
    const { student_id, name, email, course, year_level, password } = publicReg;
    if (!student_id || !name || !email || !course || !year_level || !password) {
        showAlert('Please fill in all required fields.');
        return;
    }

    let school_qr_raw = null;
    const file = publicRegQrInput.value?.files?.[0];
    if (file) {
        const decoded = await decodeQrFromFile(file);
        if (decoded) school_qr_raw = decoded;
        else showAlert('No QR code detected in the uploaded ID image. You can still register without it.');
    }

    const payload = { student_id, name, email, course, year_level, password };
    if (school_qr_raw) payload.school_qr_raw = school_qr_raw;

    try {
        await apiRequest('/api/students', { method: 'POST', body: payload });

        // Reset form
        publicReg.student_id = '';
        publicReg.name = '';
        publicReg.email = '';
        publicReg.course = '';
        publicReg.year_level = '';
        publicReg.password = '';
        if (publicRegQrInput.value) publicRegQrInput.value.value = '';

        showAlert('Registration successful! You can now log in.', 'Welcome!');
        setLoginMode('student');
    } catch (e) {
        showAlert(e.message || 'Registration failed. Please try again.');
    }
}

function editStudent(studentId) {
    const s = students.value.find((st) => st.student_id === studentId);
    if (!s) return;
    editingStudentId.value = studentId;
    showPage('register');
    reg.student_id = s.student_id;
    reg.name = s.name || '';
    reg.email = s.email || '';
    reg.course = s.course || '';
    reg.year_level = s.year_level || '';
}

function deleteStudent(studentId) {
    confirmAction('Delete Student', 'Delete this student?', async () => {
        try {
            await apiRequest(`/api/students/${encodeURIComponent(studentId)}`, { method: 'DELETE' });
            await refreshAllViews();
        } catch (e) {
            showAlert(e.message);
        }
    });
}

async function manualCheckIn() {
    const raw = manualId.value.trim();
    if (!raw) {
        showAlert('Enter a Student ID or scan a QR.');
        return;
    }
    await performCheckIn(raw);
}

async function performCheckIn(raw) {
    let student = students.value.find((s) => s.student_id === raw);

    if (!student) {
        try {
            const resolved = await apiRequest(`/api/student-by-qr?raw=${encodeURIComponent(raw)}`);
            if (resolved && resolved.student_id) student = resolved;
        } catch {
            // ignore
        }
    }

    if (!student || !student.student_id) {
        showAlert('Student not found.');
        return;
    }

    if (!events.value.length) {
        showAlert('No events configured.');
        return;
    }

    const targetEvent = pickBestEventForCheckIn(events.value);
    if (!targetEvent) {
        showAlert('No active event for check-in.');
        return;
    }

    try {
        await apiRequest('/api/attendances', {
            method: 'POST',
            body: { student_id: student.student_id, event_id: targetEvent.id },
        });
        await refreshAllViews();
        showAlert('Check-in successful.');
    } catch (e) {
        showAlert(e.message);
    }
}

function pickBestEventForCheckIn(allEvents) {
    const now = new Date();
    const list = Array.isArray(allEvents) ? allEvents : [];
    if (!list.length) return null;

    const normalizeIso = (value) => {
        if (!value) return null;
        return String(value).replace(' ', 'T');
    };

    const eventWindow = (ev) => {
        const start = ev.starts_at ? new Date(normalizeIso(ev.starts_at)) : new Date(new Date(ev.date).setHours(0, 0, 0, 0));
        const end = ev.ends_at ? new Date(normalizeIso(ev.ends_at)) : new Date(new Date(ev.date).setHours(23, 59, 59, 999));
        return { start, end };
    };

    const active = list
        .map((ev) => ({ ev, ...eventWindow(ev) }))
        .filter(({ start, end }) => now >= start && now <= end)
        .sort((a, b) => a.end - b.end);
    if (active.length) return active[0].ev;

    const todayStr = now.toISOString().split('T')[0];
    const todayEvent = list.find((e) => e.date === todayStr);
    if (todayEvent) return todayEvent;

    const sortedByDate = [...list].sort((a, b) => new Date(b.date) - new Date(a.date));
    return sortedByDate[0] || null;
}

async function handleUploadQR(event) {
    const file = event.target.files?.[0];
    if (!file) return;

    try {
        await ensureExternalScript('https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js');
    } catch {
        showAlert('Failed to load QR scanner library. Please check your internet connection.');
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        const img = new Image();
        img.onload = function () {
            const canvas = document.createElement('canvas');
            canvas.width = img.width;
            canvas.height = img.height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const code = window.jsQR?.(imageData.data, canvas.width, canvas.height);
            if (code) {
                const id = code.data.trim();
                manualId.value = id;
                performCheckIn(id);
            } else {
                showAlert('No QR code detected in the image.');
            }
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

async function decodeQrFromFile(file) {
    if (!file) return null;

    try {
        await ensureExternalScript('https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js');
    } catch {
        return null;
    }

    return new Promise((resolve) => {
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = new Image();
            img.onload = function () {
                const canvas = document.createElement('canvas');
                canvas.width = img.width;
                canvas.height = img.height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const code = window.jsQR?.(imageData.data, canvas.width, canvas.height, { inversionAttempts: 'attemptBoth' });
                if (code && code.data) resolve(String(code.data).trim());
                else resolve(null);
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
}

function resetScanner() {
    manualId.value = '';
}

function deleteParticipant(attendanceId) {
    confirmAction('Remove Participant', 'Remove this participant record?', async () => {
        try {
            await apiRequest(`/api/attendances/${attendanceId}`, { method: 'DELETE' });
            await refreshAllViews();
        } catch (e) {
            showAlert(e.message);
        }
    });
}

function printParticipants() {
    if (!attendances.value.length) {
        showAlert('No participants to print.');
        return;
    }

    const rows = attendances.value
        .map((a) => {
            const s = a.student || { student_id: '', name: 'Unknown', email: '', course: '' };
            const ev = a.event || { name: '' };
            const time = a.checked_in_at ? new Date(a.checked_in_at).toLocaleString() : '';
            return `${s.student_id}\t${s.name}\t${s.email}\t${s.course}\t${time}\t${ev.name}`;
        })
        .join('\n');

    const win = window.open('', '_blank');
    win.document.write('<pre>' + rows + '</pre>');
    win.document.close();
    win.print();
}

function combineDateAndTimeToIso(dateStr, timeStr) {
    if (!dateStr || !timeStr) return null;
    return `${dateStr}T${timeStr}`;
}

function isoToTimeValue(isoStr) {
    if (!isoStr) return '';
    const normalized = String(isoStr).replace(' ', 'T');
    const dt = new Date(normalized);
    if (Number.isNaN(dt.getTime())) return '';
    return `${String(dt.getHours()).padStart(2, '0')}:${String(dt.getMinutes()).padStart(2, '0')}`;
}

async function onEventSubmit() {
    const name = eventForm.name.trim();
    const date = eventForm.date;
    const description = eventForm.description.trim();
    const starts_at = eventForm.starts_time ? combineDateAndTimeToIso(date, eventForm.starts_time) : null;
    const ends_at = eventForm.ends_time ? combineDateAndTimeToIso(date, eventForm.ends_time) : null;
    const target_year_level = eventForm.target_year_level;

    if (!name || !date) {
        showAlert('Please provide at least a name and date for the event.');
        return;
    }

    try {
        if (editingEventId.value === null) {
            await apiRequest('/api/events', { method: 'POST', body: { name, date, starts_at, ends_at, description, target_year_level, venue: eventForm.venue, latitude: eventForm.latitude, longitude: eventForm.longitude, radius_meters: eventForm.radius_meters } });
        } else {
            await apiRequest(`/api/events/${editingEventId.value}`, {
                method: 'PUT',
                body: { name, date, starts_at, ends_at, description, target_year_level, venue: eventForm.venue, latitude: eventForm.latitude, longitude: eventForm.longitude, radius_meters: eventForm.radius_meters },
            });
        }

        eventForm.name = '';
        eventForm.date = '';
        eventForm.description = '';
        eventForm.starts_time = '';
        eventForm.ends_time = '';
        eventForm.target_year_level = 'All';
        eventForm.venue = '';
        eventForm.latitude = null;
        eventForm.longitude = null;
        eventForm.radius_meters = 100;
        editingEventId.value = null;

        await refreshAllViews();
    } catch (e) {
        showAlert(e.message);
    }
}

function editEvent(eventId) {
    const ev = events.value.find((e) => e.id === eventId);
    if (!ev) return;

    editingEventId.value = eventId;
    eventForm.name = ev.name;
    eventForm.date = ev.date;
    eventForm.description = ev.description || '';
    eventForm.starts_time = isoToTimeValue(ev.starts_at);
    eventForm.ends_time = isoToTimeValue(ev.ends_at);
    eventForm.target_year_level = ev.target_year_level || 'All';
    eventForm.venue = ev.venue || '';
    eventForm.latitude = ev.latitude || null;
    eventForm.longitude = ev.longitude || null;
    eventForm.radius_meters = ev.radius_meters || 100;
}

async function startAttendanceSession(eventId) {
    try {
        const res = await apiRequest('/api/attendance-sessions', {
            method: 'POST',
            body: { event_id: eventId }
        });
        activeSessions.value[eventId] = res;
        showSessionQr(eventId);
    } catch (e) {
        showAlert(e.message);
    }
}

function stopAttendanceSession(eventId, sessionId) {
    confirmAction('Stop Attendance', 'Are you sure you want to close attendance for this event?', async () => {
        try {
            await apiRequest(`/api/attendance-sessions/${sessionId}/stop`, { method: 'PATCH' });
            delete activeSessions.value[eventId];
            showAlert('Attendance session has been stopped.');
        } catch (e) {
            showAlert(e.message);
        }
    });
}

async function showSessionQr(eventId) {
    const session = activeSessions.value[eventId];
    if (!session) return;
    
    // We need the token. If it's not in the object (e.g. loaded from activeForEvent which doesn't return token for security reasons if we didn't want to, but actually I need the token!)
    // Wait, activeForEvent doesn't return the token in the controller! 
    // Let me check what activeForEvent returns... I didn't include the token there. Let me fix the controller or just make a new session.
    // Actually, I'll just update activeForEvent in AttendanceSessionController to return the token so admin can show it again.
    
    eventQrModal.token = session.token;
    eventQrModal.isOpen = true;
    
    await nextTick();
    
    if (eventQrCanvas.value && window.QRious) {
        new window.QRious({
            element: eventQrCanvas.value,
            value: session.token,
            size: 250,
            level: 'H'
        });
    } else {
        await ensureExternalScript('https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js');
        if (eventQrCanvas.value) {
            new window.QRious({
                element: eventQrCanvas.value,
                value: session.token,
                size: 250,
                level: 'H'
            });
        }
    }
}

function deleteEvent(eventId) {
    confirmAction('Delete Event', 'Delete this event?', async () => {
        try {
            await apiRequest(`/api/events/${eventId}`, { method: 'DELETE' });
            await refreshAllViews();
        } catch (e) {
            showAlert(e.message);
        }
    });
}

async function refreshAllViews() {
    await loadAll();
    await nextTick();
}

async function initAdminApp() {
    try {
        await refreshAllViews();
        showPage('dashboard');
    } catch (e) {
        showAlert(e.message);
    }
}

async function openStudentQrScannerFromAdmin() {
    studentQrModalOpen.value = true;
    studentQrError.value = false;

    try {
        await ensureExternalScript('https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js');
        await ensureExternalScript('https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js');
    } catch {
        // ignore; if it fails, scanning will fail gracefully
    }

    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
        studentQrStream = stream;
        const video = studentQrVideo.value;
        if (!video) return;
        video.srcObject = stream;
        video.setAttribute('playsinline', 'true');
        await video.play();
        startStudentQrLoop();
    } catch {
        studentQrError.value = true;
    }
}

function closeStudentQrScanner() {
    studentQrModalOpen.value = false;

    if (studentQrAnimationId) {
        cancelAnimationFrame(studentQrAnimationId);
        studentQrAnimationId = null;
    }
    if (studentQrStream) {
        studentQrStream.getTracks().forEach((t) => t.stop());
        studentQrStream = null;
    }
}

function startStudentQrLoop() {
    const video = studentQrVideo.value;
    if (!video) return;

    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');

    const tick = () => {
        if (!studentQrModalOpen.value) return;
        if (!video.readyState || video.readyState < 2) {
            studentQrAnimationId = requestAnimationFrame(tick);
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = window.jsQR?.(imageData.data, canvas.width, canvas.height);
        if (code && code.data) {
            const raw = String(code.data).trim();
            closeStudentQrScanner();
            loginStudentFromQr(raw);
            return;
        }

        studentQrAnimationId = requestAnimationFrame(tick);
    };

    tick();
}

function loginStudentFromQr(raw) {
    if (!raw) {
        showAlert('Invalid QR code.');
        return;
    }

    apiRequest(`/api/student-by-qr?raw=${encodeURIComponent(raw)}`)
        .then((student) => {
            if (!student || !student.student_id) throw new Error('Student record is missing student_id.');
            localStorage.setItem('studentId', student.student_id);
            window.location.href = '/student';
        })
        .catch((e) => {
            showAlert(e?.message || 'Student not found for this QR code.');
        });
}

watch(
    () => activePage.value,
    async (page) => {
        if (page === 'dashboard' || page === 'students' || page === 'participants' || page === 'events') {
            await nextTick();
        }
    },
);

onMounted(async () => {
    // Ensure QR libs are available for QR rendering/scanning
    try {
        await ensureExternalScript('https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js');
        await ensureExternalScript('https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js');
    } catch {
        // ignore
    }

    isAdminLoggedIn.value = localStorage.getItem('isAdminLoggedIn') === '1';

    const params = new URLSearchParams(window.location.search);
    const mode = params.get('mode');
    if (!isAdminLoggedIn.value && mode === 'student') {
        setLoginMode('student');
    }

    if (isAdminLoggedIn.value) {
        await initAdminApp();
    }
});

onBeforeUnmount(() => {
    closeStudentQrScanner();
});
</script>
