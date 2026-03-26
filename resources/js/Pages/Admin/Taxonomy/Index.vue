<script setup>
import { ref, markRaw, onMounted, computed, watch } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { PhPencilSimple, PhPlusCircle, PhTag, PhPlus, PhTrash, PhHouse, PhTextT, PhFileText, PhHash } from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ResourceTable from '@/features/admin/shared/components/ResourceTable.vue';
import { useTranslations } from '@/Composables/useTranslations';
import { useToastStore } from '@/Stores/useToastStore';

const { t } = useTranslations();
const toast = useToastStore();

const props = defineProps({
    taxonomies: Object,
    currentType: String,
    currentModule: String
});

const tableRef = ref(null);
const isCreating = ref(false);
const editingTaxonomy = ref(null);
const page = usePage();
const locales = computed(() => {
    const codes = Array.isArray(page.props.languages)
        ? page.props.languages.map(lang => lang.code).filter(Boolean)
        : [];
    return codes.length ? codes : ['en'];
});
const defaultLocale = computed(() => {
    return page.props.default_locale
        || page.props.locale
        || locales.value[0]
        || 'en';
});
const currentLocale = ref(document.documentElement.lang || defaultLocale.value);

function initForm() {
    const translations = Object.fromEntries(locales.value.map(lang => [lang, '']));

    return {
        type: props.currentType || 'category',
        module: props.currentModule || 'posts',
        order: 0,
        color: '#3b82f6',
        icon: '',
        title: { ...translations },
        slug: { ...translations },
        description: { ...translations }
    };
}

const form = useForm(initForm());

const breadcrumbs = computed(() => {
    const list = [
        { label: t('admin.dashboard.title', 'Dashboard'), url: route('admin.dashboard.index'), icon: markRaw(PhHouse) },
    ];

    if (props.currentModule === 'posts') {
        list.push({ label: t('admin.menu.posts', 'Posts'), url: route('admin.posts.index') });
    } else if (props.currentModule === 'projects') {
        list.push({ label: t('admin.menu.projects', 'Projects'), url: route('admin.projects.index') });
    }

    list.push({ label: props.currentType === 'category' ? t('admin.taxonomy.type_category', 'Categories') : t('admin.taxonomy.type_tag', 'Tags') });
    
    return list;
});

const columns = [
    { key: 'id', label: t('admin.common.id', 'ID'), sortable: true },
    { key: 'title', label: t('admin.taxonomy.title', 'Title'), sortable: false },
    { key: 'slug', label: t('admin.taxonomy.slug', 'Slug'), sortable: false },
    { key: 'order', label: t('admin.taxonomy.order', 'Order'), sortable: true },
    { key: 'actions', label: t('admin.common.actions', 'Actions'), sortable: false, align: 'right' }
];

const bulkActions = [
    { value: 'delete', label: t('admin.common.delete', 'Delete') }
];

const types = [
    { id: 'category', label: t('admin.taxonomy.type_category', 'Categories'), icon: markRaw(PhFileText) },
    { id: 'tag', label: t('admin.taxonomy.type_tag', 'Tags'), icon: markRaw(PhTag) }
];


