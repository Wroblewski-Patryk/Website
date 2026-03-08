<template>
    <AdminLayout :full-width="true">
        <BlockBuilder 
            :categories="store.categories"
            :saving="form.processing"
            :templates="templates"
            @save="save"
        >
            <template #info>
                <div class="flex flex-col gap-6">
                    <!-- Actions Section -->
                    <div class="flex items-center gap-2 p-1 bg-base-200/50 rounded-box border border-base-content/5">
                        <a :href="'/blog/' + (form.slug.pl || '')" target="_blank" class="btn btn-ghost btn-sm flex-1 gap-2">
                            <PhEye weight="bold" class="w-4 h-4" />
                            Preview
                        </a>
                        <button @click="save" class="btn btn-primary btn-sm flex-[2] gap-2 shadow-lg shadow-primary/20" :disabled="form.processing">
                            <PhFloppyDisk weight="fill" class="w-4 h-4" />
                            Save Post
                        </button>
                    </div>

                    <!-- Post Identity (Out of container) -->
                    <div class="space-y-4">
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Post Title</span></label>
                            <input type="text" v-model="form.title.pl" class="input input-bordered input-sm focus:input-primary transition-all" placeholder="Title (PL)" />
                        </div>
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Slug</span></label>
                            <div class="flex items-center gap-2">
                                <input type="text" v-model="form.slug.pl" class="input input-bordered input-sm focus:input-primary transition-all flex-1 font-mono text-xs" placeholder="post-slug" />
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
                    </div>

                    <!-- Metadata Section (Between status/date and media) -->
                    <div class="space-y-3 bg-base-200/20 p-3 rounded-xl border border-base-content/5">
                        <div class="flex items-center justify-between text-[10px] uppercase tracking-wider opacity-50 font-bold px-1">
                            <span>Metadata</span>
                            <PhFingerprint weight="bold" class="w-3 h-3 text-primary" />
                        </div>
                        <div class="flex flex-col gap-2 text-[11px]">
                            <div class="flex justify-between items-center bg-base-100/30 p-2 rounded-lg">
                                <span class="opacity-50">Created:</span>
                                <span class="font-mono">{{ post?.created_at ? new Date(post.created_at).toLocaleString() : 'New Content' }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-base-100/30 p-2 rounded-lg">
                                <span class="opacity-50">Last Edit:</span>
                                <span class="font-mono">{{ post?.updated_at ? new Date(post.updated_at).toLocaleString() : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Media Section -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 px-1">
                            <div class="h-[1px] flex-1 bg-base-content/10"></div>
                            <span class="text-[10px] font-bold uppercase tracking-widest opacity-30">Featured Media</span>
                            <div class="h-[1px] flex-1 bg-base-content/10"></div>
                        </div>
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Featured Image URL</span></label>
                            <div class="flex flex-col gap-2">
                                <input type="text" v-model="form.featured_image.pl" class="input input-bordered input-sm focus:input-primary transition-all w-full" placeholder="Image URL" />
                                <div v-if="form.featured_image.pl" class="mt-2 rounded-lg overflow-hidden border border-base-content/10 aspect-video bg-base-200 shadow-inner group">
                                    <img :src="form.featured_image.pl" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                </div>
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
                        </div>
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60 text-primary">Meta Description</span></label>
                            <textarea v-model="form.meta_description" class="textarea textarea-bordered textarea-sm focus:textarea-primary transition-all h-24" placeholder="SEO Description"></textarea>
                        </div>
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60 transition-colors">Canonical URL</span></label>
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
    post: Object,
    templates: Array
});

const store = useBlockBuilderStore();

const form = useForm({
    title: props.post?.title || { pl: '', en: '' },
    slug: props.post?.slug || { pl: '', en: '' },
    excerpt: props.post?.excerpt || { pl: '', en: '' },
    content: props.post?.content || [],
    status: props.post?.status || 'draft',
    published_at: props.post?.published_at ? props.post.published_at.substring(0, 19).replace('T', ' ') : '',
    featured_image: props.post?.featured_image || { pl: '', en: '' },
    // SEO Fields
    meta_title: props.post?.meta_title || '',
    meta_description: props.post?.meta_description || '',
    canonical_url: props.post?.canonical_url || '',
    og_image: props.post?.og_image || '',
    seo_index: props.post?.seo_index ?? true,
    seo_follow: props.post?.seo_follow ?? true,
});

onMounted(() => {
    store.init(props.post?.content || []);
});

const generateSlug = (text) => {
    if (!text) return '';
    return text.toString().toLowerCase()
        .normalize('NFD') // Rozbicie znaków diakrytycznych
        .replace(/[\u0300-\u036f]/g, '') // Usunięcie ogonków
        .replace(/[^a-z0-9 -]/g, '') // Usunięcie znaków specjalnych
        .replace(/\s+/g, '-') // Spacje na myślniki
        .replace(/-+/g, '-') // Podwójne myślniki na pojedyncze
        .trim()
        .replace(/^-+/, '') // Usuń myślniki z początku
        .replace(/-+$/, ''); // Usuń myślniki z końca
};

// Auto-slug generation - always update on title change
watch(() => form.title.pl, (newTitle) => {
    form.slug.pl = generateSlug(newTitle);
});

const save = () => {
    form.content = store.blocks;
    if (props.post?.id) {
        form.put(route('admin.posts.update', props.post.id), {
            onSuccess: () => store.isDirty = false
        });
    } else {
        form.post(route('admin.posts.store'), {
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

.ghost-block {
    opacity: 0.5;
    background: #c8ebfb;
    border: 2px dashed #000;
}
</style>
