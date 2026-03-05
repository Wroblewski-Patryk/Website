<script setup>
import { ref, onMounted, watch } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import ThemeStyleProvider from '@/Components/ThemeStyleProvider.vue';
import { PhFileText, PhFeather, PhCards, PhTextbox, PhPaintRoller, PhCube, PhLayout, PhList, PhImageSquare, PhGlobe, PhTranslate, PhGearSix, PhBell, PhCaretLeft, PhCaretRight, PhSun, PhMoon, PhPalette, PhUser, PhUsers, PhSignOut, PhLifebuoy } from '@phosphor-icons/vue';

defineProps({
    fullWidth: {
        type: Boolean,
        default: false
    }
});

// Themes supported in our DaisyUI configuration
const themesList = ['light', 'dark', 'emerald', 'corporate', 'retro', 'cyberpunk', 'dracula'];
const currentTheme = ref('light');
const isSidebarCollapsed = ref(false);

onMounted(() => {
    // Check localStorage for a saved theme
    const savedTheme = localStorage.getItem('admin-theme');
    
    if (savedTheme && themesList.includes(savedTheme)) {
        currentTheme.value = savedTheme;
    } else {
        // Fallback to media query preference if available, else 'light'
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            currentTheme.value = 'dark';
        }
    }
    
    // Apply theme
    applyTheme(currentTheme.value);

    // Sidebar state
    const savedSidebar = localStorage.getItem('admin-sidebar-collapsed');
    if (savedSidebar) {
        isSidebarCollapsed.value = savedSidebar === 'true';
    }
});

watch(currentTheme, (newTheme) => {
    applyTheme(newTheme);
    localStorage.setItem('admin-theme', newTheme);
});

watch(isSidebarCollapsed, (newVal) => {
    localStorage.setItem('admin-sidebar-collapsed', newVal);
});

function applyTheme(themeName) {
    document.documentElement.setAttribute('data-theme', themeName);
}
</script>

