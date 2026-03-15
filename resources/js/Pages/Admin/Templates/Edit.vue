<template>
    <AdminLayout :full-width="true">
        <BlockBuilder 
            v-model:title="form.title"
            :module-label="template?.id ? t('admin.templates.edit_template', 'Edit Template') : t('admin.templates.add_template', 'Add New Template')"
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
    PhArrowsClockwise,
    PhArrowSquareOut,
    PhFingerprint,
    PhClockCounterClockwise,
    PhShareNetwork
} from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BlockBuilder from '@/features/admin/block-builder/components/BlockBuilderMain.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useBlockBuilderStore } from '@/features/admin/block-builder/store/useBlockBuilderStore';
import { useToastStore } from '@/Stores/useToastStore';
import { useTranslations } from '@/Composables/useTranslations';
import { useFormatter } from '@/Composables/useFormatter';
import { computed, onMounted, ref, watch } from 'vue';

const { t } = useTranslations();
const { formatDateTime } = useFormatter();

const pageProps = usePage().props;
const activeLocale = computed(() => store.editingLocale || pageProps.locale || 'pl');

const props = defineProps({
    template: Object,
    templates: [Array, Object],
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
        store.init(rev.content || getEmptyLocales());
        store.isDirty = true;
    }
};

const save = () => {
    form.content = store.blocks;
    
    if (props.template?.id) {
        form.put(route('admin.templates.update', props.template.id), {
            onSuccess: () => {
                store.isDirty = false;
                toast.success('Szablon został pomyślnie zaktualizowany! 🎉');
            },
            onError: (errors) => {
                console.error(errors);
                toast.error('Wystąpił błąd podczas zapisywania szablonu. ❌');
            },
            preserveScroll: true,
            preserveState: true
        });
    } else {
        form.post(route('admin.templates.store'), {
            onSuccess: () => {
                store.isDirty = false;
                toast.success('Szablon został pomyślnie utworzony! ✨');
            },
            onError: (errors) => {
                console.error(errors);
                toast.error('Wystąpił błąd podczas tworzenia szablonu. ❌');
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
