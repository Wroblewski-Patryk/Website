<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ResourceTable from '@/Components/ResourceTable.vue';

import { ref } from 'vue';
defineProps(['menus']);
const tableRef = ref(null);

const breadcrumbs = [
    { label: 'Admin', url: '/admin', icon: 'fas fa-home' },
    { label: 'Menus' }
];

const columns = [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'name', label: 'Name', sortable: true },
    { key: 'items_count', label: 'Items', sortable: false, align: 'center', optional: true },
    { key: 'created_at', label: 'Created At', sortable: true, optional: true },
    { key: 'actions', label: 'Actions', sortable: false, align: 'right' }
];

function deleteMenu(item) {
    router.delete(`/admin/menus/${item.id}`);
}
</script>

<template>
    <Head title="Menus Management" />
    <AdminLayout>
        <ResourceTable
            title="Menus"
            description="Build and organize your site's navigation structures."
            icon="fas fa-bars"
            :breadcrumbs="breadcrumbs"
            :resources="menus"
            :columns="columns"
            create-route="/admin/menus/create"
            create-label="Create Menu"
            persistence-key="menus"
            ref="tableRef"
            @delete-confirmed="deleteMenu"
        >
            <template #cell-items_count="{ item }">
                <div class="badge badge-primary badge-outline badge-sm rounded-full px-3 font-medium border-opacity-20 text-[10px]">
                    {{ (item.items || []).length }} LINKS
                </div>
            </template>

            <template #cell-actions="{ item }">
                <div class="flex justify-end gap-2">
                    <Link :href="`/admin/menus/${item.id}/edit`" class="btn btn-sm btn-ghost btn-square hover:bg-primary/10 hover:text-primary transition-all">
                        <i class="fas fa-edit text-xs"></i>
                    </Link>
                    <button @click="tableRef?.openDeleteModal(item)" class="btn btn-sm btn-ghost btn-square hover:bg-error/10 hover:text-error transition-all">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
            </template>
        </ResourceTable>
    </AdminLayout>
</template>