<template>
    <ThemeStyleProvider />
    <div class="min-h-screen bg-base-200 text-base-content font-sans">
        <!-- Top Navbar -->
        <div class="navbar bg-base-100/80 backdrop-blur-md shadow-lg border-b border-base-300 z-50 sticky top-0 px-4">
            <div class="flex-1">
                <Link href="/admin/" class="btn btn-ghost normal-case hover:bg-transparent flex items-center gap-1 group w-fit px-2">
                    <div class="h-8 w-8 bg-gradient-to-r from-primary to-accent" 
                         style="mask: url('/img/featherly-sygnet.svg') no-repeat center / contain; -webkit-mask: url('/img/featherly-sygnet.svg') no-repeat center / contain; background-size: 160px 100%; background-position: left center;">
                    </div>
                    <span class="bg-gradient-to-r from-primary to-accent bg-clip-text text-transparent text-2xl font-normal tracking-wide m-auto pr-1"
                          style="background-size: 160px 100%; background-position: -44px center;">
                        Featherly
                    </span>
                </Link>
            </div>
            
            <div class="flex-none gap-2">
                <!-- Color Themes Dropdown -->
                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-ghost btn-circle">
                        <PhPalette weight="regular" class="w-5 h-5 text-base-content/70" />
                    </label>
                    <ul tabindex="0" class="mt-3 z-[1] p-2 shadow-xl menu menu-sm dropdown-content bg-base-100 rounded-box w-52 border border-base-200">
                        <li class="menu-title"><span>Wybierz Motyw</span></li>
                        <li v-for="theme in themesList" :key="theme">
                            <a :class="{ 'active': currentTheme === theme }" @click="currentTheme = theme">
                                <span class="capitalize w-full">{{ theme }}</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Admin Profile/Logout Dropdown -->
                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-ghost btn-circle avatar placeholder transition-transform hover:scale-105 ml-1 ring ring-primary ring-offset-base-100 ring-offset-1">
                        <div class="bg-primary text-primary-content rounded-full w-9">
                            <span class="text-sm">{{ $page.props.auth?.user?.name?.substring(0, 2)?.toUpperCase() || 'AD' }}</span>
                        </div>
                    </label>
                    <ul tabindex="0" class="mt-3 z-[1] p-2 shadow-xl menu menu-sm dropdown-content bg-base-100 rounded-box w-56 border border-base-200 gap-1">
                        <li class="menu-title flex flex-col items-start gap-0 p-3 border-b border-base-200/50 mb-1 pointer-events-none">
                            <span class="font-bold text-sm text-base-content w-full whitespace-nowrap overflow-hidden text-ellipsis">{{ $page.props.auth?.user?.name || 'Administrator' }}</span>
                            <span class="text-xs font-normal text-base-content/60 w-full whitespace-nowrap overflow-hidden text-ellipsis mt-1">{{ $page.props.auth?.user?.email || 'admin@example.com' }}</span>
                        </li>
                        <li v-if="$page.props.auth?.user?.id"><Link :href="`/admin/users/${$page.props.auth.user.id}/edit`"><PhUser weight="regular" class="w-4 h-4 text-base-content/70" /> Mój Profil</Link></li>
                        <li><Link href="/admin/settings"><PhGearSix weight="regular" class="w-4 h-4 text-base-content/70" /> Ustawienia konta</Link></li>
                        <li><Link href="#"><PhLifebuoy weight="regular" class="w-4 h-4 text-base-content/70" /> Pomoc techniczna</Link></li>
                        <div class="h-[1px] bg-base-200 my-1 mx-2"></div>
                        <li><Link href="#" class="text-error hover:bg-error/10 hover:text-error"><PhSignOut weight="regular" class="w-4 h-4" /> Wyloguj się</Link></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="flex h-[calc(100vh-4rem)]">
            <!-- Sidebar Navigation -->
            <div class="drawer lg:drawer-open w-auto z-40 relative">
                <input id="admin-drawer" type="checkbox" class="drawer-toggle" />
                <div class="drawer-side h-full absolute lg:static shadow-xl lg:shadow-none overflow-visible">
                    <label for="admin-drawer" class="drawer-overlay"></label> 
                    
                    <div class="relative h-full flex flex-col">
                        <!-- Toggle Button -->
                        <button 
                            @click="isSidebarCollapsed = !isSidebarCollapsed"
                            class="hidden lg:flex absolute top-6 right-0 translate-x-1/2 btn btn-circle btn-sm bg-base-100 border-base-200 shadow-sm z-50 transition-transform duration-300 hover:bg-base-200 hover:scale-110">
                            <PhCaretRight v-if="isSidebarCollapsed" weight="bold" class="w-4 h-4 text-base-content/70" />
                            <PhCaretLeft v-else weight="bold" class="w-4 h-4 text-base-content/70" />
                        </button>

                        <ul class="menu p-4 h-[calc(100vh-4rem)] bg-base-100/80 backdrop-blur-md text-base-content border-r border-base-200 gap-1 overflow-y-auto overflow-x-hidden transition-[width] duration-300 ease-in-out flex-nowrap"
                            :class="[isSidebarCollapsed ? 'w-[84px] [&_details_summary::after]:hidden' : 'w-64']">
                            
                            <!-- Sidebar content here -->
                            <li class="menu-title mt-2 mb-2 border-b border-base-200/50 pb-1 pointer-events-none">
                                <span class="text-[10px] uppercase font-bold tracking-widest text-primary/60 whitespace-nowrap overflow-hidden transition-all duration-300" 
                                      v-show="!isSidebarCollapsed">Content</span>
                            </li>
                            <li>
                                <Link href="/admin/pages" class="group hover:bg-primary/5 hover:text-primary transition-all bg-transparent flex items-center text-nowrap" :class="{ 'text-primary': $page.url.startsWith('/admin/pages') }">
                                    <PhFileText weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-primary': $page.url.startsWith('/admin/pages'), 'text-base-content/50 group-hover:text-primary': !$page.url.startsWith('/admin/pages')}" />
                                    <span v-show="!isSidebarCollapsed" class="ml-2 transition-opacity duration-300">Pages</span>
                                </Link>
                            </li>
                            <li>
                                <details :open="['/admin/posts'].some(p => $page.url.startsWith(p))">
                                    <summary class="group hover:bg-primary/5 hover:text-primary transition-all bg-transparent flex items-center text-nowrap" :class="{'text-primary': $page.url.startsWith('/admin/posts') || $page.url.startsWith('/admin/categories')}">
                                        <PhFeather weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-primary': $page.url.startsWith('/admin/posts') || $page.url.startsWith('/admin/categories'), 'text-base-content/50 group-hover:text-primary': !($page.url.startsWith('/admin/posts') || $page.url.startsWith('/admin/categories'))}" />
                                        <span v-show="!isSidebarCollapsed" class="ml-2 transition-opacity duration-300">Posts</span>
                                    </summary>
                                    <ul v-show="!isSidebarCollapsed">
                                        <li><Link href="/admin/posts" class="group hover:bg-primary/5 hover:text-primary transition-all bg-transparent text-nowrap" :class="{ 'text-primary': $page.url === '/admin/posts' || $page.url.startsWith('/admin/posts/') }">All Posts</Link></li>
                                        <li><Link href="#" class="group hover:bg-primary/5 hover:text-primary transition-all bg-transparent text-nowrap" :class="{ 'text-primary': $page.url.startsWith('/admin/categories') }">Categories</Link></li>
                                    </ul>
                                </details>
                            </li>
                            <li>
                                <Link href="/admin/projects" class="group hover:bg-primary/5 hover:text-primary transition-all bg-transparent flex items-center text-nowrap" :class="{ 'text-primary': $page.url.startsWith('/admin/projects') }">
                                    <PhCards weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-primary': $page.url.startsWith('/admin/projects'), 'text-base-content/50 group-hover:text-primary': !$page.url.startsWith('/admin/projects')}" />
                                    <span v-show="!isSidebarCollapsed" class="ml-2 transition-opacity duration-300">Projects</span>
                                </Link>
                            </li>
                            <li>
                                <Link href="/admin/forms" class="group hover:bg-primary/5 hover:text-primary transition-all bg-transparent flex items-center text-nowrap" :class="{ 'text-primary': $page.url === '/admin/forms' || $page.url.startsWith('/admin/forms/') }">
                                    <PhTextbox weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-primary': $page.url.startsWith('/admin/forms'), 'text-base-content/50 group-hover:text-primary': !$page.url.startsWith('/admin/forms')}" />
                                    <span v-show="!isSidebarCollapsed" class="ml-2 transition-opacity duration-300">Forms</span>
                                </Link>
                            </li>
                            
                            <li class="menu-title mt-6 mb-2 border-b border-base-200/50 pb-1 pointer-events-none">
                                <span class="text-[10px] uppercase font-bold tracking-widest text-secondary/60 whitespace-nowrap overflow-hidden transition-all duration-300" 
                                      v-show="!isSidebarCollapsed">Design</span>
                            </li>
                            <li>
                                <details :open="['/admin/theme/colors', '/admin/theme/fonts', '/admin/theme/typography', '/admin/theme/sizes', '/admin/theme/effects'].includes($page.url)">
                                    <summary class="group hover:bg-secondary/5 hover:text-secondary transition-all bg-transparent flex items-center text-nowrap" :class="{'text-secondary': ['/admin/theme/colors', '/admin/theme/fonts', '/admin/theme/typography', '/admin/theme/sizes', '/admin/theme/effects'].includes($page.url)}">
                                        <PhPaintRoller weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-secondary': ['/admin/theme/colors', '/admin/theme/fonts', '/admin/theme/typography', '/admin/theme/sizes', '/admin/theme/effects'].includes($page.url), 'text-base-content/50 group-hover:text-secondary': !['/admin/theme/colors', '/admin/theme/fonts', '/admin/theme/typography', '/admin/theme/sizes', '/admin/theme/effects'].includes($page.url)}" /> 
                                        <span v-show="!isSidebarCollapsed" class="ml-2 transition-opacity duration-300">Theme</span>
                                    </summary>
                                    <ul v-show="!isSidebarCollapsed">
                                        <li><Link href="/admin/theme/colors" class="group hover:bg-secondary/5 hover:text-secondary transition-all bg-transparent text-nowrap" :class="{ 'text-secondary': $page.url === '/admin/theme/colors' }">Colors</Link></li>
                                        <li><Link href="/admin/theme/fonts" class="group hover:bg-secondary/5 hover:text-secondary transition-all bg-transparent text-nowrap" :class="{ 'text-secondary': $page.url === '/admin/theme/fonts' }">Fonts</Link></li>
                                        <li><Link href="/admin/theme/typography" class="group hover:bg-secondary/5 hover:text-secondary transition-all bg-transparent text-nowrap" :class="{ 'text-secondary': $page.url === '/admin/theme/typography' }">Typography</Link></li>
                                        <li><Link href="/admin/theme/sizes" class="group hover:bg-secondary/5 hover:text-secondary transition-all bg-transparent text-nowrap" :class="{ 'text-secondary': $page.url === '/admin/theme/sizes' }">Sizes / Metrics</Link></li>
                                        <li><Link href="/admin/theme/effects" class="group hover:bg-secondary/5 hover:text-secondary transition-all bg-transparent text-nowrap" :class="{ 'text-secondary': $page.url === '/admin/theme/effects' }">Effects</Link></li>
                                    </ul>
                                </details>
                            </li>
                            <li>
                                <Link href="/admin/blocks" class="group hover:bg-secondary/5 hover:text-secondary transition-all bg-transparent flex items-center text-nowrap" :class="{ 'text-secondary': $page.url === '/admin/blocks' }">
                                    <PhCube weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-secondary': $page.url === '/admin/blocks', 'text-base-content/50 group-hover:text-secondary': $page.url !== '/admin/blocks'}" />
                                    <span v-show="!isSidebarCollapsed" class="ml-2 transition-opacity duration-300">Blocks</span>
                                </Link>
                            </li>
                            <li>
                                <Link href="/admin/templates" class="group hover:bg-secondary/5 hover:text-secondary transition-all bg-transparent flex items-center text-nowrap" :class="{ 'text-secondary': $page.url.startsWith('/admin/templates') }">
                                    <PhLayout weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-secondary': $page.url.startsWith('/admin/templates'), 'text-base-content/50 group-hover:text-secondary': !($page.url.startsWith('/admin/templates'))}" />
                                    <span v-show="!isSidebarCollapsed" class="ml-2 transition-opacity duration-300">Templates</span>
                                </Link>
                            </li>
                            <li>
                                <Link href="/admin/menus" class="group hover:bg-secondary/5 hover:text-secondary transition-all bg-transparent flex items-center text-nowrap" :class="{ 'text-secondary': $page.url.startsWith('/admin/menus') }">
                                    <PhList weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-secondary': $page.url.startsWith('/admin/menus'), 'text-base-content/50 group-hover:text-secondary': !($page.url.startsWith('/admin/menus'))}" />
                                    <span v-show="!isSidebarCollapsed" class="ml-2 transition-opacity duration-300">Menus</span>
                                </Link>
                            </li>
                            
                            <li class="menu-title mt-6 mb-2 border-b border-base-200/50 pb-1 pointer-events-none">
                                <span class="text-[10px] uppercase font-bold tracking-widest text-accent/60 whitespace-nowrap overflow-hidden transition-all duration-300" 
                                      v-show="!isSidebarCollapsed">Library</span>
                            </li>
                            <li>
                                <Link href="/admin/media" class="group hover:bg-accent/5 hover:text-accent transition-all bg-transparent flex items-center text-nowrap" :class="{ 'text-accent': $page.url.startsWith('/admin/media') }">
                                    <PhImageSquare weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-accent': $page.url.startsWith('/admin/media'), 'text-base-content/50 group-hover:text-accent': !($page.url.startsWith('/admin/media'))}" />
                                    <span v-show="!isSidebarCollapsed" class="ml-2 transition-opacity duration-300">Media</span>
                                </Link>
                            </li>
                            
                            <li class="menu-title mt-6 mb-2 border-b border-base-200/50 pb-1 pointer-events-none">
                                <span class="text-[10px] uppercase font-bold tracking-widest text-base-content/50 whitespace-nowrap overflow-hidden transition-all duration-300" 
                                      v-show="!isSidebarCollapsed">System</span>
                            </li>
                            <li>
                                <Link href="/admin/users" class="group hover:bg-base-content/5 hover:text-base-content transition-all bg-transparent flex items-center text-nowrap" :class="{ 'text-base-content': $page.url === '/admin/users' || $page.url.startsWith('/admin/users/') }">
                                    <PhUsers weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-base-content': $page.url.startsWith('/admin/users'), 'text-base-content/50 group-hover:text-base-content': !$page.url.startsWith('/admin/users')}" />
                                    <span v-show="!isSidebarCollapsed" class="ml-2 transition-opacity duration-300">Users</span>
                                </Link>
                            </li>
                            <li>
                                <Link href="/admin/languages" class="group hover:bg-base-content/5 hover:text-base-content transition-all bg-transparent flex items-center text-nowrap" :class="{ 'text-base-content': $page.url.startsWith('/admin/languages') }">
                                    <PhGlobe weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-base-content': $page.url.startsWith('/admin/languages'), 'text-base-content/50 group-hover:text-base-content': !($page.url.startsWith('/admin/languages'))}" />
                                    <span v-show="!isSidebarCollapsed" class="ml-2 transition-opacity duration-300">Languages</span>
                                </Link>
                            </li>
                            <li>
                                <Link href="/admin/translations" class="group hover:bg-base-content/5 hover:text-base-content transition-all bg-transparent flex items-center text-nowrap" :class="{ 'text-base-content': $page.url.startsWith('/admin/translations') }">
                                    <PhTranslate weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-base-content': $page.url.startsWith('/admin/translations'), 'text-base-content/50 group-hover:text-base-content': !($page.url.startsWith('/admin/translations'))}" />
                                    <span v-show="!isSidebarCollapsed" class="ml-2 transition-opacity duration-300">Translations</span>
                                </Link>
                            </li>
                            <li>
                                <Link href="/admin/settings" class="group hover:bg-base-content/5 hover:text-base-content transition-all bg-transparent flex items-center text-nowrap" :class="{ 'text-base-content': $page.url.startsWith('/admin/settings') }">
                                    <PhGearSix weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-base-content': $page.url.startsWith('/admin/settings'), 'text-base-content/50 group-hover:text-base-content': !($page.url.startsWith('/admin/settings'))}" />
                                    <span v-show="!isSidebarCollapsed" class="ml-2 transition-opacity duration-300">Settings</span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-base-200" :class="{ 'p-6': !fullWidth }">
                <!-- Mobile drawer toggle -->
                <div class="lg:hidden" :class="{ 'mb-4': !fullWidth, 'p-4 border-b border-base-300': fullWidth }">
                    <label for="admin-drawer" class="btn btn-primary drawer-button">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-5 h-5 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        Menu
                    </label>
                </div>

                <div :class="{ 'max-w-7xl mx-auto': !fullWidth, 'h-full flex flex-col': fullWidth }">
                    <!-- Header Section if provided -->
                    <div v-if="$slots.header" :class="[fullWidth ? 'm-4' : 'mb-6', 'p-6 bg-base-100 rounded-box shadow-sm border border-base-300']">
                        <slot name="header"></slot>
                    </div>

                    <div :class="{ 'h-full flex-1': fullWidth }">
                        <slot></slot>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
