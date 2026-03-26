<template>
    <Head :title="project ? t('admin.projects.edit_project', 'Edit Project') : t('admin.projects.add_project', 'Add Project')" />
    <AdminLayout :full-width="true">
        <BlockBuilder 
            v-model:title="form.title"
            :module-label="project?.id ? t('admin.projects.edit_project', 'Edit Project') : t('admin.projects.add_project', 'Add New Project')"
            :categories="store.categories"
            :module-categories="moduleCategories"
            :saving="form.processing"
            :templates="templates"
            :preview-url="previewUrl"
            @save="submit"
            @autosave="submit"
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
                                :placeholder="t('admin.projects.title_field', 'Project Title')"
                            />
                        </div>

                        <div class="form-control">
                             <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">{{ t('admin.common.url_slug', 'URL Slug') }}</span></label>
                            <div class="join w-full">
                                <input type="text" v-model="form.slug[activeLocale]" class="input input-bordered input-sm join-item focus:border-primary/50 transition-all font-mono text-xs w-full" :placeholder="t('admin.common.slug_placeholder', 'project-slug')" />
                                <button @click="form.slug[activeLocale] = generateSlug(form.title[activeLocale])" type="button" class="btn btn-sm btn-ghost join-item" :title="t('admin.common.regenerate_slug', 'Regenerate Slug')">
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
                                    :placeholder="form.slug?.[activeLocale] ? '' : t('admin.common.slug_required_for_url', 'Slug required for URL')"
                                />
                                <a
                                    v-if="previewUrl"
                                    :href="previewUrl"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="btn btn-sm btn-ghost join-item"
                                    :title="t('admin.common.preview', 'Preview')"
                                >
                                    <PhArrowSquareOut weight="bold" class="w-3 h-3" />
                                </a>
                                <button v-else type="button" class="btn btn-sm btn-ghost join-item" disabled :title="t('admin.common.url_unavailable', 'URL unavailable')">
                                    <PhArrowSquareOut weight="bold" class="w-3 h-3 opacity-40" />
                                </button>
                            </div>
                        </div>

                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">{{ t('admin.common.status', 'Status') }}</span></label>
                            <select v-model="form.status" class="select select-bordered select-sm focus:select-primary transition-all w-full">
                                <option value="draft">{{ t('admin.common.draft', 'Draft') }}</option>
                                <option value="published">{{ t('admin.common.published', 'Published') }}</option>
                                <option value="archived">{{ t('admin.common.archived', 'Archived') }}</option>
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

                    <div class="space-y-4">
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">{{ t('admin.projects.category', 'Category') }}</span></label>
                            <input type="text" v-model="form.category" class="input input-bordered input-sm focus:input-primary transition-all" placeholder="Web Design" />
                        </div>
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">{{ t('admin.projects.client', 'Client') }}</span></label>
                            <select v-model="form.client_id" class="select select-bordered select-sm focus:select-primary transition-all">
                                <option :value="null">{{ t('admin.common.none', 'None') }}</option>
                                <option v-for="cl in availableClients" :key="cl.id" :value="cl.id">{{ t(cl.title) }}</option>
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">{{ t('admin.projects.external_url', 'External URL') }}</span></label>
                            <input type="text" v-model="form.url" class="input input-bordered input-sm focus:input-primary transition-all font-mono text-[10px]" placeholder="https://..." />
                        </div>
                    </div>
                </div>
            </template>

            <template #seo>
                <div class="flex flex-col gap-6">
                    <div class="space-y-4">
                         <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60 text-primary">{{ t('admin.common.meta_title', 'Meta Title') }}</span></label>
                            <input type="text" v-model="form.meta_title[activeLocale]" class="input input-bordered input-sm focus:input-primary transition-all" :placeholder="t('admin.common.meta_title_placeholder', 'SEO Title')" />
                            <label class="label"><span class="label-text-alt opacity-40">{{ form.meta_title[activeLocale]?.length || 0 }}/60 chars</span></label>
                        </div>
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60 text-primary">{{ t('admin.common.meta_description', 'Meta Description') }}</span></label>
                            <textarea v-model="form.meta_description[activeLocale]" class="textarea textarea-bordered textarea-sm focus:textarea-primary transition-all h-24" :placeholder="t('admin.common.meta_description_placeholder', 'SEO Description')"></textarea>
                            <label class="label"><span class="label-text-alt opacity-40">{{ form.meta_description[activeLocale]?.length || 0 }}/160 chars</span></label>
                        </div>
                    </div>
                </div>
            </template>
        </BlockBuilder>
    </AdminLayout>
