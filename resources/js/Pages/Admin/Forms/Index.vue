<script setup>
import { ref, markRaw, computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { PhPencilSimple, PhTrash, PhHouse, PhTextbox, PhEye, PhCircle, PhClock, PhCheckCircle, PhFileText, PhArchive } from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ResourceTable from '@/features/admin/shared/components/ResourceTable.vue';
import { useTranslations } from '@/Composables/useTranslations';
import { useFormatter } from '@/Composables/useFormatter';

const { t } = useTranslations();
const { formatDate } = useFormatter();

const props = defineProps(['forms']);
const activeLocale = computed(() => usePage().props.locale);
const tableRef = ref(null);
const deleteForm = useForm({});

const breadcrumbs = [
    { label: t('admin.dashboard.title', 'Dashboard'), url: route('admin.dashboard.index'), icon: markRaw(PhHouse) },
    { label: t('admin.menu.forms', 'Forms') }
];

const columns = [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'title', label: t('admin.forms.title_field', 'Title'), sortable: true },
    { key: 'status', label: t('admin.forms.status_field', 'Status'), sortable: true },
    { key: 'created_at', label: t('admin.common.created', 'Created'), sortable: true, optional: true },
    { key: 'updated_at', label: t('admin.common.edited', 'Edited'), sortable: true, optional: true },
    { key: 'published_at', label: t('admin.common.published', 'Published'), sortable: true, optional: true },
    { key: 'actions', label: t('admin.common.actions', 'Actions'), sortable: false, align: 'right' }
];

function deleteFormItem(item) {
    deleteForm.delete(route('admin.forms.destroy', item.id));
}
</script>

<template>
    <Head :title="t('admin.forms.management_title', 'Manage Forms')" />
    <AdminLayout>
        <ResourceTable
            :title="t('admin.menu.forms', 'Forms')"
            :description="t('admin.forms.description', 'Capture leads and feedback with custom-built forms.')"
            :icon="markRaw(PhTextbox)"
            :breadcrumbs="breadcrumbs"
            :resources="forms"
            :columns="columns"
            :create-route="route('admin.forms.create')"
            :create-label="t('admin.forms.create_btn', 'Create Form')"
            persistence-key="forms"
            ref="tableRef"
            @delete-confirmed="deleteFormItem"
        >
            <template #cell-title="{ item }">
                <Link :href="route('admin.forms.edit', item.id)" class="font-medium hover:text-primary transition-colors">
                    {{ t(item.title) }}
                </Link>
            </template>

            <template #cell-status="{ item }">
                <div v-if="item.status === 'published'" class="flex items-center gap-2 text-success text-xs">
                    <PhCheckCircle weight="fill" class="w-3.5 h-3.5" />
                    {{ t('admin.common.published', 'Published') }}
                </div>
                <div v-else-if="item.status === 'planned'" class="flex items-center gap-2 text-info text-xs">
                    <PhClock weight="fill" class="w-3.5 h-3.5" />
                    {{ t('admin.common.planned', 'Planned') }}
                </div>
                <div v-else-if="item.status === 'archived'" class="flex items-center gap-2 text-error text-xs">
                    <PhArchive weight="fill" class="w-3.5 h-3.5" />
                    {{ t('admin.common.archived', 'Archived') }}
                </div>
                <div v-else class="flex items-center gap-2 opacity-40 text-xs text-base-content">
                    <PhFileText weight="fill" class="w-3.5 h-3.5" />
                    {{ t('admin.common.draft', 'Draft') }}
                </div>
            </template>

            <template #cell-published_at="{ item }">
                <div class="flex items-center gap-2" :class="new Date(item.published_at) > new Date() ? 'text-info font-bold' : 'opacity-60'">
                    <PhClock v-if="new Date(item.published_at) > new Date()" weight="bold" class="w-3 h-3" />
                    <span class="text-xs">{{ item.published_at ? formatDate(item.published_at) : '-' }}</span>
                </div>
            </template>

            <template #cell-actions="{ item }">
                <div class="flex justify-end gap-2">
                    <Link :href="`/${activeLocale}/forms/${item.id}/preview`" target="_blank" class="btn btn-sm btn-ghost btn-square hover:bg-info/10 hover:text-info transition-all" :title="t('admin.common.preview', 'Preview')">
                        <PhEye weight="bold" class="w-4 h-4" />
                    </Link>
                    <Link :href="route('admin.forms.edit', item.id)" class="btn btn-sm btn-ghost btn-square hover:bg-primary/10 hover:text-primary transition-all" :title="t('admin.common.edit', 'Edit')">
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
