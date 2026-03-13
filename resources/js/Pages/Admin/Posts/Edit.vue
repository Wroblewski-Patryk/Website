<template>
    <AdminLayout :full-width="true">
        <BlockBuilder 
            v-model:title="form.title[activeLocale]"
            :categories="store.categories"
            :saving="form.processing"
            :templates="templates"
            :preview-url="previewUrl"
            @save="save"
        >
            <template #info>
                <div class="flex flex-col gap-6">
                    <div class="space-y-4">
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">URL Slug</span></label>
                            <div class="join w-full">
                                <input type="text" v-model="form.slug[activeLocale]" class="input input-bordered input-sm join-item focus:input-primary transition-all flex-1 font-mono text-xs" placeholder="post-slug" />
                                <button @click="form.slug[activeLocale] = generateSlug(form.title[activeLocale])" type="button" class="btn btn-sm btn-ghost join-item" title="Regenerate Slug">
                                    <PhArrowsClockwise weight="bold" class="w-3 h-3" />
                                </button>
                            </div>
                        </div>

                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Generated URL</span></label>
                            <div class="join w-full">
                                <input
                                    type="text"
                                    :value="previewUrl || ''"
                                    readonly
                                    class="input input-bordered input-sm join-item w-full font-mono text-[10px]"
                                    :placeholder="form.slug?.[activeLocale] ? '' : 'Slug required for URL'"
                                />
                                <a
                                    v-if="previewUrl"
                                    :href="previewUrl"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="btn btn-sm btn-ghost join-item"
                                    title="Open Preview URL"
                                >
                                    <PhArrowSquareOut weight="bold" class="w-3 h-3" />
                                </a>
                                <button v-else type="button" class="btn btn-sm btn-ghost join-item" disabled title="URL unavailable">
                                    <PhArrowSquareOut weight="bold" class="w-3 h-3 opacity-40" />
                                </button>
                            </div>
                        </div>

                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Status</span></label>
                            <select v-model="form.status" class="select select-bordered select-sm focus:select-primary transition-all w-full">
                                <option value="draft">Draft (Private)</option>
                                <option value="published">Published (Public)</option>
                                <option value="planned">Planned (Scheduled)</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>

                        <div v-if="form.status === 'planned' || form.status === 'published'" class="form-control animate-in slide-in-from-top-2">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Publish Date & Time</span></label>
                            <DatePicker v-model="form.published_at" />
                        </div>

                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Excerpt / Summary</span></label>
                            <textarea v-model="form.excerpt[activeLocale]" class="textarea textarea-bordered textarea-sm focus:border-primary/50 transition-all h-20 font-sans text-xs" placeholder="Brief summary of the post..."></textarea>
                        </div>
                    </div>

                    <div class="divider opacity-10 my-0"></div>

                    <div class="space-y-4">
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Featured Image URL</span></label>
                            <div class="flex flex-col gap-2">
                                <input type="text" v-model="form.featured_image[activeLocale]" class="input input-bordered input-sm focus:input-primary transition-all w-full" placeholder="Image URL" />
                                <div v-if="form.featured_image[activeLocale]" class="rounded-lg overflow-hidden border border-base-content/10 aspect-video bg-base-200 shadow-inner group">
                                    <img :src="form.featured_image[activeLocale]" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="divider opacity-10 my-0"></div>

                    <div class="space-y-3 bg-base-200/30 p-4 rounded-xl border border-base-content/10">
                        <div class="flex items-center justify-between text-[10px] uppercase tracking-wider opacity-60 font-bold px-1">
                            <span>Metadata</span>
                            <PhFingerprint weight="bold" class="w-3 h-3 text-primary" />
                        </div>
                        <div class="flex flex-col gap-2 text-[11px]">
                            <div class="flex justify-between items-center bg-base-100/50 p-2 rounded-lg border border-base-content/5">
                                <span class="opacity-60">Created</span>
                                <span class="font-mono">{{ post?.created_at ? new Date(post.created_at).toLocaleString() : 'New Content' }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-base-100/50 p-2 rounded-lg border border-base-content/5">
                                <span class="opacity-60">Last Edit</span>
                                <span class="font-mono">{{ post?.updated_at ? new Date(post.updated_at).toLocaleString() : 'N/A' }}</span>
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
                            <input type="text" v-model="form.meta_title[activeLocale]" class="input input-bordered input-sm focus:input-primary transition-all" placeholder="SEO Title" />
                        </div>
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60 text-primary">Meta Description</span></label>
                            <textarea v-model="form.meta_description[activeLocale]" class="textarea textarea-bordered textarea-sm focus:textarea-primary transition-all h-24" placeholder="SEO Description"></textarea>
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
                            <input type="text" v-model="form.og_image[activeLocale]" class="input input-bordered input-sm focus:input-primary transition-all font-mono text-[10px]" placeholder="Image URL for social media" />
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
                <div v-if="!post.revisions || post.revisions.length === 0" class="text-center py-10 opacity-30 text-xs italic">
                    <PhClockCounterClockwise weight="thin" class="w-10 h-10 mx-auto mb-3 opacity-20" />
                    No history yet. Revisions are created when you save changes.
                </div>
                <div v-else class="space-y-3">
                    <div v-for="rev in post.revisions" :key="rev.id" class="p-3 bg-base-200/50 rounded-xl border border-base-content/5 flex flex-col gap-2 hover:border-primary/30 transition-all group">
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
    PhFingerprint,
    PhShareNetwork,
    PhArrowsClockwise,
    PhClockCounterClockwise,
    PhArrowSquareOut
} from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BlockBuilder from '@/Components/BlockBuilder.vue';
import DatePicker from '@/Components/DatePicker.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useBlockBuilderStore } from '@/Stores/useBlockBuilderStore';
import { useToastStore } from '@/Stores/useToastStore';
import { computed, onMounted, watch } from 'vue';

