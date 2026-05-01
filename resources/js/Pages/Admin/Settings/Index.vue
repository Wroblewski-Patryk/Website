<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { markRaw, ref, computed } from 'vue';
import { 
    PhFloppyDisk, PhHouse, PhGearSix, PhBrowser, PhBookOpen, PhMagnifyingGlass, PhGlobe, PhImage, PhCheckCircle, PhXCircle, PhMapTrifold, PhLayout
} from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ModuleHeader from '@/features/admin/shared/components/ModuleHeader.vue';
import { useTranslations } from '@/Composables/useTranslations';
import MediaPickerModal from '@/features/admin/media/components/MediaPickerModal.vue';

const props = defineProps({
    settings: Object,
    pages: Array,
    updateStatus: Object
});

const languages = usePage().props.languages || [];
const { t } = useTranslations();
const isMediaPickerOpen = ref(false);
const mediaTargetField = ref('default_og_image');
const currentLocale = computed(() => usePage().props.locale || 'en');

const getEmptyLocales = (field) => {
    return languages.reduce((acc, lang) => {
        acc[lang.code] = props.settings[field]?.[lang.code] || '';
        return acc;
    }, {});
};

const form = useForm({
    home_page_id: props.settings.home_page_id || '',
    blog_page_id: props.settings.blog_page_id || '',
    projects_page_id: props.settings.projects_page_id || '',
    maintenance_mode: props.settings.maintenance_mode === '1' || props.settings.maintenance_mode === true,
    maintenance_page_id: props.settings.maintenance_page_id || '',
    coming_soon_page_id: props.settings.coming_soon_page_id || '',
    page_404_id: props.settings.page_404_id || '',
    page_500_id: props.settings.page_500_id || '',
    page_503_id: props.settings.page_503_id || '',
    brand_logo_light: props.settings.brand_logo_light || '',
    brand_logo_dark: props.settings.brand_logo_dark || '',
    brand_favicon: props.settings.brand_favicon || '',
    
    // SEO Settings (Dynamic)
    site_name: getEmptyLocales('site_name'),
    site_description: getEmptyLocales('site_description'),
    title_separator: props.settings.title_separator || ' - ',
    homepage_include_page_title: props.settings.homepage_include_page_title === '1' || props.settings.homepage_include_page_title === true,
    title_order: props.settings.title_order || 'brand_first',
    default_meta_description: getEmptyLocales('default_meta_description'),
    default_og_image: props.settings.default_og_image || '',
    admin_noindex: props.settings.admin_noindex !== '0' && props.settings.admin_noindex !== false, // default true
    robots_disallow_admin: props.settings.robots_disallow_admin !== '0' && props.settings.robots_disallow_admin !== false, // default true
    sitemap_enabled: props.settings.sitemap_enabled !== '0' && props.settings.sitemap_enabled !== false, // default true
    sitemap_cache_minutes: parseInt(props.settings.sitemap_cache_minutes) || 60,
    update_checks_enabled: props.settings.update_checks_enabled !== '0' && props.settings.update_checks_enabled !== false,
    auto_update_enabled: props.settings.auto_update_enabled === '1' || props.settings.auto_update_enabled === true,
    update_release_channel: props.settings.update_release_channel || props.updateStatus?.release_channel || 'stable',
    preferred_update_driver: props.settings.preferred_update_driver || props.updateStatus?.preferred_update_driver || 'auto',
});
const checkUpdatesForm = useForm({});
const applyUpdateForm = useForm({});

function openMediaPickerFor(field) {
    mediaTargetField.value = field;
    isMediaPickerOpen.value = true;
}

function selectMedia(file) {
    form[mediaTargetField.value] = file.url;
    isMediaPickerOpen.value = false;
}

function saveSettings() {
    form.post(route('admin.settings.store'), {
        preserveScroll: true
    });
}

function checkForUpdates() {
    checkUpdatesForm.post(route('admin.settings.check-updates'), {
        preserveScroll: true,
    });
}

