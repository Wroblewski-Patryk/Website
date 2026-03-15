<script setup>
import { ref, markRaw, onMounted, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { PhPencilSimple, PhPlusCircle, PhTag, PhPlus, PhTrash, PhHouse, PhTextT, PhFileText, PhHash } from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ResourceTable from '@/features/admin/shared/components/ResourceTable.vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();

const props = defineProps({
    taxonomies: Object,
    currentType: String
});

const tableRef = ref(null);
const isCreating = ref(false);
const editingTaxonomy = ref(null);
const currentLocale = ref(document.documentElement.lang || 'pl');
const locales = ['pl', 'en'];

function initForm() {
    return {
        type: props.currentType || 'category',
        order: 0,
        color: '#3b82f6',
        icon: '',
        title: { pl: '', en: '' },
        slug: { pl: '', en: '' },
        description: { pl: '', en: '' }
    };
}

const form = useForm(initForm());

const breadcrumbs = [
    { label: t('admin.dashboard.title', 'Dashboard'), url: route('admin.dashboard.index'), icon: markRaw(PhHouse) },
    { label: t('admin.menu.taxonomies', 'Taxonomies') }
];

const columns = [
    { key: 'id', label: t('admin.common.id', 'ID'), sortable: true },
    { key: 'title', label: t('admin.taxonomy.title', 'Title'), sortable: false },
    { key: 'slug', label: t('admin.taxonomy.slug', 'Slug'), sortable: false },
    { key: 'order', label: t('admin.taxonomy.order', 'Order'), sortable: true },
    { key: 'actions', label: t('admin.common.actions', 'Actions'), sortable: false, align: 'right' }
];

const types = [
    { id: 'category', label: t('admin.taxonomy.type_category', 'Categories'), icon: markRaw(PhFileText) },
    { id: 'tag', label: t('admin.taxonomy.type_tag', 'Tags'), icon: markRaw(PhTag) }
];

function setType(typeId) {
    router.get(route('admin.taxonomies.index'), { type: typeId }, { preserveState: true });
}

function openCreate() {
    form.reset();
    form.type = props.currentType;
    editingTaxonomy.value = null;
    isCreating.value = true;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function openEdit(item) {
    editingTaxonomy.value = item;
    form.type = item.type;
    form.order = item.order;
    form.color = item.color;
    form.icon = item.icon;
    
    // Handle translatable fields
    locales.forEach(lang => {
        form.title[lang] = item.title[lang] || '';
        form.slug[lang] = item.slug[lang] || '';
        form.description[lang] = item.description[lang] || '';
    });

    isCreating.value = true;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function submit() {
    if (editingTaxonomy.value) {
        form.put(route('admin.taxonomies.update', editingTaxonomy.value.id), {
            onSuccess: () => {
                isCreating.value = false;
                form.reset();
            }
        });
    } else {
        form.post(route('admin.taxonomies.store'), {
            onSuccess: () => {
                isCreating.value = false;
                form.reset();
            }
        });
    }
}

function deleteTaxonomy(item) {
    router.delete(route('admin.taxonomies.destroy', item.id));
}
</script>

<template>
    <Head :title="t('admin.taxonomy.management', 'Taxonomy Management')" />
    <AdminLayout>
        <!-- Tabs for Types -->
        <div class="tabs tabs-boxed mb-6 bg-base-200/50 p-1 w-fit">
            <button 
                v-for="type in types" 
                :key="type.id"
                @click="setType(type.id)"
                class="tab rounded-lg px-6 flex items-center gap-2 transition-all"
                :class="{ 'tab-active bg-primary text-white shadow-md': currentType === type.id }"
            >
                <component :is="type.icon" weight="regular" class="w-4 h-4" />
                {{ type.label }}
            </button>
        </div>

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
                        class="btn btn-xs rounded-lg px-4 border-none shadow-none uppercase"
                        :class="currentLocale === lang ? 'btn-primary' : 'btn-ghost opacity-50'">
                        {{ lang }}
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-bold opacity-50">{{ t('admin.taxonomy.title_label', 'Title') }} ({{ currentLocale.toUpperCase() }})</span></label>
                        <input type="text" v-model="form.title[currentLocale]" class="input input-bordered w-full rounded-xl bg-base-200/50" required />
                    </div>
                    
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-bold opacity-50">{{ t('admin.taxonomy.slug_label', 'Slug') }} ({{ currentLocale.toUpperCase() }})</span></label>
                        <input type="text" v-model="form.slug[currentLocale]" class="input input-bordered w-full rounded-xl bg-base-200/50" />
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
                    <span class="font-bold">{{ item.title[currentLocale] || item.title['pl'] }}</span>
                </div>
            </template>

            <template #cell-slug="{ item }">
                <span class="text-xs font-mono opacity-50">{{ item.slug[currentLocale] || item.slug['pl'] }}</span>
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
