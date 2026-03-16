<script setup>
import { ref, markRaw, computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { PhPencilSimple, PhTrash, PhHouse, PhUsers, PhGlobe, PhCheckCircle, PhXCircle, PhList, PhPlus } from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ResourceTable from '@/features/admin/shared/components/ResourceTable.vue';
import { useTranslations } from '@/Composables/useTranslations';
import { useFormatter } from '@/Composables/useFormatter';

const { t } = useTranslations();
const { formatDate } = useFormatter();

const props = defineProps(['clients']);
const activeLocale = computed(() => usePage().props.locale);
const tableRef = ref(null);
const deleteForm = useForm({});

const breadcrumbs = [
    { label: t('admin.dashboard.title', 'Dashboard'), url: route('admin.dashboard.index'), icon: markRaw(PhHouse) },
    { label: t('admin.menu.projects', 'Projects'), url: route('admin.projects.index') },
    { label: t('admin.menu.clients', 'Clients') }
];

const columns = [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'title', label: t('admin.clients.name_field', 'Name'), sortable: true },
    { key: 'website_url', label: t('admin.clients.website_field', 'Website'), sortable: true, optional: true },
    { key: 'is_active', label: t('admin.common.status', 'Status'), sortable: true },
    { key: 'created_at', label: t('admin.common.created', 'Created'), sortable: true, optional: true },
    { key: 'actions', label: t('admin.common.actions', 'Actions'), sortable: false, align: 'right' }
];

function deleteClient(item) {
    deleteForm.delete(route('admin.projects.clients.destroy', item.id));
}
</script>

<template>
    <Head :title="t('admin.clients.management_title', 'Manage Clients')" />
    <AdminLayout>
        <ResourceTable
            :title="t('admin.menu.clients', 'Clients')"
            :description="t('admin.clients.description', 'Manage your partners and clients.')"
            :icon="markRaw(PhUsers)"
            :breadcrumbs="breadcrumbs"
            :resources="clients"
            :columns="columns"
            :create-route="route('admin.projects.clients.create')"
            :create-label="t('admin.clients.create_btn', 'Add Client')"
            persistence-key="clients"
            ref="tableRef"
            @delete-confirmed="deleteClient"
        >
            <template #cell-title="{ item }">
                <div class="flex items-center gap-3">
                    <div v-if="item.logo" class="w-8 h-8 rounded-lg bg-base-200 flex items-center justify-center overflow-hidden">
                        <img :src="item.logo" :alt="t(item.title)" class="w-full h-full object-cover">
                    </div>
                    <Link :href="route('admin.projects.clients.edit', item.id)" class="font-medium hover:text-primary transition-colors">
                        {{ t(item.title) }}
                    </Link>
                </div>
            </template>

            <template #cell-website_url="{ item }">
                <a v-if="item.website_url" :href="item.website_url" target="_blank" class="text-xs link link-hover opacity-50 flex items-center gap-1">
                    <PhGlobe class="w-3 h-3" />
                    {{ item.website_url.replace(/https?:\/\//, '') }}
                </a>
                <span v-else class="opacity-20">-</span>
            </template>

            <template #cell-is_active="{ item }">
                <div v-if="item.is_active" class="flex items-center gap-2 text-success text-xs font-bold">
                    <PhCheckCircle weight="fill" class="w-3.5 h-3.5" />
                    {{ t('admin.common.active', 'Active') }}
                </div>
                <div v-else class="flex items-center gap-2 text-error text-xs opacity-50">
                    <PhXCircle weight="fill" class="w-3.5 h-3.5" />
                    {{ t('admin.common.inactive', 'Inactive') }}
                </div>
            </template>

            <template #cell-actions="{ item }">
                <div class="flex justify-end gap-2">
                    <Link :href="route('admin.projects.clients.edit', item.id)" class="btn btn-sm btn-ghost btn-square hover:bg-primary/10 hover:text-primary transition-all" :title="t('admin.common.edit', 'Edit')">
                        <PhPencilSimple weight="regular" class="w-4 h-4" />
                    </Link>
                    <button @click="tableRef?.openDeleteModal(item)" class="btn btn-sm btn-ghost btn-square hover:bg-error/10 hover:text-error transition-all" :title="t('admin.common.delete', 'Delete')">
                        <PhTrash weight="regular" class="w-4 h-4" />
                    </button>
                </div>
            </template>
        </ResourceTable>
    </AdminLayout>
</template>
