<script setup>
import { ref, markRaw } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { PhPencilSimple, PhTrash, PhCircle, PhHouse, PhFeather, PhEye, PhClock, PhCheckCircle, PhFileText, PhArchive } from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ResourceTable from '@/Components/ResourceTable.vue';

const props = defineProps(['posts']);
const tableRef = ref(null);
const deleteForm = useForm({});

const breadcrumbs = [
    { label: 'Dashboard', url: route('admin.dashboard.index'), icon: markRaw(PhHouse) },
    { label: 'Posts' }
];

const columns = [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'title', label: 'Title', sortable: true },
    { key: 'slug', label: 'Slug', sortable: true, optional: true },
    { key: 'status', label: 'Status', sortable: true },
    { key: 'created_at', label: 'Created', sortable: true, optional: true },
    { key: 'updated_at', label: 'Edited', sortable: true, optional: true },
    { key: 'published_at', label: 'Published', sortable: true, optional: true },
    { key: 'actions', label: 'Actions', sortable: false, align: 'right' }
];

function deletePost(item) {
    deleteForm.delete(route('admin.posts.destroy', item.id));
}
</script>

<template>
    <Head title="Manage Posts" />
    <AdminLayout>
        <ResourceTable
            title="Posts"
            description="Share your thoughts and news with the world."
            :icon="markRaw(PhFeather)"
            :breadcrumbs="breadcrumbs"
            :resources="posts"
            :columns="columns"
            :create-route="route('admin.posts.create')"
            create-label="Create Post"
            persistence-key="posts"
            ref="tableRef"
            @delete-confirmed="deletePost"
        >
            <template #cell-title="{ item }">
                <Link :href="route('admin.posts.edit', item.id)" class="font-medium hover:text-primary transition-colors">
                    {{ item.title?.pl || item.title?.en || item.title }}
                </Link>
            </template>

            <template #cell-slug="{ item }">
                <span class="text-xs font-mono opacity-50">{{ item.slug }}</span>
            </template>

            <template #cell-status="{ item }">
                <div v-if="item.status === 'published'" class="flex items-center gap-2 text-success text-xs">
                    <PhCheckCircle weight="fill" class="w-3.5 h-3.5" />
                    Published
                </div>
                <div v-else-if="item.status === 'planned'" class="flex items-center gap-2 text-info text-xs">
                    <PhClock weight="fill" class="w-3.5 h-3.5" />
                    Planned
                </div>
                <div v-else-if="item.status === 'archived'" class="flex items-center gap-2 text-error text-xs">
                    <PhArchive weight="fill" class="w-3.5 h-3.5" />
                    Archived
                </div>
                <div v-else class="flex items-center gap-2 opacity-40 text-xs text-base-content">
                    <PhFileText weight="fill" class="w-3.5 h-3.5" />
                    Draft
                </div>
            </template>

            <template #cell-published_at="{ item }">
                <div class="flex items-center gap-2" :class="new Date(item.published_at) > new Date() ? 'text-info font-bold' : 'opacity-60'">
                    <PhClock v-if="new Date(item.published_at) > new Date()" weight="bold" class="w-3 h-3" />
                    <span class="text-xs">{{ item.published_at ? new Date(item.published_at).toLocaleDateString() : '-' }}</span>
                </div>
            </template>

            <template #cell-actions="{ item }">
                <div class="flex justify-end gap-2">
                    <a :href="`/blog/${item.slug}`" target="_blank" class="btn btn-sm btn-ghost btn-square hover:bg-info/10 hover:text-info transition-all" title="View Public Post">
                        <PhEye weight="regular" class="w-4 h-4" />
                    </a>
                    <Link :href="route('admin.posts.edit', item.id)" class="btn btn-sm btn-ghost btn-square hover:bg-primary/10 hover:text-primary transition-all">
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
