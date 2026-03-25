<template>
    <AdminLayout :full-width="true">
        <BlockBuilder 
            v-model:title="form.title"
            :module-label="page?.id ? t('admin.pages.edit_page', 'Edit Page') : t('admin.pages.add_page', 'Add New Page')"
            :categories="store.categories"
            :saving="form.processing"
            :templates="templates"
            :preview-url="previewUrl"
            canvas-min-height="720px"
            @save="save"
        >
            <!-- Info Tab -->
            <template #info>
                <div class="flex flex-col gap-6">
                    <div class="space-y-4">
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">{{ t('admin.pages.url_slug', 'URL Slug') }}</span></label>
                            <div class="join w-full">
                                <input type="text" v-model="form.slug[activeLocale]" class="input input-bordered input-sm join-item focus:border-primary/50 transition-all font-mono text-xs w-full" :placeholder="t('admin.pages.slug_field', 'page-slug')" />
                                <button @click="form.slug[activeLocale] = generateSlug(form.title[activeLocale])" type="button" class="btn btn-sm btn-ghost join-item" :title="t('admin.pages.regenerate_slug', 'Regenerate Slug')">
                                    <PhArrowsClockwise weight="bold" class="w-3 h-3" />
                                </button>
                            </div>
                        </div>

                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">{{ t('admin.pages.generated_url', 'Generated URL') }}</span></label>
                            <div class="join w-full">
                                <input
                                    type="text"
                                    :value="previewUrl || ''"
                                    readonly
                                    class="input input-bordered input-sm join-item w-full font-mono text-[10px]"
                                    :placeholder="form.slug?.[activeLocale] ? '' : t('admin.pages.slug_required', 'Slug required for URL')"
                                />
                                <a
                                    v-if="previewUrl"
                                    :href="previewUrl"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="btn btn-sm btn-ghost join-item"
                                    :title="t('admin.pages.open_preview', 'Open Preview URL')"
                                >
                                    <PhArrowSquareOut weight="bold" class="w-3 h-3" />
                                </a>
                                <button v-else type="button" class="btn btn-sm btn-ghost join-item" disabled :title="t('admin.pages.url_unavailable', 'URL unavailable')">
                                    <PhArrowSquareOut weight="bold" class="w-3 h-3 opacity-40" />
                                </button>
                            </div>
                        </div>

                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">{{ t('admin.common.status', 'Status') }}</span></label>
                            <select v-model="form.status" class="select select-bordered select-sm focus:select-primary transition-all w-full">
                                <option value="draft">{{ t('admin.pages.status_draft', 'Draft (Private)') }}</option>
                                <option value="published">{{ t('admin.pages.status_published', 'Published (Public)') }}</option>
                                <option value="planned">{{ t('admin.pages.status_planned', 'Planned (Scheduled)') }}</option>
                                <option value="archived">{{ t('admin.common.archived', 'Archived') }}</option>
                            </select>
                        </div>

                        <div v-if="form.status === 'planned' || form.status === 'published'" class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">{{ t('admin.pages.publish_date_time', 'Publish Date & Time') }}</span></label>
                            <DatePicker v-model="form.published_at" />
                        </div>
                    </div>

                    <div class="divider opacity-10 my-0"></div>

                    <div class="space-y-4">
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">{{ t('admin.pages.main_template', 'Main Page Template') }}</span></label>
                            <select v-model="form.template_id" class="select select-bordered select-sm text-xs w-full">
                                <option :value="null">{{ t('admin.pages.layout_default', 'Default Page Layout') }}</option>
                                <option v-for="t in templates.page || []" :key="t.id" :value="t.id">
                                    {{ t.title?.[activeLocale] || t.title?.pl || Object.values(t.title || {})[0] || 'Untitled' }}
                                </option>
                            </select>
                        </div>

                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">{{ t('admin.pages.header_section', 'Header Section') }}</span></label>
                            <select v-model="form.header_override_id" class="select select-bordered select-sm text-xs w-full">
                                <option :value="null">{{ t('admin.pages.header_default', 'System Default Header') }}</option>
                                <option v-for="t in templates.header || []" :key="t.id" :value="t.id">
                                    {{ t.title?.[activeLocale] || t.title?.pl || Object.values(t.title || {})[0] || 'Untitled Header' }}
                                </option>
                            </select>
                        </div>

                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">{{ t('admin.pages.sidebar_section', 'Sidebar Section') }}</span></label>
                            <select v-model="form.sidebar_override_id" class="select select-bordered select-sm text-xs w-full">
                                <option :value="null">{{ t('admin.pages.sidebar_default', 'System Default Sidebar') }}</option>
                                <option v-for="t in templates.sidebar || []" :key="t.id" :value="t.id">
                                    {{ t.title?.[activeLocale] || t.title?.pl || Object.values(t.title || {})[0] || 'Untitled Sidebar' }}
                                </option>
                            </select>
                        </div>

                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">{{ t('admin.pages.footer_section', 'Footer Section') }}</span></label>
                            <select v-model="form.footer_override_id" class="select select-bordered select-sm text-xs w-full">
                                <option :value="null">{{ t('admin.pages.footer_default', 'System Default Footer') }}</option>
                                <option v-for="t in templates.footer || []" :key="t.id" :value="t.id">
                                    {{ t.title?.[activeLocale] || t.title?.pl || Object.values(t.title || {})[0] || 'Untitled Footer' }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="divider opacity-10 my-0"></div>

                    <TaxonomySelect 
                        v-model="form.taxonomies"
                        :available-taxonomies="availableTaxonomies"
                        :active-locale="activeLocale"
                    />

                    <div class="divider opacity-10 my-0"></div>

                    <div class="space-y-3 bg-base-200/30 p-4 rounded-xl border border-base-content/10">
                        <div class="flex items-center justify-between text-[10px] uppercase tracking-wider opacity-60 font-bold px-1">
                            <span>{{ t('admin.pages.metadata', 'Metadata') }}</span>
                            <PhFingerprint weight="bold" class="w-3 h-3 text-primary" />
                        </div>
                        <div class="flex flex-col gap-2 text-[11px]">
                            <div class="flex justify-between items-center bg-base-100/50 p-2 rounded-lg border border-base-content/5">
                                <span class="opacity-60">{{ t('admin.common.created', 'Created') }}</span>
                                <span class="font-mono">{{ page?.created_at ? new Date(page.created_at).toLocaleString() : 'New Content' }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-base-100/50 p-2 rounded-lg border border-base-content/5">
                                <span class="opacity-60">{{ t('admin.common.edited', 'Last Edit') }}</span>
                                <span class="font-mono">{{ page?.updated_at ? new Date(page.updated_at).toLocaleString() : 'N/A' }}</span>
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
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60 text-primary">{{ t('admin.pages.meta_title', 'Meta Title') }}</span></label>
                            <input type="text" v-model="form.meta_title[activeLocale]" class="input input-bordered input-sm focus:input-primary transition-all" :placeholder="t('admin.pages.meta_title', 'SEO Title')" />
                            <label class="label"><span class="label-text-alt opacity-40">{{ form.meta_title[activeLocale]?.length || 0 }}/60 chars</span></label>
                        </div>
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60 text-primary">{{ t('admin.pages.meta_desc', 'Meta Description') }}</span></label>
                            <textarea v-model="form.meta_description[activeLocale]" class="textarea textarea-bordered textarea-sm focus:textarea-primary transition-all h-24" :placeholder="t('admin.pages.meta_desc', 'SEO Description')"></textarea>
                            <label class="label"><span class="label-text-alt opacity-40">{{ form.meta_description[activeLocale]?.length || 0 }}/160 chars</span></label>
                        </div>
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">{{ t('admin.pages.canonical_url', 'Canonical URL') }}</span></label>
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
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">{{ t('admin.pages.og_image', 'OG Image URL') }}</span></label>
                            <input type="text" v-model="form.og_image[activeLocale]" class="input input-bordered input-sm focus:input-primary transition-all font-mono text-[10px]" :placeholder="t('admin.pages.og_image', 'Image URL for social media')" />
                        </div>
                    </div>

                    <!-- Robots Settings -->
                    <div class="space-y-4 bg-base-200/30 p-4 rounded-2xl border border-base-content/5">
                        <label class="text-[10px] font-bold uppercase tracking-widest opacity-30">{{ t('admin.pages.search_visibility', 'Search Engine Visibility') }}</label>
                        <div class="flex flex-col gap-3">
                            <label class="flex items-center justify-between cursor-pointer group">
                                <span class="text-xs group-hover:text-primary transition-colors">{{ t('admin.pages.index_page', 'Index Page') }}</span>
                                <input type="checkbox" v-model="form.seo_index" class="toggle toggle-primary toggle-sm" />
                            </label>
                            <label class="flex items-center justify-between cursor-pointer group">
                                <span class="text-xs group-hover:text-primary transition-colors">{{ t('admin.pages.follow_links', 'Follow Links') }}</span>
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
                    {{ t('admin.pages.history_empty', 'No history yet. Revisions are created when you save changes.') }}
                </div>
                <div v-else class="space-y-3">
                    <div v-for="rev in page.revisions" :key="rev.id" class="p-3 bg-base-200/50 rounded-xl border border-base-content/5 flex flex-col gap-2 hover:border-primary/30 transition-all group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold opacity-70">{{ new Date(rev.created_at).toLocaleString() }}</span>
                            <button @click="restoreRevision(rev)" class="btn btn-xs btn-outline btn-primary opacity-0 group-hover:opacity-100 scale-90 transition-all">{{ t('admin.pages.restore_btn', 'Restore') }}</button>
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
import BlockBuilder from '@/features/admin/block-builder/components/BlockBuilderMain.vue';
import TaxonomySelect from '@/features/admin/shared/components/TaxonomySelect.vue';
import DatePicker from '@/features/admin/shared/components/DatePicker.vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import { useBlockBuilderStore } from '@/features/admin/block-builder/store/useBlockBuilderStore';
import { useToastStore } from '@/Stores/useToastStore';
import { useTranslations } from '@/Composables/useTranslations';
import { computed, onMounted, watch } from 'vue';

const { t } = useTranslations();
const pageProps = usePage().props;
const activeLocale = computed(() => store.editingLocale || pageProps.locale || 'en');

const getEmptyLocales = () => {
    const locales = (pageProps.languages || []).map(l => l.code);
    return locales.reduce((acc, code) => ({ ...acc, [code]: '' }), {});
};

const props = defineProps({
    page: Object,
    taxonomies: {
        type: Array,
        default: () => []
    },
    availableTaxonomies: {
        type: Array,
        default: () => []
    },
    templates: [Array, Object],
    menus: Array
});

const store = useBlockBuilderStore();
const toast = useToastStore();

const isObject = (val) => val && typeof val === 'object' && !Array.isArray(val);

const form = useForm({
    title: isObject(props.page?.title) ? props.page.title : getEmptyLocales(),
    slug: isObject(props.page?.slug) ? props.page.slug : getEmptyLocales(),
    content: props.page?.content || [],
    optimistic_lock: props.page?.updated_at || null,
    status: props.page?.status || 'draft',
    published_at: props.page?.published_at ? props.page.published_at.substring(0, 19).replace('T', ' ') : '',
    header_override_id: props.page?.header_override_id || null,
    footer_override_id: props.page?.footer_override_id || null,
    sidebar_override_id: props.page?.sidebar_override_id || null,
    template_id: props.page?.template_id || null,
    taxonomies: props.taxonomies || [],
    // SEO Fields
    meta_title: isObject(props.page?.meta_title) ? props.page.meta_title : getEmptyLocales(),
    meta_description: isObject(props.page?.meta_description) ? props.page.meta_description : getEmptyLocales(),
    canonical_url: props.page?.canonical_url || '',
    og_image: isObject(props.page?.og_image) ? props.page.og_image : getEmptyLocales(),
    seo_index: props.page?.seo_index ?? true,
    seo_follow: props.page?.seo_follow ?? true,
});

const previewUrl = computed(() => form.slug?.[activeLocale.value] ? `/${form.slug[activeLocale.value]}` : null);

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
watch(() => form.title[activeLocale.value], (newTitle) => {
    if (newTitle) {
        form.slug[activeLocale.value] = generateSlug(newTitle);
    }
});

const restoreRevision = (rev) => {
    if (confirm(t('admin.common.are_you_sure', 'Are you sure you want to restore this version? Current unsaved changes will be lost.'))) {
        router.post(route('admin.pages.revisions.restore', { page: props.page.id, revision: rev.id }), {
            optimistic_lock: form.optimistic_lock,
        }, {
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

const save = () => {
    form.content = store.blocks;
    
    if (props.page?.id) {
        form.put(route('admin.pages.update', props.page.id), {
            onSuccess: () => {
                store.isDirty = false;
                form.optimistic_lock = new Date().toISOString();
                toast.success('Strona została pomyślnie zaktualizowana! 🎉');
            },
            onError: (errors) => {
                console.error(errors);
                toast.error('Wystąpił błąd podczas zapisywania strony. ❌');
            },
            preserveScroll: true,
            preserveState: true
        });
    } else {
        form.post(route('admin.pages.store'), {
            onSuccess: () => {
                store.isDirty = false;
                toast.success('Strona została pomyślnie utworzona! ✨');
            },
            onError: (errors) => {
                console.error(errors);
                toast.error('Wystąpił błąd podczas tworzenia strony. ❌');
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
