<script setup>
import { ref, onMounted, watch } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import ThemeStyleProvider from '@/Components/ThemeStyleProvider.vue';
import { PhFileText, PhFeather, PhCards, PhTextbox, PhPaintRoller, PhCube, PhLayout, PhList, PhImageSquare, PhGlobe, PhTranslate, PhGearSix, PhBell, PhCaretLeft, PhCaretRight, PhSun, PhMoon, PhPalette, PhUser, PhUsers, PhSignOut, PhLifebuoy, PhPlus, PhCaretDown } from '@phosphor-icons/vue';

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

const menuOpen = ref({
    posts: false,
    projects: false,
    theme: false
});

// Watch for URL changes to keep submenus open
watch(() => usePage().url, (url) => {
    if (url.startsWith('/admin/posts') || url.startsWith('/admin/categories')) menuOpen.value.posts = true;
    if (url.startsWith('/admin/projects') || url.startsWith('/admin/clients')) menuOpen.value.projects = true;
    if (url.startsWith('/admin/theme')) menuOpen.value.theme = true;
}, { immediate: true });

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
            <div class="drawer lg:drawer-open w-full flex-1 z-40 relative">
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

                            <!-- Pages -->
                            <li class="relative group/menu-item">
                                <div class="flex items-center justify-between group transition-all px-3 py-1.5" 
                                     :class="{'bg-primary/5 text-primary font-medium border-l-2 border-primary': $page.url.startsWith('/admin/pages'), 'hover:bg-transparent hover:text-primary': !$page.url.startsWith('/admin/pages')}">
                                    <Link href="/admin/pages" class="flex items-center flex-1">
                                        <PhFileText weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-primary': $page.url.startsWith('/admin/pages')}" />
                                        <span v-show="!isSidebarCollapsed" class="ml-2.5 transition-opacity duration-300 text-sm">Pages</span>
                                    </Link>
                                    <div v-show="!isSidebarCollapsed" class="flex items-center pr-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <Link href="/admin/pages/create" class="p-1.5 hover:bg-primary/10 hover:text-primary transition-all">
                                            <PhPlus weight="bold" class="w-3.5 h-3.5" />
                                        </Link>
                                    </div>
                                </div>
                            </li>

                            <!-- Posts -->
                            <li class="relative group/menu-item">
                                <div class="flex items-center justify-between group transition-all px-3 py-1.5" 
                                     :class="{'bg-primary/5 text-primary font-medium border-l-2 border-primary': $page.url.startsWith('/admin/posts') || $page.url.startsWith('/admin/categories'), 'hover:bg-transparent hover:text-primary': !($page.url.startsWith('/admin/posts') || $page.url.startsWith('/admin/categories'))}">
                                    <Link href="/admin/posts" class="flex items-center flex-1">
                                        <PhFeather weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-primary': $page.url.startsWith('/admin/posts') || $page.url.startsWith('/admin/categories')}" />
                                        <span v-show="!isSidebarCollapsed" class="ml-2.5 transition-opacity duration-300 text-sm">Posts</span>
                                    </Link>
                                    <div v-show="!isSidebarCollapsed" class="flex items-center pr-1 gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button @click="menuOpen.posts = !menuOpen.posts" class="p-1.5 hover:bg-primary/10 hover:text-primary transition-all">
                                            <PhCaretDown weight="bold" class="w-3.5 h-3.5 transition-transform duration-300" :class="{'rotate-180': menuOpen.posts}" />
                                        </button>
                                        <Link href="/admin/posts/create" class="p-1.5 hover:bg-primary/10 hover:text-primary transition-all">
                                            <PhPlus weight="bold" class="w-3.5 h-3.5" />
                                        </Link>
                                    </div>
                                </div>
                                <ul v-show="menuOpen.posts && !isSidebarCollapsed" class="mt-0.5 ml-4 border-l border-base-300 pl-2 space-y-0.5">
                                    <li><Link href="/admin/categories" class="py-1 px-3 text-xs hover:text-primary hover:bg-transparent transition-colors block" :class="{'text-primary font-medium': $page.url.startsWith('/admin/categories')}">Categories</Link></li>
                                </ul>
                            </li>

                            <!-- Projects -->
                            <li class="relative group/menu-item">
                                <div class="flex items-center justify-between group transition-all px-3 py-1.5" 
                                     :class="{'bg-primary/5 text-primary font-medium border-l-2 border-primary': $page.url.startsWith('/admin/projects') || $page.url.startsWith('/admin/clients'), 'hover:bg-transparent hover:text-primary': !($page.url.startsWith('/admin/projects') || $page.url.startsWith('/admin/clients'))}">
                                    <Link href="/admin/projects" class="flex items-center flex-1">
                                        <PhCards weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-primary': $page.url.startsWith('/admin/projects') || $page.url.startsWith('/admin/clients')}" />
                                        <span v-show="!isSidebarCollapsed" class="ml-2.5 transition-opacity duration-300 text-sm">Projects</span>
                                    </Link>
                                    <div v-show="!isSidebarCollapsed" class="flex items-center pr-1 gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button @click="menuOpen.projects = !menuOpen.projects" class="p-1.5 hover:bg-primary/10 hover:text-primary transition-all">
                                            <PhCaretDown weight="bold" class="w-3.5 h-3.5 transition-transform duration-300" :class="{'rotate-180': menuOpen.projects}" />
                                        </button>
                                        <Link href="/admin/projects/create" class="p-1.5 hover:bg-primary/10 hover:text-primary transition-all">
                                            <PhPlus weight="bold" class="w-3.5 h-3.5" />
                                        </Link>
                                    </div>
                                </div>
                                <ul v-show="menuOpen.projects && !isSidebarCollapsed" class="mt-0.5 ml-4 border-l border-base-300 pl-2 space-y-0.5">
                                    <li><Link href="/admin/clients" class="py-1 px-3 text-xs hover:text-primary hover:bg-transparent transition-colors block" :class="{'text-primary font-medium': $page.url.startsWith('/admin/clients')}">Clients</Link></li>
                                </ul>
                            </li>

                            <!-- Forms -->
                            <li class="relative group/menu-item">
                                <div class="flex items-center justify-between group transition-all px-3 py-1.5" 
                                     :class="{'bg-primary/5 text-primary font-medium border-l-2 border-primary': $page.url.startsWith('/admin/forms'), 'hover:bg-transparent hover:text-primary': !$page.url.startsWith('/admin/forms')}">
                                    <Link href="/admin/forms" class="flex items-center flex-1">
                                        <PhTextbox weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-primary': $page.url.startsWith('/admin/forms')}" />
                                        <span v-show="!isSidebarCollapsed" class="ml-2.5 transition-opacity duration-300 text-sm">Forms</span>
                                    </Link>
                                    <div v-show="!isSidebarCollapsed" class="flex items-center pr-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <Link href="/admin/forms/create" class="p-1.5 hover:bg-primary/10 hover:text-primary transition-all">
                                            <PhPlus weight="bold" class="w-3.5 h-3.5" />
                                        </Link>
                                    </div>
                                </div>
                            </li>

                            <!-- Media -->
                            <li class="relative group/menu-item">
                                <div class="flex items-center justify-between group transition-all px-3 py-1.5" 
                                     :class="{'bg-primary/5 text-primary font-medium border-l-2 border-primary': $page.url.startsWith('/admin/media'), 'hover:bg-transparent hover:text-primary': !$page.url.startsWith('/admin/media')}">
                                    <Link href="/admin/media" class="flex items-center flex-1">
                                        <PhImageSquare weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-primary': $page.url.startsWith('/admin/media')}" />
                                        <span v-show="!isSidebarCollapsed" class="ml-2.5 transition-opacity duration-300 text-sm">Media Library</span>
                                    </Link>
                                </div>
                            </li>
                            
                            <!-- Design Title -->
                            <li class="menu-title mt-6 mb-2 border-b border-base-200/50 pb-1 pointer-events-none">
                                <span class="text-[10px] uppercase font-bold tracking-widest text-secondary/60 whitespace-nowrap overflow-hidden transition-all duration-300" 
                                      v-show="!isSidebarCollapsed">Design</span>
                            </li>

                            <!-- Theme -->
                            <li class="relative group/menu-item">
                                <div class="flex items-center justify-between group transition-all px-3 py-1.5" 
                                     :class="{'bg-secondary/5 text-secondary font-medium border-l-2 border-secondary': $page.url.startsWith('/admin/theme'), 'hover:bg-transparent hover:text-secondary': !$page.url.startsWith('/admin/theme')}">
                                    <Link href="/admin/theme" class="flex items-center flex-1">
                                        <PhPaintRoller weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-secondary': $page.url.startsWith('/admin/theme')}" />
                                        <span v-show="!isSidebarCollapsed" class="ml-2.5 transition-opacity duration-300 text-sm">Theme</span>
                                    </Link>
                                    <div v-show="!isSidebarCollapsed" class="flex items-center pr-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button @click="menuOpen.theme = !menuOpen.theme" class="p-1.5 hover:bg-secondary/10 hover:text-secondary transition-all">
                                            <PhCaretDown weight="bold" class="w-3.5 h-3.5 transition-transform duration-300" :class="{'rotate-180': menuOpen.theme}" />
                                        </button>
                                    </div>
                                </div>
                                <ul v-show="menuOpen.theme && !isSidebarCollapsed" class="mt-0.5 ml-4 border-l border-base-300 pl-2 space-y-0.5">
                                    <li><Link href="/admin/theme/colors" class="py-1 px-3 text-xs hover:text-secondary hover:bg-transparent transition-colors block" :class="{'text-secondary font-medium': $page.url.startsWith('/admin/theme/colors')}">Colors</Link></li>
                                    <li><Link href="/admin/theme/fonts" class="py-1 px-3 text-xs hover:text-secondary hover:bg-transparent transition-colors block" :class="{'text-secondary font-medium': $page.url.startsWith('/admin/theme/fonts')}">Fonts</Link></li>
                                    <li><Link href="/admin/theme/typography" class="py-1 px-3 text-xs hover:text-secondary hover:bg-transparent transition-colors block" :class="{'text-secondary font-medium': $page.url.startsWith('/admin/theme/typography')}">Typography</Link></li>
                                    <li><Link href="/admin/theme/sizes" class="py-1 px-3 text-xs hover:text-secondary hover:bg-transparent transition-colors block" :class="{'text-secondary font-medium': $page.url.startsWith('/admin/theme/sizes')}">Sizes / Metrics</Link></li>
                                    <li><Link href="/admin/theme/effects" class="py-1 px-3 text-xs hover:text-secondary hover:bg-transparent transition-colors block" :class="{'text-secondary font-medium': $page.url.startsWith('/admin/theme/effects')}">Shadows / Effects</Link></li>
                                </ul>
                            </li>

                            <!-- Blocks -->
                            <li class="relative group/menu-item">
                                <div class="flex items-center justify-between group transition-all px-3 py-1.5" 
                                     :class="{'bg-secondary/5 text-secondary font-medium border-l-2 border-secondary': $page.url.startsWith('/admin/blocks'), 'hover:bg-transparent hover:text-secondary': !$page.url.startsWith('/admin/blocks')}">
                                    <Link href="/admin/blocks" class="flex items-center flex-1">
                                        <PhCube weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-secondary': $page.url.startsWith('/admin/blocks')}" />
                                        <span v-show="!isSidebarCollapsed" class="ml-2.5 transition-opacity duration-300 text-sm">Blocks</span>
                                    </Link>
                                </div>
                            </li>

                            <!-- Templates -->
                            <li class="relative group/menu-item">
                                <div class="flex items-center justify-between group transition-all px-3 py-1.5" 
                                     :class="{'bg-secondary/5 text-secondary font-medium border-l-2 border-secondary': $page.url.startsWith('/admin/templates'), 'hover:bg-transparent hover:text-secondary': !$page.url.startsWith('/admin/templates')}">
                                    <Link href="/admin/templates" class="flex items-center flex-1">
                                        <PhLayout weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-secondary': $page.url.startsWith('/admin/templates')}" />
                                        <span v-show="!isSidebarCollapsed" class="ml-2.5 transition-opacity duration-300 text-sm">Templates</span>
                                    </Link>
                                </div>
                            </li>

                            <li class="menu-title mt-6 mb-2 border-b border-base-200/50 pb-1 pointer-events-none">
                                <span class="text-[10px] uppercase font-bold tracking-widest text-accent/60 whitespace-nowrap overflow-hidden transition-all duration-300" 
                                      v-show="!isSidebarCollapsed">System</span>
                            </li>
                            <!-- Translations -->
                            <li class="relative group/menu-item">
                                <div class="flex items-center justify-between group transition-all px-3 py-1.5" 
                                     :class="{'bg-accent/5 text-accent font-medium border-l-2 border-accent': $page.url === '/admin/translations' || $page.url.startsWith('/admin/translations/'), 'hover:bg-transparent hover:text-accent': !($page.url === '/admin/translations' || $page.url.startsWith('/admin/translations/'))}">
                                    <Link href="/admin/translations" class="flex items-center flex-1">
                                        <PhTranslate weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-accent': $page.url.startsWith('/admin/translations')}" />
                                        <span v-show="!isSidebarCollapsed" class="ml-2.5 transition-opacity duration-300 text-sm">Translations</span>
                                    </Link>
                                </div>
                            </li>

                            <!-- Languages -->
                            <li class="relative group/menu-item">
                                <div class="flex items-center justify-between group transition-all px-3 py-1.5" 
                                     :class="{'bg-accent/5 text-accent font-medium border-l-2 border-accent': $page.url === '/admin/languages' || $page.url.startsWith('/admin/languages/'), 'hover:bg-transparent hover:text-accent': !($page.url === '/admin/languages' || $page.url.startsWith('/admin/languages/'))}">
                                    <Link href="/admin/languages" class="flex items-center flex-1">
                                        <PhGlobe weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-accent': $page.url.startsWith('/admin/languages')}" />
                                        <span v-show="!isSidebarCollapsed" class="ml-2.5 transition-opacity duration-300 text-sm">Languages</span>
                                    </Link>
                                </div>
                            </li>

                            <!-- Users -->
                            <li class="relative group/menu-item">
                                <div class="flex items-center justify-between group transition-all px-3 py-1.5" 
                                     :class="{'bg-accent/5 text-accent font-medium border-l-2 border-accent': $page.url === '/admin/users' || $page.url.startsWith('/admin/users/'), 'hover:bg-transparent hover:text-accent': !($page.url === '/admin/users' || $page.url.startsWith('/admin/users/'))}">
                                    <Link href="/admin/users" class="flex items-center flex-1">
                                        <PhUsers weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-accent': $page.url.startsWith('/admin/users')}" />
                                        <span v-show="!isSidebarCollapsed" class="ml-2.5 transition-opacity duration-300 text-sm">Users</span>
                                    </Link>
                                </div>
                            </li>

                            <!-- Settings -->
                            <li class="relative group/menu-item">
                                <div class="flex items-center justify-between group transition-all px-3 py-1.5" 
                                     :class="{'bg-accent/5 text-accent font-medium border-l-2 border-accent': $page.url === '/admin/settings' || $page.url.startsWith('/admin/settings/'), 'hover:bg-transparent hover:text-accent': !($page.url === '/admin/settings' || $page.url.startsWith('/admin/settings/'))}">
                                    <Link href="/admin/settings" class="flex items-center flex-1">
                                        <PhGearSix weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-accent': $page.url === '/admin/settings'}" />
                                        <span v-show="!isSidebarCollapsed" class="ml-2.5 transition-opacity duration-300 text-sm">Settings</span>
                                    </Link>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="drawer-content flex flex-col min-w-0 flex-1 overflow-x-hidden pt-0 shadow-inner bg-base-200">
                    <main class="p-4 lg:p-8 flex-grow max-h-full" :class="{ 'max-w-7xl mx-auto w-full': !fullWidth }">
                        <!-- Flash Messages (Optional) -->
                        <div v-if="$page.props.flash?.success" class="alert alert-success shadow-lg mb-6">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <span>{{ $page.props.flash.success }}</span>
                            </div>
                        </div>

                        <slot />
                    </main>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Transposition animations or other specific styles */
.menu li > a.active {
    background-color: transparent;
}

/* Custom scrollbar for sidebar */
.drawer-side .menu::-webkit-scrollbar {
    width: 4px;
}
.drawer-side .menu::-webkit-scrollbar-track {
    background: transparent;
}
.drawer-side .menu::-webkit-scrollbar-thumb {
    background: hsl(var(--bc) / 0.1);
    border-radius: 10px;
}
.drawer-side .menu::-webkit-scrollbar-thumb:hover {
    background: hsl(var(--bc) / 0.2);
}

/* Tooltip replacement for collapsed state icons if needed */
</style>
