<script setup>
import { ref, markRaw } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { PhPencilSimple, PhTrash, PhHouse, PhUsers } from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ResourceTable from '@/Components/ResourceTable.vue';

const props = defineProps(['users']);
const tableRef = ref(null);
const deleteForm = useForm({});

const breadcrumbs = [
    { label: 'Admin', url: '/admin', icon: markRaw(PhHouse) },
    { label: 'Użytkownicy' }
];

const columns = [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'name', label: 'Imię i nazwisko', sortable: true },
    { key: 'email', label: 'E-mail', sortable: true },
    { key: 'created_at', label: 'Data dodania', sortable: true, optional: true },
    { key: 'actions', label: 'Akcje', sortable: false, align: 'right' }
];

function deleteUser(item) {
    if (confirm('Czy na pewno chcesz usunąć tego użytkownika?')) {
        deleteForm.delete(`/admin/users/${item.id}`, {
            preserveScroll: true
        });
    }
}
</script>

<template>
    <Head title="Zarządzaj użytkownikami" />
    <AdminLayout>
        <ResourceTable
            title="Użytkownicy"
            description="Zarządzaj kontami użytkowników z dostępem do panelu."
            :icon="markRaw(PhUsers)"
            :breadcrumbs="breadcrumbs"
            :resources="users"
            :columns="columns"
            create-route="/admin/users/create"
            create-label="Dodaj użytkownika"
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
                    <Link :href="`/admin/users/${item.id}/edit`" class="btn btn-sm btn-ghost btn-square hover:bg-primary/10 hover:text-primary transition-all">
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
