<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    settings: Object,
    templates: Array
});

const form = useForm({
    home_page_slug: props.settings.home_page_slug || 'home',
    blog_page_slug: props.settings.blog_page_slug || 'blog',
    default_header_id: props.settings.default_header_id || '',
    default_footer_id: props.settings.default_footer_id || '',
    brand_primary_color: props.settings.brand_primary_color || '#000000',
    brand_secondary_color: props.settings.brand_secondary_color || '#ffffff',
    brand_font_family: props.settings.brand_font_family || 'sans-serif',
});

const headers = computed(() => props.templates.filter(t => t.type === 'header'));
const footers = computed(() => props.templates.filter(t => t.type === 'footer'));

function saveSettings() {
    form.post('/admin/settings');
}
</script>

<template>
    <Head title="Global Settings" />
    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-black tracking-tight flex items-center gap-3">
                        <i class="fas fa-cog text-primary"></i>
                        Global Settings
                    </h1>
                    <p class="text-sm opacity-50 mt-1">Configure your website's main parameters.</p>
                </div>
                <button @click="saveSettings" class="btn btn-primary px-6 shadow-lg shadow-primary/20" :disabled="form.processing">
                    <span v-if="form.processing" class="loading loading-spinner loading-sm mr-2"></span>
                    <i class="fas fa-save mr-2"></i> Save Settings
                </button>
            </div>
        </template>

        <div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Routing Settings -->
                <div class="card bg-base-100 shadow-sm border border-base-200">
                    <div class="card-body">
                        <h2 class="card-title text-lg border-b border-base-200 pb-2 mb-4">Routing Setup</h2>
                        
                        <div class="form-control mb-4">
                            <label class="label"><span class="label-text flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" /></svg> Home Page Slug</span></label>
                            <input type="text" v-model="form.home_page_slug" class="input input-bordered" placeholder="e.g. home" />
                            <label class="label"><span class="label-text-alt text-base-content/60">Which page acts as the root URL?</span></label>
                        </div>
                        
                        <div class="form-control mb-4">
                            <label class="label"><span class="label-text flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd" /><path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z" /></svg> Blog Page Slug</span></label>
                            <input type="text" v-model="form.blog_page_slug" class="input input-bordered" placeholder="e.g. blog" />
                            <label class="label"><span class="label-text-alt text-base-content/60">Which page acts as the blog index?</span></label>
                        </div>
                    </div>
                </div>

                <!-- Global Templates -->
                <div class="card bg-base-100 shadow-sm border border-base-200">
                    <div class="card-body">
                        <h2 class="card-title text-lg border-b border-base-200 pb-2 mb-4">Global Layout Templates</h2>
                        
                        <div class="form-control mb-4">
                            <label class="label"><span class="label-text">Default Header</span></label>
                            <select v-model="form.default_header_id" class="select select-bordered w-full">
                                <option value="">None</option>
                                <option v-for="header in headers" :key="header.id" :value="header.id">{{ header.name }}</option>
                            </select>
                        </div>
                        
                        <div class="form-control mb-4">
                            <label class="label"><span class="label-text">Default Footer</span></label>
                            <select v-model="form.default_footer_id" class="select select-bordered w-full">
                                <option value="">None</option>
                                <option v-for="footer in footers" :key="footer.id" :value="footer.id">{{ footer.name }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Brand Personalization -->
                <div class="card bg-base-100 shadow-sm border border-base-200 md:col-span-2">
                    <div class="card-body">
                        <h2 class="card-title text-lg border-b border-base-200 pb-2 mb-4">Brand Personalization</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="form-control">
                                <label class="label"><span class="label-text">Primary Color</span></label>
                                <div class="flex gap-2 items-center">
                                    <input type="color" v-model="form.brand_primary_color" class="input input-bordered p-1 w-16 h-12 rounded cursor-pointer" />
                                    <input type="text" v-model="form.brand_primary_color" class="input input-bordered w-full font-mono text-sm" />
                                </div>
                            </div>
                            
                            <div class="form-control">
                                <label class="label"><span class="label-text">Secondary Color</span></label>
                                <div class="flex gap-2 items-center">
                                    <input type="color" v-model="form.brand_secondary_color" class="input input-bordered p-1 w-16 h-12 rounded cursor-pointer" />
                                    <input type="text" v-model="form.brand_secondary_color" class="input input-bordered w-full font-mono text-sm" />
                                </div>
                            </div>
                            
                            <div class="form-control">
                                <label class="label"><span class="label-text">Default Font Family</span></label>
                                <select v-model="form.brand_font_family" class="select select-bordered w-full font-sans">
                                    <option value="sans-serif">System Sans-Serif</option>
                                    <option value="'Titillium Web', sans-serif">Titillium Web (Custom)</option>
                                    <option value="serif">System Serif</option>
                                    <option value="monospace">Monospace</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
