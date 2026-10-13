<template>
    <div class="public-page">
        <TopBar />

        <!-- HERO -->
        <section class="hero-section">
            <div class="hero-bg-decoration"></div>
            <div class="hero-content">
                <div class="hero-badge">📋 Attendance Management System</div>
                <h1 class="hero-title">Smart Attendance<br>for Modern Schools</h1>
                <p class="hero-subtitle">
                    Streamline event attendance with QR-code scanning, real-time tracking,
                    and powerful analytics — all in one dashboard.
                </p>
                <div class="hero-actions">
                    <a href="/admin" class="hero-btn-primary">🚀 Get Started</a>
                    <a href="/about" class="hero-btn-outline">Learn More →</a>
                </div>
            </div>
            <div class="hero-visual">
                <div class="stat-card-float" style="top:10%; left:5%;">
                    <div class="stat-float-icon">✅</div>
                    <div>
                        <div class="stat-float-num">1,240</div>
                        <div class="stat-float-label">Check-ins Today</div>
                    </div>
                </div>
                <div class="stat-card-float" style="top:55%; right:5%;">
                    <div class="stat-float-icon">🎓</div>
                    <div>
                        <div class="stat-float-num">98.5%</div>
                        <div class="stat-float-label">Accuracy Rate</div>
                    </div>
                </div>
                <div class="hero-dashboard-mock">
                    <div class="mock-bar" style="width:85%;"></div>
                    <div class="mock-bar" style="width:65%;"></div>
                    <div class="mock-bar" style="width:90%;"></div>
                    <div class="mock-bar" style="width:50%;"></div>
                    <div class="mock-bar" style="width:75%;"></div>
                </div>
            </div>
        </section>

        <!-- FEATURES -->
        <section class="features-section">
            <div class="section-container">
                <div class="section-label">Why Choose Us</div>
                <h2 class="section-title">Everything you need to manage attendance</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">📷</div>
                        <h3>QR Code Scanning</h3>
                        <p>Students scan their school IDs in seconds. No typing, no delays — just fast and accurate check-ins.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon" style="background: linear-gradient(135deg, #10b981, #059669);">📊</div>
                        <h3>Real-time Analytics</h3>
                        <p>View attendance rates, event participation, and year-level breakdowns instantly on your dashboard.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">👥</div>
                        <h3>Student Management</h3>
                        <p>Register, edit, and manage all student records in one place. CSV export included.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">📅</div>
                        <h3>Event Management</h3>
                        <p>Create events with targeted year levels, set schedules, and track participants automatically.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">🔒</div>
                        <h3>Secure Access</h3>
                        <p>Separate admin and student portals with password-protected logins keep your data safe.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon" style="background: linear-gradient(135deg, #06b6d4, #0891b2);">🖨️</div>
                        <h3>Print Reports</h3>
                        <p>Generate and print participant lists for any event with a single click — always ready.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA BANNER -->
        <section class="cta-section">
            <div class="cta-inner">
                <h2>Ready to modernize your attendance?</h2>
                <p>Get started in minutes. No setup required.</p>
                <a href="/admin" class="hero-btn-primary">Go to Admin Dashboard →</a>
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
</script>

<style scoped>
.public-page { min-height: 100vh; background: #f8fafc; }

/* HERO */
.hero-section {
    background: linear-gradient(135deg, #0f1c3f 0%, #1e3a8a 60%, #1d4ed8 100%);
    min-height: 88vh;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 5rem 6rem;
    position: relative;
    overflow: hidden;
    gap: 3rem;
}
.hero-bg-decoration {
    position: absolute;
    width: 600px; height: 600px;
    background: radial-gradient(circle, rgba(99,102,241,0.2) 0%, transparent 70%);
    top: -150px; right: -100px;
    border-radius: 50%;
    pointer-events: none;
}
.hero-content { flex: 1; max-width: 540px; z-index: 1; }
.hero-badge {
    display: inline-block;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    color: #a5b4fc;
    font-size: 0.8rem;
    font-weight: 600;
    padding: 0.4rem 1rem;
    border-radius: 999px;
    margin-bottom: 1.5rem;
    letter-spacing: 0.05em;
}
.hero-title {
    font-size: 3.5rem;
    font-weight: 900;
    color: white;
    line-height: 1.1;
    margin-bottom: 1.25rem;
}
.hero-subtitle {
    font-size: 1.1rem;
    color: #93c5fd;
    line-height: 1.7;
    margin-bottom: 2rem;
}
.hero-actions { display: flex; gap: 1rem; flex-wrap: wrap; }
.hero-btn-primary {
    display: inline-block;
    background: #2563eb;
    color: white;
    font-weight: 700;
    padding: 0.85rem 2rem;
    border-radius: 999px;
    text-decoration: none;
    font-size: 0.95rem;
    transition: background 0.2s, transform 0.2s;
    box-shadow: 0 4px 14px rgba(37,99,235,0.4);
}
.hero-btn-primary:hover { background: #1d4ed8; transform: translateY(-2px); }
.hero-btn-outline {
    display: inline-block;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.3);
    color: white;
    font-weight: 600;
    padding: 0.85rem 2rem;
    border-radius: 999px;
    text-decoration: none;
    font-size: 0.95rem;
    transition: background 0.2s;
}
.hero-btn-outline:hover { background: rgba(255,255,255,0.15); }

/* HERO VISUAL */
.hero-visual {
    flex: 1;
    max-width: 420px;
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: center;
}
.stat-card-float {
    position: absolute;
    background: white;
    border-radius: 16px;
    padding: 0.85rem 1.1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    box-shadow: 0 8px 32px rgba(0,0,0,0.18);
    animation: float 3s ease-in-out infinite alternate;
    z-index: 2;
}
.stat-card-float:nth-child(2) { animation-delay: 1.5s; }
.stat-float-icon { font-size: 1.5rem; }
.stat-float-num { font-size: 1.2rem; font-weight: 800; color: #0f1c3f; }
.stat-float-label { font-size: 0.72rem; color: #64748b; font-weight: 500; }
@keyframes float {
    from { transform: translateY(0); }
    to { transform: translateY(-10px); }
}
.hero-dashboard-mock {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 20px;
    padding: 2rem;
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
}
.mock-bar {
    height: 14px;
    background: linear-gradient(90deg, #3b82f6, #6366f1);
    border-radius: 999px;
    opacity: 0.7;
}

/* FEATURES */
.features-section { padding: 6rem 4rem; background: #f8fafc; }
.section-container { max-width: 1100px; margin: 0 auto; }
.section-label {
    text-align: center;
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: #2563eb;
    margin-bottom: 0.75rem;
}
.section-title {
    text-align: center;
    font-size: 2.25rem;
    font-weight: 800;
    color: #0f1c3f;
    margin-bottom: 3rem;
}
.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}
.feature-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid #f1f5f9;
}
.feature-card:hover { transform: translateY(-4px); box-shadow: 0 8px 28px rgba(0,0,0,0.1); }
.feature-icon {
    width: 52px; height: 52px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1.25rem;
}
.feature-card h3 { font-size: 1.05rem; font-weight: 700; color: #0f1c3f; margin-bottom: 0.5rem; }
.feature-card p { font-size: 0.9rem; color: #64748b; line-height: 1.65; }

/* CTA */
.cta-section { background: linear-gradient(135deg, #0f1c3f 0%, #1e3a8a 100%); padding: 5rem 2rem; text-align: center; }
.cta-inner h2 { font-size: 2rem; font-weight: 800; color: white; margin-bottom: 0.75rem; }
.cta-inner p { font-size: 1rem; color: #93c5fd; margin-bottom: 2rem; }

/* FOOTER */
.public-footer {
    background: #0f1c3f;
    padding: 1.5rem 4rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    border-top: 1px solid rgba(255,255,255,0.06);
}
.public-footer p { color: #64748b; font-size: 0.85rem; }
.public-footer nav { display: flex; gap: 1.5rem; }
.public-footer nav a { color: #64748b; text-decoration: none; font-size: 0.85rem; transition: color 0.15s; }
.public-footer nav a:hover { color: white; }

/* RESPONSIVE */
@media (max-width: 768px) {
    .hero-section { flex-direction: column; padding: 4rem 2rem; text-align: center; }
    .hero-title { font-size: 2.5rem; }
    .hero-actions { justify-content: center; }
    .hero-visual { display: none; }
    .features-section { padding: 4rem 1.5rem; }
    .public-footer { flex-direction: column; text-align: center; }
}
</style>
