<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ResourceTable from '@/Components/ResourceTable.vue';

const props = defineProps(['templates']);
const tableRef = ref(null);
const deleteForm = useForm({});

const breadcrumbs = [
    { label: 'Admin', url: '/admin', icon: 'fas fa-home' },
    { label: 'Templates' }
];

const columns = [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'name', label: 'Name', sortable: true },
    { key: 'type', label: 'Type', sortable: true, optional: true },
    { key: 'is_active', label: 'Status', sortable: true, optional: true },
    { key: 'actions', label: 'Actions', sortable: false, align: 'right' }
];

function deleteTemplate(item) {
    deleteForm.delete(`/admin/templates/${item.id}`);
}
</script>

<template>
    <Head title="Manage Templates" />
    <AdminLayout>
        <ResourceTable
            title="Templates"
            description="Reusable layouts for headers, footers, and global sections."
            icon="fas fa-layer-group"
            :breadcrumbs="breadcrumbs"
            :resources="templates"
            :columns="columns"
            create-route="/admin/templates/create"
            create-label="Create Template"
            persistence-key="templates"
            ref="tableRef"
            @delete-confirmed="deleteTemplate"
        >
            <template #cell-type="{ item }">
                <div class="badge badge-neutral capitalize font-bold bg-base-300 border-none px-3">
                    {{ item.type }}
                </div>
            </template>

            <template #cell-actions="{ item }">
                <div class="flex justify-end gap-2">
                    <Link :href="`/admin/templates/${item.id}/edit`" class="btn btn-sm btn-ghost btn-square hover:bg-primary/10 hover:text-primary transition-all">
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
