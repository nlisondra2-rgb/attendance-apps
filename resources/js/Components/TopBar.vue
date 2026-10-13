<template>
    <header id="publicHeader" class="topbar">
        <div class="topbar-container">
            <div class="topbar-left">
                <div class="brand">Event Attendance</div>
            </div>

            <!-- Mobile menu button -->
            <button class="mobile-menu-toggle" @click="menuOpen = !menuOpen">
                <svg v-if="!menuOpen" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg v-else width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <!-- Desktop & Mobile Nav -->
            <div class="topbar-right" :class="{ 'is-open': menuOpen }">
                <nav class="topnav">
                    <a href="/">Home</a>
                    <a href="/about">About</a>
                    <a href="/contact">Contact</a>
                </nav>

                <a v-if="showLogout" class="logout-btn" href="#" @click.prevent="$emit('logout')">Logout</a>
                <a v-else class="logout-btn" href="/admin">Login</a>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref } from 'vue';

defineProps({
    showLogout: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['logout']);

const menuOpen = ref(false);
</script>

<style scoped>
.topbar-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
}
.topbar-right {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.mobile-menu-toggle {
    display: none;
    background: transparent;
    border: none;
    color: white;
    cursor: pointer;
    padding: 0.5rem;
}

@media (max-width: 768px) {
    .mobile-menu-toggle {
        display: block;
    }
    .topbar-container {
        flex-wrap: wrap;
    }
    .topbar-right {
        display: none;
        flex-direction: column;
        width: 100%;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        gap: 1rem;
    }
    .topbar-right.is-open {
        display: flex;
    }
    .topnav {
        flex-direction: column;
        width: 100%;
        gap: 0.5rem;
    }
    .topnav a {
        display: block;
        width: 100%;
        text-align: center;
        padding: 0.75rem;
    }
    .logout-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
