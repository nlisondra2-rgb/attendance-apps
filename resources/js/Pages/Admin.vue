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

        <!-- ===================== PARTICIPANTS MODAL ===================== -->
        <div v-if="participantsModal.isOpen" class="qr-modal" @click.self="participantsModal.isOpen = false">
            <div class="qr-modal-inner" style="max-width: 800px; max-height: 85vh; overflow-y: auto;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1rem;">
                    <div>
                        <h3 style="margin:0; font-weight:800; color:#0f1c3f;">{{ participantsModal.event?.name }} — Attendance</h3>
                        <p style="margin:0.25rem 0 0; color:#64748b; font-size:0.85rem;">
                            Eligible: {{ participantsModal.data?.total_eligible || 0 }} | 
                            Present: <strong style="color:#059669;">{{ participantsModal.data?.present_count || 0 }}</strong> | 
                            Absent: <strong style="color:#dc2626;">{{ participantsModal.data?.absent_count || 0 }}</strong> | 
                            Excused: <strong style="color:#7c3aed;">{{ participantsModal.data?.excused_count || 0 }}</strong> | 
                            Pending: {{ participantsModal.data?.pending_count || 0 }}
                        </p>
                    </div>
                    <div style="display:flex; gap:0.5rem; align-items:center;">
                        <button class="btn btn-primary btn-sm" type="button" @click="openPrintModal(participantsModal.event?.id)">🖨️ Print</button>
                        <button class="btn btn-outline btn-sm" @click="participantsModal.isOpen = false">✕ Close</button>
                    </div>
                </div>

                <div style="margin-bottom:1rem;">
                    <input v-model.trim="participantsModal.search" type="text" class="form-input" placeholder="Search student name or ID..." style="width:100%; padding:0.5rem 0.75rem;" />
                </div>

                <div class="table-container" style="max-height:400px; overflow-y:auto;">
                    <table class="event-table">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Course / Year</th>
                                <th>Status</th>
                                <th>Check-in Time</th>
                                <th>Correct Record</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="p in filteredModalParticipants" :key="p.id">
                                <td style="font-family:monospace;font-weight:700;">{{ p.student_id }}</td>
                                <td>{{ p.name }}</td>
                                <td>{{ p.course }} ({{ p.year_level }})</td>
                                <td>
                                    <span class="status-badge" :class="p.status">
                                        {{ p.status.toUpperCase() }}
                                    </span>
                                </td>
                                <td>{{ p.checked_in_at ? formatTime(p.checked_in_at) : '---' }}</td>
                                <td>
                                    <div class="table-actions" style="gap:0.3rem;">
                                        <button v-if="p.status !== 'present'" class="btn btn-sm" style="background:#d1fae5;color:#065f46;border:none;padding:0.2rem 0.5rem;font-size:0.75rem;" @click="correctAttendance(p, 'present')">Mark Present</button>
                                        <button v-if="p.status !== 'absent'" class="btn btn-sm" style="background:#fee2e2;color:#991b1b;border:none;padding:0.2rem 0.5rem;font-size:0.75rem;" @click="correctAttendance(p, 'absent')">Mark Absent</button>
                                        <button v-if="p.status !== 'excused'" class="btn btn-sm" style="background:#ede9fe;color:#5b21b6;border:none;padding:0.2rem 0.5rem;font-size:0.75rem;" @click="correctAttendance(p, 'excused')">Mark Excused</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ===================== RECORD PAYMENT MODAL ===================== -->
        <div v-if="paymentModal.isOpen" class="qr-modal" @click.self="paymentModal.isOpen = false">
            <div class="qr-modal-inner" style="max-width: 440px;">
                <h3 style="margin-bottom:0.5rem; font-weight:800; color:#0f1c3f;">Record Verified Payment</h3>
                <p style="margin-bottom:1.25rem; font-size:0.85rem; color:#64748b;">
                    Recording payment for <strong>{{ paymentModal.fine?.student?.name }}</strong> (Ref: {{ paymentModal.fine?.reference_no }}).
                </p>

                <div style="margin-bottom:1rem;">
                    <label class="form-label">Payment Amount (₱)</label>
                    <input v-model.number="paymentModal.amount" type="number" step="any" min="0.01" class="form-input" style="font-weight:700; font-size:1.1rem;" />
                    <small style="color:#64748b;">Remaining balance: ₱{{ Number(paymentModal.fine?.balance || 0).toFixed(2) }}</small>
                </div>

                <div style="margin-bottom:1.25rem;">
                    <label class="form-label">Official Receipt / Cashier Notes</label>
                    <input v-model.trim="paymentModal.notes" type="text" class="form-input" placeholder="e.g. Official Receipt #123456" />
                </div>

                <div style="display:flex; justify-content:flex-end; gap:0.75rem;">
                    <button class="btn btn-outline" @click="paymentModal.isOpen = false">Cancel</button>
                    <button class="btn btn-primary" style="background:#059669; border-color:#059669;" @click="submitPayment">Save Payment</button>
                </div>
            </div>
        </div>

        <!-- ===================== WAIVE FINE MODAL ===================== -->
        <div v-if="waiveModal.isOpen" class="qr-modal" @click.self="waiveModal.isOpen = false">
            <div class="qr-modal-inner" style="max-width: 440px;">
                <h3 style="margin-bottom:0.5rem; font-weight:800; color:#0f1c3f;">Waive Attendance Fine</h3>
                <p style="margin-bottom:1.25rem; font-size:0.85rem; color:#64748b;">
                    Waiving fine for <strong>{{ waiveModal.fine?.student?.name }}</strong> (Ref: {{ waiveModal.fine?.reference_no }}).
                </p>

                <div style="margin-bottom:1.25rem;">
                    <label class="form-label">Authorized Waiver Reason (Mandatory)</label>
                    <textarea v-model.trim="waiveModal.waiver_reason" rows="3" class="form-input" placeholder="State approved university authority or official reason for waiver..."></textarea>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:0.75rem;">
                    <button class="btn btn-outline" @click="waiveModal.isOpen = false">Cancel</button>
                    <button class="btn btn-primary" style="background:#7c3aed; border-color:#7c3aed;" @click="submitWaiver">Confirm Waiver</button>
                </div>
            </div>
        </div>

        <!-- ===================== AUDIT LOGS MODAL ===================== -->
        <div v-if="auditModal.isOpen" class="qr-modal" @click.self="auditModal.isOpen = false">
            <div class="qr-modal-inner" style="max-width: 520px;">
                <h3 style="margin-bottom:0.5rem; font-weight:800; color:#0f1c3f;">Fine Audit Trail</h3>
                <p style="margin-bottom:1.25rem; font-size:0.85rem; color:#64748b;">
                    History for Ref: <strong>{{ auditModal.fine?.reference_no }}</strong>
                </p>

                <div style="max-height:300px; overflow-y:auto; display:flex; flex-direction:column; gap:0.6rem;">
                    <div v-for="log in auditModal.fine?.audit_logs" :key="log.id" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:0.75rem; font-size:0.84rem;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:0.25rem;">
                            <strong style="color:#0f1c3f; text-transform:capitalize;">{{ log.action.replace('_', ' ') }}</strong>
                            <span style="color:#64748b; font-size:0.75rem;">{{ formatDate(log.created_at) }}</span>
                        </div>
                        <div style="color:#475569;">By: {{ log.performed_by_name || log.performed_by_type }}</div>
                        <div v-if="log.details" style="margin-top:0.35rem; font-size:0.78rem; color:#64748b; background:white; padding:0.4rem; border-radius:4px; font-family:monospace;">
                            {{ JSON.stringify(log.details) }}
                        </div>
                    </div>
                    <div v-if="!auditModal.fine?.audit_logs || auditModal.fine?.audit_logs.length === 0" style="text-align:center; color:#94a3b8; padding:1.5rem;">
                        No audit logs recorded for this fine.
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end; margin-top:1.25rem;">
                    <button class="btn btn-outline" @click="auditModal.isOpen = false">Close</button>
                </div>
            </div>
        </div>

        <!-- ===================== PRINT PARTICIPANTS MODAL & PREVIEW ===================== -->
        <div v-if="printModal.isOpen" class="print-modal-overlay">
            <!-- Top Controls Toolbar (Hidden in actual print dialog) -->
            <div class="print-modal-toolbar no-print">
                <div class="print-modal-title">
                    <span style="font-size:1.35rem;">🖨️</span>
                    <div>
                        <div style="font-weight:800; font-size:1.05rem; letter-spacing:0.2px;">Print Participants Report</div>
                        <div style="font-size:0.75rem; color:#94a3b8; font-weight:500;">A4 Standard Bond Paper · Portrait Orientation</div>
                    </div>
                </div>

                <div class="print-modal-controls">
                    <!-- Scope Switcher -->
                    <div class="print-segmented-btn">
                        <button type="button" :class="{ active: printModal.mode === 'selected' }" @click="printModal.mode = 'selected'">
                            📌 Print Selected Event
                        </button>
                        <button type="button" :class="{ active: printModal.mode === 'all' }" @click="printModal.mode = 'all'">
                            📚 Print All Events
                        </button>
                    </div>

                    <!-- Event Select Dropdown (Active in 'selected' mode) -->
                    <div v-if="printModal.mode === 'selected'" class="print-control-group">
                        <label>Event:</label>
                        <select v-model="printModal.selectedEventId" class="print-select-input">
                            <option v-for="ev in events" :key="ev.id" :value="ev.id">
                                {{ ev.name }} ({{ countEventParticipants(ev.id) }} participants)
                            </option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="print-control-group">
                        <label>Status:</label>
                        <select v-model="printModal.statusFilter" class="print-select-input">
                            <option value="all">All Attendees</option>
                            <option value="present">Present Only</option>
                            <option value="absent">Absent Only</option>
                        </select>
                    </div>

                    <!-- Sort Order -->
                    <div class="print-control-group">
                        <label>Sort By:</label>
                        <select v-model="printModal.sortBy" class="print-select-input">
                            <option value="last_name">Last Name (A-Z)</option>
                            <option value="name">First Name (A-Z)</option>
                            <option value="student_id">Student ID</option>
                        </select>
                    </div>
                </div>

                <div class="print-modal-actions">
                    <button class="btn btn-primary" type="button" @click="triggerPrint" style="padding:0.55rem 1.35rem; font-weight:700; display:flex; align-items:center; gap:0.4rem; box-shadow:0 4px 14px rgba(37,99,235,0.45);">
                        🖨️ Print / Save PDF
                    </button>
                    <button class="btn btn-outline" type="button" @click="printModal.isOpen = false" style="border-color:#475569; color:#f1f5f9;">
                        ✕ Close Preview
                    </button>
                </div>
            </div>

            <!-- Scrollable Document Canvas Preview -->
            <div class="print-modal-scroll" ref="printScrollArea">
                <div id="printReportContainer" class="print-report-container">
                    <!-- Event A4 Sheet (Repeated per event when printing All Events) -->
                    <div 
                        v-for="(grp, gIdx) in printableEventsData" 
                        :key="grp.event.id"
                        class="print-paper-sheet"
                    >
                        <!-- University Letterhead Header -->
                        <div class="print-univ-header">
                            <img :src="'/images/logo-left.png'" alt="SNSU Logo" class="print-univ-logo" />
                            <div class="print-univ-text">
                                <div class="print-univ-republic">Republic of the Philippines</div>
                                <div class="print-univ-name">SURIGAO DEL NORTE STATE UNIVERSITY</div>
                                <div class="print-univ-sub">Narciso Street, Surigao City, Surigao del Norte 8400 · snsu.edu.ph</div>
                                <div class="print-univ-office">OFFICE OF STUDENT AFFAIRS AND SERVICES</div>
                            </div>
                            <img :src="'/images/logo-right.png'" alt="Institutional Seal" class="print-univ-logo" />
                        </div>

                        <!-- Report Title -->
                        <div class="print-doc-title-bar">
                            <h2>Official Event Attendance Manifest</h2>
                            <p>Automated University Attendance & Event Management System</p>
                        </div>

                        <!-- Event Details Metadata Grid -->
                        <div class="print-event-meta-box">
                            <div class="print-meta-item">
                                <span class="print-meta-label">EVENT TITLE:</span>
                                <span class="print-meta-value" style="font-weight:700; color:#0f172a;">{{ grp.event.name }}</span>
                            </div>
                            <div class="print-meta-item">
                                <span class="print-meta-label">DATE:</span>
                                <span class="print-meta-value">{{ grp.formattedDate }}</span>
                            </div>
                            <div class="print-meta-item">
                                <span class="print-meta-label">SCHEDULE:</span>
                                <span class="print-meta-value">{{ grp.scheduleText || 'Scheduled Event Hours' }}</span>
                            </div>
                            <div class="print-meta-item">
                                <span class="print-meta-label">VENUE:</span>
                                <span class="print-meta-value">{{ grp.venue }}</span>
                            </div>
                            <div class="print-meta-item print-meta-full">
                                <span class="print-meta-label">PARTICIPANTS:</span>
                                <span class="print-meta-value" style="color:#1e3a8a; font-weight:700;">
                                    {{ grp.totalCount }} Total ({{ grp.presentCount }} Present, {{ grp.absentCount }} Absent)
                                </span>
                            </div>
                        </div>

                        <!-- Table with Repeating Headers for Multi-Page Tables -->
                        <table class="print-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%; text-align: center;">#</th>
                                    <th style="width: 17%;">Student ID</th>
                                    <th style="width: 32%;">Participant Name</th>
                                    <th style="width: 18%;">Course & Year</th>
                                    <th style="width: 13%; text-align: center;">Status / Time</th>
                                    <th style="width: 15%; text-align: center;">Signature</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(p, pIdx) in grp.participants" :key="p.id">
                                    <td class="text-center" style="font-weight: 600; color: #475569;">{{ pIdx + 1 }}</td>
                                    <td style="font-family: monospace; font-weight: 700; color: #0f172a;">{{ p.student_id }}</td>
                                    <td style="font-weight: 600; color: #0f172a;">{{ p.name }}</td>
                                    <td>{{ p.course_year }}</td>
                                    <td class="text-center">
                                        <span :class="p.status === 'present' ? 'status-text-present' : 'status-text-absent'">
                                            {{ p.status === 'present' ? (p.time || 'Present') : 'Absent' }}
                                        </span>
                                    </td>
                                    <td class="sig-cell">
                                        <div class="sig-line"></div>
                                    </td>
                                </tr>
                                <tr v-if="!grp.participants.length">
                                    <td colspan="6" class="text-center" style="padding: 2.5rem; color: #64748b; font-style: italic;">
                                        No participant records found for this event under the selected filter.
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Signatories & Document Meta Footer -->
                        <div class="print-bottom-block">
                            <div class="print-signatories">
                                <div class="print-sig-col">
                                    <p class="print-sig-role">Prepared by:</p>
                                    <div class="print-sig-underline"></div>
                                    <p class="print-sig-name">{{ printModal.preparedBy }}</p>
                                    <p class="print-sig-title">System Administrator / Event Facilitator</p>
                                </div>
                                <div class="print-sig-col">
                                    <p class="print-sig-role">Verified & Certified Correct:</p>
                                    <div class="print-sig-underline"></div>
                                    <p class="print-sig-name">{{ printModal.notedBy }}</p>
                                    <p class="print-sig-title">Office of Student Affairs and Services</p>
                                </div>
                            </div>

                            <div class="print-footer-meta">
                                <span>SNSU Attendance Management System · Official University Document</span>
                                <span>Generated: {{ currentPrintDateTime }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Empty state if no events matched -->
                    <div v-if="!printableEventsData.length" class="print-paper-sheet" style="text-align: center; padding: 4rem 2rem;">
                        <h3 style="color: #64748b; margin-bottom: 0.5rem;">No Events Selected or Available to Print</h3>
                        <p style="color: #94a3b8;">Please select an event with registered participants or choose 'Print All Events'.</p>
                    </div>
                </div>
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
                    <a href="#" :class="{ active: activePage === 'student-fines' }" @click.prevent="showPage('student-fines'); isMobileMenuOpen = false">
                        <span class="nav-icon">💰</span> Student Fines
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

                            <!-- Dashboard Header -->
                            <div class="dash-header">
                                <div class="dash-header-left">
                                    <div class="dash-greeting-line">
                                        <span class="dash-greeting-dot"></span>
                                        <span class="dash-greeting-label">Administration Panel</span>
                                    </div>
                                    <h1 class="dash-title">Good morning, Admin</h1>
                                    <p class="dash-subtitle">Here's an overview of your attendance system right now.</p>
                                </div>
                                <div class="dash-header-right">
                                    <button class="dash-primary-btn" type="button" @click="showPage('checkin')">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Start Check-in
                                    </button>
                                </div>
                            </div>

                            <!-- Metric Strip -->
                            <div class="metric-strip">
                                <div class="metric-item">
                                    <div class="metric-icon" style="background:#eff6ff;">
                                        <svg width="18" height="18" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <div class="metric-body">
                                        <span class="metric-value">{{ totalStudents }}</span>
                                        <span class="metric-label">Total Students</span>
                                    </div>
                                </div>
                                <div class="metric-divider"></div>
                                <div class="metric-item">
                                    <div class="metric-icon" style="background:#f0fdf4;">
                                        <svg width="18" height="18" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div class="metric-body">
                                        <span class="metric-value">{{ checkedInCount }}</span>
                                        <span class="metric-label">Checked In</span>
                                    </div>
                                </div>
                                <div class="metric-divider"></div>
                                <div class="metric-item">
                                    <div class="metric-icon" style="background:#faf5ff;">
                                        <svg width="18" height="18" fill="none" stroke="#9333ea" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    </div>
                                    <div class="metric-body">
                                        <span class="metric-value">{{ bsisCount }}</span>
                                        <span class="metric-label">BSIS Students</span>
                                    </div>
                                </div>
                                <div class="metric-divider"></div>
                                <div class="metric-item">
                                    <div class="metric-icon" style="background:#fff7ed;">
                                        <svg width="18" height="18" fill="none" stroke="#ea580c" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <div class="metric-body">
                                        <span class="metric-value">{{ otherCount }}</span>
                                        <span class="metric-label">Other Courses</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Main Grid: Events + Sidebar -->
                            <div class="dash-main-grid">

                                <!-- Upcoming Events Panel -->
                                <div class="dash-panel">
                                    <div class="dash-panel-header">
                                        <div class="dash-panel-title">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                            Upcoming Events
                                        </div>
                                        <button class="dash-link-btn" type="button" @click="showPage('events')">
                                            Manage all
                                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                        </button>
                                    </div>
                                    <div v-if="!dashboardEvents.length" class="dash-empty">
                                        <svg width="36" height="36" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                        <p>No events scheduled yet</p>
                                        <button class="dash-link-btn" type="button" @click="showPage('events')" style="margin-top:0.25rem;">Create one →</button>
                                    </div>
                                    <div v-else class="event-list">
                                        <div v-for="(ev, i) in dashboardEvents.slice(0, 6)" :key="ev.id" class="event-list-item">
                                            <div class="event-list-index">{{ String(i + 1).padStart(2, '0') }}</div>
                                            <div class="event-list-body">
                                                <span class="event-list-name">{{ ev.name }}</span>
                                                <span class="event-list-meta">
                                                    {{ formatDate(ev.date) }}
                                                    <span v-if="ev.starts_at"> · {{ formatTime(ev.starts_at) }}</span>
                                                    <span v-if="ev.venue"> · 📍 {{ ev.venue }}</span>
                                                </span>
                                            </div>
                                            <div class="event-list-time">{{ formatTime(ev.ends_at) }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="dash-side-col">

                                    <!-- Quick Actions -->
                                    <div class="dash-panel">
                                        <div class="dash-panel-header">
                                            <div class="dash-panel-title">
                                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                                                Quick Actions
                                            </div>
                                        </div>
                                        <div class="quick-actions-grid">
                                            <button class="qa-btn qa-btn-primary" type="button" @click="showPage('checkin')">
                                                <span class="qa-icon">
                                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </span>
                                                <span class="qa-label">Start Check-in</span>
                                            </button>
                                            <button class="qa-btn" type="button" @click="showPage('register')">
                                                <span class="qa-icon">
                                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                                </span>
                                                <span class="qa-label">Register Student</span>
                                            </button>
                                            <button class="qa-btn" type="button" @click="showPage('events')">
                                                <span class="qa-icon">
                                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                                </span>
                                                <span class="qa-label">Add Event</span>
                                            </button>
                                            <button class="qa-btn" type="button" @click="showPage('student-fines')">
                                                <span class="qa-icon">
                                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </span>
                                                <span class="qa-label">Student Fines</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- System Status -->
                                    <div class="dash-panel">
                                        <div class="dash-panel-header">
                                            <div class="dash-panel-title">
                                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                System Status
                                            </div>
                                        </div>
                                        <div class="status-list">
                                            <div class="status-row">
                                                <span class="status-dot green"></span>
                                                <span class="status-label">Attendance Recording</span>
                                                <span class="status-val">Active</span>
                                            </div>
                                            <div class="status-row">
                                                <span class="status-dot blue"></span>
                                                <span class="status-label">Total Events</span>
                                                <span class="status-val">{{ events.length }}</span>
                                            </div>
                                            <div class="status-row">
                                                <span class="status-dot orange"></span>
                                                <span class="status-label">Attendance Records</span>
                                                <span class="status-val">{{ checkedInCount }}</span>
                                            </div>
                                            <div class="status-row">
                                                <span class="status-dot purple"></span>
                                                <span class="status-label">Registered Students</span>
                                                <span class="status-val">{{ totalStudents }}</span>
                                            </div>
                                        </div>
                                    </div>

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
                                        <button class="btn btn-primary btn-sm" type="button" @click="openPrintModal()">🖨️ Print Participants</button>
                                        <select v-model="participantEventFilter" class="filter-select">
                                            <option value="">All Events ({{ attendances.length }})</option>
                                            <option v-for="ev in events" :key="ev.id" :value="ev.id">
                                                {{ ev.name }} ({{ countEventParticipants(ev.id) }})
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="search-wrapper" style="margin-bottom:1rem;">
                                    <span class="search-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/></svg></span>
                                    <input v-model.trim="searchParticipant" type="text" placeholder="Search participant by name, student ID, email, course..." class="search-box" />
                                </div>
                                <div class="table-container">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>👤 Name</th>
                                                <th>🆔 Student ID</th>
                                                <th>✉️ Email</th>
                                                <th>🎓 Course & Year</th>
                                                <th>📅 Event</th>
                                                <th>🕐 Time / Status</th>
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
                                                <td style="font-family:monospace;font-weight:700;color:#0f172a;">{{ row.student_id || '—' }}</td>
                                                <td style="color:#64748b;">{{ row.email || '—' }}</td>
                                                <td>
                                                    <span v-if="row.course" class="badge badge-blue">{{ row.course }} {{ row.year_level ? '· ' + row.year_level : '' }}</span>
                                                    <span v-else style="color:#94a3b8;">—</span>
                                                </td>
                                                <td>
                                                    <span style="font-weight:600;color:#1e293b;">{{ row.event || '—' }}</span>
                                                </td>
                                                <td>
                                                    <span :class="row.status === 'present' ? 'badge badge-green' : 'badge badge-red'">
                                                        {{ row.status === 'present' ? (row.time || 'Present') : 'Absent' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="table-btn delete" type="button" @click="deleteParticipant(row.attendanceId)">🗑️ Remove</button>
                                                </td>
                                            </tr>
                                            <tr v-if="!filteredParticipants.length">
                                                <td colspan="8" style="text-align:center;color:#94a3b8;padding:2.5rem;">No participants found matching the selected filter.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="pagination-bar" v-if="filteredParticipants.length">
                                    <span class="pagination-info">Showing {{ filteredParticipants.length }} participant{{ filteredParticipants.length !== 1 ? 's' : '' }}</span>
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
                                    <!-- ── Fine & Deadline Settings ───────────────── -->
                                    <div class="form-group" style="grid-column: 1 / -1; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                                        <h4 style="font-size: 0.95rem; font-weight: 700; color: #0f1c3f; margin-bottom: 0.75rem;">⏱️ Deadline &amp; Fine Settings</h4>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Grace Period (minutes)
                                            <span style="font-size:0.72rem;color:#64748b;font-weight:400;"> – check-in window after start time</span>
                                        </label>
                                        <input v-model.number="eventForm.grace_period_minutes" type="number" min="0" max="1440" class="form-input" placeholder="30" />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Absence Fine Amount (₱)
                                            <span style="font-size:0.72rem;color:#64748b;font-weight:400;"> – set 0 for no fine</span>
                                        </label>
                                        <input v-model.number="eventForm.fine_amount" type="number" min="0" max="10000" step="0.01" class="form-input" placeholder="0.00" />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Fine Policy</label>
                                        <select v-model="eventForm.fine_policy" class="form-select">
                                            <option value="per_event">Per Event (flat fee)</option>
                                            <option value="hourly">Hourly Rate</option>
                                            <option value="none">No Fine</option>
                                        </select>
                                    </div>
                                    <!-- ── Location & GPS ─────────────────────────── -->
                                    <div class="form-group" style="grid-column: 1 / -1; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                                        <h4 style="font-size: 0.95rem; font-weight: 700; color: #0f1c3f; margin-bottom: 0.75rem;">📍 Location &amp; GPS (Optional)</h4>
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
                                        <thead>
                                            <tr>
                                                <th>Name & Venue</th>
                                                <th>Schedule</th>
                                                <th>Status & Grace</th>
                                                <th>Attendees Overview</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
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
                                                        
                                                        <button class="table-btn" type="button" @click="openPrintModal(ev.id)" title="Print participants for this event">🖨️ Print</button>
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

                        <!-- ============ STUDENT FINES ============ -->
                        <div id="studentFines" class="page" :class="{ active: activePage === 'student-fines' }">
                            <div class="page-banner">
                                <div class="page-banner-icon"><span>💰</span></div>
                                <div class="page-banner-text">
                                    <h1>Student Overall Fines</h1>
                                    <p>Review overall student fines summary and record payments to clear student balances.</p>
                                </div>
                            </div>

                            <!-- Fines KPI Summary Cards -->
                            <div class="stats-grid" style="margin-bottom: 1.5rem;">
                                <div class="stat-card">
                                    <div class="stat-icon" style="background:#fef2f2;color:#dc2626;">🚨</div>
                                    <div class="stat-info">
                                        <h3>{{ overallFinesSummary.students_with_unpaid || 0 }}</h3>
                                        <p>Students with Unpaid Fines</p>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon" style="background:#eff6ff;color:#2563eb;">💵</div>
                                    <div class="stat-info">
                                        <h3>₱{{ Number(overallFinesSummary.total_unpaid_amount || 0).toFixed(2) }}</h3>
                                        <p>Total Outstanding Fines</p>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon" style="background:#f0fdf4;color:#16a34a;">🎓</div>
                                    <div class="stat-info">
                                        <h3>{{ overallFinesSummary.total_students_with_fines || 0 }}</h3>
                                        <p>Total Students with Fines</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Fines Table Card -->
                            <div class="card">
                                <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
                                    <div class="card-title">
                                        <span class="card-title-icon">📋</span> Student Fines Summary
                                    </div>
                                    <button class="btn btn-outline btn-sm" type="button" @click="loadStudentFinesSummary">
                                        🔄 Refresh
                                    </button>
                                </div>

                                <!-- Filters -->
                                <div style="display:flex; gap:0.75rem; flex-wrap:wrap; margin-bottom:1.25rem;">
                                    <div class="search-wrapper" style="flex:1; min-width:240px; margin-bottom:0;">
                                        <span class="search-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/></svg></span>
                                        <input v-model.trim="finesSearch" type="text" placeholder="Search by student ID, name, or course..." class="search-box" />
                                    </div>
                                    <select v-model="finesStatusFilter" class="form-select" style="width:auto; min-width:180px;">
                                        <option value="">All Statuses</option>
                                        <option value="unpaid">Has Unpaid Fines</option>
                                        <option value="paid">Fully Settled (Paid)</option>
                                    </select>
                                </div>

                                <div class="table-container">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Student ID</th>
                                                <th>Student Name</th>
                                                <th>Course &amp; Year</th>
                                                <th>Fines Count</th>
                                                <th>Overall Fines</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="st in filteredStudentFines" :key="st.student_id">
                                                <td style="font-family:monospace;font-size:0.85rem;font-weight:600;color:#0f172a;">
                                                    {{ st.student_id }}
                                                </td>
                                                <td>
                                                    <div class="td-name">
                                                        <div class="student-avatar">{{ (st.name || '?').slice(0, 2).toUpperCase() }}</div>
                                                        <span class="td-name-text">{{ st.name }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span v-if="st.course" class="badge badge-blue">
                                                        {{ st.course }} <template v-if="st.year_level">• {{ st.year_level }}</template>
                                                    </span>
                                                    <span v-else style="color:#94a3b8;">—</span>
                                                </td>
                                                <td style="color:#64748b; font-size:0.85rem;">
                                                    {{ st.unpaid_fines_count }} unpaid / {{ st.fines_count }} total
                                                </td>
                                                <td>
                                                    <span style="font-weight:700; font-size:0.95rem;" :style="st.overall_fines > 0 ? 'color:#dc2626;' : 'color:#16a34a;'">
                                                        ₱{{ Number(st.overall_fines).toFixed(2) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span v-if="st.status === 'paid'" class="badge badge-green" style="font-weight:600;">
                                                        ✓ Fully Paid
                                                    </span>
                                                    <span v-else class="badge badge-red" style="font-weight:600;">
                                                        ● Unpaid
                                                    </span>
                                                </td>
                                                <td>
                                                    <button v-if="st.status === 'unpaid' && st.overall_fines > 0"
                                                            class="btn btn-primary btn-sm"
                                                            type="button"
                                                            style="background:#16a34a; border-color:#16a34a; padding:0.35rem 0.75rem;"
                                                            @click="markStudentFinesAsPaid(st)">
                                                        ✓ Mark as Paid
                                                    </button>
                                                    <span v-else class="badge badge-green" style="opacity:0.85; padding:0.35rem 0.6rem;">
                                                        Cleared
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr v-if="!filteredStudentFines.length">
                                                <td colspan="7" style="text-align:center;color:#94a3b8;padding:2.5rem;">
                                                    No student fine records found matching the filter.
                                                </td>
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
const participantEventFilter = ref('');

const printModal = reactive({
    isOpen: false,
    mode: 'selected', // 'selected' | 'all'
    selectedEventId: '',
    statusFilter: 'all', // 'all' | 'present' | 'absent'
    sortBy: 'last_name', // 'last_name' | 'name' | 'student_id'
    preparedBy: 'System Administrator',
    notedBy: 'Office of Student Affairs and Services',
});

const editingEventId = ref(null);
const eventForm = reactive({ name: '', date: '', starts_time: '', ends_time: '', description: '', target_year_level: 'All', venue: '', latitude: null, longitude: null, radius_meters: 100, grace_period_minutes: 30, fine_amount: 0, fine_policy: 'per_event' });

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


const studentFinesList = ref([]);
const overallFinesSummary = ref({ total_students_with_fines: 0, students_with_unpaid: 0, total_unpaid_amount: 0 });
const finesSearch = ref('');
const finesStatusFilter = ref('');

async function loadStudentFinesSummary() {
    try {
        const res = await apiRequest('/api/admin/student-fines-summary');
        if (res) {
            studentFinesList.value = res.students || [];
            overallFinesSummary.value = res.summary || {};
        }
    } catch (e) {
        console.error('Error loading fines summary', e);
    }
}

const filteredStudentFines = computed(() => {
    const q = finesSearch.value.toLowerCase().trim();
    return studentFinesList.value.filter(st => {
        const matchesSearch = !q || st.name.toLowerCase().includes(q) || st.student_id.toLowerCase().includes(q) || (st.course && st.course.toLowerCase().includes(q));
        const matchesStatus = !finesStatusFilter.value || st.status === finesStatusFilter.value;
        return matchesSearch && matchesStatus;
    });
});

function markStudentFinesAsPaid(st) {
    confirmAction(
        'Mark Fines as Paid',
        `Confirm that ${st.name} (${st.student_id}) has paid their overall fines of ₱${Number(st.overall_fines).toFixed(2)}?`,
        async () => {
            try {
                await apiRequest(`/api/admin/students/${st.student_id}/mark-fines-paid`, { method: 'POST' });
                showAlert(`Fines for ${st.name} marked as paid.`);
                await loadStudentFinesSummary();
            } catch (e) {
                showAlert(e.message || 'Failed to mark fines as paid.');
            }
        }
    );
}

function showPage(pageId) {
    activePage.value = pageId;
    if (pageId === 'student-fines') {
        loadStudentFinesSummary();
    }
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

function getLastName(fullName) {
    if (!fullName) return '';
    const clean = fullName.trim();
    if (clean.includes(',')) {
        return clean.split(',')[0].trim().toLowerCase();
    }
    const parts = clean.split(/\s+/);
    if (parts.length <= 1) return clean.toLowerCase();
    
    // Check compound prefixes in Filipino/international names
    const prefixes = ['de', 'del', 'dela', 'de los', 'de las', 'van', 'von', 'san', 'santa', 'st.'];
    if (parts.length >= 3) {
        const secondToLast = parts[parts.length - 2].toLowerCase();
        if (prefixes.includes(secondToLast)) {
            return `${parts[parts.length - 2]} ${parts[parts.length - 1]}`.toLowerCase();
        }
    }
    return parts[parts.length - 1].toLowerCase();
}

function countEventParticipants(eventId) {
    return attendances.value.filter(a => String(a.event_id) === String(eventId)).length;
}

function formatReportDate(dateStr) {
    if (!dateStr) return '—';
    const dt = new Date(String(dateStr).replace(' ', 'T'));
    if (Number.isNaN(dt.getTime())) return dateStr;
    return dt.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

const currentPrintDateTime = computed(() => {
    const now = new Date();
    return now.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
});

const filteredParticipants = computed(() => {
    const search = searchParticipant.value.toLowerCase();
    const eventFilter = participantEventFilter.value ? String(participantEventFilter.value) : '';

    return attendances.value
        .filter((a) => {
            if (eventFilter && String(a.event_id) !== eventFilter) {
                return false;
            }
            return true;
        })
        .map((a) => {
            const student = a.student || { student_id: '', name: 'Unknown', email: '', course: '', year_level: '' };
            const event = a.event || { id: null, name: '' };
            return {
                attendanceId: a.id,
                student_id: student.student_id,
                name: student.name,
                email: student.email,
                course: student.course,
                year_level: student.year_level,
                time: a.checked_in_at ? new Date(a.checked_in_at).toLocaleString() : '',
                status: a.status || 'present',
                event_id: a.event_id || event.id,
                event: event.name || '—',
            };
        })
        .filter((r) => {
            if (!search) return true;
            return (
                (r.student_id || '').toLowerCase().includes(search) ||
                (r.name || '').toLowerCase().includes(search) ||
                (r.email || '').toLowerCase().includes(search) ||
                (r.course || '').toLowerCase().includes(search) ||
                (r.event || '').toLowerCase().includes(search)
            );
        });
});

const printableEventsData = computed(() => {
    let targetEvents = [];
    if (printModal.mode === 'selected') {
        const ev = events.value.find(e => String(e.id) === String(printModal.selectedEventId));
        if (ev) {
            targetEvents = [ev];
        } else if (events.value.length > 0) {
            targetEvents = [events.value[0]];
        }
    } else {
        // All events mode: sorted chronologically
        targetEvents = [...events.value].sort((a, b) => new Date(a.date || 0) - new Date(b.date || 0));
    }

    return targetEvents.map(ev => {
        let atts = attendances.value.filter(a => String(a.event_id) === String(ev.id));

        if (printModal.statusFilter !== 'all') {
            atts = atts.filter(a => a.status === printModal.statusFilter);
        }

        let list = atts.map((a) => {
            const s = a.student || {};
            return {
                id: a.id,
                student_id: s.student_id || '—',
                name: s.name || 'Unknown',
                email: s.email || '—',
                course: s.course || '',
                year_level: s.year_level || '',
                course_year: [s.course, s.year_level].filter(Boolean).join(' - ') || '—',
                status: a.status || 'present',
                checked_in_at: a.checked_in_at,
                time: a.checked_in_at ? formatTime(a.checked_in_at) : (a.status === 'present' ? 'Present' : (a.status === 'absent' ? 'Absent' : 'Pending')),
            };
        });

        // Fall back to eligible students if no attendance records yet
        if (!list.length && printModal.statusFilter === 'all' && students.value.length) {
            const targetYr = (ev.target_year_level || 'all').toLowerCase();
            const eligible = students.value.filter(s => {
                if (targetYr !== 'all' && (s.year_level || '').toLowerCase() !== targetYr) return false;
                return true;
            });
            list = eligible.map(s => ({
                id: s.id,
                student_id: s.student_id || '—',
                name: s.name || 'Unknown',
                email: s.email || '—',
                course: s.course || '',
                year_level: s.year_level || '',
                course_year: [s.course, s.year_level].filter(Boolean).join(' - ') || '—',
                status: 'pending',
                checked_in_at: null,
                time: 'Pending',
            }));
        }

        // Sort participants
        if (printModal.sortBy === 'last_name') {
            list.sort((a, b) => {
                const lnA = getLastName(a.name);
                const lnB = getLastName(b.name);
                if (lnA !== lnB) return lnA.localeCompare(lnB);
                return (a.name || '').localeCompare(b.name || '');
            });
        } else if (printModal.sortBy === 'name') {
            list.sort((a, b) => (a.name || '').localeCompare(b.name || ''));
        } else if (printModal.sortBy === 'student_id') {
            list.sort((a, b) => (a.student_id || '').localeCompare(b.student_id || '', undefined, { numeric: true }));
        }

        const presentCount = list.filter(p => p.status === 'present').length;
        const absentCount = list.filter(p => p.status === 'absent').length;

        return {
            event: ev,
            participants: list,
            totalCount: list.length,
            presentCount,
            absentCount,
            formattedDate: formatReportDate(ev.date),
            scheduleText: `${formatTime(ev.starts_at) || ''}${ev.ends_at ? ' - ' + formatTime(ev.ends_at) : ''}`,
            venue: ev.venue || 'Surigao Del Norte State University Campus',
        };
    }).filter(group => {
        if (printModal.mode === 'all') {
            return group.participants.length > 0;
        }
        return true;
    });
});

function openPrintModal(eventId = null) {
    if (eventId) {
        printModal.mode = 'selected';
        printModal.selectedEventId = eventId;
    } else if (participantEventFilter.value) {
        printModal.mode = 'selected';
        printModal.selectedEventId = participantEventFilter.value;
    } else {
        const firstEventWithAtt = events.value.find(ev => attendances.value.some(a => String(a.event_id) === String(ev.id)));
        if (firstEventWithAtt) {
            printModal.selectedEventId = firstEventWithAtt.id;
        } else if (events.value.length > 0) {
            printModal.selectedEventId = events.value[0].id;
        }
        printModal.mode = 'selected';
    }
    printModal.isOpen = true;
}

function triggerPrint() {
    window.print();
}

function printParticipants() {
    openPrintModal();
}

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
        const payload = {
            name, date, starts_at, ends_at, description, target_year_level,
            venue: eventForm.venue,
            latitude: eventForm.latitude,
            longitude: eventForm.longitude,
            radius_meters: eventForm.radius_meters,
            grace_period_minutes: eventForm.grace_period_minutes ?? 30,
            fine_amount: eventForm.fine_amount ?? 0,
            fine_policy: eventForm.fine_policy || 'per_event',
        };
        if (editingEventId.value === null) {
            await apiRequest('/api/events', { method: 'POST', body: payload });
        } else {
            await apiRequest(`/api/events/${editingEventId.value}`, { method: 'PUT', body: payload });
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
        eventForm.grace_period_minutes = 30;
        eventForm.fine_amount = 0;
        eventForm.fine_policy = 'per_event';
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
    eventForm.grace_period_minutes = ev.grace_period_minutes ?? 30;
    eventForm.fine_amount = ev.fine_amount ?? 0;
    eventForm.fine_policy = ev.fine_policy || 'per_event';
    showPage('events');
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
        if (page === 'dashboard' || page === 'students' || page === 'participants' || page === 'events' || page === 'fines') {
            await nextTick();
        }
    },
);

// ─── NEW: Participants modal ───────────────────────────────────────────────
const participantsModal = reactive({
  isOpen: false,
  event: null,
  data: null,
  search: '',
  loading: false,
});

const filteredModalParticipants = computed(() => {
  const list = participantsModal.data?.participants || [];
  const q = (participantsModal.search || '').toLowerCase();
  if (!q) return list;
  return list.filter(p =>
    (p.name || '').toLowerCase().includes(q) ||
    (p.student_id || '').toLowerCase().includes(q)
  );
});

async function viewParticipants(ev) {
  participantsModal.event = ev;
  participantsModal.search = '';
  participantsModal.data = null;
  participantsModal.isOpen = true;
  participantsModal.loading = true;
  try {
    const res = await apiRequest(`/api/events/${ev.id}/participants`);
    participantsModal.data = res;
  } catch (e) {
    showAlert(e.message || 'Failed to load participants.');
    participantsModal.isOpen = false;
  } finally {
    participantsModal.loading = false;
  }
}

async function correctAttendance(participant, newStatus) {
  const reason = window.prompt(`Reason for marking ${participant.name} as ${newStatus}:`);
  if (reason === null) return;
  try {
    await apiRequest(`/api/attendances/correct`, {
      method: 'POST',
      body: {
        event_id: participantsModal.event?.id,
        student_id: participant.student_id,
        status: newStatus,
        reason,
      },
    });
    await viewParticipants(participantsModal.event);
  } catch (e) {
    showAlert(e.message || 'Correction failed.');
  }
}

async function finalizeEvent(ev) {
  confirmAction(
    'Finalize Event',
    `Finalize attendance & process fines for "${ev.name}"? This will mark absent students and issue fines.`,
    async () => {
      try {
        await apiRequest(`/api/events/${ev.id}/finalize`, { method: 'POST' });
        showAlert('Event finalized successfully.');
        await loadAll();
      } catch (e) {
        showAlert(e.message || 'Finalization failed.');
      }
    }
  );
}

// ─── NEW: Payment modal ────────────────────────────────────────────────────
const paymentModal = reactive({
  isOpen: false,
  fine: null,
  amount: 0,
  reference: '',
});

function openPaymentModal(fine) {
  paymentModal.fine = fine;
  paymentModal.amount = Number(fine?.balance || 0);
  paymentModal.reference = '';
  paymentModal.isOpen = true;
}

async function submitPayment() {
  try {
    await apiRequest(`/api/fines/${paymentModal.fine.id}/pay`, {
      method: 'POST',
      body: { amount: paymentModal.amount, reference: paymentModal.reference },
    });
    showAlert('Payment recorded successfully.');
    paymentModal.isOpen = false;
    await loadFines();
  } catch (e) {
    showAlert(e.message || 'Payment failed.');
  }
}

// ─── NEW: Waive modal ──────────────────────────────────────────────────────
const waiveModal = reactive({
  isOpen: false,
  fine: null,
  reason: '',
});

function openWaiveModal(fine) {
  waiveModal.fine = fine;
  waiveModal.reason = '';
  waiveModal.isOpen = true;
}

async function submitWaiver() {
  if (!waiveModal.reason.trim()) {
    showAlert('Please enter a waiver reason.');
    return;
  }
  try {
    await apiRequest(`/api/fines/${waiveModal.fine.id}/waive`, {
      method: 'POST',
      body: { reason: waiveModal.reason },
    });
    showAlert('Fine waived successfully.');
    waiveModal.isOpen = false;
    await loadFines();
  } catch (e) {
    showAlert(e.message || 'Waiver failed.');
  }
}

// ─── NEW: Audit log modal ──────────────────────────────────────────────────
const auditModal = reactive({
  isOpen: false,
  fine: null,
  logs: [],
  loading: false,
});

async function openAuditModal(fine) {
  auditModal.fine = fine;
  auditModal.logs = [];
  auditModal.isOpen = true;
  auditModal.loading = true;
  try {
    const res = await apiRequest(`/api/fines/${fine.id}/audit`);
    auditModal.logs = res?.logs || [];
  } catch (e) {
    auditModal.logs = [];
  } finally {
    auditModal.loading = false;
  }
}

// ─── NEW: Fines management ─────────────────────────────────────────────────
const fines = ref([]);
const fineSearch = ref('');
const fineStatusFilter = ref('');

const filteredFines = computed(() => {
  let list = fines.value;
  const q = (fineSearch.value || '').toLowerCase();
  if (q) list = list.filter(f =>
    (f.student?.name || '').toLowerCase().includes(q) ||
    (f.student?.student_id || '').toLowerCase().includes(q) ||
    (f.reference_no || '').toLowerCase().includes(q)
  );
  if (fineStatusFilter.value) list = list.filter(f => f.status === fineStatusFilter.value);
  return list;
});

const fineKpis = computed(() => {
  const all = fines.value;
  return {
    total: all.length,
    outstanding: all.filter(f => f.status === 'unpaid' || f.status === 'pending_review')
                    .reduce((s, f) => s + Number(f.balance || 0), 0),
    collected: all.filter(f => f.status === 'paid')
                  .reduce((s, f) => s + Number(f.amount || 0), 0),
    waived: all.filter(f => f.status === 'waived')
               .reduce((s, f) => s + Number(f.amount || 0), 0),
  };
});

async function loadFines() {
  try {
    const res = await apiRequest('/api/fines');
    fines.value = Array.isArray(res) ? res : (res?.data || []);
  } catch (e) {
    fines.value = [];
  }
}

async function handleReviewExcuse(excuse, approved) {
  const remarks = window.prompt(`Remarks for this ${approved ? 'approval' : 'rejection'}:`);
  if (remarks === null) return;
  try {
    await apiRequest(`/api/fines/excuses/${excuse.id}/review`, {
      method: 'POST',
      body: { approved, remarks },
    });
    showAlert(approved ? 'Excuse approved – fine waived.' : 'Excuse rejected.');
    await loadFines();
  } catch (e) {
    showAlert(e.message || 'Review failed.');
  }
}

// ─── Patch loadAll to also load fines ─────────────────────────────────────
const _origLoadAll = loadAll;


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

<style>
/* =========================================================
   PRINT PARTICIPANTS MODAL & SCREEN PREVIEW
========================================================= */
.print-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(15, 23, 42, 0.85);
    backdrop-filter: blur(5px);
    z-index: 99999;
    display: flex;
    flex-direction: column;
}

.print-modal-toolbar {
    background: #0f172a;
    color: #ffffff;
    padding: 0.75rem 1.75rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.25rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
    flex-wrap: wrap;
    z-index: 100;
    border-bottom: 1px solid #1e293b;
}

.print-modal-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.print-modal-controls {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    flex-wrap: wrap;
}

.print-control-group {
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.print-control-group label {
    font-size: 0.8rem;
    color: #94a3b8;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.print-segmented-btn {
    display: inline-flex;
    background: #1e293b;
    border-radius: 8px;
    padding: 3px;
    border: 1px solid #334155;
}

.print-segmented-btn button {
    background: transparent;
    border: none;
    color: #94a3b8;
    padding: 6px 14px;
    font-size: 0.84rem;
    font-weight: 600;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.15s ease;
}

.print-segmented-btn button.active {
    background: #2563eb;
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
}

.print-select-input {
    background: #1e293b;
    border: 1px solid #334155;
    color: #f8fafc;
    border-radius: 6px;
    padding: 6px 12px;
    font-size: 0.84rem;
    outline: none;
    cursor: pointer;
}

.print-select-input:focus {
    border-color: #3b82f6;
}

.print-modal-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.print-modal-scroll {
    flex: 1;
    overflow-y: auto;
    background: #334155;
    padding: 2.5rem 1rem;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.print-report-container {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* =========================================================
   A4 SHEET SCREEN PREVIEW
   Standard A4: 210mm x 297mm
========================================================= */
.print-paper-sheet {
    background: #ffffff;
    color: #0f172a;
    width: 210mm;
    min-height: 297mm;
    padding: 14mm 16mm;
    margin: 0 auto 2.5rem auto;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
    position: relative;
}

/* Header with Logos */
.print-univ-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 2.5px solid #0f172a;
    padding-bottom: 8px;
    margin-bottom: 12px;
}

.print-univ-logo {
    width: 68px;
    height: 68px;
    object-fit: contain;
    flex-shrink: 0;
}

.print-univ-text {
    text-align: center;
    flex: 1;
    padding: 0 12px;
}

.print-univ-republic {
    font-size: 8.5pt;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #475569;
    margin-bottom: 1px;
}

.print-univ-name {
    font-size: 13pt;
    font-weight: 800;
    color: #0f172a;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 2px;
}

.print-univ-sub {
    font-size: 8.5pt;
    color: #64748b;
    margin-bottom: 2px;
}

.print-univ-office {
    font-size: 9.5pt;
    font-weight: 700;
    color: #1e3a8a;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Document Title Bar */
.print-doc-title-bar {
    text-align: center;
    margin-bottom: 12px;
}

.print-doc-title-bar h2 {
    font-size: 12.5pt;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.75px;
    color: #0f172a;
    margin: 0 0 2px 0;
}

.print-doc-title-bar p {
    font-size: 8.5pt;
    color: #64748b;
    margin: 0;
}

/* Event Metadata Box */
.print-event-meta-box {
    border: 1.5px solid #0f172a;
    border-radius: 4px;
    background: #f8fafc;
    padding: 8px 12px;
    margin-bottom: 14px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px 16px;
    font-size: 9pt;
}

.print-meta-item {
    display: flex;
    align-items: baseline;
    gap: 8px;
}

.print-meta-full {
    grid-column: span 2;
    border-top: 1px dashed #cbd5e1;
    padding-top: 5px;
    margin-top: 2px;
}

.print-meta-label {
    font-weight: 800;
    color: #334155;
    font-size: 8pt;
    letter-spacing: 0.3px;
    min-width: 95px;
}

.print-meta-value {
    color: #0f172a;
    font-weight: 600;
}

/* Participants Table */
.print-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 14px;
    font-size: 9pt;
}

.print-table th,
.print-table td {
    border: 1px solid #1e293b;
    padding: 6px 7px;
    vertical-align: middle;
    line-height: 1.25;
}

.print-table th {
    background-color: #f1f5f9;
    font-weight: 800;
    color: #0f172a;
    text-align: left;
    font-size: 8.5pt;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.status-text-present {
    font-weight: 700;
    color: #059669;
}

.status-text-absent {
    font-weight: 700;
    color: #dc2626;
}

.sig-cell {
    height: 32px;
    vertical-align: bottom !important;
    padding: 2px 6px !important;
}

.sig-line {
    border-bottom: 1px dotted #94a3b8;
    height: 100%;
    min-height: 22px;
}

/* Signatories Footer */
.print-bottom-block {
    margin-top: auto;
    padding-top: 10px;
}

.print-signatories {
    display: flex;
    justify-content: space-between;
    gap: 3rem;
    margin-top: 15px;
    margin-bottom: 15px;
    page-break-inside: avoid;
    break-inside: avoid;
}

.print-sig-col {
    flex: 1;
}

.print-sig-role {
    font-size: 8.5pt;
    font-weight: 700;
    color: #334155;
    margin-bottom: 2.25rem;
}

.print-sig-underline {
    border-bottom: 1.5px solid #0f172a;
    width: 90%;
    margin-bottom: 4px;
}

.print-sig-name {
    font-size: 9pt;
    font-weight: 800;
    color: #0f172a;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin: 0;
}

.print-sig-title {
    font-size: 8pt;
    color: #64748b;
    margin: 0;
}

.print-footer-meta {
    border-top: 1px solid #cbd5e1;
    padding-top: 6px;
    display: flex;
    justify-content: space-between;
    font-size: 7.5pt;
    color: #64748b;
}

/* =========================================================
   NATIVE @MEDIA PRINT RULES FOR A4 PRINTER & PDF EXPORT
========================================================= */
@media print {
    @page {
        size: A4 portrait;
        margin: 12mm 15mm 15mm 15mm;
    }

    *, *::before, *::after {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    body {
        background: #ffffff !important;
        color: #000000 !important;
        margin: 0 !important;
        padding: 0 !important;
        font-family: Arial, "Helvetica Neue", Helvetica, sans-serif !important;
        font-size: 9.5pt !important;
    }

    /* Strict hiding of all background dashboard UI elements */
    .admin-layout,
    .topbar,
    .sidebar,
    .admin-sidebar,
    .toast-alert,
    .no-print,
    .print-modal-toolbar {
        display: none !important;
    }

    /* Print overlay collapses into standard document flow */
    .print-modal-overlay {
        position: static !important;
        background: transparent !important;
        width: 100% !important;
        height: auto !important;
        overflow: visible !important;
        display: block !important;
    }

    .print-modal-scroll {
        background: transparent !important;
        padding: 0 !important;
        overflow: visible !important;
        display: block !important;
    }

    .print-report-container {
        display: block !important;
        width: 100% !important;
    }

    /* Each event sheet starts cleanly on a fresh page */
    .print-paper-sheet {
        box-shadow: none !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        min-height: auto !important;
        page-break-after: always !important;
        break-after: page !important;
        page-break-inside: auto;
    }

    /* Suppress unnecessary trailing blank page after last event */
    .print-paper-sheet:last-child {
        page-break-after: auto !important;
        break-after: auto !important;
    }

    /* Repeating table headers for multi-page tables */
    .print-table {
        width: 100% !important;
        border-collapse: collapse !important;
    }

    .print-table thead {
        display: table-header-group !important;
    }

    .print-table tr {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }

    .print-signatories {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }

    .print-bottom-block {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }

    .print-table th {
        background-color: #f1f5f9 !important;
    }

    .print-event-meta-box {
        background-color: #f8fafc !important;
    }
}
</style>

