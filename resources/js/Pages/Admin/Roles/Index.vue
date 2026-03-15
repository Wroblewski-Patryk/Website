<script setup>
import { ref, markRaw } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { PhPencilSimple, PhTrash, PhHouse, PhShield } from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ResourceTable from '@/features/admin/shared/components/ResourceTable.vue';
import { useTranslations } from '@/Composables/useTranslations';

const props = defineProps(['roles']);
const tableRef = ref(null);
const deleteForm = useForm({});
const { t } = useTranslations();

const breadcrumbs = [
    { label: t('admin.dashboard.title', 'Dashboard'), url: route('admin.dashboard.index'), icon: markRaw(PhHouse) },
    { label: t('admin.users.title', 'Users'), url: route('admin.users.index') },
    { label: t('admin.menu.roles', 'Roles') }
];

const columns = [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'name', label: t('admin.common.name', 'Name'), sortable: true },
    { key: 'permissions_count', label: t('admin.roles.permissions', 'Permissions'), sortable: false },
    { key: 'actions', label: t('admin.common.actions', 'Actions'), sortable: false, align: 'right' }
];

function deleteRole(item) {
    if (item.name === 'admin') {
        alert(t('admin.roles.cannot_delete_admin', 'You cannot delete the admin role.'));
        return;
    }
    if (confirm(t('admin.roles.delete_confirm', 'Are you sure you want to delete this role?'))) {
        deleteForm.delete(route('admin.roles.destroy', item.id), {
            preserveScroll: true
        });
    }
}
</script>

<template>
    <Head :title="t('admin.menu.roles', 'Roles')" />
    <AdminLayout>
        <ResourceTable
            :title="t('admin.menu.roles', 'Roles')"
            :description="t('admin.roles.desc', 'Manage system roles and their permissions.')"
            :icon="markRaw(PhShield)"
            :breadcrumbs="breadcrumbs"
            :resources="roles"
            :columns="columns"
            :create-route="route('admin.roles.create')"
            :create-label="t('admin.common.create', 'Create')"
            persistence-key="roles"
            ref="tableRef"
            @delete-confirmed="deleteRole"
        >
            <template #cell-name="{ item }">
                <span class="font-bold flex items-center gap-2">
                    {{ item.name }}
                    <div v-if="item.name === 'admin'" class="badge badge-primary badge-sm">System</div>
                </span>
            </template>
            
            <template #cell-permissions_count="{ item }">
                <div class="flex flex-wrap gap-1 max-w-[400px]">
                    <span v-for="p in item.permissions" :key="p.id" class="badge badge-ghost badge-sm opacity-80">
                        {{ p.name }}
                    </span>
                    <span v-if="item.permissions.length === 0" class="text-xs text-base-content/50 italic">
                        {{ t('admin.roles.no_permissions', 'No permissions') }}
                    </span>
                </div>
            </template>

            <template #cell-actions="{ item }">
                <div class="flex justify-end gap-2">
                    <Link :href="route('admin.roles.edit', item.id)" class="btn btn-sm btn-ghost btn-square hover:bg-primary/10 hover:text-primary transition-all">
                        <PhPencilSimple weight="regular" class="w-4 h-4" />
                    </Link>
                    <button v-if="item.name !== 'admin'" @click="deleteRole(item)" class="btn btn-sm btn-ghost btn-square hover:bg-error/10 hover:text-error transition-all">
                        <PhTrash weight="regular" class="w-4 h-4" />
                    </button>
                </div>
            </template>
        </ResourceTable>
    </AdminLayout>
</template>
