<script setup>
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { markRaw, ref, onMounted, computed } from 'vue';
import { 
    PhFloppyDisk, PhHouse, PhGearSix, PhBrowser, PhBookOpen, PhMagnifyingGlass, PhGlobe, PhImage, PhCheckCircle, PhXCircle, PhMapTrifold, PhLayout
} from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ModuleHeader from '@/Components/Admin/ModuleHeader.vue';
import { useTranslations } from '@/Composables/useTranslations';
import MediaPickerModal from '@/Components/Admin/Media/MediaPickerModal.vue';

const props = defineProps({
    settings: Object,
    pages: Array
});

const languages = usePage().props.languages || [];
const { t } = useTranslations();
const isMediaPickerOpen = ref(false);
const currentLocale = computed(() => usePage().props.locale || 'en');

const getEmptyLocales = (field) => {
    return languages.reduce((acc, lang) => {
        acc[lang.code] = props.settings[field]?.[lang.code] || '';
        return acc;
    }, {});
};

const breadcrumbs = [
    { label: t('admin.dashboard.title', 'Dashboard'), url: route('admin.dashboard.index'), icon: markRaw(PhHouse) },
    { label: t('admin.menu.settings', 'Settings') }
];

const form = useForm({
    home_page_id: props.settings.home_page_id || '',
    blog_page_id: props.settings.blog_page_id || '',
    projects_page_id: props.settings.projects_page_id || '',
    maintenance_mode: props.settings.maintenance_mode === '1' || props.settings.maintenance_mode === true,
    maintenance_page_id: props.settings.maintenance_page_id || '',
    coming_soon_page_id: props.settings.coming_soon_page_id || '',
    page_404_id: props.settings.page_404_id || '',
    
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
});

function selectOgImage(file) {
    form.default_og_image = file.url;
    isMediaPickerOpen.value = false;
}

function saveSettings() {
    form.post(route('admin.settings.store'), {
        preserveScroll: true
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
                                </div>
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
                                        <button @click="isMediaPickerOpen = true" class="btn btn-sm btn-outline btn-primary">
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
            @select="selectOgImage" 
        />
    </AdminLayout>
</template>

<style scoped>
/* Clean and consistent with administrative UI standards */
</style>
