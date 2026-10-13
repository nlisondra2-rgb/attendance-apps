// Core admin app logic for Student Attendance System
// Backend is Laravel API:
//   /api/students
//   /api/events
//   /api/attendances

let students = [];
let events = [];
let attendances = [];

let editingStudentId = null;

// Student QR scanner (admin page)
let studentQrStream = null;
let studentQrAnimationId = null;

async function apiRequest(path, { method = 'GET', body } = {}) {
  const res = await fetch(path, {
    method,
    headers: {
      'Accept': 'application/json',
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
  const [s, e, a] = await Promise.all([
    apiRequest('/api/students'),
    apiRequest('/api/events'),
    apiRequest('/api/attendances'),
  ]);

  students = Array.isArray(s) ? s : [];
  events = Array.isArray(e) ? e : [];
  attendances = Array.isArray(a) ? a : [];
}

// ===== AUTH =====
async function login() {
  const email = document.getElementById('loginEmail').value.trim();
  const password = document.getElementById('loginPassword').value.trim();

  try {
    await apiRequest('/api/admin/login', {
      method: 'POST',
      body: { email, password },
    });

    localStorage.setItem('isAdminLoggedIn', '1');

    const publicHeader = document.getElementById('publicHeader');
    if (publicHeader) publicHeader.style.display = 'none';

    document.getElementById('loginPage').style.display = 'none';
    document.getElementById('mainApp').style.display = 'block';
    initAdminApp();
  } catch (e) {
    alert(e?.message || 'Invalid admin credentials.');
  }
}

function logout() {
  localStorage.removeItem('isAdminLoggedIn');
  document.getElementById('mainApp').style.display = 'none';
  document.getElementById('loginPage').style.display = 'block';

  const publicHeader = document.getElementById('publicHeader');
  if (publicHeader) publicHeader.style.display = '';

  const emailEl = document.getElementById('loginEmail');
  if (emailEl) emailEl.value = '';

  const passwordEl = document.getElementById('loginPassword');
  if (passwordEl) passwordEl.value = '';

  const studentIdEl = document.getElementById('studentIdInput');
  if (studentIdEl) studentIdEl.value = '';
}

// ===== NAVIGATION =====
function showPage(pageId) {
  const pages = document.querySelectorAll('.page');
  pages.forEach(p => p.classList.remove('active'));
  const target = document.getElementById(pageId);
  if (target) target.classList.add('active');

  if (pageId === 'dashboard') {
    updateDashboardStats();
    renderDashboardEvents();
  } else if (pageId === 'students') {
    renderStudents();
  } else if (pageId === 'participants') {
    renderParticipants();
  } else if (pageId === 'events') {
    renderEvents();
  }
}

// ===== LOGIN MODE (ADMIN PAGE) =====
function setLoginMode(mode) {
  const adminForm = document.getElementById('adminLoginForm');
  const studentForm = document.getElementById('studentLoginForm');
  if (!adminForm || !studentForm) return;

  if (mode === 'student') {
    adminForm.style.display = 'none';
    studentForm.style.display = 'block';
    const studentIdEl = document.getElementById('studentIdInput');
    if (studentIdEl) studentIdEl.focus();
  } else {
    studentForm.style.display = 'none';
    adminForm.style.display = 'block';
    const emailEl = document.getElementById('loginEmail');
    if (emailEl) emailEl.focus();
  }
}

async function loginStudentFromAdmin() {
  const idInput = document.getElementById('studentIdInput');
  if (!idInput) return;
  const studentId = idInput.value.trim();
  if (!studentId) {
    alert('Please enter your Student ID.');
    return;
  }

  try {
    await apiRequest(`/api/students/${encodeURIComponent(studentId)}`);
    localStorage.setItem('studentId', studentId);
    window.location.href = '/student';
  } catch (e) {
    alert('Student ID not found. Please check your ID.');
  }
}

// ===== DASHBOARD (ADMIN) =====
function updateDashboardStats() {
  const totalStudentsEl = document.getElementById('totalStudents');
  const checkedInEl = document.getElementById('checkedIn');
  const bsisCountEl = document.getElementById('bsisCount');
  const otherCountEl = document.getElementById('otherCount');

  const totalStudents = students.length;
  const checkedIn = attendances.length;
  const bsisCount = students.filter(s => s.course === 'BSIS').length;
  const otherCount = totalStudents - bsisCount;

  if (totalStudentsEl) totalStudentsEl.textContent = totalStudents;
  if (checkedInEl) checkedInEl.textContent = checkedIn;
  if (bsisCountEl) bsisCountEl.textContent = bsisCount;
  if (otherCountEl) otherCountEl.textContent = otherCount;
}

function renderDashboardEvents() {
  const tbody = document.getElementById('dashboardEventTable');
  if (!tbody) return;

  const sorted = [...events].sort((a, b) => new Date(a.date) - new Date(b.date));
  const fmtTime = (iso) => {
    if (!iso) return '';
    const normalized = String(iso).replace(' ', 'T');
    const dt = new Date(normalized);
    if (Number.isNaN(dt.getTime())) return '';
    return dt.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  };
  tbody.innerHTML = sorted.map(ev => `
    <tr>
      <td>${ev.name}</td>
      <td>${new Date(ev.date).toLocaleDateString()}</td>
      <td>${fmtTime(ev.starts_at)}</td>
      <td>${fmtTime(ev.ends_at)}</td>
      <td>${ev.description || ''}</td>
    </tr>
  `).join('');
}

// ===== STUDENT REGISTRATION & LIST =====
async function registerStudent() {
  const student_id = document.getElementById('regId').value.trim();
  const name = document.getElementById('regName').value.trim();
  const email = document.getElementById('regEmail').value.trim();
  const course = document.getElementById('regCourse').value;
  const qrFileInput = document.getElementById('regQrImage');
  const qrFile = qrFileInput && qrFileInput.files ? qrFileInput.files[0] : null;

  if (!student_id || !name || !email || !course) {
    alert('Please fill in all required fields.');
    return;
  }

  try {
    let school_qr_raw = null;
    if (qrFile) {
      school_qr_raw = await decodeQrFromFile(qrFile);
      if (!school_qr_raw) {
        alert('No QR code detected in the uploaded ID image. You can still register without it.');
      }
    }

    const basePayload = { student_id, name, email, course };
    const payload = school_qr_raw ? { ...basePayload, school_qr_raw } : basePayload;

    if (editingStudentId === null) {
      await apiRequest('/api/students', {
        method: 'POST',
        body: payload,
      });
    } else {
      await apiRequest(`/api/students/${encodeURIComponent(editingStudentId)}`, {
        method: 'PUT',
        body: school_qr_raw ? { name, email, course, school_qr_raw } : { name, email, course },
      });
    }

    document.getElementById('regId').value = '';
    document.getElementById('regName').value = '';
    document.getElementById('regEmail').value = '';
    document.getElementById('regCourse').value = '';

    if (qrFileInput) qrFileInput.value = '';

    const idInput = document.getElementById('regId');
    if (idInput) idInput.disabled = false;
    editingStudentId = null;

    const registerBtn = document.querySelector('#register .btn');
    if (registerBtn) registerBtn.textContent = 'Register';

    await refreshAllViews();
    alert(editingStudentId === null ? 'Student registered successfully.' : 'Student updated successfully.');
  } catch (e) {
    alert(e.message);
  }
}

function editStudent(studentId) {
  const s = students.find(st => st.student_id === studentId);
  if (!s) return;

  editingStudentId = studentId;
  showPage('register');

  const idInput = document.getElementById('regId');
  const nameInput = document.getElementById('regName');
  const emailInput = document.getElementById('regEmail');
  const courseSelect = document.getElementById('regCourse');

  if (idInput) {
    idInput.value = s.student_id;
    idInput.disabled = true;
  }
  if (nameInput) nameInput.value = s.name || '';
  if (emailInput) emailInput.value = s.email || '';
  if (courseSelect) courseSelect.value = s.course || '';

  const registerBtn = document.querySelector('#register .btn');
  if (registerBtn) registerBtn.textContent = 'Update';
}

function renderStudents() {
  const tbody = document.getElementById('studentTable');
  if (!tbody) return;

  const search = (document.getElementById('searchStudent')?.value || '').toLowerCase();

  tbody.innerHTML = students
    .filter(s => !search ||
      (s.student_id || '').toLowerCase().includes(search) ||
      s.name.toLowerCase().includes(search) ||
      s.email.toLowerCase().includes(search) ||
      (s.course || '').toLowerCase().includes(search)
    )
    .map(s => `
      <tr>
        <td>${s.student_id}</td>
        <td>${s.name}</td>
        <td>${s.email}</td>
        <td>${s.course || ''}</td>
        <td>
          ${s.school_qr_raw ? `<canvas class="qr-canvas" data-qr="${escapeHtml(s.school_qr_raw)}"></canvas>` : ''}
        </td>
        <td>
          <div class="table-actions">
            <button class="table-btn edit" onclick="editStudent('${s.student_id}')">Edit</button>
            <button class="table-btn delete" onclick="deleteStudent('${s.student_id}')">Delete</button>
          </div>
        </td>
      </tr>
    `).join('');

  renderQrCanvasesWithin(tbody);
}

function renderQrCanvasesWithin(rootEl) {
  if (!rootEl) return;

  const canvases = rootEl.querySelectorAll('canvas.qr-canvas[data-qr]');
  canvases.forEach((canvas) => {
    const value = canvas.getAttribute('data-qr') || '';
    if (!value) return;

    if (typeof QRious === 'function') {
      new QRious({
        element: canvas,
        value,
        size: 64,
      });
      return;
    }

    // Fallback if QRious isn't loaded for some reason
    const span = document.createElement('span');
    span.textContent = value;
    canvas.replaceWith(span);
  });
}

function escapeHtml(value) {
  return String(value)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

async function deleteStudent(studentId) {
  if (!confirm('Delete this student?')) return;
  try {
    await apiRequest(`/api/students/${encodeURIComponent(studentId)}`, { method: 'DELETE' });
    await refreshAllViews();
  } catch (e) {
    alert(e.message);
  }
}

function searchStudent() {
  renderStudents();
}

// ===== CHECK-IN (SCAN / MANUAL) =====
async function manualCheckIn() {
  const raw = document.getElementById('manualId').value.trim();
  if (!raw) {
    alert('Enter a Student ID or scan a QR.');
    return;
  }
  await performCheckIn(raw);
}

async function performCheckIn(raw) {
  // First, try treating raw as a direct student_id
  let student = students.find(s => s.student_id === raw);

  // If not found, try resolving via school_qr_raw using the API
  if (!student) {
    try {
      const resolved = await apiRequest(`/api/student-by-qr?raw=${encodeURIComponent(raw)}`);
      if (resolved && resolved.student_id) {
        student = resolved;
      }
    } catch (e) {
      // ignore here; we'll show a generic not-found below
    }
  }

  if (!student || !student.student_id) {
    alert('Student not found.');
    return;
  }

  if (!events.length) {
    alert('No events configured.');
    return;
  }

  const targetEvent = pickBestEventForCheckIn(events);
  if (!targetEvent) {
    alert('No active event for check-in.');
    return;
  }

  apiRequest('/api/attendances', {
    method: 'POST',
    body: { student_id: student.student_id, event_id: targetEvent.id },
  })
    .then(async () => {
      await refreshAllViews();
      alert('Check-in successful.');
    })
    .catch((e) => {
      alert(e.message);
    });
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
    const start = ev.starts_at
      ? new Date(normalizeIso(ev.starts_at))
      : new Date(new Date(ev.date).setHours(0, 0, 0, 0));
    const end = ev.ends_at
      ? new Date(normalizeIso(ev.ends_at))
      : new Date(new Date(ev.date).setHours(23, 59, 59, 999));
    return { start, end };
  };

  // 1) Prefer an event that is active right now (within the configured window)
  const active = list
    .map((ev) => ({ ev, ...eventWindow(ev) }))
    .filter(({ start, end }) => now >= start && now <= end)
    .sort((a, b) => a.end - b.end);
  if (active.length) return active[0].ev;

  // 2) Otherwise, prefer today's event (date-only match)
  const todayStr = now.toISOString().split('T')[0];
  const todayEvent = list.find((e) => e.date === todayStr);
  if (todayEvent) return todayEvent;

  // 3) Otherwise, fall back to the most recent event by date
  const sortedByDate = [...list].sort((a, b) => new Date(b.date) - new Date(a.date));
  return sortedByDate[0] || null;
}

// QR image upload using jsQR
function handleUploadQR(event) {
  const file = event.target.files[0];
  if (!file) return;

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
      const code = jsQR(imageData.data, canvas.width, canvas.height);
      if (code) {
        const id = code.data.trim();
        document.getElementById('manualId').value = id;
        performCheckIn(id);
      } else {
        alert('No QR code detected in the image.');
      }
    };
    img.src = e.target.result;
  };
  reader.readAsDataURL(file);
}

// Helper: decode a QR code from an uploaded image File, returning the raw text or null
function decodeQrFromFile(file) {
  return new Promise((resolve) => {
    if (!file) {
      resolve(null);
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
        const code = jsQR(imageData.data, canvas.width, canvas.height);
        if (code && code.data) {
          resolve(String(code.data).trim());
        } else {
          resolve(null);
        }
      };
      img.src = e.target.result;
    };
    reader.readAsDataURL(file);
  });
}

