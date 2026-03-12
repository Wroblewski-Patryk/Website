<script setup>
import { ref, onMounted, watch, computed, markRaw } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';
import ThemeStyleProvider from '@/Components/ThemeStyleProvider.vue';
import ToastContainer from '@/Components/ToastContainer.vue';
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
    if (url.includes('/admin/posts') || url.includes('/admin/categories')) menuOpen.value.posts = true;
    if (url.includes('/admin/projects') || url.includes('/admin/clients')) menuOpen.value.projects = true;
    if (url.includes('/admin/theme')) menuOpen.value.theme = true;
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

import { useToastStore } from '@/Stores/useToastStore';

const toast = useToastStore();
const page = usePage();

watch(() => page.props.flash, (flash) => {
    if (flash?.success) toast.success(flash.success);
    if (flash?.error) toast.error(flash.error);
    if (flash?.warning) toast.warning(flash.warning);
    if (flash?.info) toast.info(flash.info);
}, { deep: true, immediate: true });

function applyTheme(themeName) {
    document.documentElement.setAttribute('data-theme', themeName);
}

const { t } = useTranslations();
const adminTitle = computed(() => {
    const brand = t(page.props.seo_settings?.site_name) || 'Featherly';
    const separator = (page.props.seo_settings?.title_separator || ' - ').trim();
    const sep = ` ${separator} `;
    
    const adminSeo = page.props.admin_seo || {};
    const moduleLabel = adminSeo.module_label;
    const actionLabel = adminSeo.action_label;
    
    // Try to find entity name in props (many edit pages pass the object)
    let entityName = null;
    const entity = page.props.post || page.props.page || page.props.project || page.props.template || page.props.user || page.props.client;
    if (entity) {
        // Handle translatable title or simple title/name/label
        entityName = (entity.title && typeof entity.title === 'object' ? t(entity.title) : entity.title) 
                    || entity.name 
                    || entity.label 
                    || (entity.id && !isNaN(entity.id) ? null : entity.id);
    }

    const parts = [];
    if (entityName) parts.push(entityName);
    if (actionLabel) parts.push(actionLabel);
    if (moduleLabel) parts.push(moduleLabel);
    parts.push(t('admin.seo.admin_panel', 'Admin Panel'));
    parts.push(brand);

    return parts.filter(Boolean).join(sep);
});

const getFlagCode = (langCode) => {
    const code = langCode?.toLowerCase() || 'en';
    const map = {
        en: 'gb', // United Kingdom flag for English
        ja: 'jp',
        zh: 'cn',
        ko: 'kr',
        cs: 'cz',
        da: 'dk',
        el: 'gr',
        uk: 'ua',
        sv: 'se'
    };
    return map[code] || code;
};

const changeLanguage = (langCode) => {
    if (!langCode || page.props.locale === langCode) return;
    
    const currentPath = window.location.pathname;
    const currentQuery = window.location.search;
    
    // Jeśli ścieżka zaczyna się od "/aktualny_jezyk/" -> zamieniamy na "/nowy_jezyk/"
    if (currentPath.startsWith(`/${page.props.locale}/`)) {
        window.location.href = currentPath.replace(`/${page.props.locale}/`, `/${langCode}/`) + currentQuery;
    } else if (currentPath === `/${page.props.locale}`) {
        window.location.href = `/${langCode}` + currentQuery;
    } else {
        // Zapasowy re-kierunkowskaz na stronę startową admina w wybranym języku
        window.location.href = `/${langCode}/admin`;
    }
};
</script>

