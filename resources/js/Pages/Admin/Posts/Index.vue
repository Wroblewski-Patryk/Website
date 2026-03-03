<script setup>
import { ref } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ResourceTable from '@/Components/ResourceTable.vue';

const props = defineProps(['posts']);
const tableRef = ref(null);
const deleteForm = useForm({});

const breadcrumbs = [
    { label: 'Admin', url: '/admin', icon: 'fas fa-home' },
    { label: 'Posts' }
];

const columns = [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'title', label: 'Title', sortable: true },
    { key: 'is_published', label: 'Status', sortable: true, optional: true },
    { key: 'published_at', label: 'Published At', sortable: true, optional: true },
    { key: 'actions', label: 'Actions', sortable: false, align: 'right' }
];

function deletePost(item) {
    deleteForm.delete(`/admin/posts/${item.id}`);
}
</script>

<template>
    <Head title="Manage Posts" />
    <AdminLayout>
        <ResourceTable
            title="Posts"
            description="Share your thoughts and news with the world."
            icon="fas fa-feather"
            :breadcrumbs="breadcrumbs"
            :resources="posts"
            :columns="columns"
            create-route="/admin/posts/create"
            create-label="Create Post"
            persistence-key="posts"
            ref="tableRef"
            @delete-confirmed="deletePost"
        >
            <template #cell-is_published="{ item }">
                <div class="badge font-bold py-3 px-4 border-none shadow-sm" 
                     :class="item.is_published ? 'bg-success/10 text-success' : 'bg-base-200 text-base-content/40'">
                    <i class="fas fa-circle text-[6px] mr-2" :class="item.is_published ? 'animate-pulse' : ''"></i>
                    {{ item.is_published ? 'Published' : 'Draft' }}
                </div>
            </template>

            <template #cell-actions="{ item }">
                <div class="flex justify-end gap-2">
                    <Link :href="`/admin/posts/${item.id}/edit`" class="btn btn-sm btn-ghost btn-square hover:bg-primary/10 hover:text-primary transition-all">
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
