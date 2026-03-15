<script setup>
import { ref, markRaw } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { PhPencilSimple, PhTrash, PhHouse, PhUsers } from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ResourceTable from '@/features/admin/shared/components/ResourceTable.vue';
import { useTranslations } from '@/Composables/useTranslations';

const props = defineProps(['users_list']);
const tableRef = ref(null);
const deleteForm = useForm({});
const { t } = useTranslations();

const breadcrumbs = [
    { label: t('admin.dashboard.title', 'Dashboard'), url: route('admin.dashboard.index'), icon: markRaw(PhHouse) },
    { label: t('admin.users.title', 'Users') }
];

const columns = [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'name', label: t('admin.users.full_name', 'Full Name'), sortable: true },
    { key: 'email', label: t('admin.users.email_address', 'Email Address'), sortable: true },
    { key: 'created_at', label: t('admin.users.created_at', 'Created At'), sortable: true, optional: true },
    { key: 'actions', label: t('admin.common.actions', 'Actions'), sortable: false, align: 'right' }
];

function deleteUser(item) {
    if (confirm(t('admin.users.delete_confirm', 'Are you sure you want to delete this user?'))) {
        deleteForm.delete(route('admin.users.destroy', item.id), {
            preserveScroll: true
        });
    }
}
</script>

<template>
    <Head :title="t('admin.users.title', 'Users')" />
    <AdminLayout>
        <ResourceTable
            :title="t('admin.users.title', 'Users')"
            :description="t('admin.users.desc', 'Manage user accounts with access to the panel.')"
            :icon="markRaw(PhUsers)"
            :breadcrumbs="breadcrumbs"
            :resources="users_list"
            :columns="columns"
            :create-route="route('admin.users.create')"
            :create-label="t('admin.common.create', 'Create')"
            persistence-key="users"
            ref="tableRef"
            @delete-confirmed="deleteUser"
        >
            <template #cell-name="{ item }">
                <span class="font-bold">{{ item.name }}</span>
            </template>
            
            <template #cell-email="{ item }">
                <span class="text-xs font-mono bg-base-200 p-1 px-2 rounded-lg opacity-80">
                    {{ item.email }}
                </span>
            </template>

            <template #cell-actions="{ item }">
                <div class="flex justify-end gap-2">
                    <Link :href="route('admin.users.edit', item.id)" class="btn btn-sm btn-ghost btn-square hover:bg-primary/10 hover:text-primary transition-all">
                        <PhPencilSimple weight="regular" class="w-4 h-4" />
                    </Link>
                    <button @click="deleteUser(item)" class="btn btn-sm btn-ghost btn-square hover:bg-error/10 hover:text-error transition-all">
                        <PhTrash weight="regular" class="w-4 h-4" />
                    </button>
                </div>
            </template>
        </ResourceTable>
    </AdminLayout>
</template>