function resetScanner() {
  document.getElementById('manualId').value = '';
}

// ===== STUDENT QR LOGIN (ADMIN PAGE) =====
function openStudentQrScannerFromAdmin() {
  const modal = document.getElementById('studentQrModal');
  const video = document.getElementById('studentQrVideo');
  const errorEl = document.getElementById('studentQrError');
  if (!modal || !video) return;

  modal.style.display = 'flex';
  if (errorEl) errorEl.style.display = 'none';

  navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
    .then(stream => {
      studentQrStream = stream;
      video.srcObject = stream;
      video.setAttribute('playsinline', 'true');
      video.play();
      startStudentQrLoop();
    })
    .catch(() => {
      if (errorEl) errorEl.style.display = 'block';
    });
}

function closeStudentQrScanner() {
  const modal = document.getElementById('studentQrModal');
  if (modal) modal.style.display = 'none';

  if (studentQrAnimationId) {
    cancelAnimationFrame(studentQrAnimationId);
    studentQrAnimationId = null;
  }
  if (studentQrStream) {
    studentQrStream.getTracks().forEach(t => t.stop());
    studentQrStream = null;
  }
}

function startStudentQrLoop() {
  const video = document.getElementById('studentQrVideo');
  if (!video) return;

  const canvas = document.createElement('canvas');
  const ctx = canvas.getContext('2d');

  const tick = () => {
    if (!video.readyState || video.readyState < 2) {
      studentQrAnimationId = requestAnimationFrame(tick);
      return;
    }

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const code = jsQR(imageData.data, canvas.width, canvas.height);
    if (code && code.data) {
      const raw = String(code.data).trim();
      console.log('Student login QR scanned:', raw);
      alert('QR detected: ' + raw);
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
    alert('Invalid QR code.');
    return;
  }

  apiRequest(`/api/student-by-qr?raw=${encodeURIComponent(raw)}`)
    .then((student) => {
      if (!student || !student.student_id) {
        throw new Error('Student record is missing student_id.');
      }
      localStorage.setItem('studentId', student.student_id);
      window.location.href = '/student';
    })
    .catch((e) => {
      alert(e?.message || 'Student not found for this QR code.');
    });
}

// ===== PARTICIPANTS VIEW =====
function renderParticipants() {
  const tbody = document.getElementById('partTable');
  if (!tbody) return;

  const search = (document.getElementById('searchParticipant')?.value || '').toLowerCase();

  const rows = attendances.map(a => {
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
  }).filter(r => !search ||
    (r.student_id || '').toLowerCase().includes(search) ||
    (r.name || '').toLowerCase().includes(search) ||
    (r.email || '').toLowerCase().includes(search) ||
    (r.course || '').toLowerCase().includes(search)
  );

  tbody.innerHTML = rows.map(r => `
    <tr>
      <td>${r.student_id}</td>
      <td>${r.name}</td>
      <td>${r.email}</td>
      <td>${r.course || ''}</td>
      <td>${r.time}</td>
      <td>
        <button class="table-btn delete" onclick="deleteParticipant(${r.attendanceId})">Remove</button>
      </td>
    </tr>
  `).join('');
}

async function deleteParticipant(attendanceId) {
  if (!confirm('Remove this participant record?')) return;
  try {
    await apiRequest(`/api/attendances/${attendanceId}`, { method: 'DELETE' });
    await refreshAllViews();
  } catch (e) {
    alert(e.message);
  }
}

function searchParticipant() {
  renderParticipants();
}

function printParticipants() {
  if (!attendances.length) {
    alert('No participants to print.');
    return;
  }

  const rows = attendances.map(a => {
    const s = a.student || { student_id: '', name: 'Unknown', email: '', course: '' };
    const ev = a.event || { name: '' };
    const time = a.checked_in_at ? new Date(a.checked_in_at).toLocaleString() : '';
    return `${s.student_id}\t${s.name}\t${s.email}\t${s.course}\t${time}\t${ev.name}`;
  }).join('\n');

  const win = window.open('', '_blank');
  win.document.write('<pre>' + rows + '</pre>');
  win.document.close();
  win.print();
}

// ===== EVENTS CRUD =====
let editingEventId = null;

function combineDateAndTimeToIso(dateStr, timeStr) {
  if (!dateStr || !timeStr) return null;
  // IMPORTANT: keep as *local* datetime string to avoid timezone shifts.
  // Laravel will parse this in the server/app timezone.
  // Example: 2025-12-20T04:00
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
  const nameInput = document.getElementById('eventName');
  const dateInput = document.getElementById('eventDate');
  const descInput = document.getElementById('eventDesc');
  const startsAtInput = document.getElementById('eventStartsAt');
  const endsAtInput = document.getElementById('eventEndsAt');

  const name = nameInput.value.trim();
  const date = dateInput.value;
  const description = descInput.value.trim();
  const starts_at = startsAtInput?.value ? combineDateAndTimeToIso(date, startsAtInput.value) : null;
  const ends_at = endsAtInput?.value ? combineDateAndTimeToIso(date, endsAtInput.value) : null;

  if (!name || !date) {
    alert('Please provide at least a name and date for the event.');
    return;
  }

  try {
    if (editingEventId === null) {
      await apiRequest('/api/events', {
        method: 'POST',
        body: { name, date, starts_at, ends_at, description },
      });
    } else {
      await apiRequest(`/api/events/${editingEventId}`, {
        method: 'PUT',
        body: { name, date, starts_at, ends_at, description },
      });
    }

    nameInput.value = '';
    dateInput.value = '';
    descInput.value = '';
    if (startsAtInput) startsAtInput.value = '';
    if (endsAtInput) endsAtInput.value = '';

    editingEventId = null;
    const titleEl = document.getElementById('eventFormTitle');
    if (titleEl) titleEl.textContent = 'Create Event';
    const btn = document.getElementById('eventSubmitBtn');
    if (btn) btn.textContent = 'Create Event';

    await refreshAllViews();
  } catch (e) {
    alert(e.message);
  }
}

function renderEvents() {
  const tbody = document.getElementById('eventTable');
  if (!tbody) return;

  const fmtTime = (iso) => {
    if (!iso) return '';
    const normalized = String(iso).replace(' ', 'T');
    const dt = new Date(normalized);
    if (Number.isNaN(dt.getTime())) return '';
    return dt.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  };

  tbody.innerHTML = events.map((ev) => `
    <tr>
      <td>${ev.name}</td>
      <td>${new Date(ev.date).toLocaleDateString()}</td>
      <td>${fmtTime(ev.starts_at)}</td>
      <td>${fmtTime(ev.ends_at)}</td>
      <td>${ev.description || ''}</td>
      <td>
        <button class="table-btn edit" onclick="editEvent(${ev.id})">Edit</button>
        <button class="table-btn delete" onclick="deleteEvent(${ev.id})">Delete</button>
      </td>
    </tr>
  `).join('');
}

function editEvent(eventId) {
  const ev = events.find(e => e.id === eventId);
  if (!ev) return;

  editingEventId = eventId;
  document.getElementById('eventName').value = ev.name;
  document.getElementById('eventDate').value = ev.date;
  document.getElementById('eventDesc').value = ev.description || '';
  const startsAtEl = document.getElementById('eventStartsAt');
  const endsAtEl = document.getElementById('eventEndsAt');
  if (startsAtEl) startsAtEl.value = isoToTimeValue(ev.starts_at);
  if (endsAtEl) endsAtEl.value = isoToTimeValue(ev.ends_at);

  const titleEl = document.getElementById('eventFormTitle');
  if (titleEl) titleEl.textContent = 'Edit Event';
  const btn = document.getElementById('eventSubmitBtn');
  if (btn) btn.textContent = 'Update Event';
}

async function deleteEvent(eventId) {
  if (!confirm('Delete this event?')) return;
  try {
    await apiRequest(`/api/events/${eventId}`, { method: 'DELETE' });
    await refreshAllViews();
  } catch (e) {
    alert(e.message);
  }
}

// ===== GLOBAL REFRESH =====
async function refreshAllViews() {
  await loadAll();
  updateDashboardStats();
  renderDashboardEvents();
  renderStudents();
  renderParticipants();
  renderEvents();
}

async function initAdminApp() {
  try {
    const publicHeader = document.getElementById('publicHeader');
    if (publicHeader) publicHeader.style.display = 'none';

    document.getElementById('loginPage').style.display = 'none';
    document.getElementById('mainApp').style.display = 'block';
    await refreshAllViews();
    showPage('dashboard');
  } catch (e) {
    alert(e.message);
  }
}

// ===== INIT =====
document.addEventListener('DOMContentLoaded', function () {
  const loginEmail = document.getElementById('loginEmail');
  const loginPassword = document.getElementById('loginPassword');
  const studentIdInput = document.getElementById('studentIdInput');

  const bindEnter = (el, fn) => {
    if (!el) return;
    el.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        fn();
      }
    });
  };

  bindEnter(loginEmail, login);
  bindEnter(loginPassword, login);
  bindEnter(studentIdInput, loginStudentFromAdmin);

  if (localStorage.getItem('isAdminLoggedIn') === '1') {
    const publicHeader = document.getElementById('publicHeader');
    if (publicHeader) publicHeader.style.display = 'none';
    initAdminApp();
  } else {
    const publicHeader = document.getElementById('publicHeader');
    if (publicHeader) publicHeader.style.display = '';

    const params = new URLSearchParams(window.location.search);
    const mode = params.get('mode');
    if (mode === 'student') {
      setLoginMode('student');
    }
  }

  const eventBtn = document.getElementById('eventSubmitBtn');
  if (eventBtn) {
    eventBtn.addEventListener('click', onEventSubmit);
  }
});
