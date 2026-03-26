<script setup>
import { ref, onMounted, watch, computed, markRaw } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';
import ThemeStyleProvider from '@/Components/ThemeStyleProvider.vue';
import ToastContainer from '@/Components/ToastContainer.vue';
import { PhFileText, PhFeather, PhCards, PhTextbox, PhPaintRoller, PhCube, PhLayout, PhList, PhImageSquare, PhGlobe, PhTranslate, PhGearSix, PhBell, PhCaretLeft, PhCaretRight, PhSun, PhMoon, PhPalette, PhUser, PhUsers, PhSignOut, PhLifebuoy, PhPlus, PhCaretDown } from '@phosphor-icons/vue';
import SidebarGroup from '@/Components/Admin/Sidebar/SidebarGroup.vue';
import SidebarItem from '@/Components/Admin/Sidebar/SidebarItem.vue';
import SidebarChild from '@/Components/Admin/Sidebar/SidebarChild.vue';
import GlobalSearchDropdown from '@/features/admin/shared/components/GlobalSearchDropdown.vue';
import TopbarDropdown from '@/features/admin/shared/components/TopbarDropdown.vue';

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

// Navigation state managed by watcher on current URL

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
import { navigation } from '@/features/admin/shared/config/adminNav.config';

const toast = useToastStore();
const page = usePage();

watch(() => page.props.flash, (flash) => {
    if (flash?.success) toast.success(t(flash.success));
    if (flash?.error) toast.error(t(flash.error));
    if (flash?.warning) toast.warning(t(flash.warning));
    if (flash?.info) toast.info(t(flash.info));
}, { deep: true, immediate: true });

function applyTheme(themeName) {
    document.documentElement.setAttribute('data-theme', themeName);
}

const { t } = useTranslations();

const navConfig = computed(() => navigation(t));

const navState = ref({});

watch(() => usePage().url, (url) => {
    // Strip locale prefix for matching
    const cleanUrl = url.replace(/^\/(pl|en)/, '') || '/';

    // Determine which groups should be open based on URL
    navConfig.value.forEach((group, groupIdx) => {
        group.items.forEach((item, itemIdx) => {
            if (item.children) {
                const isActive = Array.isArray(item.active) 
                    ? item.active.some(a => cleanUrl.startsWith(a))
                    : cleanUrl.startsWith(item.active);
                
                if (isActive) {
                    navState.value[`${groupIdx}-${itemIdx}`] = true;
                }
            }
        });
    });
}, { immediate: true });

const toggleSubmenu = (key) => {
    navState.value[key] = !navState.value[key];
};

const hasPermission = (permission) => {
    if (!permission) return true;
    return page.props.auth?.user?.permissions?.[permission] || false;
};

const isItemActive = (item) => {
    let url = usePage().url;
    // Strip locale prefix (e.g., /pl, /en)
    url = url.replace(/^\/(pl|en)/, '');
    if (url === '') url = '/';

    const itemActive = item.active;
    
    if (!itemActive) return false;

    if (item.exact) {
        if (Array.isArray(itemActive)) {
            return itemActive.some(a => url === a);
        }
        return url === itemActive;
    }

    if (Array.isArray(itemActive)) {
        return itemActive.some(a => url.startsWith(a));
    }
    return url.startsWith(itemActive);
};