const pageProps = usePage().props;
const activeLocale = computed(() => store.editingLocale || pageProps.locale || 'pl');

const props = defineProps({
    post: Object,
    templates: [Array, Object]
});

const store = useBlockBuilderStore();
const toast = useToastStore();

const form = useForm({
    title: props.post?.title || { pl: '', en: '' },
    slug: props.post?.slug || { pl: '', en: '' },
    excerpt: props.post?.excerpt || { pl: '', en: '' },
    content: props.post?.content || [],
    status: props.post?.status || 'draft',
    published_at: props.post?.published_at ? props.post.published_at.substring(0, 19).replace('T', ' ') : '',
    featured_image: props.post?.featured_image || { pl: '', en: '' },
    // SEO Fields
    meta_title: props.post?.meta_title || { pl: '', en: '' },
    meta_description: props.post?.meta_description || { pl: '', en: '' },
    canonical_url: props.post?.canonical_url || '',
    og_image: props.post?.og_image || { pl: '', en: '' },
    seo_index: props.post?.seo_index ?? true,
    seo_follow: props.post?.seo_follow ?? true,
});

const previewUrl = computed(() => form.slug?.[activeLocale.value] ? `/blog/${form.slug[activeLocale.value]}` : null);

onMounted(() => {
    store.init(props.post?.content || []);
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
watch(() => form.title[activeLocale.value], (newTitle) => {
    form.slug[activeLocale.value] = generateSlug(newTitle);
});

const restoreRevision = (rev) => {
    if (confirm('Are you sure you want to restore this version? Current unsaved changes will be lost.')) {
        store.init(rev.content);
        store.isDirty = true;
    }
};

const save = () => {
    form.content = store.blocks;
    if (props.post?.id) {
        form.put(route('admin.posts.update', props.post.id), {
            onSuccess: () => {
                store.isDirty = false;
                toast.success('Post został pomyślnie zaktualizowany! 🎉');
            },
            onError: (errors) => {
                console.error(errors);
                toast.error('Wystąpił błąd podczas zapisywania postu. ❌');
            },
            preserveScroll: true,
            preserveState: true
        });
    } else {
        form.post(route('admin.posts.store'), {
            onSuccess: () => {
                store.isDirty = false;
                toast.success('Post został pomyślnie utworzony! ✨');
            },
            onError: (errors) => {
                console.error(errors);
                toast.error('Wystąpił błąd podczas tworzenia postu. ❌');
            }
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
