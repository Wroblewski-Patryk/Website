<template>
    <AdminLayout :full-width="true">
        <BlockBuilder 
            v-model:title="form.title[activeLocale]"
            :module-label="formModel?.id ? t('admin.forms.edit_form', 'Edit Form') : t('admin.forms.add_form', 'Add New Form')"
            :categories="store.categories"
            :module-categories="moduleCategories"
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
                                :placeholder="t('admin.forms.title_field', 'Form Name')"
                            />
                        </div>

                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">{{ t('admin.common.url_slug', 'URL Slug') }}</span></label>
                            <div class="join w-full">
                                <input type="text" v-model="form.settings.slug" class="input input-bordered input-sm join-item focus:border-primary/50 transition-all font-mono text-xs w-full" :placeholder="t('admin.common.slug_placeholder', 'form-slug')" />
                                <button @click="form.settings.slug = generateSlug(form.title[activeLocale])" type="button" class="btn btn-sm btn-ghost join-item" :title="t('admin.common.regenerate_slug', 'Regenerate Slug')">
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
                                    :placeholder="previewUrl ? '' : t('admin.common.save_to_preview', 'Save first to generate preview URL')"
                                />
                                <a
                                    v-if="previewUrl"
                                    :href="previewUrl"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="btn btn-sm btn-ghost join-item"
                                    :title="t('admin.common.preview', 'Open Preview URL')"
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
                                <option value="draft">{{ t('admin.common.status_draft', 'Draft') }}</option>
                                <option value="published">{{ t('admin.common.status_published', 'Published') }}</option>
                                <option value="planned">{{ t('admin.common.status_planned', 'Planned') }}</option>
                                <option value="archived">{{ t('admin.common.status_archived', 'Archived') }}</option>
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
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">{{ t('admin.forms.success_message', 'Success Message') }}</span></label>
                            <textarea v-model="form.settings.success_message" class="textarea textarea-bordered textarea-sm h-20 focus:border-primary/50 transition-all font-sans text-xs" :placeholder="t('admin.forms.success_placeholder', 'Thank you for your message!')"></textarea>
                        </div>
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">{{ t('admin.forms.notification_email', 'Notification Email') }}</span></label>
                            <input type="email" v-model="form.settings.notification_email" class="input input-bordered input-sm focus:border-primary/50 transition-all" :placeholder="t('admin.forms.email_placeholder', 'admin@example.com')" />
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
                                <span class="font-mono">{{ formModel?.created_at ? formatDateTime(formModel.created_at) : t('admin.common.new_content', 'New Content') }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-base-100/50 p-2 rounded-lg border border-base-content/5">
                                <span class="opacity-60">{{ t('admin.common.edited', 'Last Edit') }}</span>
                                <span class="font-mono">{{ formModel?.updated_at ? formatDateTime(formModel.updated_at) : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </BlockBuilder>
    </AdminLayout>
</template>

<script setup>
import { onMounted, computed, watch } from 'vue';
import { PhArrowsClockwise, PhArrowSquareOut, PhFingerprint } from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BlockBuilder from '@/features/admin/block-builder/components/BlockBuilderMain.vue';
import DatePicker from '@/features/admin/shared/components/DatePicker.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useBlockBuilderStore } from '@/features/admin/block-builder/store/useBlockBuilderStore';
import { useToastStore } from '@/Stores/useToastStore';
import { useTranslations } from '@/Composables/useTranslations';
import { useFormatter } from '@/Composables/useFormatter';

const pageProps = usePage().props;
const { t } = useTranslations();
const { formatDateTime } = useFormatter();
const store = useBlockBuilderStore();
const fallbackLocale = computed(() => {
    return pageProps.default_locale
        || pageProps.locale
        || pageProps.languages?.find?.(lang => lang?.is_default)?.code
        || pageProps.languages?.[0]?.code
        || 'en';
});
const activeLocale = computed(() => store.editingLocale || pageProps.locale || fallbackLocale.value);

const props = defineProps({
    formModel: Object,
    moduleCategories: {
        type: Array,
        default: () => []
    },
    templates: [Array, Object]
});

const toast = useToastStore();

const getEmptyLocales = () => {
    const locales = (pageProps.languages || []).map(l => l.code);
    return locales.reduce((acc, code) => ({ ...acc, [code]: '' }), {});
};

const moduleCategories = computed(() => props.moduleCategories || []);

const previewUrl = computed(() => props.formModel?.id ? `/${activeLocale.value}/forms/${props.formModel.id}/preview` : null);

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

const form = useForm({
    title: props.formModel?.title || getEmptyLocales(),
    content: props.formModel?.content || [],
    optimistic_lock: props.formModel?.updated_at || null,
    settings: {
        success_message: props.formModel?.settings?.success_message || t('admin.forms.success_placeholder', 'Message sent!'),
        notification_email: props.formModel?.settings?.notification_email || '',
        submit_url: props.formModel?.settings?.submit_url || '',
        slug: props.formModel?.settings?.slug || '',
    },
    status: props.formModel?.status || 'draft',
    published_at: props.formModel?.published_at ? props.formModel.published_at.substring(0, 19).replace('T', ' ') : '',
});

watch(() => form.title[activeLocale.value], (newTitle) => {
    form.settings.slug = generateSlug(newTitle);
});

onMounted(() => {
    store.init(props.formModel?.content || getEmptyLocales());
});

const save = ({ autosave = false } = {}) => {
    form.content = store.blocks;
    if (props.formModel?.id) {
        form.put(route('admin.forms.update', props.formModel.id), {
            onSuccess: () => {
                store.isDirty = false;
                store.markSavedSnapshot();
                form.optimistic_lock = new Date().toISOString();
                if (!autosave) {
                    toast.success('Form updated successfully.');
                }
            },
            onError: (errors) => {
                if (errors?.optimistic_lock) {
                    const message = Array.isArray(errors.optimistic_lock)
                        ? errors.optimistic_lock[0]
                        : errors.optimistic_lock;
                    store.setAutosaveConflict({ message });
                    return;
                }
                if (!autosave) {
                    toast.error('Could not save form.');
                }
            },
            preserveScroll: true,
            preserveState: true
        });
    } else {
        form.post(route('admin.forms.store'), {
            onSuccess: () => {
                store.isDirty = false;
                store.markSavedSnapshot();
                if (!autosave) {
                    toast.success('Form created successfully.');
                }
            },
            onError: (errors) => {
                if (errors?.optimistic_lock) {
                    const message = Array.isArray(errors.optimistic_lock)
                        ? errors.optimistic_lock[0]
                        : errors.optimistic_lock;
                    store.setAutosaveConflict({ message });
                    return;
                }
                if (!autosave) {
                    toast.error('Could not create form.');
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
