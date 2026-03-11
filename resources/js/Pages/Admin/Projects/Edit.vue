<script setup>
import { onMounted, computed, watch } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { PhArrowsClockwise, PhArrowSquareOut, PhFingerprint, PhShareNetwork } from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BlockBuilder from '@/Components/BlockBuilder.vue';
import DatePicker from '@/Components/DatePicker.vue';
import { useBlockBuilderStore } from '@/Stores/useBlockBuilderStore';
import { useToastStore } from '@/Stores/useToastStore';

const props = defineProps({
    project: Object,
    templates: [Array, Object]
});

const store = useBlockBuilderStore();
const toast = useToastStore();

const form = useForm({
    title: props.project?.title || { pl: '', en: '' },
    slug: props.project?.slug || '',
    description: props.project?.description || { pl: '', en: '' },
    content: props.project?.content || [],
    desktop_image: props.project?.desktop_image || '',
    mobile_image: props.project?.mobile_image || '',
    url: props.project?.url || '',
    category: props.project?.category || '',
    client: props.project?.client || '',
    order: props.project?.order || 0,
    status: props.project?.status || 'draft',
    published_at: props.project?.published_at ? props.project.published_at.substring(0, 19).replace('T', ' ') : '',
    // SEO Fields
    meta_title: props.project?.meta_title?.pl || (typeof props.project?.meta_title === 'string' ? props.project.meta_title : ''),
    meta_description: props.project?.meta_description?.pl || (typeof props.project?.meta_description === 'string' ? props.project.meta_description : ''),
    canonical_url: props.project?.canonical_url || '',
    og_image: props.project?.og_image?.pl || (typeof props.project?.og_image === 'string' ? props.project.og_image : ''),
    seo_index: props.project?.seo_index ?? true,
    seo_follow: props.project?.seo_follow ?? true,
});

const previewUrl = computed(() => form.slug ? `/projects/${form.slug}` : null);

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

watch(() => form.title.pl, (newTitle) => {
    form.slug = generateSlug(newTitle);
});

onMounted(() => {
    store.init(props.project?.content || []);
});

function submit() {
    form.content = store.blocks;
    if (props.project?.id) {
        form.put(route('admin.projects.update', props.project.id), {
            onSuccess: () => {
                store.isDirty = false;
                toast.success('Projekt został pomyślnie zaktualizowany! 🎉');
            },
            onError: () => {
                toast.error('Wystąpił błąd podczas zapisywania projektu. ❌');
            },
            preserveScroll: true,
            preserveState: true
        });
    } else {
        form.post(route('admin.projects.store'), {
            onSuccess: () => {
                store.isDirty = false;
                toast.success('Projekt został pomyślnie utworzony! ✨');
            },
            onError: () => {
                toast.error('Wystąpił błąd podczas tworzenia projektu. ❌');
            }
        });
    }
}
</script>

<template>
    <Head :title="project ? 'Edit Project' : 'Add Project'" />
    <AdminLayout :full-width="true">
        <BlockBuilder 
            v-model:title="form.title.pl"
            :categories="store.categories"
            :saving="form.processing"
            :templates="templates"
            :preview-url="previewUrl"
            @save="submit"
        >
            <template #info>
                <div class="flex flex-col gap-6">
                    <div class="space-y-4">
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">URL Slug</span></label>
                            <div class="join w-full">
                                <input type="text" v-model="form.slug" class="input input-bordered input-sm join-item focus:border-primary/50 transition-all font-mono text-xs w-full" placeholder="project-slug" />
                                <button @click="form.slug = generateSlug(form.title.pl)" type="button" class="btn btn-sm btn-ghost join-item" title="Regenerate Slug">
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
                                    :placeholder="form.slug ? '' : 'Slug required for URL'"
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
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="planned">Planned</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>

                        <div v-if="form.status === 'planned' || form.status === 'published'" class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Publish Date & Time</span></label>
                            <DatePicker v-model="form.published_at" />
                        </div>
                    </div>

                    <div class="divider opacity-10 my-0"></div>

                    <div class="space-y-4">
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Client</span></label>
                            <input type="text" v-model="form.client" class="input input-bordered input-sm focus:border-primary/50" placeholder="Client Name" />
                        </div>
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Category</span></label>
                            <input type="text" v-model="form.category" class="input input-bordered input-sm focus:border-primary/50" placeholder="e.g. Residential" />
                        </div>
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">External URL</span></label>
                            <input type="text" v-model="form.url" class="input input-bordered input-sm focus:border-primary/50 font-mono text-xs" placeholder="https://..." />
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
                                <span class="font-mono">{{ project?.created_at ? new Date(project.created_at).toLocaleString() : 'New Content' }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-base-100/50 p-2 rounded-lg border border-base-content/5">
                                <span class="opacity-60">Last Edit</span>
                                <span class="font-mono">{{ project?.updated_at ? new Date(project.updated_at).toLocaleString() : 'N/A' }}</span>
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
