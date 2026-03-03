<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ResourceTable from '@/Components/ResourceTable.vue';

const props = defineProps(['projects']);
const tableRef = ref(null);
const deleteForm = useForm({});

const breadcrumbs = [
    { label: 'Admin', url: '/admin', icon: 'fas fa-home' },
    { label: 'Projects' }
];

const columns = [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'title', label: 'Title', sortable: true },
    { key: 'category', label: 'Category', sortable: true, optional: true },
    { key: 'order', label: 'Order', sortable: true, optional: true },
    { key: 'actions', label: 'Actions', sortable: false, align: 'right' }
];

function deleteProject(item) {
    deleteForm.delete(`/admin/projects/${item.id}`);
}
</script>

<template>
    <Head title="Manage Projects" />
    <AdminLayout>
        <ResourceTable
            title="Projects"
            description="Showcase your best work in a curated portfolio."
            icon="fas fa-project-diagram"
            :breadcrumbs="breadcrumbs"
            :resources="projects"
            :columns="columns"
            create-route="/admin/projects/create"
            create-label="Add Project"
            persistence-key="projects"
            ref="tableRef"
            @delete-confirmed="deleteProject"
        >
            <template #cell-title="{ item }">
                <div class="font-bold">{{ item.title?.pl || item.title?.en || item.title }}</div>
                <div class="text-xs opacity-50">{{ item.slug }}</div>
            </template>

            <template #cell-category="{ item }">
                <div class="badge badge-outline opacity-50 uppercase text-[10px] font-black tracking-wider">
                    {{ item.category || 'Uncategorized' }}
                </div>
            </template>

            <template #cell-actions="{ item }">
                <div class="flex justify-end gap-2">
                    <Link :href="`/admin/projects/${item.id}/edit`" class="btn btn-sm btn-ghost btn-square hover:bg-primary/10 hover:text-primary transition-all">
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
