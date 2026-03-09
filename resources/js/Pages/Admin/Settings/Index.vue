<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { markRaw } from 'vue';
import { 
    PhFloppyDisk, PhHouse, PhGearSix, PhBrowser, PhBookOpen 
} from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ModuleHeader from '@/Components/Admin/ModuleHeader.vue';
import { useTranslations } from '@/Composables/useTranslations';

const props = defineProps({
    settings: Object,
    pages: Array
});

const { t } = useTranslations();

const breadcrumbs = [
    { label: 'Admin', url: '/admin', icon: markRaw(PhHouse) },
    { label: 'Settings' }
];

const form = useForm({
    home_page_id: props.settings.home_page_id || '',
    blog_page_id: props.settings.blog_page_id || '',
    projects_page_id: props.settings.projects_page_id || '',
    maintenance_mode: props.settings.maintenance_mode === '1' || props.settings.maintenance_mode === true,
    maintenance_page_id: props.settings.maintenance_page_id || '',
    coming_soon_page_id: props.settings.coming_soon_page_id || '',
    page_404_id: props.settings.page_404_id || '',
});

function saveSettings() {
    form.post('/admin/settings', {
        preserveScroll: true
    });
}
</script>

<template>
    <Head title="Global Settings" />
    <AdminLayout>
        <div class="space-y-6">
            <!-- Module Title Section (Matching ResourceTable) -->
            <div class="bg-base-100 p-6 rounded-box shadow-sm border border-base-300">
                <ModuleHeader
                    title="Global Settings"
                    description="Manage core website behavior and routing configurations."
                    :icon="markRaw(PhGearSix)"
                    :breadcrumbs="breadcrumbs"
                >
                    <template #actions>
                        <button 
                            @click="saveSettings" 
                            class="btn btn-primary shadow-lg shadow-primary/20 hover:shadow-xl hover:-translate-y-0.5 transition-all px-6" 
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing" class="loading loading-spinner loading-sm mr-2"></span>
                            <PhFloppyDisk weight="bold" class="w-4 h-4 mr-2" />
                            Save Changes
                        </button>
                    </template>
                </ModuleHeader>
            </div>

            <!-- Settings Content Section -->
            <div class="grid grid-cols-1 gap-6 max-w-5xl">
                <!-- Routing Section -->
                <div class="bg-base-100 rounded-box shadow-sm border border-base-300 overflow-hidden">
                    <div class="p-6 border-b border-base-200 bg-base-200/20">
                        <h2 class="text-sm font-black uppercase tracking-widest opacity-60 flex items-center gap-2">
                             <PhGearSix weight="bold" class="w-4 h-4" />
                             Routing & Navigation
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
                                        Home Page
                                    </span>
                                </label>
                                <select v-model="form.home_page_id" class="select select-bordered w-full focus:select-primary transition-all bg-base-100">
                                    <option value="">None (Returns 404)</option>
                                    <option v-for="page in pages" :key="page.id" :value="page.id">
                                        {{ t(page.title) }}
                                    </option>
                                </select>
                                <label class="label">
                                    <span class="label-text-alt text-base-content/40 italic">Root URL (/) behavior.</span>
                                </label>
                            </div>

                            <!-- Blog Page -->
                            <div class="form-control">
                                <label class="label pt-0">
                                    <span class="label-text flex items-center gap-2 font-bold text-base-content/70">
                                        <PhBookOpen weight="duotone" class="w-5 h-5 text-primary" />
                                        Blog Index
                                    </span>
                                </label>
                                <select v-model="form.blog_page_id" class="select select-bordered w-full focus:select-primary transition-all bg-base-100">
                                    <option value="">None (Static Layout)</option>
                                    <option v-for="page in pages" :key="page.id" :value="page.id">
                                        {{ t(page.title) }}
                                    </option>
                                </select>
                                <label class="label">
                                    <span class="label-text-alt text-base-content/40 italic">Blog listing meta data.</span>
                                </label>
                            </div>

                            <!-- Projects Page -->
                            <div class="form-control">
                                <label class="label pt-0">
                                    <span class="label-text flex items-center gap-2 font-bold text-base-content/70">
                                        <PhLayout weight="duotone" class="w-5 h-5 text-primary" />
                                        Projects Page
                                    </span>
                                </label>
                                <select v-model="form.projects_page_id" class="select select-bordered w-full focus:select-primary transition-all bg-base-100">
                                    <option value="">None (Static Projects)</option>
                                    <option v-for="page in pages" :key="page.id" :value="page.id">
                                        {{ t(page.title) }}
                                    </option>
                                </select>
                                <label class="label">
                                    <span class="label-text-alt text-base-content/40 italic">Portfolio listing source.</span>
                                </label>
                            </div>
                        </div>

                        <!-- System Behavior Group -->
                        <div class="bg-base-200/40 p-6 md:p-8 rounded-3xl border border-base-300/50">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
                                <div class="flex-1">
                                    <h3 class="text-xl font-black italic uppercase tracking-tighter text-primary flex items-center gap-3">
                                        System Behavior & Maintenance
                                    </h3>
                                    <p class="text-sm opacity-50 mt-1">Configure global redirects, states, and error handling.</p>
                                </div>
                                <div class="form-control bg-base-100 p-4 px-6 rounded-2xl border border-base-300 shadow-sm">
                                    <label class="label cursor-pointer gap-4 p-0">
                                        <span class="text-xs font-black uppercase tracking-widest opacity-60">Maintenance Mode</span>
                                        <input type="checkbox" v-model="form.maintenance_mode" class="toggle toggle-primary toggle-md" />
                                    </label>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div class="form-control w-full">
                                    <label class="label">
                                        <span class="label-text font-bold text-base-content/70 text-xs uppercase tracking-widest">Maintenance Page</span>
                                    </label>
                                    <select v-model="form.maintenance_page_id" class="select select-bordered w-full bg-base-100">
                                        <option value="">Default theme page...</option>
                                        <option v-for="page in pages" :key="page.id" :value="page.id">
                                            {{ t(page.title) }}
                                        </option>
                                    </select>
                                    <p class="mt-2 text-[10px] opacity-40 italic">Redirect here when mode is ON.</p>
                                </div>

                                <div class="form-control w-full">
                                    <label class="label">
                                        <span class="label-text font-bold text-base-content/70 text-xs uppercase tracking-widest">Coming Soon</span>
                                    </label>
                                    <select v-model="form.coming_soon_page_id" class="select select-bordered w-full bg-base-100">
                                        <option value="">Show regular 404...</option>
                                        <option v-for="page in pages" :key="page.id" :value="page.id">
                                            {{ t(page.title) }}
                                        </option>
                                    </select>
                                    <p class="mt-2 text-[10px] opacity-40 italic">For pages with "Planned" status.</p>
                                </div>

                                <div class="form-control w-full">
                                    <label class="label">
                                        <span class="label-text font-bold text-base-content/70 text-xs uppercase tracking-widest">Custom 404</span>
                                    </label>
                                    <select v-model="form.page_404_id" class="select select-bordered w-full bg-base-100">
                                        <option value="">Standard 404...</option>
                                        <option v-for="page in pages" :key="page.id" :value="page.id">
                                            {{ t(page.title) }}
                                        </option>
                                    </select>
                                    <p class="mt-2 text-[10px] opacity-40 italic">Global error destination.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
/* Clean and consistent with administrative UI standards */
</style>


