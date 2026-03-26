<template>
    <AdminLayout :full-width="true">
        <BlockBuilder 
            v-model:title="form.title"
            :module-label="template?.id ? t('admin.templates.edit_template', 'Edit Template') : t('admin.templates.add_template', 'Add New Template')"
            :categories="store.categories"
            :module-categories="scopedModuleCategories"
            :saving="form.processing"
            :templates="templates"
            :preview-url="previewUrl"
            @save="save"
            @autosave="save"
        >
            <template #info>
                <div class="flex flex-col gap-6">
                    <div class="space-y-4">
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">{{ t('admin.common.name', 'Name') }}</span></label>
                            <input
                                type="text"
                                v-model="form.title[activeLocale]"
                                class="input input-bordered input-sm focus:border-primary/50 transition-all"
                                :placeholder="t('admin.templates.title_field', 'Template Name')"
                            />
                        </div>

                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">{{ t('admin.common.url_slug', 'URL Slug') }}</span></label>
                            <div class="join w-full">
                                <input type="text" v-model="templateSlug" class="input input-bordered input-sm join-item focus:border-primary/50 transition-all font-mono text-xs w-full" :placeholder="t('admin.templates.slug_placeholder', 'template-slug')" />
                                <button @click="templateSlug = generateSlug(form.title[activeLocale])" type="button" class="btn btn-sm btn-ghost join-item" :title="t('admin.common.regenerate_slug', 'Regenerate Slug')">
                                    <PhArrowsClockwise weight="bold" class="w-3 h-3" />
                                </button>
                            </div>
                        </div>

                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">{{ t('admin.common.generated_url', 'Generated URL') }}</span></label>
                            <div class="join w-full">
                                <input
                                    type="text"
                                    :value="previewUrl || ''"
                                    readonly
                                    class="input input-bordered input-sm join-item w-full font-mono text-[10px]"
                                    :placeholder="t('admin.templates.preview_unavailable', 'Preview URL not available for templates')"
                                />
                                <button type="button" class="btn btn-sm btn-ghost join-item" disabled :title="t('admin.common.url_unavailable', 'URL unavailable')">
                                    <PhArrowSquareOut weight="bold" class="w-3 h-3 opacity-40" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="divider opacity-10 my-0"></div>

                    <div class="space-y-4">
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">{{ t('admin.templates.type', 'Template Type') }}</span></label>
                            <select v-model="form.type" class="select select-bordered select-sm focus:select-primary w-full">
                                <option value="header">{{ t('admin.templates.type_header', 'Header') }}</option>
                                <option value="footer">{{ t('admin.templates.type_footer', 'Footer') }}</option>
                                <option value="sidebar">{{ t('admin.templates.type_sidebar', 'Sidebar') }}</option>
                                <option value="page">{{ t('admin.templates.type_page', 'Page Template') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="divider opacity-10 my-0"></div>

                    <div class="space-y-3 bg-base-200/30 p-4 rounded-xl border border-base-content/10">
                        <div class="flex items-center justify-between text-[10px] uppercase tracking-wider opacity-60 font-bold px-1">
                            <span>{{ t('admin.common.metadata', 'Metadata') }}</span>
                            <PhFingerprint weight="bold" class="w-3 h-3 text-primary" />
                        </div>
                        <div class="flex flex-col gap-2 text-[11px]">
                            <div class="flex justify-between items-center bg-base-100/50 p-2 rounded-lg border border-base-content/5">
                                <span class="opacity-60">{{ t('admin.common.created', 'Created') }}</span>
                                <span class="font-mono">{{ template?.created_at ? formatDateTime(template.created_at) : t('admin.common.new_content', 'New Content') }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-base-100/50 p-2 rounded-lg border border-base-content/5">
                                <span class="opacity-60">{{ t('admin.common.edited', 'Last Edit') }}</span>
                                <span class="font-mono">{{ template?.updated_at ? formatDateTime(template.updated_at) : 'N/A' }}</span>
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
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60 text-primary">{{ t('admin.common.meta_title', 'Meta Title') }}</span></label>
                            <input type="text" v-model="form.meta_title[activeLocale]" class="input input-bordered input-sm focus:input-primary transition-all" :placeholder="t('admin.common.meta_title_placeholder', 'SEO Title')" />
                        </div>
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60 text-primary">{{ t('admin.common.meta_description', 'Meta Description') }}</span></label>
                            <textarea v-model="form.meta_description[activeLocale]" class="textarea textarea-bordered textarea-sm focus:textarea-primary transition-all h-24" :placeholder="t('admin.common.meta_description_placeholder', 'SEO Description')"></textarea>
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
                        <label class="text-[10px] font-bold uppercase tracking-widest opacity-30">{{ t('admin.common.search_engine_visibility', 'Search Engine Visibility') }}</label>
                        <div class="flex flex-col gap-3">
                            <label class="flex items-center justify-between cursor-pointer group">
                                <span class="text-xs group-hover:text-primary transition-colors">{{ t('admin.common.index_page', 'Index Page') }}</span>
                                <input type="checkbox" v-model="form.seo_index" class="toggle toggle-primary toggle-sm" />
                            </label>
                            <label class="flex items-center justify-between cursor-pointer group">
                                <span class="text-xs group-hover:text-primary transition-colors">{{ t('admin.common.follow_links', 'Follow Links') }}</span>
                                <input type="checkbox" v-model="form.seo_follow" class="toggle toggle-primary toggle-sm" />
                            </label>
                        </div>
                    </div>
                </div>
            </template>

            <!-- History Tab -->
            <template #history>
                <div v-if="!template.revisions || template.revisions.length === 0" class="text-center py-10 opacity-30 text-xs italic">
                    <PhClockCounterClockwise weight="thin" class="w-10 h-10 mx-auto mb-3 opacity-20" />
                    No history yet. Revisions are created when you save changes.
                </div>
                <div v-else class="space-y-3">
                    <div v-for="rev in template.revisions" :key="rev.id" class="p-3 bg-base-200/50 rounded-xl border border-base-content/5 flex flex-col gap-2 hover:border-primary/30 transition-all group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold opacity-70">{{ formatDateTime(rev.created_at) }}</span>
                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 scale-90 transition-all">
                                <button @click="openRevisionDiff(rev)" class="btn btn-xs btn-outline">Compare</button>
                                <button @click="restoreRevision(rev)" class="btn btn-xs btn-outline btn-primary">Restore</button>
                            </div>
                        </div>
                        <span class="text-[10px] opacity-40">{{ rev.content?.length || 0 }} blocks total</span>
                    </div>
                </div>
            </template>
        </BlockBuilder>

        <div v-if="showRevisionDiffModal" class="fixed inset-0 z-[100] bg-base-content/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="w-full max-w-3xl bg-base-100 rounded-2xl border border-base-300 shadow-2xl">
                <div class="p-5 border-b border-base-300 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest opacity-80">Revision Diff</h3>
                        <p class="text-xs opacity-50 mt-1">
                            Comparing revision from {{ selectedRevision ? formatDateTime(selectedRevision.created_at) : '-' }} with current unsaved canvas state.
                        </p>
                    </div>
                    <button class="btn btn-sm btn-ghost" @click="closeRevisionDiff">Close</button>
                </div>

                <div class="p-5 space-y-5">
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-xs">
                        <div class="rounded-xl border border-base-300 p-3 bg-base-200/40">
                            <p class="opacity-50">Previous</p>
                            <p class="font-black text-lg">{{ revisionDiff.counts.previous }}</p>
                        </div>
                        <div class="rounded-xl border border-base-300 p-3 bg-base-200/40">
                            <p class="opacity-50">Current</p>
                            <p class="font-black text-lg">{{ revisionDiff.counts.current }}</p>
                        </div>
                        <div class="rounded-xl border border-success/30 p-3 bg-success/10">
                            <p class="opacity-60">Added</p>
                            <p class="font-black text-lg">{{ revisionDiff.counts.added }}</p>
                        </div>
                        <div class="rounded-xl border border-error/30 p-3 bg-error/10">
                            <p class="opacity-60">Removed</p>
                            <p class="font-black text-lg">{{ revisionDiff.counts.removed }}</p>
                        </div>
                        <div class="rounded-xl border border-warning/30 p-3 bg-warning/10">
                            <p class="opacity-60">Changed</p>
                            <p class="font-black text-lg">{{ revisionDiff.counts.changed }}</p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-3 gap-4 text-xs">
                        <div>
                            <h4 class="font-black uppercase tracking-widest opacity-60 mb-2">Added Blocks</h4>
                            <ul class="space-y-1 max-h-40 overflow-auto">
                                <li v-for="row in revisionDiff.added.slice(0, 30)" :key="`added-${row.id}`" class="font-mono opacity-80">
                                    + {{ row.type }} ({{ row.id }})
                                </li>
                                <li v-if="revisionDiff.added.length === 0" class="opacity-30 italic">No additions</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-black uppercase tracking-widest opacity-60 mb-2">Removed Blocks</h4>
                            <ul class="space-y-1 max-h-40 overflow-auto">
                                <li v-for="row in revisionDiff.removed.slice(0, 30)" :key="`removed-${row.id}`" class="font-mono opacity-80">
                                    - {{ row.type }} ({{ row.id }})
                                </li>
                                <li v-if="revisionDiff.removed.length === 0" class="opacity-30 italic">No removals</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-black uppercase tracking-widest opacity-60 mb-2">Changed Blocks</h4>
                            <ul class="space-y-1 max-h-40 overflow-auto">
                                <li v-for="row in revisionDiff.changed.slice(0, 30)" :key="`changed-${row.id}`" class="font-mono opacity-80">
                                    ~ {{ row.type }} ({{ row.id }})
                                </li>
                                <li v-if="revisionDiff.changed.length === 0" class="opacity-30 italic">No content changes</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import {
    PhArrowsClockwise,
    PhArrowSquareOut,
    PhFingerprint,
    PhClockCounterClockwise,
    PhShareNetwork
} from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BlockBuilder from '@/features/admin/block-builder/components/BlockBuilderMain.vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import { useBlockBuilderStore } from '@/features/admin/block-builder/store/useBlockBuilderStore';
import { useToastStore } from '@/Stores/useToastStore';
import { useTranslations } from '@/Composables/useTranslations';
import { useFormatter } from '@/Composables/useFormatter';
import { compareRevisionContent } from '@/features/admin/shared/utils/revisionDiff';
import { computed, onMounted, ref, watch } from 'vue';

const { t } = useTranslations();
const { formatDateTime } = useFormatter();

const pageProps = usePage().props;
const fallbackLocale = computed(() => {
    return pageProps.default_locale
        || pageProps.locale
        || pageProps.languages?.find?.(lang => lang?.is_default)?.code
        || pageProps.languages?.[0]?.code
        || 'en';
});
const activeLocale = computed(() => store.editingLocale || pageProps.locale || fallbackLocale.value);

const props = defineProps({
    template: Object,
    moduleCategories: {
        type: Array,
        default: () => []
    },
    templates: [Array, Object],
});

const moduleCategories = computed(() => props.moduleCategories || []);
const scopedModuleCategories = computed(() => {
    const templateType = String(form.type || 'page');

    return moduleCategories.value
        .map((category) => {
            const blocks = Array.isArray(category.blocks)
                ? category.blocks.filter((block) => {
                    if (!Array.isArray(block?.template_types) || block.template_types.length === 0) {
                        return true;
                    }

                    return block.template_types.includes(templateType);
                })
                : [];

            return {
                ...category,
                blocks,
            };
        })
        .filter((category) => Array.isArray(category.blocks) && category.blocks.length > 0);
});

const store = useBlockBuilderStore();
const toast = useToastStore();

const isObject = (val) => val && typeof val === 'object' && !Array.isArray(val);

const getEmptyLocales = () => {
    const locales = (pageProps.languages || []).map(l => l.code);
    return locales.reduce((acc, code) => ({ ...acc, [code]: '' }), {});
};

const form = useForm({
    title: isObject(props.template?.title) ? props.template.title : getEmptyLocales(),
    type: props.template?.type || 'header',
    content: props.template?.content || [],
    optimistic_lock: props.template?.updated_at || null,
    is_default: props.template?.is_default ?? false,
    // SEO Fields
    meta_title: isObject(props.template?.meta_title) ? props.template.meta_title : getEmptyLocales(),
    meta_description: isObject(props.template?.meta_description) ? props.template.meta_description : getEmptyLocales(),
    canonical_url: props.template?.canonical_url || '',
    og_image: isObject(props.template?.og_image) ? props.template.og_image : getEmptyLocales(),
    seo_index: props.template?.seo_index ?? true,
    seo_follow: props.template?.seo_follow ?? true,
});

const previewUrl = computed(() => null);

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

const templateSlug = ref(generateSlug(form.title[activeLocale.value]));
const showRevisionDiffModal = ref(false);
const selectedRevision = ref(null);

const revisionDiff = computed(() => {
    if (!selectedRevision.value) {
        return compareRevisionContent([], store.blocks || []);
    }

    return compareRevisionContent(selectedRevision.value.content || [], store.blocks || []);
});

watch(() => form.title[activeLocale.value], (newTitle) => {
    if (newTitle && (!props.template?.id || !templateSlug.value)) {
        templateSlug.value = generateSlug(newTitle);
    }
});

onMounted(() => {
    store.init(props.template?.content || getEmptyLocales());
});

const restoreRevision = (rev) => {
    if (confirm(t('admin.common.restore_confirm', 'Are you sure you want to restore this version? Current unsaved changes will be lost.'))) {
        router.post(route('admin.templates.revisions.restore', { template: props.template.id, revision: rev.id }), {}, {
            onSuccess: () => {
                toast.success('Revision restored successfully.');
                store.isDirty = false;
            },
            onError: () => {
                toast.error('Could not restore revision.');
            },
        });
    }
};

const openRevisionDiff = (rev) => {
    selectedRevision.value = rev;
    showRevisionDiffModal.value = true;
};

const closeRevisionDiff = () => {
    showRevisionDiffModal.value = false;
    selectedRevision.value = null;
};

const save = ({ autosave = false } = {}) => {
    form.content = store.blocks;

    if (props.template?.id) {
        form.put(route('admin.templates.update', props.template.id), {
            onSuccess: () => {
                store.isDirty = false;
                store.markSavedSnapshot();
                form.optimistic_lock = new Date().toISOString();
                if (!autosave) {
                    toast.success('Template updated successfully.');
                }
            },
            onError: (errors) => {
                console.error(errors);
                if (errors?.optimistic_lock) {
                    const message = Array.isArray(errors.optimistic_lock)
                        ? errors.optimistic_lock[0]
                        : errors.optimistic_lock;
                    store.setAutosaveConflict({ message });
                    return;
                }
                if (!autosave) {
                    toast.error('Could not save template.');
                }
            },
            preserveScroll: true,
            preserveState: true
        });
    } else {
        form.post(route('admin.templates.store'), {
            onSuccess: () => {
                store.isDirty = false;
                store.markSavedSnapshot();
                if (!autosave) {
                    toast.success('Template created successfully.');
                }
            },
            onError: (errors) => {
                console.error(errors);
                if (!autosave) {
                    toast.error('Could not create template.');
                }
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
