<script setup>
import { markRaw, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { PhBracketsCurly, PhPencilSimple, PhTrash, PhHouse } from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ResourceTable from '@/features/admin/shared/components/ResourceTable.vue';
import { useTranslations } from '@/Composables/useTranslations';

const props = defineProps({
    composed_blocks: Object,
});

const { t } = useTranslations();
const tableRef = ref(null);
const deleteForm = useForm({});

const breadcrumbs = [
    { label: t('admin.dashboard.title', 'Dashboard'), url: route('admin.dashboard.index'), icon: markRaw(PhHouse) },
    { label: t('admin.blocks.title', 'Blocks') },
];

const columns = [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'title', label: t('admin.common.name', 'Name'), sortable: false },
    { key: 'slug', label: t('admin.common.url_slug', 'Slug'), sortable: true },
    { key: 'is_active', label: t('admin.common.status', 'Status'), sortable: true },
    { key: 'actions', label: t('admin.common.actions', 'Actions'), sortable: false, align: 'right' },
];

const deleteBlock = (item) => {
    deleteForm.delete(route('admin.blocks.destroy', item.id), {
        preserveScroll: true,
    });
};

const getTitle = (item) => {
    if (!item?.title) return 'Untitled';
    if (typeof item.title === 'string') return item.title;
    return item.title.en || item.title.pl || Object.values(item.title)[0] || 'Untitled';
};
</script>

<template>
    <Head :title="t('admin.blocks.title', 'Blocks')" />
    <AdminLayout>
        <ResourceTable
            ref="tableRef"
            :title="t('admin.blocks.title', 'Blocks')"
            :description="t('admin.composed_blocks.desc', 'Reusable multi-block compositions for the editor.')"
            :icon="markRaw(PhBracketsCurly)"
            :breadcrumbs="breadcrumbs"
            :resources="composed_blocks"
            :columns="columns"
            :create-route="route('admin.blocks.create')"
            :create-label="t('admin.common.add', 'Add New')"
            persistence-key="blocks"
            @delete-confirmed="deleteBlock"
        >
            <template #cell-title="{ item }">
                <span class="font-bold">{{ getTitle(item) }}</span>
            </template>

            <template #cell-slug="{ item }">
                <span class="font-mono text-xs bg-base-200 px-2 py-1 rounded-lg">{{ item.slug }}</span>
            </template>

            <template #cell-is_active="{ item }">
                <span class="badge" :class="item.is_active ? 'badge-success' : 'badge-ghost'">
                    {{ item.is_active ? t('admin.common.active', 'Active') : t('admin.common.inactive', 'Inactive') }}
                </span>
            </template>

            <template #cell-actions="{ item }">
                <div class="flex justify-end gap-2">
                    <Link :href="route('admin.blocks.edit', item.id)" class="btn btn-sm btn-ghost btn-square">
                        <PhPencilSimple weight="regular" class="w-4 h-4" />
                    </Link>
                    <button class="btn btn-sm btn-ghost btn-square hover:text-error" @click="tableRef?.openDeleteModal(item)">
                        <PhTrash weight="regular" class="w-4 h-4" />
                    </button>
                </div>
            </template>
        </ResourceTable>
    </AdminLayout>
</template>

