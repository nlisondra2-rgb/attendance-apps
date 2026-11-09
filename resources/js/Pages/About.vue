<template>
    <div class="public-page">
        <TopBar />

        <!-- PAGE HERO -->
        <section class="about-hero">
            <div class="about-hero-bg"></div>
            <div class="about-hero-content">
                <div class="about-hero-badge">About the System</div>
                <h1>Built for schools.<br>Designed for simplicity.</h1>
                <p>Event Attendance eliminates paper sign-ups and manual lists. With QR-based check-ins and a powerful admin dashboard, managing attendance has never been easier.</p>
            </div>
        </section>

        <!-- MISSION -->
        <section class="about-section">
            <div class="about-container">
                <div class="about-mission-grid">
                    <div class="about-mission-text">
                        <div class="section-label">Our Mission</div>
                        <h2>Accurate attendance,<br>zero headaches.</h2>
                        <p>We built this system to solve a real problem: messy paper attendance sheets, students missing check-ins, and admins spending hours compiling records. Event Attendance digitizes the entire workflow — from student registration to final reporting — so everyone can focus on what matters.</p>
                        <div class="mission-stats">
                            <div class="mission-stat">
                                <span class="mission-stat-num">100%</span>
                                <span class="mission-stat-label">Digital Records</span>
                            </div>
                            <div class="mission-stat">
                                <span class="mission-stat-num">⚡</span>
                                <span class="mission-stat-label">Instant Check-in</span>
                            </div>
                            <div class="mission-stat">
                                <span class="mission-stat-num">📊</span>
                                <span class="mission-stat-label">Real-time Reports</span>
                            </div>
                        </div>
                    </div>
                    <div class="about-mission-visual">
                        <div class="visual-card">
                            <div class="visual-card-header">📋 Event: Foundation Day</div>
                            <div class="visual-card-row">
                                <span class="vcr-name">Juan Dela Cruz</span>
                                <span class="vcr-badge present">✓ Present</span>
                            </div>
                            <div class="visual-card-row">
                                <span class="vcr-name">Maria Santos</span>
                                <span class="vcr-badge present">✓ Present</span>
                            </div>
                            <div class="visual-card-row">
                                <span class="vcr-name">Jose Reyes</span>
                                <span class="vcr-badge absent">✗ Absent</span>
                            </div>
                            <div class="visual-card-row">
                                <span class="vcr-name">Ana Lim</span>
                                <span class="vcr-badge present">✓ Present</span>
                            </div>
                            <div class="visual-card-footer">3 of 4 attended • 75%</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FEATURES LIST -->
        <section class="about-features">
            <div class="about-container">
                <div class="section-label" style="text-align:center;">Features</div>
                <h2 class="section-title-dark" style="text-align:center; margin-bottom:2.5rem;">Everything you need, nothing you don't</h2>
                <div class="feature-list-grid">
                    <div class="feature-list-item" v-for="f in features" :key="f.title">
                        <div class="fli-icon">{{ f.icon }}</div>
                        <div>
                            <div class="fli-title">{{ f.title }}</div>
                            <div class="fli-desc">{{ f.desc }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FOOTER -->
        <footer class="public-footer">
            <p>© 2025 Attendance Management System. All rights reserved.</p>
            <nav>
                <a href="/">Home</a>
                <a href="/about">About</a>
                <a href="/contact">Contact</a>
            </nav>
        </footer>
    </div>
</template>

<script setup>
import TopBar from '../Components/TopBar.vue';
const features = [
    { icon: '👤', title: 'Student Registration', desc: 'Register students with ID, course, year level, and optional QR from their school ID image.' },
    { icon: '📷', title: 'QR Code Check-in', desc: 'Students scan their QR code for instant check-in. Manual ID entry is also supported.' },
    { icon: '📅', title: 'Event Management', desc: 'Create and manage events with schedules, target year levels, and auto-tracking.' },
    { icon: '📊', title: 'Analytics Dashboard', desc: 'View attendance rates, event summaries, and year-level breakdowns in real-time.' },
    { icon: '🖨️', title: 'Print Reports', desc: 'Export and print participant lists for any event in one click.' },
    { icon: '🔒', title: 'Secure Portals', desc: 'Separate admin and student logins keep your data private and organized.' },
];
</script>

<style scoped>
.public-page { min-height: 100vh; background: #f8fafc; }
.section-label { font-size: 0.8rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: #2563eb; margin-bottom: 0.6rem; }

/* HERO */
.about-hero {
    background: linear-gradient(135deg, #0f1c3f 0%, #1e3a8a 100%);
    padding: 6rem 6rem 5rem;
    position: relative;
    overflow: hidden;
}
.about-hero-bg {
    position: absolute; inset: 0;
    background: radial-gradient(ellipse at 80% 50%, rgba(99,102,241,0.2) 0%, transparent 65%);
    pointer-events: none;
}
.about-hero-content { position: relative; z-index: 1; max-width: 700px; }
.about-hero-badge {
    display: inline-block;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    color: #a5b4fc;
    font-size: 0.78rem;
    font-weight: 600;
    padding: 0.35rem 0.9rem;
    border-radius: 999px;
    margin-bottom: 1.25rem;
    letter-spacing: 0.05em;
}
.about-hero-content h1 { font-size: 3rem; font-weight: 900; color: white; line-height: 1.15; margin-bottom: 1.25rem; }
.about-hero-content p { font-size: 1.05rem; color: #93c5fd; line-height: 1.7; max-width: 560px; }

/* MISSION */
.about-section { padding: 5rem 4rem; background: white; }
.about-container { max-width: 1100px; margin: 0 auto; }
.about-mission-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center; }
.about-mission-text h2 { font-size: 2rem; font-weight: 800; color: #0f1c3f; line-height: 1.2; margin: 0.5rem 0 1rem; }
.about-mission-text p { font-size: 0.95rem; color: #64748b; line-height: 1.75; margin-bottom: 2rem; }
.mission-stats { display: flex; gap: 2rem; flex-wrap: wrap; }
.mission-stat { display: flex; flex-direction: column; gap: 0.25rem; }
.mission-stat-num { font-size: 1.5rem; font-weight: 800; color: #2563eb; }
.mission-stat-label { font-size: 0.8rem; color: #64748b; font-weight: 500; }

/* VISUAL CARD */
.about-mission-visual { display: flex; justify-content: center; }
.visual-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 1.5rem;
    width: 100%;
    max-width: 360px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.07);
}
.visual-card-header { font-size: 0.9rem; font-weight: 700; color: #0f1c3f; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid #e2e8f0; }
.visual-card-row { display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0; border-bottom: 1px solid #f1f5f9; }
.vcr-name { font-size: 0.875rem; color: #334155; font-weight: 500; }
.vcr-badge { font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.65rem; border-radius: 999px; }
.vcr-badge.present { background: #d1fae5; color: #065f46; }
.vcr-badge.absent { background: #fee2e2; color: #991b1b; }
.visual-card-footer { margin-top: 0.85rem; font-size: 0.8rem; color: #64748b; text-align: right; font-weight: 600; }

/* FEATURES */
.about-features { padding: 5rem 4rem; background: #f8fafc; }
.section-title-dark { font-size: 2rem; font-weight: 800; color: #0f1c3f; }
.feature-list-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.25rem; }
.feature-list-item {
    display: flex; gap: 1rem; align-items: flex-start;
    background: white;
    border: 1px solid #f1f5f9;
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    box-shadow: 0 1px 6px rgba(0,0,0,0.04);
    transition: transform 0.2s;
}
.feature-list-item:hover { transform: translateY(-2px); }
.fli-icon { font-size: 1.5rem; flex-shrink: 0; margin-top: 0.1rem; }
.fli-title { font-size: 0.95rem; font-weight: 700; color: #0f1c3f; margin-bottom: 0.25rem; }
.fli-desc { font-size: 0.85rem; color: #64748b; line-height: 1.6; }

/* FOOTER */
.public-footer {
    background: #0f1c3f;
    padding: 1.5rem 4rem;
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;
    border-top: 1px solid rgba(255,255,255,0.06);
}
.public-footer p { color: #64748b; font-size: 0.85rem; }
.public-footer nav { display: flex; gap: 1.5rem; }
.public-footer nav a { color: #64748b; text-decoration: none; font-size: 0.85rem; transition: color 0.15s; }
.public-footer nav a:hover { color: white; }

@media (max-width: 768px) {
    .about-hero { padding: 4rem 1.5rem; }
    .about-hero-content h1 { font-size: 2rem; }
    .about-section { padding: 3rem 1.5rem; }
    .about-mission-grid { grid-template-columns: 1fr; }
    .about-features { padding: 3rem 1.5rem; }
    .public-footer { flex-direction: column; text-align: center; }
}

@media (max-width: 480px) {
    .about-hero { padding: 3rem 1rem; }
    .about-hero-content h1 { font-size: 1.75rem; }
    .about-hero-content p { font-size: 0.95rem; }
    .about-section { padding: 2.5rem 1rem; }
    .about-features { padding: 2.5rem 1rem; }
    .about-values-grid { grid-template-columns: 1fr; }
}
</style>
