<template>
    <AdminLayout :full-width="true">
        <BlockBuilder
            v-model:title="form.title"
            :module-label="composed_block?.id ? t('admin.composed_blocks.edit', 'Edit Composed Block') : t('admin.composed_blocks.create', 'Create Composed Block')"
            :categories="store.categories"
            :saving="form.processing"
            @save="save"
            @autosave="save"
        >
            <template #info>
                <div class="flex flex-col gap-4">
                    <div class="form-control">
                        <label class="label pt-0">
                            <span class="label-text text-xs font-bold opacity-60">{{ t('admin.common.name', 'Name') }}</span>
                        </label>
                        <input
                            v-model="form.title[activeLocale]"
                            type="text"
                            class="input input-bordered input-sm"
                            :placeholder="t('admin.composed_blocks.name_placeholder', 'Composed block name')"
                        />
                    </div>

                    <div class="form-control">
                        <label class="label pt-0">
                            <span class="label-text text-xs font-bold opacity-60">{{ t('admin.common.url_slug', 'Slug') }}</span>
                        </label>
                        <div class="join w-full">
                            <input
                                v-model="form.slug"
                                type="text"
                                class="input input-bordered input-sm join-item w-full font-mono text-xs"
                                :placeholder="t('admin.composed_blocks.slug_placeholder', 'hero-section')"
                            />
                            <button
                                type="button"
                                class="btn btn-sm btn-ghost join-item"
                                @click="form.slug = generateSlug(form.title[activeLocale])"
                            >
                                {{ t('admin.common.regenerate', 'Regenerate') }}
                            </button>
                        </div>
                    </div>

                    <label class="label cursor-pointer justify-between rounded-xl border border-base-content/10 bg-base-200/30 px-3 py-2">
                        <span class="label-text text-xs font-bold opacity-70">{{ t('admin.common.active', 'Active') }}</span>
                        <input v-model="form.is_active" type="checkbox" class="toggle toggle-primary toggle-sm" />
                    </label>
                </div>
            </template>
        </BlockBuilder>
    </AdminLayout>
</template>

<script setup>
import { computed, onMounted, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BlockBuilder from '@/features/admin/block-builder/components/BlockBuilderMain.vue';
import { useBlockBuilderStore } from '@/features/admin/block-builder/store/useBlockBuilderStore';
import { useToastStore } from '@/Stores/useToastStore';
import { useTranslations } from '@/Composables/useTranslations';

const props = defineProps({
    composed_block: Object,
});

const pageProps = usePage().props;
const store = useBlockBuilderStore();
const toast = useToastStore();
const { t } = useTranslations();

const activeLocale = computed(() => store.editingLocale || pageProps.locale || 'en');

const getEmptyLocales = () => {
    const locales = (pageProps.languages || []).map((lang) => lang.code);
    return locales.reduce((acc, code) => ({ ...acc, [code]: '' }), {});
};

const isObject = (value) => value && typeof value === 'object' && !Array.isArray(value);

const form = useForm({
    title: isObject(props.composed_block?.title) ? props.composed_block.title : getEmptyLocales(),
    slug: props.composed_block?.slug || '',
    content: props.composed_block?.content || [],
    settings: props.composed_block?.settings || {},
    is_active: props.composed_block?.is_active ?? true,
    optimistic_lock: props.composed_block?.updated_at || null,
});

const generateSlug = (text) => {
    if (!text) return '';
    return String(text)
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9 -]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-+|-+$/g, '');
};

watch(() => form.title[activeLocale.value], (value) => {
    if (!props.composed_block?.id || !form.slug) {
        form.slug = generateSlug(value);
    }
});

onMounted(() => {
    store.init(props.composed_block?.content || []);
});

const save = ({ autosave = false } = {}) => {
    form.content = store.blocks;

    if (props.composed_block?.id) {
        form.put(route('admin.blocks.update', props.composed_block.id), {
            onSuccess: () => {
                store.isDirty = false;
                store.markSavedSnapshot();
                form.optimistic_lock = new Date().toISOString();
                if (!autosave) toast.success(t('admin.composed_blocks.update_success', 'Composed block updated.'));
            },
            onError: (errors) => {
                if (errors?.optimistic_lock) {
                    const message = Array.isArray(errors.optimistic_lock)
                        ? errors.optimistic_lock[0]
                        : errors.optimistic_lock;
                    store.setAutosaveConflict({ message });
                    return;
                }
                if (!autosave) toast.error(t('admin.composed_blocks.update_error', 'Could not update composed block.'));
            },
            preserveScroll: true,
            preserveState: true,
        });
        return;
    }

    form.post(route('admin.blocks.store'), {
        onSuccess: () => {
            store.isDirty = false;
            store.markSavedSnapshot();
            if (!autosave) toast.success(t('admin.composed_blocks.create_success', 'Composed block created.'));
        },
        onError: () => {
            if (!autosave) toast.error(t('admin.composed_blocks.create_error', 'Could not create composed block.'));
        },
    });
};
</script>

