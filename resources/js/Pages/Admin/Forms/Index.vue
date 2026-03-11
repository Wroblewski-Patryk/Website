<script setup>
import { ref, markRaw } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { PhPencilSimple, PhTrash, PhHouse, PhTextbox, PhEye, PhCircle, PhClock, PhCheckCircle, PhFileText, PhArchive } from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ResourceTable from '@/Components/ResourceTable.vue';

const props = defineProps(['forms']);
const tableRef = ref(null);
const deleteForm = useForm({});

const breadcrumbs = [
    { label: 'Dashboard', url: route('admin.dashboard.index'), icon: markRaw(PhHouse) },
    { label: 'Forms' }
];

const columns = [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'title', label: 'Title', sortable: true },
    { key: 'status', label: 'Status', sortable: true },
    { key: 'created_at', label: 'Created', sortable: true, optional: true },
    { key: 'updated_at', label: 'Edited', sortable: true, optional: true },
    { key: 'published_at', label: 'Published', sortable: true, optional: true },
    { key: 'actions', label: 'Actions', sortable: false, align: 'right' }
];

function deleteFormItem(item) {
    deleteForm.delete(route('admin.forms.destroy', item.id));
}
</script>

<template>
    <Head title="Manage Forms" />
    <AdminLayout>
        <ResourceTable
            title="Forms"
            description="Capture leads and feedback with custom-built forms."
            :icon="markRaw(PhTextbox)"
            :breadcrumbs="breadcrumbs"
            :resources="forms"
            :columns="columns"
            :create-route="route('admin.forms.create')"
            create-label="Create Form"
            persistence-key="forms"
            ref="tableRef"
            @delete-confirmed="deleteFormItem"
        >
            <template #cell-title="{ item }">
                <Link :href="route('admin.forms.edit', item.id)" class="font-medium hover:text-primary transition-colors">
                    {{ item.title }}
                </Link>
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
                <div v-else class="flex items-center gap-2 opacity-40 text-xs">
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
                    <Link :href="route('forms.preview', item.id)" target="_blank" class="btn btn-sm btn-ghost btn-square hover:bg-info/10 hover:text-info transition-all" title="Preview Form">
                        <PhEye weight="bold" class="w-4 h-4" />
                    </Link>
                    <Link :href="route('admin.forms.edit', item.id)" class="btn btn-sm btn-ghost btn-square hover:bg-primary/10 hover:text-primary transition-all">
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