const adminTitle = computed(() => {
    const brand = t(page.props.seo_settings?.site_name) || 'Featherly';
    const separator = (page.props.seo_settings?.title_separator || ' - ').trim();
    const sep = ` ${separator} `;
    
    const adminSeo = page.props.admin_seo || {};
    const moduleLabel = adminSeo.module_label;
    const actionLabel = adminSeo.action_label;
    
    // Try to find entity name in props (many edit pages pass the object)
    let entityName = null;
    const entity = page.props.post || page.props.page || page.props.project || page.props.template || page.props.user;
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

const userAvatarUrl = computed(() => {
    const user = page.props.auth?.user || {};
    return user.avatar_url || user.avatar || user.profile_photo_url || null;
});
</script>

<template>
    <Head :title="adminTitle" />
    <ThemeStyleProvider />
    <ToastContainer />
    <div class="min-h-screen bg-base-200 text-base-content font-sans">
        <!-- Top Navbar -->
        <div class="navbar bg-base-100/80 backdrop-blur-md shadow-lg border-b border-base-300 z-50 sticky top-0 px-4">
            <div class="flex-none">
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

            <div class="flex-1 hidden lg:flex justify-center px-4">
                <GlobalSearchDropdown />
            </div>
            
            <div class="flex-none flex items-center gap-3">
                <!-- Language Dropdown -->
                <TopbarDropdown trigger-class="h-10 min-h-10 px-3 rounded-full sm:rounded-box flex flex-nowrap items-center gap-2" menu-class="w-52">
                    <template #trigger>
                        <span :class="['fi', `fi-${getFlagCode($page.props.locale || 'en')}`, 'inline-block', 'w-5', 'h-4', 'rounded-sm', 'shadow-sm']"></span>
                        <span class="text-sm font-bold uppercase hidden sm:block">{{ $page.props.locale || 'en' }}</span>
                        <svg class="w-3 h-3 opacity-60 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </template>
                    <li class="menu-title px-4 py-2 opacity-50 text-xs tracking-wider"><span>{{ t('admin.nav.language', 'Language') }}</span></li>
                    <template v-if="$page.props.languages && $page.props.languages.length">
                        <li v-for="lang in $page.props.languages.filter(l => l && l.code)" :key="lang.code">
                            <a :class="['group flex items-center gap-3 py-2 px-3 rounded-lg transition-colors', { 'active !bg-primary !text-primary-content': $page.props.locale === lang.code }]" @click.prevent="changeLanguage(lang.code)">
                                <span :class="['fi', `fi-${getFlagCode(lang.code)}`, 'inline-block', 'w-6', 'h-[18px]', 'rounded-sm', 'shadow-sm', 'transition-transform', 'group-hover:scale-110']"></span>
                                <div class="flex flex-col flex-1">
                                    <span class="text-sm font-semibold leading-none">{{ lang.name }}</span>
                                    <span class="text-[10px] uppercase font-bold opacity-60 mt-1">{{ lang.code }}</span>
                                </div>
                                <svg v-if="$page.props.locale === lang.code" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </a>
                        </li>
                    </template>
                </TopbarDropdown>

                <!-- Color Themes Dropdown -->
                <TopbarDropdown trigger-class="btn-circle h-10 w-10 min-h-10 p-0" menu-class="w-52">
                    <template #trigger>
                        <PhPalette weight="regular" class="w-5 h-5 text-base-content/70" />
                    </template>
                    <li class="menu-title"><span>{{ t('admin.theme.select_theme', 'Choose Theme') }}</span></li>
                    <li v-for="theme in themesList" :key="theme">
                        <a :class="['flex items-center justify-between gap-3', { 'active': currentTheme === theme }]" @click="currentTheme = theme">
                            <span class="capitalize">{{ theme }}</span>
                            <span :data-theme="theme" class="flex items-center gap-1.5 rounded-full px-1.5 py-1 bg-base-100/80 border border-base-300/60">
                                <span class="w-2.5 h-2.5 rounded-full bg-primary border border-base-300/50"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-secondary border border-base-300/50"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-accent border border-base-300/50"></span>
                            </span>
                        </a>
                    </li>
                </TopbarDropdown>

                <!-- Admin Profile/Logout Dropdown -->
                <TopbarDropdown trigger-class="btn-circle h-10 w-10 min-h-10 p-0 ml-1 rounded-full ring ring-primary ring-offset-base-100 ring-offset-1" menu-class="w-56 gap-1">
                    <template #trigger>
                        <div class="w-9 h-9 rounded-full overflow-hidden bg-base-200 text-base-content/70 flex items-center justify-center">
                            <img
                                v-if="userAvatarUrl"
                                :src="userAvatarUrl"
                                :alt="$page.props.auth?.user?.name || 'User avatar'"
                                class="w-full h-full object-cover"
                            />
                            <PhUser v-else weight="regular" class="w-5 h-5" />
                        </div>
                    </template>
                    <li class="menu-title flex flex-col items-start gap-0 p-3 border-b border-base-200/50 mb-1 pointer-events-none">
                        <span class="font-bold text-sm text-base-content w-full whitespace-nowrap overflow-hidden text-ellipsis">{{ $page.props.auth?.user?.name || 'Administrator' }}</span>
                        <span class="text-xs font-normal text-base-content/60 w-full whitespace-nowrap overflow-hidden text-ellipsis mt-1">{{ $page.props.auth?.user?.email || 'admin@example.com' }}</span>
                    </li>
                    <li v-if="$page.props.auth?.user?.id"><Link :href="route('admin.users.edit', $page.props.auth.user.id)"><PhUser weight="regular" class="w-4 h-4 text-base-content/70" /> {{ t('admin.nav.my_profile', 'My Profile') }}</Link></li>
                    <li v-if="$page.props.auth?.user?.permissions?.can_manage_settings"><Link :href="route('admin.settings.index')"><PhGearSix weight="regular" class="w-4 h-4 text-base-content/70" /> {{ t('admin.nav.account_settings', 'Account Settings') }}</Link></li>
                    <li><Link href="#"><PhLifebuoy weight="regular" class="w-4 h-4 text-base-content/70" /> {{ t('admin.nav.support', 'Support') }}</Link></li>
                    <div class="h-[1px] bg-base-200 my-1 mx-2"></div>
                    <li><Link :href="route('auth.logout')" method="post" as="button" class="w-full text-left text-error hover:bg-error/10 hover:text-error"><PhSignOut weight="regular" class="w-4 h-4" /> {{ t('admin.nav.logout', 'Logout') }}</Link></li>
                </TopbarDropdown>
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

                        <ul class="menu px-0 py-3 h-[calc(100vh-4rem)] bg-base-100/80 backdrop-blur-md text-base-content border-r border-base-200 gap-0.5 overflow-y-auto overflow-x-hidden transition-[width] duration-300 ease-in-out flex-nowrap"
                            :class="[isSidebarCollapsed ? 'w-20 [&_details_summary::after]:hidden' : 'w-64']"
                            :style="{ width: isSidebarCollapsed ? '80px' : '256px' }">
                            
                            <!-- Sidebar content here -->
                            <!-- Dynamic Sidebar Content -->
                            <template v-for="(group, groupIdx) in navConfig" :key="groupIdx">
                                <SidebarGroup 
                                    v-if="hasPermission(group.permission)"
                                    :title="group.title" 
                                    :collapsed="isSidebarCollapsed"
                                    :color="group.items[0]?.color || 'primary'"
                                />

                                <template v-for="(item, itemIdx) in group.items" :key="itemIdx">
                                    <SidebarItem 
                                        v-if="hasPermission(item.permission)"
                                        :item="item"
                                        :collapsed="isSidebarCollapsed"
                                        :isActive="isItemActive(item)"
                                    >
                                        <SidebarChild 
                                            v-for="(child, childIdx) in item.children" 
                                            :key="childIdx"
                                            :child="child"
                                            :isActive="isItemActive(child)"
                                            :collapsed="isSidebarCollapsed"
                                            :color="item.color || 'primary'"
                                        />
                                    </SidebarItem>
                                </template>
                            </template>
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