function applyUpdate() {
    applyUpdateForm.post(route('admin.settings.apply-update'), {
        preserveScroll: true,
    });
}
</script>

<template>

    <AdminLayout>
        <div class="space-y-6">
            <div class="bg-base-100 p-6 rounded-box shadow-sm border border-base-300">
                <ModuleHeader
                    :title="t('admin.settings.general_config', 'Global Settings')"
                    :description="t('admin.settings.general_desc', 'Manage core website behavior and routing configurations.')"
                    :icon="markRaw(PhGearSix)"
                    :breadcrumbs="[
                        { label: t('admin.dashboard.title', 'Dashboard'), url: route('admin.dashboard.index'), icon: markRaw(PhHouse) },
                        { label: t('admin.menu.settings', 'Settings') }
                    ]"
                >
                    <template #actions>
                        <button 
                            @click="saveSettings" 
                            class="btn btn-primary shadow-lg shadow-primary/20 hover:shadow-xl hover:-translate-y-0.5 transition-all px-6" 
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing" class="loading loading-spinner loading-sm mr-2"></span>
                            <PhFloppyDisk weight="bold" class="w-4 h-4 mr-2" v-else />
                            {{ t('admin.settings.save_changes', 'Save Changes') }}
                        </button>
                    </template>
                </ModuleHeader>
            </div>

            <div class="space-y-6 pb-20 mt-6">
                <!-- Settings Content Section -->
                <div class="grid grid-cols-1 gap-6 max-w-5xl">
                    <!-- Routing Section -->
                    <div class="bg-base-100 rounded-box shadow-sm border border-base-300 overflow-hidden">
                        <div class="p-6 border-b border-base-200 bg-base-200/20">
                            <h2 class="text-sm font-black uppercase tracking-widest opacity-60 flex items-center gap-2">
                                <PhGearSix weight="bold" class="w-4 h-4" />
                                {{ t('admin.settings.routing_nav', 'Routing & Navigation') }}
                            </h2>
                        </div>
                    
                        <div class="p-8">
                            <!-- Public Pages Group -->
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
                                <!-- Home Page -->
                                <div class="form-control">
                                    <label class="label pt-0">
                                        <span class="label-text flex items-center gap-2 font-bold text-base-content/70">
                                            <PhBrowser weight="duotone" class="w-5 h-5 text-primary" />
                                            {{ t('admin.settings.home_page', 'Home Page') }}
                                        </span>
                                    </label>
                                    <select v-model="form.home_page_id" class="select select-bordered w-full focus:select-primary transition-all bg-base-100">
                                        <option value="">{{ t('admin.settings.none_404', 'None (Returns 404)') }}</option>
                                        <option v-for="page in pages" :key="page.id" :value="page.id">
                                            {{ t(page.title) }}
                                        </option>
                                    </select>
                                    <label class="label">
                                        <span class="label-text-alt text-base-content/40 italic">{{ t('admin.settings.home_page_desc', 'Root URL (/) behavior.') }}</span>
                                    </label>
                                </div>

                                <!-- Blog Page -->
                                <div class="form-control">
                                    <label class="label pt-0">
                                        <span class="label-text flex items-center gap-2 font-bold text-base-content/70">
                                            <PhBookOpen weight="duotone" class="w-5 h-5 text-primary" />
                                            {{ t('admin.settings.blog_index', 'Blog Index') }}
                                        </span>
                                    </label>
                                    <select v-model="form.blog_page_id" class="select select-bordered w-full focus:select-primary transition-all bg-base-100">
                                        <option value="">{{ t('admin.settings.none_static', 'None (Static Layout)') }}</option>
                                        <option v-for="page in pages" :key="page.id" :value="page.id">
                                            {{ t(page.title) }}
                                        </option>
                                    </select>
                                    <label class="label">
                                        <span class="label-text-alt text-base-content/40 italic">{{ t('admin.settings.blog_index_desc', 'Blog listing meta data.') }}</span>
                                    </label>
                                </div>

                                <!-- Projects Page -->
                                <div class="form-control">
                                    <label class="label pt-0">
                                        <span class="label-text flex items-center gap-2 font-bold text-base-content/70">
                                            <PhLayout weight="duotone" class="w-5 h-5 text-primary" />
                                            {{ t('admin.settings.projects_page', 'Projects Page') }}
                                        </span>
                                    </label>
                                    <select v-model="form.projects_page_id" class="select select-bordered w-full focus:select-primary transition-all bg-base-100">
                                        <option value="">{{ t('admin.settings.none_projects', 'None (Static Projects)') }}</option>
                                        <option v-for="page in pages" :key="page.id" :value="page.id">
                                            {{ t(page.title) }}
                                        </option>
                                    </select>
                                    <label class="label">
                                        <span class="label-text-alt text-base-content/40 italic">{{ t('admin.settings.projects_page_desc', 'Portfolio listing source.') }}</span>
                                    </label>
                                </div>
                            </div>

                            <!-- System Behavior Group -->
                            <div class="bg-base-200/40 p-6 md:p-8 rounded-3xl border border-base-300/50">
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
                                    <div class="flex-1">
                                        <h3 class="text-xl font-black italic uppercase tracking-tighter text-primary flex items-center gap-3">
                                            {{ t('admin.settings.system_behavior', 'System Behavior & Maintenance') }}
                                        </h3>
                                        <p class="text-sm opacity-50 mt-1">{{ t('admin.settings.system_behavior_desc', 'Configure global redirects, states, and error handling.') }}</p>
                                    </div>
                                    <div class="form-control bg-base-100 p-4 px-6 rounded-2xl border border-base-300 shadow-sm">
                                        <label class="label cursor-pointer gap-4 p-0">
                                            <span class="text-xs font-black uppercase tracking-widest opacity-60">{{ t('admin.settings.maintenance_mode', 'Maintenance Mode') }}</span>
                                            <input type="checkbox" v-model="form.maintenance_mode" class="toggle toggle-primary toggle-md" />
                                        </label>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                    <div class="form-control w-full">
                                        <label class="label">
                                            <span class="label-text font-bold text-base-content/70 text-xs uppercase tracking-widest">{{ t('admin.settings.maintenance_page', 'Maintenance Page') }}</span>
                                        </label>
                                        <select v-model="form.maintenance_page_id" class="select select-bordered w-full bg-base-100">
                                            <option value="">{{ t('admin.settings.default_theme_page', 'Default theme page...') }}</option>
                                            <option v-for="page in pages" :key="page.id" :value="page.id">
                                                {{ t(page.title) }}
                                            </option>
                                        </select>
                                        <p class="mt-2 text-[10px] opacity-40 italic">{{ t('admin.settings.maintenance_page_desc', 'Redirect here when mode is ON.') }}</p>
                                    </div>

                                    <div class="form-control w-full">
                                        <label class="label">
                                            <span class="label-text font-bold text-base-content/70 text-xs uppercase tracking-widest">{{ t('admin.settings.coming_soon', 'Coming Soon') }}</span>
                                        </label>
                                        <select v-model="form.coming_soon_page_id" class="select select-bordered w-full bg-base-100">
                                            <option value="">{{ t('admin.settings.show_regular_404', 'Show regular 404...') }}</option>
                                            <option v-for="page in pages" :key="page.id" :value="page.id">
                                                {{ t(page.title) }}
                                            </option>
                                        </select>
                                        <p class="mt-2 text-[10px] opacity-40 italic">{{ t('admin.settings.coming_soon_desc', 'For pages with "Planned" status.') }}</p>
                                    </div>

                                    <div class="form-control w-full">
                                        <label class="label">
                                            <span class="label-text font-bold text-base-content/70 text-xs uppercase tracking-widest">{{ t('admin.settings.custom_404', 'Custom 404') }}</span>
                                        </label>
                                        <select v-model="form.page_404_id" class="select select-bordered w-full bg-base-100">
                                            <option value="">{{ t('admin.settings.standard_404', 'Standard 404...') }}</option>
                                            <option v-for="page in pages" :key="page.id" :value="page.id">
                                                {{ t(page.title) }}
                                            </option>
                                        </select>
                                        <p class="mt-2 text-[10px] opacity-40 italic">{{ t('admin.settings.custom_404_desc', 'Global error destination.') }}</p>
                                    </div>

                                    <div class="form-control w-full">
                                        <label class="label">
                                            <span class="label-text font-bold text-base-content/70 text-xs uppercase tracking-widest">Custom 500</span>
                                        </label>
                                        <select v-model="form.page_500_id" class="select select-bordered w-full bg-base-100">
                                            <option value="">Standard 500...</option>
                                            <option v-for="page in pages" :key="page.id" :value="page.id">
                                                {{ t(page.title) }}
                                            </option>
                                        </select>
                                        <p class="mt-2 text-[10px] opacity-40 italic">Internal server error fallback destination.</p>
                                    </div>

                                    <div class="form-control w-full">
                                        <label class="label">
                                            <span class="label-text font-bold text-base-content/70 text-xs uppercase tracking-widest">Custom 503</span>
                                        </label>
                                        <select v-model="form.page_503_id" class="select select-bordered w-full bg-base-100">
                                            <option value="">Standard 503...</option>
                                            <option v-for="page in pages" :key="page.id" :value="page.id">
                                                {{ t(page.title) }}
                                            </option>
                                        </select>
                                        <p class="mt-2 text-[10px] opacity-40 italic">Service unavailable / maintenance fallback destination.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-base-100 rounded-box shadow-sm border border-base-300 overflow-hidden">
                        <div class="p-6 border-b border-base-200 bg-base-200/20">
                            <h2 class="text-sm font-black uppercase tracking-widest opacity-60 flex items-center gap-2">
                                <PhCheckCircle weight="bold" class="w-4 h-4" />
                                {{ t('admin.settings.updates_title', 'System Updates') }}
                            </h2>
                            <p class="mt-2 text-sm opacity-60">
                                {{ t('admin.settings.updates_desc', 'Monitor available Featherly releases and the safe update mode.') }}
                            </p>
                        </div>

                        <div class="p-8 space-y-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                                <div class="rounded-2xl border border-base-300 bg-base-200/30 p-4">
                                    <div class="text-[10px] font-bold uppercase tracking-widest opacity-50">{{ t('admin.settings.current_version', 'Current version') }}</div>
                                    <div class="mt-2 text-2xl font-black">{{ props.updateStatus?.current_version || '0.0.0' }}</div>
                                </div>
                                <div class="rounded-2xl border border-base-300 bg-base-200/30 p-4">
                                    <div class="text-[10px] font-bold uppercase tracking-widest opacity-50">{{ t('admin.settings.latest_version', 'Latest version') }}</div>
                                    <div class="mt-2 text-2xl font-black">{{ props.updateStatus?.latest_version || 'n/a' }}</div>
                                </div>
                                <div class="rounded-2xl border border-base-300 bg-base-200/30 p-4">
                                    <div class="text-[10px] font-bold uppercase tracking-widest opacity-50">{{ t('admin.settings.status', 'Status') }}</div>
                                    <div class="mt-2 flex items-center gap-2 text-sm font-semibold">
                                        <PhXCircle v-if="props.updateStatus?.failure_message" class="w-4 h-4 text-error" />
                                        <PhCheckCircle v-else class="w-4 h-4 text-success" />
                                        <span>{{ props.updateStatus?.status_label || t('admin.settings.not_checked_yet', 'Not checked yet') }}</span>
                                    </div>
                                </div>
                                <div class="rounded-2xl border border-base-300 bg-base-200/30 p-4">
                                    <div class="text-[10px] font-bold uppercase tracking-widest opacity-50">{{ t('admin.settings.last_check', 'Last check') }}</div>
                                    <div class="mt-2 text-sm font-medium">{{ props.updateStatus?.checked_at || t('admin.settings.not_checked_yet', 'Not checked yet') }}</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4 rounded-3xl border border-base-300 bg-base-200/20 p-6">
                                    <div class="flex items-center justify-between gap-4">
                                        <label class="text-xs font-black uppercase tracking-widest opacity-60">{{ t('admin.settings.update_checks_enabled', 'Enable update checks') }}</label>
                                        <input v-model="form.update_checks_enabled" type="checkbox" class="toggle toggle-primary" />
                                    </div>
                                    <div class="flex items-center justify-between gap-4">
                                        <label class="text-xs font-black uppercase tracking-widest opacity-60">{{ t('admin.settings.auto_update_enabled', 'Enable automatic updates') }}</label>
                                        <input v-model="form.auto_update_enabled" type="checkbox" class="toggle toggle-primary" />
                                    </div>
                                    <p v-if="props.updateStatus?.auto_apply_env_enabled === false" class="text-xs text-warning">
                                        {{ t('admin.settings.auto_apply_forced_off', 'Automatic apply is disabled by environment configuration.') }}
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 rounded-3xl border border-base-300 bg-base-200/20 p-6">
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text text-xs font-bold uppercase tracking-widest opacity-60">{{ t('admin.settings.channel', 'Channel') }}</span>
                                        </label>
                                        <select v-model="form.update_release_channel" class="select select-bordered bg-base-100">
                                            <option value="stable">stable</option>
                                        </select>
                                    </div>
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text text-xs font-bold uppercase tracking-widest opacity-60">{{ t('admin.settings.preferred_driver', 'Preferred driver') }}</span>
                                        </label>
                                        <select v-model="form.preferred_update_driver" class="select select-bordered bg-base-100">
                                            <option value="auto">{{ t('admin.settings.auto_driver', 'Automatic selection') }}</option>
                                            <option value="manual">{{ t('admin.settings.manual_driver', 'Manual mode') }}</option>
                                            <option value="coolify">Coolify</option>
                                            <option value="archive">{{ t('admin.settings.archive_driver', 'Archive mode') }}</option>
                                        </select>
                                    </div>
                                    <div class="sm:col-span-2 rounded-2xl border border-base-300 bg-base-100 p-4 text-xs opacity-70">
                                        <div class="font-bold uppercase tracking-widest opacity-50">{{ t('admin.settings.driver', 'Driver') }}</div>
                                        <div class="mt-2">{{ props.updateStatus?.driver_label || 'Manual' }}</div>
                                        <p class="mt-3">{{ props.updateStatus?.driver_preflight_message || t('admin.settings.manual_only', 'This slice runs in manual mode: it shows status and settings, but does not apply updates yet.') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div v-if="props.updateStatus?.driver_options?.length" class="rounded-3xl border border-base-300 bg-base-200/20 p-6">
                                <div class="text-xs font-black uppercase tracking-widest opacity-60">
                                    {{ t('admin.settings.driver_preflight', 'Driver preflight') }}
                                </div>
                                <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-3">
                                    <div
                                        v-for="driver in props.updateStatus.driver_options"
                                        :key="driver.key"
                                        class="rounded-2xl border border-base-300 bg-base-100 p-4 text-sm"
                                    >
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="font-bold">{{ driver.label }}</div>
                                            <span
                                                class="badge badge-sm"
                                                :class="driver.preflight_ok ? 'badge-success' : 'badge-ghost'"
                                            >
                                                {{ driver.preflight_ok ? t('admin.settings.ready', 'Ready') : t('admin.settings.not_ready', 'Not ready') }}
                                            </span>
                                        </div>
                                        <p class="mt-3 text-xs opacity-70">{{ driver.message }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col gap-4 rounded-3xl border border-base-300 bg-base-200/20 p-6 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <div class="text-xs font-black uppercase tracking-widest opacity-60">
                                        {{ t('admin.settings.check_now', 'Check now') }}
                                    </div>
                                    <p class="mt-2 text-sm opacity-70">
                                        {{ t('admin.settings.check_now_desc', 'Run a server-side manifest check immediately without enabling automatic apply.') }}
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    class="btn btn-outline btn-primary"
                                    :disabled="checkUpdatesForm.processing"
                                    @click="checkForUpdates"
                                >
                                    <span v-if="checkUpdatesForm.processing" class="loading loading-spinner loading-sm mr-2"></span>
                                    <PhCheckCircle v-else weight="bold" class="mr-2 h-4 w-4" />
                                    {{ t('admin.settings.check_now', 'Check now') }}
                                </button>
                            </div>

                            <div class="flex flex-col gap-4 rounded-3xl border border-base-300 bg-base-200/20 p-6 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <div class="text-xs font-black uppercase tracking-widest opacity-60">
                                        {{ t('admin.settings.apply_update', 'Apply update') }}
                                    </div>
                                    <p class="mt-2 text-sm opacity-70">
                                        {{ t('admin.settings.apply_update_desc', 'Run the configured server-side update driver contract. Manual mode records operator instructions and does not change files.') }}
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    class="btn btn-outline"
                                    :disabled="applyUpdateForm.processing || !props.updateStatus?.update_available"
                                    @click="applyUpdate"
                                >
                                    <span v-if="applyUpdateForm.processing" class="loading loading-spinner loading-sm mr-2"></span>
                                    <PhCheckCircle v-else weight="bold" class="mr-2 h-4 w-4" />
                                    {{ t('admin.settings.apply_update', 'Apply update') }}
                                </button>
                            </div>

                            <div v-if="props.updateStatus?.operator_message" class="rounded-2xl border border-base-300 bg-base-100 p-4 text-sm">
                                <div class="text-[10px] font-bold uppercase tracking-widest opacity-50">{{ t('admin.settings.operator_message', 'Operator message') }}</div>
                                <p class="mt-2">{{ props.updateStatus.operator_message }}</p>
                                <ul v-if="props.updateStatus.operator_instructions?.length" class="mt-3 list-disc space-y-1 pl-5">
                                    <li v-for="instruction in props.updateStatus.operator_instructions" :key="instruction">
                                        {{ instruction }}
                                    </li>
                                </ul>
                                <p v-if="props.updateStatus.rollback_note" class="mt-3 text-xs opacity-60">
                                    {{ props.updateStatus.rollback_note }}
                                </p>
                            </div>

                            <div v-if="props.updateStatus?.failure_message" class="rounded-2xl border border-error/30 bg-error/5 p-4 text-sm text-error">
                                <div class="text-[10px] font-bold uppercase tracking-widest">{{ t('admin.settings.failure', 'Failure') }}</div>
                                <div class="mt-2">{{ props.updateStatus.failure_message }}</div>
                            </div>

                            <div v-if="props.updateStatus?.release_notes_url" class="rounded-2xl border border-base-300 bg-base-100 p-4 text-sm">
                                <div class="text-[10px] font-bold uppercase tracking-widest opacity-50">{{ t('admin.settings.release_notes', 'Release notes') }}</div>
                                <a
                                    :href="props.updateStatus.release_notes_url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="mt-2 inline-flex text-primary underline underline-offset-4"
                                >
                                    {{ props.updateStatus.release_notes_url }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Section -->
                    <div class="bg-base-100 rounded-box shadow-sm border border-base-300 overflow-hidden">
                        <div class="p-6 border-b border-base-200 bg-base-200/20">
                            <h2 class="text-sm font-black uppercase tracking-widest opacity-60 flex items-center gap-2">
                                <PhMagnifyingGlass weight="bold" class="w-4 h-4" />
                                {{ t('admin.settings.seo_indexing', 'SEO & Indexing') }}
                            </h2>
                        </div>
                        
                        <div class="p-8 space-y-10">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6 bg-base-200/20 rounded-2xl border border-base-300/50">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text text-xs font-bold uppercase tracking-widest opacity-70">Logo Light</span>
                                    </label>
                                    <div class="space-y-2">
                                        <div class="relative w-full aspect-[4/3] rounded-xl bg-base-200 border border-dashed border-base-300 flex items-center justify-center overflow-hidden">
                                            <img v-if="form.brand_logo_light" :src="form.brand_logo_light" class="w-full h-full object-contain p-3" />
                                            <PhImage v-else weight="duotone" class="w-8 h-8 opacity-20" />
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="button" class="btn btn-xs btn-outline btn-primary" @click="openMediaPickerFor('brand_logo_light')">Select</button>
                                            <button v-if="form.brand_logo_light" type="button" class="btn btn-xs btn-ghost text-error" @click="form.brand_logo_light = ''">Remove</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text text-xs font-bold uppercase tracking-widest opacity-70">Logo Dark</span>
                                    </label>
                                    <div class="space-y-2">
                                        <div class="relative w-full aspect-[4/3] rounded-xl bg-base-200 border border-dashed border-base-300 flex items-center justify-center overflow-hidden">
                                            <img v-if="form.brand_logo_dark" :src="form.brand_logo_dark" class="w-full h-full object-contain p-3" />
                                            <PhImage v-else weight="duotone" class="w-8 h-8 opacity-20" />
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="button" class="btn btn-xs btn-outline btn-primary" @click="openMediaPickerFor('brand_logo_dark')">Select</button>
                                            <button v-if="form.brand_logo_dark" type="button" class="btn btn-xs btn-ghost text-error" @click="form.brand_logo_dark = ''">Remove</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text text-xs font-bold uppercase tracking-widest opacity-70">Favicon</span>
                                    </label>
                                    <div class="space-y-2">
                                        <div class="relative w-full aspect-[4/3] rounded-xl bg-base-200 border border-dashed border-base-300 flex items-center justify-center overflow-hidden">
                                            <img v-if="form.brand_favicon" :src="form.brand_favicon" class="w-12 h-12 object-contain" />
                                            <PhImage v-else weight="duotone" class="w-8 h-8 opacity-20" />
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="button" class="btn btn-xs btn-outline btn-primary" @click="openMediaPickerFor('brand_favicon')">Select</button>
                                            <button v-if="form.brand_favicon" type="button" class="btn btn-xs btn-ghost text-error" @click="form.brand_favicon = ''">Remove</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Site Name -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text flex items-center gap-2 font-bold text-base-content/70">
                                            <PhGlobe weight="duotone" class="w-4 h-4 text-primary" />
                                            {{ t('admin.settings.site_name', 'Site Name') }} ({{ currentLocale.toUpperCase() }})
                                        </span>
                                    </label>
                                    <input type="text" v-model="form.site_name[currentLocale]" class="input input-bordered w-full focus:input-primary transition-all" />
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text flex items-center gap-2 font-bold text-base-content/70">
                                            <PhMapTrifold weight="duotone" class="w-4 h-4 text-primary" />
                                            {{ t('admin.settings.site_description', 'Site Description') }} ({{ currentLocale.toUpperCase() }})
                                        </span>
                                    </label>
                                    <input type="text" v-model="form.site_description[currentLocale]" class="input input-bordered w-full focus:input-primary transition-all" />
                                </div>
                            </div>

                            <!-- Title Settings -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 p-6 bg-base-200/30 rounded-2xl border border-base-300/50">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-bold text-base-content/70 text-xs uppercase tracking-widest">{{ t('admin.settings.separator', 'Separator') }}</span>
                                    </label>
                                    <input type="text" v-model="form.title_separator" class="input input-bordered w-full focus:input-primary transition-all" />
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-bold text-base-content/70 text-xs uppercase tracking-widest">{{ t('admin.settings.title_order', 'Title Order') }}</span>
                                    </label>
                                    <select v-model="form.title_order" class="select select-bordered w-full focus:select-primary transition-all bg-base-100">
                                        <option value="brand_first">{{ t('admin.settings.order_brand_first', 'Brand - Page') }}</option>
                                        <option value="page_first">{{ t('admin.settings.order_page_first', 'Page - Brand') }}</option>
                                    </select>
                                </div>

                                <div class="form-control">
                                    <label class="label cursor-pointer flex flex-col items-start gap-2 h-full justify-center">
                                        <span class="label-text font-bold text-base-content/70 text-xs uppercase tracking-widest">{{ t('admin.settings.include_home_title', 'Include Home Title') }}</span>
                                        <input type="checkbox" v-model="form.homepage_include_page_title" class="toggle toggle-primary toggle-sm" />
                                    </label>
                                </div>
                            </div>

                            <!-- Localized Descriptions -->
                            <div class="space-y-6">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text flex items-center gap-2 font-bold text-base-content/70 text-xs uppercase tracking-widest">
                                            {{ t('admin.settings.meta_desc', 'Default Meta Description') }} ({{ currentLocale.toUpperCase() }})
                                        </span>
                                    </label>
                                    <textarea v-model="form.default_meta_description[currentLocale]" class="textarea textarea-bordered h-24 focus:textarea-primary transition-all" :placeholder="t('admin.settings.meta_desc_placeholder', 'Enter meta description for SEO purposes...')"></textarea>
                                </div>
                            </div>

                            <!-- OG Image -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-bold text-base-content/70 text-xs uppercase tracking-widest">{{ t('admin.settings.og_image', 'Default OG Image') }}</span>
                                </label>
                                <div class="flex items-center gap-4 group">
                                    <div class="relative w-40 aspect-video rounded-xl bg-base-200 border-2 border-dashed border-base-300 flex items-center justify-center overflow-hidden transition-all group-hover:border-primary/50">
                                        <img v-if="form.default_og_image" :src="form.default_og_image" class="w-full h-full object-cover" />
                                        <PhImage v-else weight="duotone" class="w-10 h-10 opacity-20" />
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <button @click="openMediaPickerFor('default_og_image')" class="btn btn-sm btn-outline btn-primary">
                                            {{ t('admin.settings.select_image', 'Select Image') }}
                                        </button>
                                        <button v-if="form.default_og_image" @click="form.default_og_image = ''" class="btn btn-sm btn-ghost text-error">
                                            {{ t('admin.settings.remove', 'Remove') }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Technical Toggles -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 pt-6 border-t border-base-200">
                                <div class="flex items-center gap-3 p-4 bg-base-200/50 rounded-xl border border-base-300/50">
                                    <input type="checkbox" v-model="form.admin_noindex" class="checkbox checkbox-primary checkbox-sm" id="admin_noindex" />
                                    <label for="admin_noindex" class="text-xs font-bold uppercase tracking-wider opacity-70 cursor-pointer">{{ t('admin.settings.admin_noindex', 'Admin No-Index') }}</label>
                                </div>
                                <div class="flex items-center gap-3 p-4 bg-base-200/50 rounded-xl border border-base-300/50">
                                    <input type="checkbox" v-model="form.robots_disallow_admin" class="checkbox checkbox-primary checkbox-sm" id="robots_admin" />
                                    <label for="robots_admin" class="text-xs font-bold uppercase tracking-wider opacity-70 cursor-pointer">{{ t('admin.settings.robots_disallow', 'Robots Disallow Admin') }}</label>
                                </div>
                                <div class="flex items-center gap-3 p-4 bg-base-200/50 rounded-xl border border-base-300/50">
                                    <input type="checkbox" v-model="form.sitemap_enabled" class="checkbox checkbox-primary checkbox-sm" id="sitemap_enabled" />
                                    <label for="sitemap_enabled" class="text-xs font-bold uppercase tracking-wider opacity-70 cursor-pointer">{{ t('admin.settings.sitemap_enabled', 'Sitemap Enabled') }}</label>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <label class="text-[10px] font-bold uppercase opacity-40 ml-1">{{ t('admin.settings.sitemap_cache', 'Sitemap Cache (min)') }}</label>
                                    <input type="number" v-model="form.sitemap_cache_minutes" class="input input-bordered input-sm w-full focus:input-primary transition-all" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Media Picker Modal -->
        <MediaPickerModal 
            v-if="isMediaPickerOpen" 
            @close="isMediaPickerOpen = false" 
            @select="selectMedia"
        />
    </AdminLayout>
</template>

<style scoped>
/* Clean and consistent with administrative UI standards */
</style>
