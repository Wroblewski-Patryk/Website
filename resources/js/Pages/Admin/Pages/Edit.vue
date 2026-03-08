<template>
    <AdminLayout :full-width="true">
        <BlockBuilder 
            :categories="store.categories"
            :saving="form.processing"
            :templates="templates"
            @save="save"
        >
            <!-- Info Tab -->
            <template #info>
                <div class="flex flex-col gap-6">
                    <!-- Actions Section -->
                    <div class="flex items-center gap-2 p-1 bg-base-200/50 rounded-box border border-base-content/5">
                        <a :href="'/' + (form.slug.pl || '')" target="_blank" class="btn btn-ghost btn-sm flex-1 gap-2">
                            <PhEye weight="bold" class="w-4 h-4" />
                            Preview
                        </a>
                        <button @click="save" class="btn btn-primary btn-sm flex-[2] gap-2 shadow-lg shadow-primary/20" :disabled="form.processing">
                            <PhFloppyDisk weight="fill" class="w-4 h-4" />
                            Save Page
                        </button>
                    </div>

                    <!-- Page Identity (No container, like Page Title) -->
                    <div class="space-y-4">
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Page Title</span></label>
                            <input type="text" v-model="form.title.pl" class="input input-bordered input-sm focus:input-primary transition-all" placeholder="Title (PL)" />
                        </div>
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Slug</span></label>
                            <div class="flex items-center gap-2">
                                <input type="text" v-model="form.slug.pl" class="input input-bordered input-sm focus:input-primary transition-all flex-1 font-mono text-xs" placeholder="slug-pl" />
                                <button @click="form.slug.pl = generateSlug(form.title.pl)" type="button" class="btn btn-square btn-sm btn-ghost opacity-40 hover:opacity-100" title="Regenerate Slug"><PhArrowsClockwise weight="bold" class="w-4 h-4"/></button>
                            </div>
                        </div>
                    </div>

                    <!-- Status Selection (Out of container) -->
                    <div class="form-control">
                        <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Status</span></label>
                        <select v-model="form.status" class="select select-bordered select-sm focus:select-primary transition-all w-full">
                            <option value="draft">Draft (Private)</option>
                            <option value="published">Published (Public)</option>
                            <option value="planned">Planned (Scheduled)</option>
                        </select>
                    </div>

                    <!-- Publish Date & Time (Out of container) -->
                    <div v-if="form.status === 'planned'" class="form-control animate-in slide-in-from-top-2">
                        <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Publish Date & Time</span></label>
                        <DatePicker v-model="form.published_at" />
                        <p class="text-[9px] mt-2 opacity-40 italic px-1">Scheduled pages will appear on the site at the selected time automatically.</p>
                    </div>

                    <!-- Metadata Section (Between status/date and templates) -->
                    <div class="space-y-3 bg-base-200/20 p-3 rounded-xl border border-base-content/5">
                        <div class="flex items-center justify-between text-[10px] uppercase tracking-wider opacity-50 font-bold px-1">
                            <span>Metadata</span>
                            <PhFingerprint weight="bold" class="w-3 h-3 text-primary" />
                        </div>
                        <div class="flex flex-col gap-2 text-[11px]">
                            <div class="flex justify-between items-center bg-base-100/30 p-2 rounded-lg">
                                <span class="opacity-50">Created:</span>
                                <span class="font-mono">{{ page?.created_at ? new Date(page.created_at).toLocaleString() : 'New Content' }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-base-100/30 p-2 rounded-lg">
                                <span class="opacity-50">Last Edit:</span>
                                <span class="font-mono">{{ page?.updated_at ? new Date(page.updated_at).toLocaleString() : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Overrides Section (Bottom) -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 px-1">
                            <div class="h-[1px] flex-1 bg-base-content/10"></div>
                            <span class="text-[10px] font-bold uppercase tracking-widest opacity-30">Template Settings</span>
                            <div class="h-[1px] flex-1 bg-base-content/10"></div>
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <div class="form-control">
                                <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Page Layout (Master Template)</span></label>
                                <select v-model="form.template_id" class="select select-bordered select-sm text-xs w-full">
                                    <option :value="null">Default Page Layout</option>
                                    <option v-for="t in templates.page || []" :key="t.id" :value="t.id">{{ t.name }}</option>
                                </select>
                            </div>

                            <div class="form-control">
                                <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Layout Type (Override)</span></label>
                                <select v-model="form.template" class="select select-bordered select-sm text-xs">
                                    <option value="default">Default Template</option>
                                    <option value="full-width">Full Width Canvas</option>
                                    <option value="landing">Landing Page (No UI)</option>
                                </select>
                            </div>

                            <div class="form-control">
                                <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Header Section</span></label>
                                <select v-model="form.header_override_id" class="select select-bordered select-sm text-xs w-full">
                                    <option :value="null">System Default Header</option>
                                    <option v-for="t in templates.header || []" :key="t.id" :value="t.id">{{ t.name }}</option>
                                </select>
                            </div>

                            <div class="form-control">
                                <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Sidebar Section</span></label>
                                <select v-model="form.sidebar_override_id" class="select select-bordered select-sm text-xs w-full">
                                    <option :value="null">System Default Sidebar</option>
                                    <option v-for="t in templates.sidebar || []" :key="t.id" :value="t.id">{{ t.name }}</option>
                                </select>
                            </div>

                            <div class="form-control">
                                <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Footer Section</span></label>
                                <select v-model="form.footer_override_id" class="select select-bordered select-sm text-xs w-full">
                                    <option :value="null">System Default Footer</option>
                                    <option v-for="t in templates.footer || []" :key="t.id" :value="t.id">{{ t.name }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- SEO Tab -->
            <template #seo>
                <div class="flex flex-col gap-6">
                    <div class="space-y-4">
                         <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60 text-primary">Meta Title</span></label>
                            <input type="text" v-model="form.meta_title" class="input input-bordered input-sm focus:input-primary transition-all" placeholder="SEO Title" />
                            <label class="label"><span class="label-text-alt opacity-40">{{ form.meta_title?.length || 0 }}/60 chars</span></label>
                        </div>
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60 text-primary">Meta Description</span></label>
                            <textarea v-model="form.meta_description" class="textarea textarea-bordered textarea-sm focus:textarea-primary transition-all h-24" placeholder="SEO Description"></textarea>
                            <label class="label"><span class="label-text-alt opacity-40">{{ form.meta_description?.length || 0 }}/160 chars</span></label>
                        </div>
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Canonical URL</span></label>
                            <input type="text" v-model="form.canonical_url" class="input input-bordered input-sm focus:input-primary transition-all font-mono text-[10px]" placeholder="https://..." />
                        </div>
                    </div>

                    <div class="divider opacity-5 my-0"></div>

                    <!-- Social Sharing (OG) -->
                    <div class="space-y-4">
                        <label class="text-[10px] font-bold uppercase tracking-widest opacity-30 flex items-center gap-2">
                            <PhShareNetwork weight="bold" class="w-3 h-3 text-secondary" /> Social Sharing (OG)
                        </label>
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">OG Image URL</span></label>
                            <input type="text" v-model="form.og_image" class="input input-bordered input-sm focus:input-primary transition-all font-mono text-[10px]" placeholder="Image URL for social media" />
                        </div>
                    </div>

                    <!-- Robots Settings -->
                    <div class="space-y-4 bg-base-200/30 p-4 rounded-2xl border border-base-content/5">
                        <label class="text-[10px] font-bold uppercase tracking-widest opacity-30">Search Engine Visibility</label>
                        <div class="flex flex-col gap-3">
                            <label class="flex items-center justify-between cursor-pointer group">
                                <span class="text-xs group-hover:text-primary transition-colors">Index Page</span>
                                <input type="checkbox" v-model="form.seo_index" class="toggle toggle-primary toggle-sm" />
                            </label>
                            <label class="flex items-center justify-between cursor-pointer group">
                                <span class="text-xs group-hover:text-primary transition-colors">Follow Links</span>
                                <input type="checkbox" v-model="form.seo_follow" class="toggle toggle-primary toggle-sm" />
                            </label>
                        </div>
                    </div>
                </div>
            </template>

            <!-- History Tab -->
            <template #history>
                <div v-if="!page.revisions || page.revisions.length === 0" class="text-center py-10 opacity-30 text-xs italic">
                    <PhClockCounterClockwise weight="thin" class="w-10 h-10 mx-auto mb-3 opacity-20" />
                    No history yet. Revisions are created when you save changes.
                </div>
                <div v-else class="space-y-3">
                    <div v-for="rev in page.revisions" :key="rev.id" class="p-3 bg-base-200/50 rounded-xl border border-base-content/5 flex flex-col gap-2 hover:border-primary/30 transition-all group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold opacity-70">{{ new Date(rev.created_at).toLocaleString() }}</span>
                            <button @click="restoreRevision(rev)" class="btn btn-xs btn-outline btn-primary opacity-0 group-hover:opacity-100 scale-90 transition-all">Restore</button>
                        </div>
                        <span class="text-[10px] opacity-40">{{ rev.content?.length || 0 }} blocks total</span>
                    </div>
                </div>
            </template>
        </BlockBuilder>
    </AdminLayout>
</template>

<script setup>
import { 
    PhEye, 
    PhFloppyDisk, 
    PhFingerprint, 
    PhShareNetwork,
    PhArrowsClockwise,
    PhClockCounterClockwise
} from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BlockBuilder from '@/Components/BlockBuilder.vue';
import DatePicker from '@/Components/DatePicker.vue';
import { useForm } from '@inertiajs/vue3';
import { useBlockBuilderStore } from '@/Stores/useBlockBuilderStore';
import { onMounted, watch } from 'vue';

const props = defineProps({
    page: Object,
    templates: Object,
    menus: Array
});

const store = useBlockBuilderStore();

const form = useForm({
    title: props.page?.title || { pl: '', en: '' },
    slug: props.page?.slug || { pl: '', en: '' },
    content: props.page?.content || [],
    status: props.page?.status || 'draft',
    published_at: props.page?.published_at ? props.page.published_at.substring(0, 19).replace('T', ' ') : '',
    header_override_id: props.page?.header_override_id || null,
    footer_override_id: props.page?.footer_override_id || null,
    sidebar_override_id: props.page?.sidebar_override_id || null,
    template_id: props.page?.template_id || null,
    template: props.page?.template || 'default',
    // SEO Fields
    meta_title: props.page?.meta_title || '',
    meta_description: props.page?.meta_description || '',
    canonical_url: props.page?.canonical_url || '',
    og_image: props.page?.og_image || '',
    seo_index: props.page?.seo_index ?? true,
    seo_follow: props.page?.seo_follow ?? true,
});

onMounted(() => {
    store.init(props.page?.content || []);
});

const generateSlug = (text) => {
    if (!text) return '';
    return text.toString().toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9 -]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim()
        .replace(/^-+/, '')
        .replace(/-+$/, '');
};

// Auto-slug generation - always update on title change
watch(() => form.title.pl, (newTitle) => {
    form.slug.pl = generateSlug(newTitle);
});

const restoreRevision = (rev) => {
    if (confirm('Are you sure you want to restore this version? Current unsaved changes will be lost.')) {
        store.init(rev.content);
        store.isDirty = true;
    }
};

const save = () => {
    form.content = store.blocks;
    if (props.page?.id) {
        form.put(route('admin.pages.update', props.page.id), {
            onSuccess: () => store.isDirty = false
        });
    } else {
        form.post(route('admin.pages.store'), {
            onSuccess: () => store.isDirty = false
        });
    }
};
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.1);
}
</style>