<template>
    <Head :title="adminTitle" />
    <ThemeStyleProvider />
    <ToastContainer />
    <div class="min-h-screen bg-base-200 text-base-content font-sans">
        <!-- Top Navbar -->
        <div class="navbar bg-base-100/80 backdrop-blur-md shadow-lg border-b border-base-300 z-50 sticky top-0 px-4">
            <div class="flex-1">
                <Link :href="route('admin.dashboard.index')" class="btn btn-ghost normal-case hover:bg-transparent flex items-center gap-1 group w-fit px-2">
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
                        <li class="menu-title"><span>{{ t('admin.theme.select_theme', 'Choose Theme') }}</span></li>
                        <li v-for="theme in themesList" :key="theme">
                            <a :class="{ 'active': currentTheme === theme }" @click="currentTheme = theme">
                                <span class="capitalize w-full">{{ theme }}</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Language Dropdown -->
                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-ghost hover:bg-base-200/50 flex flex-nowrap items-center gap-2 px-3 border border-transparent shadow-none hover:border-base-300 transition-all rounded-full sm:rounded-box">
                        <span :class="['fi', `fi-${getFlagCode($page.props.locale || 'en')}`, 'rounded-sm', 'text-sm', 'shadow-sm']"></span>
                        <span class="text-sm font-bold uppercase hidden sm:block">{{ $page.props.locale || 'en' }}</span>
                        <!-- Chevron icon -->
                        <svg class="w-3 h-3 opacity-60 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </label>
                    <ul tabindex="0" class="mt-3 z-[1] p-2 shadow-xl menu menu-sm dropdown-content bg-base-100 rounded-box w-52 border border-base-200">
                        <li class="menu-title px-4 py-2 opacity-50 text-xs tracking-wider"><span>{{ t('admin.nav.language', 'Language') }}</span></li>
                        <template v-if="$page.props.languages && $page.props.languages.length">
                            <li v-for="lang in $page.props.languages.filter(l => l && l.code)" :key="lang.code">
                                <a :class="['group flex items-center gap-3 py-2 px-3 rounded-lg transition-colors', { 'active !bg-primary !text-primary-content': $page.props.locale === lang.code }]" @click.prevent="changeLanguage(lang.code)">
                                    <span :class="['fi', `fi-${getFlagCode(lang.code)}`, 'rounded-sm', 'text-xl', 'shadow-sm', 'transition-transform', 'group-hover:scale-110']"></span>
                                    <div class="flex flex-col flex-1">
                                        <span class="text-sm font-semibold leading-none">{{ lang.name }}</span>
                                        <span class="text-[10px] uppercase font-bold opacity-60 mt-1">{{ lang.code }}</span>
                                    </div>
                                    <!-- Checkmark for active -->
                                    <svg v-if="$page.props.locale === lang.code" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </a>
                            </li>
                        </template>
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
                        <li v-if="$page.props.auth?.user?.id"><Link :href="route('admin.users.edit', $page.props.auth.user.id)"><PhUser weight="regular" class="w-4 h-4 text-base-content/70" /> {{ t('admin.nav.my_profile', 'My Profile') }}</Link></li>
                        <li><Link :href="route('admin.settings.index')"><PhGearSix weight="regular" class="w-4 h-4 text-base-content/70" /> {{ t('admin.nav.account_settings', 'Account Settings') }}</Link></li>
                        <li><Link href="#"><PhLifebuoy weight="regular" class="w-4 h-4 text-base-content/70" /> {{ t('admin.nav.support', 'Support') }}</Link></li>
                        <div class="h-[1px] bg-base-200 my-1 mx-2"></div>
                        <li><Link :href="route('auth.logout')" method="post" as="button" class="w-full text-left text-error hover:bg-error/10 hover:text-error"><PhSignOut weight="regular" class="w-4 h-4" /> {{ t('admin.nav.logout', 'Logout') }}</Link></li>
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
                                      v-show="!isSidebarCollapsed">{{ t('admin.menu.content', 'Content') }}</span>
                            </li>

                            <!-- Pages -->
                            <li class="relative group/menu-item">
                                <div class="flex items-center justify-between group transition-all px-3 py-1.5" 
                                     :class="{'bg-primary/5 text-primary font-medium border-l-2 border-primary': $page.url.startsWith('/admin/pages'), 'hover:bg-transparent hover:text-primary': !$page.url.startsWith('/admin/pages')}">
                                    <Link :href="route('admin.pages.index')" class="flex items-center flex-1">
                                        <PhFileText weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-primary': $page.url.startsWith('/admin/pages')}" />
                                        <span v-show="!isSidebarCollapsed" class="ml-2.5 transition-opacity duration-300 text-sm">{{ t('admin.menu.pages', 'Pages') }}</span>
                                    </Link>
                                    <div v-show="!isSidebarCollapsed" class="flex items-center pr-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <Link :href="route('admin.pages.create')" class="p-1.5 hover:bg-primary/10 hover:text-primary transition-all">
                                            <PhPlus weight="bold" class="w-3.5 h-3.5" />
                                        </Link>
                                    </div>
                                </div>
                            </li>

                            <!-- Posts -->
                            <li class="relative group/menu-item">
                                <div class="flex items-center justify-between group transition-all px-3 py-1.5" 
                                     :class="{'bg-primary/5 text-primary font-medium border-l-2 border-primary': $page.url.startsWith('/admin/posts') || $page.url.startsWith('/admin/categories'), 'hover:bg-transparent hover:text-primary': !($page.url.startsWith('/admin/posts') || $page.url.startsWith('/admin/categories'))}">
                                    <Link :href="route('admin.posts.index')" class="flex items-center flex-1">
                                        <PhFeather weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-primary': $page.url.startsWith('/admin/posts') || $page.url.startsWith('/admin/categories')}" />
                                        <span v-show="!isSidebarCollapsed" class="ml-2.5 transition-opacity duration-300 text-sm">{{ t('admin.menu.posts', 'Posts') }}</span>
                                    </Link>
                                    <div v-show="!isSidebarCollapsed" class="flex items-center pr-1 gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button @click="menuOpen.posts = !menuOpen.posts" class="p-1.5 hover:bg-primary/10 hover:text-primary transition-all">
                                            <PhCaretDown weight="bold" class="w-3.5 h-3.5 transition-transform duration-300" :class="{'rotate-180': menuOpen.posts}" />
                                        </button>
                                        <Link :href="route('admin.posts.create')" class="p-1.5 hover:bg-primary/10 hover:text-primary transition-all">
                                            <PhPlus weight="bold" class="w-3.5 h-3.5" />
                                        </Link>
                                    </div>
                                </div>
                                <ul v-show="menuOpen.posts && !isSidebarCollapsed" class="mt-0.5 ml-4 border-l border-base-300 pl-2 space-y-0.5">
                                    <li><Link :href="route('admin.categories.index')" class="py-1 px-3 text-xs hover:text-primary hover:bg-transparent transition-colors block" :class="{'text-primary font-medium': $page.url.startsWith('/admin/categories')}">{{ t('admin.menu.categories', 'Categories') }}</Link></li>
                                </ul>
                            </li>

                            <!-- Projects -->
                            <li class="relative group/menu-item">
                                <div class="flex items-center justify-between group transition-all px-3 py-1.5" 
                                     :class="{'bg-primary/5 text-primary font-medium border-l-2 border-primary': $page.url.startsWith('/admin/projects') || $page.url.startsWith('/admin/clients'), 'hover:bg-transparent hover:text-primary': !($page.url.startsWith('/admin/projects') || $page.url.startsWith('/admin/clients'))}">
                                    <Link :href="route('admin.projects.index')" class="flex items-center flex-1">
                                        <PhCards weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-primary': $page.url.startsWith('/admin/projects') || $page.url.startsWith('/admin/clients')}" />
                                        <span v-show="!isSidebarCollapsed" class="ml-2.5 transition-opacity duration-300 text-sm">{{ t('admin.menu.projects', 'Projects') }}</span>
                                    </Link>
                                    <div v-show="!isSidebarCollapsed" class="flex items-center pr-1 gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button @click="menuOpen.projects = !menuOpen.projects" class="p-1.5 hover:bg-primary/10 hover:text-primary transition-all">
                                            <PhCaretDown weight="bold" class="w-3.5 h-3.5 transition-transform duration-300" :class="{'rotate-180': menuOpen.projects}" />
                                        </button>
                                        <Link :href="route('admin.projects.create')" class="p-1.5 hover:bg-primary/10 hover:text-primary transition-all">
                                            <PhPlus weight="bold" class="w-3.5 h-3.5" />
                                        </Link>
                                    </div>
                                </div>
                                <ul v-show="menuOpen.projects && !isSidebarCollapsed" class="mt-0.5 ml-4 border-l border-base-300 pl-2 space-y-0.5">
                                    <li><Link :href="route('admin.clients.index')" class="py-1 px-3 text-xs hover:text-primary hover:bg-transparent transition-colors block" :class="{'text-primary font-medium': $page.url.startsWith('/admin/clients')}">{{ t('admin.menu.clients', 'Clients') }}</Link></li>
                                </ul>
                            </li>

                            <!-- Forms -->
                            <li class="relative group/menu-item">
                                <div class="flex items-center justify-between group transition-all px-3 py-1.5" 
                                     :class="{'bg-primary/5 text-primary font-medium border-l-2 border-primary': $page.url.startsWith('/admin/forms'), 'hover:bg-transparent hover:text-primary': !$page.url.startsWith('/admin/forms')}">
                                    <Link :href="route('admin.forms.index')" class="flex items-center flex-1">
                                        <PhTextbox weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-primary': $page.url.startsWith('/admin/forms')}" />
                                        <span v-show="!isSidebarCollapsed" class="ml-2.5 transition-opacity duration-300 text-sm">{{ t('admin.menu.forms', 'Forms') }}</span>
                                    </Link>
                                    <div v-show="!isSidebarCollapsed" class="flex items-center pr-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <Link :href="route('admin.forms.create')" class="p-1.5 hover:bg-primary/10 hover:text-primary transition-all">
                                            <PhPlus weight="bold" class="w-3.5 h-3.5" />
                                        </Link>
                                    </div>
                                </div>
                            </li>

                            <!-- Library Title -->
                            <li class="menu-title mt-6 mb-2 border-b border-base-200/50 pb-1 pointer-events-none">
                                <span class="text-[10px] uppercase font-bold tracking-widest text-secondary/60 whitespace-nowrap overflow-hidden transition-all duration-300" 
                                      v-show="!isSidebarCollapsed">{{ t('admin.menu.library', 'Library') }}</span>
                            </li>

                            <!-- Media -->
                            <li class="relative group/menu-item">
                                <div class="flex items-center justify-between group transition-all px-3 py-1.5" 
                                     :class="{'bg-secondary/5 text-secondary font-medium border-l-2 border-secondary': $page.url.startsWith('/admin/media'), 'hover:bg-transparent hover:text-secondary': !$page.url.startsWith('/admin/media')}">
                                    <Link :href="route('admin.media.index')" class="flex items-center flex-1">
                                        <PhImageSquare weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-secondary': $page.url.startsWith('/admin/media')}" />
                                        <span v-show="!isSidebarCollapsed" class="ml-2.5 transition-opacity duration-300 text-sm">{{ t('admin.menu.media', 'Media') }}</span>
                                    </Link>
                                </div>
                            </li>

                            <!-- Templates -->
                            <li class="relative group/menu-item">
                                <div class="flex items-center justify-between group transition-all px-3 py-1.5" 
                                     :class="{'bg-secondary/5 text-secondary font-medium border-l-2 border-secondary': $page.url.startsWith('/admin/templates'), 'hover:bg-transparent hover:text-secondary': !$page.url.startsWith('/admin/templates')}">
                                    <Link :href="route('admin.templates.index')" class="flex items-center flex-1">
                                        <PhLayout weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-secondary': $page.url.startsWith('/admin/templates')}" />
                                        <span v-show="!isSidebarCollapsed" class="ml-2.5 transition-opacity duration-300 text-sm">{{ t('admin.menu.templates', 'Templates') }}</span>
                                    </Link>
                                </div>
                            </li>

                            <!-- Design Title -->
                            <li class="menu-title mt-6 mb-2 border-b border-base-200/50 pb-1 pointer-events-none">
                                <span class="text-[10px] uppercase font-bold tracking-widest text-accent/60 whitespace-nowrap overflow-hidden transition-all duration-300" 
                                      v-show="!isSidebarCollapsed">{{ t('admin.menu.design', 'Design') }}</span>
                            </li>

                            <!-- Theme -->
                            <li class="relative group/menu-item">
                                <div class="flex items-center justify-between group transition-all px-3 py-1.5" 
                                     :class="{'bg-accent/5 text-accent font-medium border-l-2 border-accent': $page.url.startsWith('/admin/theme'), 'hover:bg-transparent hover:text-accent': !$page.url.startsWith('/admin/theme')}">
                                    <Link :href="route('admin.theme.index')" class="flex items-center flex-1">
                                        <PhPaintRoller weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-accent': $page.url.startsWith('/admin/theme')}" />
                                        <span v-show="!isSidebarCollapsed" class="ml-2.5 transition-opacity duration-300 text-sm">{{ t('admin.menu.theme', 'Theme') }}</span>
                                    </Link>
                                    <div v-show="!isSidebarCollapsed" class="flex items-center pr-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button @click="menuOpen.theme = !menuOpen.theme" class="p-1.5 hover:bg-accent/10 hover:text-accent transition-all">
                                            <PhCaretDown weight="bold" class="w-3.5 h-3.5 transition-transform duration-300" :class="{'rotate-180': menuOpen.theme}" />
                                        </button>
                                    </div>
                                </div>
                                <ul v-show="menuOpen.theme && !isSidebarCollapsed" class="mt-0.5 ml-4 border-l border-base-300 pl-2 space-y-0.5">
                                    <li><Link :href="route('admin.theme.colors')" class="py-1 px-3 text-xs hover:text-accent hover:bg-transparent transition-colors block" :class="{'text-accent font-medium': $page.url.startsWith('/admin/theme/colors')}">{{ t('admin.menu.colors', 'Colors') }}</Link></li>
                                    <li><Link :href="route('admin.theme.fonts')" class="py-1 px-3 text-xs hover:text-accent hover:bg-transparent transition-colors block" :class="{'text-accent font-medium': $page.url.startsWith('/admin/theme/fonts')}">{{ t('admin.menu.fonts', 'Fonts') }}</Link></li>
                                    <li><Link :href="route('admin.theme.typography')" class="py-1 px-3 text-xs hover:text-accent hover:bg-transparent transition-colors block" :class="{'text-accent font-medium': $page.url.startsWith('/admin/theme/typography')}">{{ t('admin.menu.typography', 'Typography') }}</Link></li>
                                    <li><Link :href="route('admin.theme.sizes')" class="py-1 px-3 text-xs hover:text-accent hover:bg-transparent transition-colors block" :class="{'text-accent font-medium': $page.url.startsWith('/admin/theme/sizes')}">{{ t('admin.menu.sizes', 'Sizes') }}</Link></li>
                                    <li><Link :href="route('admin.theme.effects')" class="py-1 px-3 text-xs hover:text-accent hover:bg-transparent transition-colors block" :class="{'text-accent font-medium': $page.url.startsWith('/admin/theme/effects')}">{{ t('admin.menu.effects', 'Effects') }}</Link></li>
                                </ul>
                            </li>

                            <!-- Blocks -->
                            <li class="relative group/menu-item">
                                <div class="flex items-center justify-between group transition-all px-3 py-1.5" 
                                     :class="{'bg-accent/5 text-accent font-medium border-l-2 border-accent': $page.url.startsWith('/admin/blocks'), 'hover:bg-transparent hover:text-accent': !$page.url.startsWith('/admin/blocks')}">
                                    <Link :href="route('admin.blocks')" class="flex items-center flex-1">
                                        <PhCube weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-accent': $page.url.startsWith('/admin/blocks')}" />
                                        <span v-show="!isSidebarCollapsed" class="ml-2.5 transition-opacity duration-300 text-sm">{{ t('admin.menu.blocks', 'Blocks') }}</span>
                                    </Link>
                                </div>
                            </li>

                            <li class="menu-title mt-6 mb-2 border-b border-base-200/50 pb-1 pointer-events-none">
                                <span class="text-[10px] uppercase font-bold tracking-widest text-info/60 whitespace-nowrap overflow-hidden transition-all duration-300" 
                                      v-show="!isSidebarCollapsed">{{ t('admin.menu.system', 'System Settings') }}</span>
                            </li>
                            <!-- Translations -->
                            <li class="relative group/menu-item">
                                <div class="flex items-center justify-between group transition-all px-3 py-1.5" 
                                     :class="{'bg-info/5 text-info font-medium border-l-2 border-info': $page.url === '/admin/translations' || $page.url.startsWith('/admin/translations/'), 'hover:bg-transparent hover:text-info': !($page.url === '/admin/translations' || $page.url.startsWith('/admin/translations/'))}">
                                    <Link :href="route('admin.translations.index')" class="flex items-center flex-1">
                                        <PhTranslate weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-info': $page.url.startsWith('/admin/translations')}" />
                                        <span v-show="!isSidebarCollapsed" class="ml-2.5 transition-opacity duration-300 text-sm">{{ t('admin.menu.translations', 'Translations') }}</span>
                                    </Link>
                                </div>
                            </li>

                            <!-- Languages -->
                            <li class="relative group/menu-item">
                                <div class="flex items-center justify-between group transition-all px-3 py-1.5" 
                                     :class="{'bg-info/5 text-info font-medium border-l-2 border-info': $page.url === '/admin/languages' || $page.url.startsWith('/admin/languages/'), 'hover:bg-transparent hover:text-info': !($page.url === '/admin/languages' || $page.url.startsWith('/admin/languages/'))}">
                                    <Link :href="route('admin.languages.index')" class="flex items-center flex-1">
                                        <PhGlobe weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-info': $page.url.startsWith('/admin/languages')}" />
                                        <span v-show="!isSidebarCollapsed" class="ml-2.5 transition-opacity duration-300 text-sm">{{ t('admin.menu.languages', 'Languages') }}</span>
                                    </Link>
                                </div>
                            </li>

                            <!-- Users -->
                            <li class="relative group/menu-item">
                                <div class="flex items-center justify-between group transition-all px-3 py-1.5" 
                                     :class="{'bg-info/5 text-info font-medium border-l-2 border-info': $page.url === '/admin/users' || $page.url.startsWith('/admin/users/'), 'hover:bg-transparent hover:text-info': !($page.url === '/admin/users' || $page.url.startsWith('/admin/users/'))}">
                                    <Link :href="route('admin.users.index')" class="flex items-center flex-1">
                                        <PhUsers weight="regular" class="w-4 h-4 text-base-content/70" />
                                        <span v-show="!isSidebarCollapsed" class="ml-2.5 transition-opacity duration-300 text-sm">{{ t('admin.menu.users', 'Users') }}</span>
                                    </Link>
                                </div>
                            </li>

                            <!-- Settings -->
                            <li class="relative group/menu-item">
                                <div class="flex items-center justify-between group transition-all px-3 py-1.5" 
                                     :class="{'bg-info/5 text-info font-medium border-l-2 border-info': $page.url === '/admin/settings' || $page.url.startsWith('/admin/settings/'), 'hover:bg-transparent hover:text-info': !($page.url === '/admin/settings' || $page.url.startsWith('/admin/settings/'))}">
                                    <Link :href="route('admin.settings.index')" class="flex items-center flex-1">
                                        <PhGearSix weight="regular" class="w-5 h-5 shrink-0 transition-colors" :class="{'text-info': $page.url === '/admin/settings'}" />
                                        <span v-show="!isSidebarCollapsed" class="ml-2.5 transition-opacity duration-300 text-sm">{{ t('admin.menu.settings', 'Settings') }}</span>
                                    </Link>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="drawer-content flex flex-col min-w-0 flex-1 overflow-x-hidden pt-0 shadow-inner bg-base-200">
                    <slot name="header" />
                    <main class="p-4 lg:p-8 flex-grow max-h-full" :class="{ 'max-w-7xl mx-auto w-full': !fullWidth }">
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
