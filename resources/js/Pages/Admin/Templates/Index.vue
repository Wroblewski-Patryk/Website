<script setup>
import { ref, markRaw } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { PhPencilSimple, PhTrash, PhHouse, PhLayout } from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ResourceTable from '@/Components/ResourceTable.vue';

const props = defineProps(['templates']);
const tableRef = ref(null);
const deleteForm = useForm({});

const breadcrumbs = [
    { label: 'Dashboard', url: route('admin.dashboard.index'), icon: markRaw(PhHouse) },
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
    deleteForm.delete(route('admin.templates.destroy', item.id));
}
</script>

<template>
    <Head title="Manage Templates" />
    <AdminLayout>
        <ResourceTable
            title="Templates"
            description="Reusable layouts for headers, footers, and global sections."
            :icon="markRaw(PhLayout)"
            :breadcrumbs="breadcrumbs"
            :resources="templates"
            :columns="columns"
            :create-route="route('admin.templates.create')"
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
                    <Link :href="route('admin.templates.edit', item.id)" class="btn btn-sm btn-ghost btn-square hover:bg-primary/10 hover:text-primary transition-all">
                        <PhPencilSimple weight="regular" class="w-4 h-4" />
                    </Link>
                    <button @click="tableRef?.openDeleteModal(item)" class="btn btn-sm btn-ghost btn-square hover:bg-error/10 hover:text-error transition-all">
                        <PhTrash weight="regular" class="w-4 h-4" />
                    </button>
                </div>
            </template>
        </ResourceTable>
    </AdminLayout>
</template>