</template>

<script setup>
import {
    PhArrowsClockwise,
    PhArrowSquareOut
} from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BlockBuilder from '@/features/admin/block-builder/components/BlockBuilderMain.vue';
import TaxonomySelect from '@/features/admin/shared/components/TaxonomySelect.vue';
import { useForm, usePage, Head } from '@inertiajs/vue3';
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
    project: Object,
    taxonomies: {
        type: Array,
        default: () => []
    },
    availableTaxonomies: {
        type: Array,
        default: () => []
    },
    availableClients: {
        type: Array,
        default: () => []
    },
    moduleCategories: {
        type: Array,
        default: () => []
    },
    templates: [Array, Object],
});

const moduleCategories = computed(() => props.moduleCategories || []);

const store = useBlockBuilderStore();
const toast = useToastStore();

const isObject = (val) => val && typeof val === 'object' && !Array.isArray(val);

const form = useForm({
    title: isObject(props.project?.title) ? props.project.title : getEmptyLocales(),
    slug: isObject(props.project?.slug) ? props.project.slug : getEmptyLocales(),
    description: isObject(props.project?.description) ? props.project.description : getEmptyLocales(),
    content: props.project?.content || [],
    optimistic_lock: props.project?.updated_at || null,
    desktop_image: props.project?.desktop_image || '',
    mobile_image: props.project?.mobile_image || '',
    url: props.project?.url || '',
    category: props.project?.category || '',
    client_id: props.project?.client_id || null,
    order: props.project?.order || 0,
    status: props.project?.status || 'draft',
    taxonomies: props.taxonomies || [],
    published_at: props.project?.published_at ? props.project.published_at.substring(0, 19).replace('T', ' ') : '',
    // SEO Fields
    meta_title: isObject(props.project?.meta_title) ? props.project.meta_title : getEmptyLocales(),
    meta_description: isObject(props.project?.meta_description) ? props.project.meta_description : getEmptyLocales(),
    canonical_url: props.project?.canonical_url || '',
    og_image: isObject(props.project?.og_image) ? props.project.og_image : getEmptyLocales(),
    seo_index: props.project?.seo_index ?? true,
    seo_follow: props.project?.seo_follow ?? true,
});

const previewUrl = computed(() => form.slug?.[activeLocale.value] ? `/projekty/${form.slug[activeLocale.value]}` : null);

onMounted(() => {
    store.init(props.project?.content || getEmptyLocales());
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

function submit({ autosave = false } = {}) {
    form.content = store.blocks;

    if (props.project?.id) {
        form.put(route('admin.projects.update', props.project.id), {
            onSuccess: () => {
                store.isDirty = false;
                store.markSavedSnapshot();
                form.optimistic_lock = new Date().toISOString();
                if (!autosave) {
                    toast.success(t('admin.projects.update_success', 'Project updated successfully.'));
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
                    toast.error(t('admin.common.error_saving', 'Error saving project.'));
                }
            },
            preserveScroll: true,
            preserveState: true
        });
    } else {
        form.post(route('admin.projects.store'), {
            onSuccess: () => {
                store.isDirty = false;
                store.markSavedSnapshot();
                if (!autosave) {
                    toast.success(t('admin.projects.create_success', 'Project created successfully.'));
                }
            },
            onError: (errors) => {
                console.error(errors);
                if (!autosave) {
                    toast.error(t('admin.common.error_creating', 'Error creating project.'));
                }
            }
        });
    }
}
</script>
