<script setup>
import { ref, onMounted, watch } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

// Themes supported in our DaisyUI configuration
const themes = ['light', 'dark', 'luxury', 'cyberpunk'];
const currentTheme = ref('light');

onMounted(() => {
    // Check localStorage for a saved theme
    const savedTheme = localStorage.getItem('admin-theme');
    if (savedTheme && themes.includes(savedTheme)) {
        currentTheme.value = savedTheme;
    } else {
        // Fallback to media query preference if available, else 'light'
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            currentTheme.value = 'dark';
        }
    }
    
    // Apply theme
    applyTheme(currentTheme.value);
});

watch(currentTheme, (newTheme) => {
    applyTheme(newTheme);
    localStorage.setItem('admin-theme', newTheme);
});

function applyTheme(themeName) {
    document.documentElement.setAttribute('data-theme', themeName);
}
</script>

<template>
    <div class="min-h-screen bg-base-200 text-base-content font-sans">
        <!-- Top Navbar -->
        <div class="navbar bg-base-100/80 backdrop-blur-md shadow-lg border-b border-base-300 z-50 sticky top-0 px-4">
            <div class="flex-1">
                <a class="btn btn-ghost normal-case text-2xl font-black tracking-tighter bg-gradient-to-r from-primary to-accent bg-clip-text text-transparent italic">VueCMS</a>
            </div>
            
            <div class="flex-none gap-2">
                <!-- Theme Switcher Dropdown -->
                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-ghost btn-circle">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" /></svg>
                    </label>
                    <ul tabindex="0" class="mt-3 z-[1] p-2 shadow-xl menu menu-sm dropdown-content bg-base-100 rounded-box w-52 border border-base-200">
                        <li class="menu-title"><span>Select Theme</span></li>
                        <li v-for="theme in themes" :key="theme">
                            <a :class="{ 'active': currentTheme === theme }" @click="currentTheme = theme">
                                <span class="capitalize">{{ theme }}</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Admin Profile/Logout Dropdown Placeholder -->
                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-ghost btn-circle avatar placeholder">
                        <div class="bg-neutral text-neutral-content rounded-full w-10">
                            <span>AD</span>
                        </div>
                    </label>
                    <ul tabindex="0" class="mt-3 z-[1] p-2 shadow-xl menu menu-sm dropdown-content bg-base-100 rounded-box w-52 border border-base-200">
                        <li><a>Settings</a></li>
                        <li><a>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="flex h-[calc(100vh-4rem)]">
            <!-- Sidebar Navigation -->
            <div class="drawer lg:drawer-open w-auto z-40 relative">
                <input id="admin-drawer" type="checkbox" class="drawer-toggle" />
                <div class="drawer-side h-full absolute lg:static">
                    <label for="admin-drawer" class="drawer-overlay"></label> 
                    <ul class="menu p-4 w-64 h-full bg-base-100 text-base-content border-r border-base-300 gap-0.5">
                        <!-- Sidebar content here -->
                        <li class="menu-title"><span>Content</span></li>
                        <li>
                            <Link href="/admin/pages" :class="{ 'active': $page.url.startsWith('/admin/pages') }">
                                <i class="fas fa-file-alt w-5"></i>
                                Pages
                            </Link>
                        </li>
                        <li>
                            <Link href="/admin/posts" :class="{ 'active': $page.url.startsWith('/admin/posts') }">
                                <i class="fas fa-blog w-5"></i>
                                Blog Posts
                            </Link>
                        </li>
                        <li>
                            <Link href="/admin/projects" :class="{ 'active': $page.url.startsWith('/admin/projects') }">
                                <i class="fas fa-project-diagram w-5"></i>
                                Projects
                            </Link>
                        </li>
                        <li>
                            <Link href="/admin/forms" :class="{ 'active': $page.url === '/admin/forms' || $page.url.startsWith('/admin/forms/') }">
                                <i class="fas fa-envelope-open-text w-5"></i>
                                Forms
                            </Link>
                        </li>
                        
                        <li class="menu-title mt-4"><span>Design & Layout</span></li>
                        <li>
                            <Link href="/admin/templates" :class="{ 'active': $page.url.startsWith('/admin/templates') }">
                                <i class="fas fa-layer-group w-5"></i>
                                Headers & Footers
                            </Link>
                        </li>
                        <li>
                            <Link href="/admin/menus" :class="{ 'active': $page.url.startsWith('/admin/menus') }">
                                <i class="fas fa-bars w-5"></i>
                                Menus
                            </Link>
                        </li>

                        <li class="menu-title mt-4"><span>Library</span></li>
                        <li>
                            <Link href="/admin/media" :class="{ 'active': $page.url.startsWith('/admin/media') }">
                                <i class="fas fa-photo-video w-5"></i>
                                Media
                            </Link>
                        </li>

                        <li class="menu-title mt-4"><span>System</span></li>
                        <li>
                            <Link href="/admin/languages" :class="{ 'active': $page.url.startsWith('/admin/languages') }">
                                <i class="fas fa-language w-5"></i>
                                Languages
                            </Link>
                        </li>
                        <li>
                            <Link href="/admin/translations" :class="{ 'active': $page.url.startsWith('/admin/translations') }">
                                <i class="fas fa-language w-5"></i>
                                Translations
                            </Link>
                        </li>
                        <li>
                            <Link href="/admin/settings" :class="{ 'active': $page.url.startsWith('/admin/settings') }">
                                <i class="fas fa-cog w-5"></i>
                                Settings
                            </Link>
                        </li>

                    </ul>
                </div>
            </div>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-base-200 p-6">
                <!-- Mobile drawer toggle -->
                <div class="lg:hidden mb-4">
                    <label for="admin-drawer" class="btn btn-primary drawer-button">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-5 h-5 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        Menu
                    </label>
                </div>

                <div class="max-w-7xl mx-auto">
                    <!-- Header Section if provided -->
                    <div v-if="$slots.header" class="mb-6 p-6 bg-base-100 rounded-box shadow-sm border border-base-300">
                        <slot name="header"></slot>
                    </div>

                    <div>
                        <slot></slot>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
