<template>
    <div class="public-page">
        <TopBar />

        <!-- PAGE HERO -->
        <section class="contact-hero">
            <div class="contact-hero-bg"></div>
            <div class="contact-hero-content">
                <div class="contact-hero-badge">Get In Touch</div>
                <h1>We're here to help.</h1>
                <p>Have a question, found a bug, or need support? Reach out to us using any of the channels below.</p>
            </div>
        </section>

        <!-- MAIN CONTENT -->
        <section class="contact-section">
            <div class="contact-container">

                <!-- INFO CARDS -->
                <div class="contact-info-grid">
                    <div class="contact-info-card">
                        <div class="cic-icon">📧</div>
                        <div class="cic-label">Email</div>
                        <div class="cic-value">support@attendance.test</div>
                    </div>
                    <div class="contact-info-card">
                        <div class="cic-icon">📞</div>
                        <div class="cic-label">Phone</div>
                        <div class="cic-value">+63 900 000 0000</div>
                    </div>
                    <div class="contact-info-card">
                        <div class="cic-icon">🏢</div>
                        <div class="cic-label">Office</div>
                        <div class="cic-value">Student Services Office</div>
                    </div>
                    <div class="contact-info-card">
                        <div class="cic-icon">🕐</div>
                        <div class="cic-label">Office Hours</div>
                        <div class="cic-value">Mon–Fri, 8:00 AM – 5:00 PM</div>
                    </div>
                </div>

                <!-- CONTACT FORM -->
                <div class="contact-form-wrap">
                    <h2 class="contact-form-title">Send us a message</h2>
                    <p class="contact-form-sub">Fill out the form and we'll get back to you as soon as possible.</p>
                    <div class="contact-form">
                        <div class="cf-row">
                            <div class="cf-group">
                                <label>Full Name</label>
                                <input v-model="form.name" type="text" placeholder="Juan Dela Cruz" class="cf-input" />
                            </div>
                            <div class="cf-group">
                                <label>Email Address</label>
                                <input v-model="form.email" type="email" placeholder="you@email.com" class="cf-input" />
                            </div>
                        </div>
                        <div class="cf-group">
                            <label>Subject</label>
                            <input v-model="form.subject" type="text" placeholder="How can we help?" class="cf-input" />
                        </div>
                        <div class="cf-group">
                            <label>Message</label>
                            <textarea v-model="form.message" rows="5" placeholder="Describe your issue or question..." class="cf-input cf-textarea"></textarea>
                        </div>
                        <button class="cf-submit" @click="submitForm">Send Message →</button>
                        <div v-if="sent" class="cf-success">✅ Thank you! Your message has been sent.</div>
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
import { reactive, ref } from 'vue';
import TopBar from '../Components/TopBar.vue';

const sent = ref(false);
const form = reactive({ name: '', email: '', subject: '', message: '' });

function submitForm() {
    if (!form.name || !form.email || !form.message) return;
    // Simulate sending
    sent.value = true;
    form.name = '';
    form.email = '';
    form.subject = '';
    form.message = '';
    setTimeout(() => { sent.value = false; }, 4000);
}
</script>

<style scoped>
.public-page { min-height: 100vh; background: #f8fafc; }

/* HERO */
.contact-hero {
    background: linear-gradient(135deg, #0f1c3f 0%, #1e3a8a 100%);
    padding: 6rem 6rem 5rem;
    position: relative;
    overflow: hidden;
}
.contact-hero-bg {
    position: absolute; inset: 0;
    background: radial-gradient(ellipse at 80% 50%, rgba(99,102,241,0.2) 0%, transparent 65%);
    pointer-events: none;
}
.contact-hero-content { position: relative; z-index: 1; max-width: 600px; }
.contact-hero-badge {
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
.contact-hero-content h1 { font-size: 3rem; font-weight: 900; color: white; margin-bottom: 1rem; }
.contact-hero-content p { font-size: 1.05rem; color: #93c5fd; line-height: 1.7; }

/* SECTION */
.contact-section { padding: 5rem 4rem; }
.contact-container { max-width: 1000px; margin: 0 auto; }

/* INFO CARDS */
.contact-info-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    margin-bottom: 3rem;
}
.contact-info-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 1.5rem;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: transform 0.2s;
}
.contact-info-card:hover { transform: translateY(-3px); }
.cic-icon { font-size: 2rem; margin-bottom: 0.75rem; }
.cic-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; margin-bottom: 0.35rem; }
.cic-value { font-size: 0.9rem; font-weight: 600; color: #0f1c3f; }

/* FORM */
.contact-form-wrap {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
}
.contact-form-title { font-size: 1.5rem; font-weight: 800; color: #0f1c3f; margin-bottom: 0.4rem; }
.contact-form-sub { font-size: 0.9rem; color: #64748b; margin-bottom: 2rem; }
.cf-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; }
.cf-group { display: flex; flex-direction: column; gap: 0.4rem; margin-bottom: 1rem; }
.cf-group label { font-size: 0.82rem; font-weight: 600; color: #334155; }
.cf-input {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
    color: #0f1c3f;
    background: #f8fafc;
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
    font-family: inherit;
    width: 100%;
    box-sizing: border-box;
}
.cf-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
.cf-textarea { resize: vertical; min-height: 130px; }
.cf-submit {
    background: #2563eb;
    color: white;
    border: none;
    padding: 0.85rem 2rem;
    border-radius: 10px;
    font-size: 0.95rem;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s, transform 0.2s;
    margin-top: 0.5rem;
    font-family: inherit;
}
.cf-submit:hover { background: #1d4ed8; transform: translateY(-1px); }
.cf-success {
    margin-top: 1rem;
    background: #d1fae5;
    color: #065f46;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
    font-weight: 600;
}

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
    .contact-hero { padding: 4rem 1.5rem; }
    .contact-hero-content h1 { font-size: 2rem; }
    .contact-section { padding: 3rem 1.5rem; }
    .contact-info-grid { grid-template-columns: 1fr 1fr; }
    .cf-row { grid-template-columns: 1fr; }
    .public-footer { flex-direction: column; text-align: center; }
}
@media (max-width: 560px) {
    .contact-info-grid { grid-template-columns: 1fr; }
}
</style>