function openCreate() {
    form.reset();
    form.type = props.currentType;
    form.module = props.currentModule;
    editingTaxonomy.value = null;
    isCreating.value = true;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function openEdit(item) {
    editingTaxonomy.value = item;
    form.type = item.type;
    form.module = item.module || props.currentModule;
    form.order = item.order;
    form.color = item.color;
    form.icon = item.icon;
    
    // Handle translatable fields
    locales.value.forEach(lang => {
        form.title[lang] = item.title[lang] || '';
        form.slug[lang] = item.slug[lang] || '';
        form.description[lang] = item.description[lang] || '';
    });

    isCreating.value = true;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function submit() {
    const handleError = (errors) => {
        const firstError = Object.values(errors || {})
            .flat()
            .find(Boolean);

        toast.error(firstError || t('admin.common.error_saving', 'Error occurred while saving.'));
    };

    if (editingTaxonomy.value) {
        form.put(route('admin.taxonomies.update', editingTaxonomy.value.id), {
            onSuccess: () => {
                isCreating.value = false;
                form.reset();
            },
            onError: handleError,
        });
    } else {
        form.post(route('admin.taxonomies.store'), {
            onSuccess: () => {
                isCreating.value = false;
                form.reset();
            },
            onError: handleError,
        });
    }
}

function deleteTaxonomy(item) {
    router.delete(route('admin.taxonomies.destroy', item.id));
}

const slugManuallyEdited = ref({ pl: false, en: false });

function generateSlug(text) {
    if (!text) return '';
    return text
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();
}

// Auto-generate slug from title
watch(() => form.title[currentLocale.value], (newVal) => {
    if (!editingTaxonomy.value && !slugManuallyEdited.value[currentLocale.value]) {
        form.slug[currentLocale.value] = generateSlug(newVal);
    }
});
</script>

<template>
    <Head :title="t('admin.taxonomy.management', 'Taxonomy Management')" />
    <AdminLayout>

        <div v-if="isCreating" class="mb-8 p-8 bg-base-100 rounded-box border border-primary/20 shadow-xl max-w-4xl mx-auto">
            <h3 class="text-xl font-black text-primary mb-6 flex items-center gap-2">
                <PhPencilSimple v-if="editingTaxonomy" weight="regular" class="w-6 h-6" />
                <PhPlusCircle v-else weight="regular" class="w-6 h-6" />
                {{ editingTaxonomy ? t('admin.taxonomy.edit', 'Edit Taxonomy') : t('admin.taxonomy.add_new', 'Add New Taxonomy') }}
            </h3>
            
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Language Selector inside Form -->
                <div class="flex gap-2 mb-4 bg-base-200/50 p-1 rounded-xl w-fit">
                    <button v-for="lang in locales" :key="lang" type="button"
                        @click="currentLocale = lang"
                        class="btn btn-xs rounded-lg px-4 border-none shadow-none uppercase relative"
                        :class="currentLocale === lang ? 'btn-primary' : 'btn-ghost opacity-50'">
                        {{ lang }}
                        <span v-if="!form.title[lang]" class="absolute -top-1 -right-1 flex h-2 w-2">
                             <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-error opacity-75"></span>
                             <span class="relative inline-flex rounded-full h-2 w-2 bg-error"></span>
                        </span>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-bold opacity-50">{{ t('admin.taxonomy.title_label', 'Title') }} ({{ currentLocale.toUpperCase() }})</span></label>
                        <input type="text" v-model="form.title[currentLocale]" class="input input-bordered w-full rounded-xl bg-base-200/50" required />
                    </div>
                    
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-bold opacity-50">{{ t('admin.taxonomy.slug_label', 'Slug') }} ({{ currentLocale.toUpperCase() }})</span></label>
                        <input type="text" v-model="form.slug[currentLocale]" @input="slugManuallyEdited[currentLocale] = true" class="input input-bordered w-full rounded-xl bg-base-200/50" />
                    </div>

                    <div class="form-control w-full md:col-span-2">
                        <label class="label"><span class="label-text font-bold opacity-50">{{ t('admin.taxonomy.description_label', 'Description') }} ({{ currentLocale.toUpperCase() }})</span></label>
                        <textarea v-model="form.description[currentLocale]" class="textarea textarea-bordered w-full rounded-xl bg-base-200/50 h-24"></textarea>
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-bold opacity-50">{{ t('admin.taxonomy.order_label', 'Order') }}</span></label>
                        <input type="number" v-model="form.order" class="input input-bordered w-full rounded-xl bg-base-200/50" />
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-bold opacity-50">{{ t('admin.taxonomy.color_label', 'Color') }}</span></label>
                        <div class="flex gap-3">
                            <input type="color" v-model="form.color" class="w-12 h-12 rounded-xl border-none p-1 bg-base-200/50 cursor-pointer" />
                            <input type="text" v-model="form.color" class="input input-bordered flex-1 rounded-xl bg-base-200/50" placeholder="#000000" />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-base-200">
                    <button type="button" @click="isCreating = false" class="btn btn-ghost rounded-xl px-6">{{ t('admin.common.cancel', 'Cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-xl px-8 shadow-lg shadow-primary/20" :disabled="form.processing">
                        {{ editingTaxonomy ? t('admin.taxonomy.update_btn', 'Update') : t('admin.taxonomy.create_btn', 'Create') }}
                    </button>
                </div>
            </form>
        </div>

        <ResourceTable
            :title="types.find(t => t.id === currentType)?.label"
            :description="t('admin.taxonomy.description_hint', 'Organize content using semantic grouping.')"
            :icon="markRaw(PhHash)"
            :breadcrumbs="breadcrumbs"
            :resources="taxonomies"
            :columns="columns"
            :search-placeholder="t('admin.common.search_placeholder', 'Search...')"
            :bulk-route="route('admin.taxonomies.bulk-action')"
            :bulk-actions="bulkActions"
            :persistence-key="'taxonomy-' + currentType"
            ref="tableRef"
            @create="openCreate"
            @delete-confirmed="deleteTaxonomy"
        >
            <template #header-actions>
                <button @click="openCreate" class="btn btn-primary shadow-lg shadow-primary/20 hover:shadow-xl hover:-translate-y-0.5 transition-all">
                    <PhPlus weight="bold" class="w-3 h-3 mr-1" /> {{ t('admin.common.create', 'Create') }}
                </button>
            </template>

            <template #cell-title="{ item }">
                <div class="flex items-center gap-3">
                    <div v-if="item.color" class="w-3 h-3 rounded-full shadow-sm" :style="{ backgroundColor: item.color }"></div>
                    <span class="font-bold">{{ item.title[currentLocale] || item.title[defaultLocale] || Object.values(item.title || {})[0] || '' }}</span>
                </div>
            </template>

            <template #cell-slug="{ item }">
                <span class="text-xs font-mono opacity-50">{{ item.slug[currentLocale] || item.slug[defaultLocale] || Object.values(item.slug || {})[0] || '' }}</span>
            </template>

            <template #cell-actions="{ item }">
                <div class="flex justify-end gap-2">
                    <button @click="openEdit(item)" class="btn btn-sm btn-ghost btn-square hover:bg-primary/10 hover:text-primary transition-all" :title="t('admin.common.edit', 'Edit')">
                        <PhPencilSimple weight="regular" class="w-4 h-4" />
                    </button>
                    <button @click="tableRef?.openDeleteModal(item)" class="btn btn-sm btn-ghost btn-square hover:bg-error/10 hover:text-error transition-all" :title="t('admin.common.delete', 'Delete')">
                        <PhTrash weight="regular" class="w-4 h-4" />
                    </button>
                </div>
            </template>
        </ResourceTable>
    </AdminLayout>
</template>
