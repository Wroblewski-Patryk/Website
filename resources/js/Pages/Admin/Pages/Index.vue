<script setup>
import { ref, markRaw, computed } from 'vue';
import { Head, useForm, Link, usePage } from '@inertiajs/vue3';
import { PhPencilSimple, PhTrash, PhHouse, PhFileText, PhEye, PhCircle, PhClock, PhCheckCircle, PhArchive } from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ResourceTable from '@/features/admin/shared/components/ResourceTable.vue';
import { useTranslations } from '@/Composables/useTranslations';
import { useFormatter } from '@/Composables/useFormatter';

const { t } = useTranslations();
const { formatDate } = useFormatter();

const props = defineProps(['pages']);
const activeLocale = computed(() => usePage().props.locale);
const tableRef = ref(null);
const deleteForm = useForm({});

const breadcrumbs = [
    { label: t('admin.dashboard.title', 'Dashboard'), url: route('admin.dashboard.index'), icon: markRaw(PhHouse) },
    { label: t('admin.menu.pages', 'Pages') }
];

const columns = [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'title', label: t('admin.pages.title_field', 'Title'), sortable: true },
    { key: 'slug', label: t('admin.pages.slug_field', 'Slug'), sortable: true, optional: true },
    { key: 'status', label: t('admin.pages.status_field', 'Status'), sortable: true },
    { key: 'created_at', label: t('admin.common.created', 'Created'), sortable: true, optional: true },
    { key: 'updated_at', label: t('admin.common.edited', 'Edited'), sortable: true, optional: true },
    { key: 'published_at', label: t('admin.common.published', 'Published'), sortable: true, optional: true },
    { key: 'actions', label: t('admin.common.actions', 'Actions'), sortable: false, align: 'right' }
];

function deletePage(item) {
    deleteForm.delete(route('admin.pages.destroy', item.id));
}
</script>

<template>
    <Head :title="t('admin.pages.management_title', 'Manage Pages')" />
    <AdminLayout>
        <ResourceTable
            :title="t('admin.menu.pages', 'Pages')"
            :description="t('admin.pages.description', 'Manage your website pages and visual content.')"
            :icon="markRaw(PhFileText)"
            :breadcrumbs="breadcrumbs"
            :resources="pages"
            :columns="columns"
            :create-route="route('admin.pages.create')"
            :create-label="t('admin.pages.create_btn', 'Create Page')"
            :bulk-route="route('admin.pages.bulk-action')"
            persistence-key="pages"
            ref="tableRef"
            @delete-confirmed="deletePage"
        >
            <template #cell-title="{ item }">
                <Link :href="route('admin.pages.edit', item.id)" class="font-medium hover:text-primary transition-colors">
                    {{ t(item.title) }}
                </Link>
            </template>
            
            <template #cell-slug="{ item }">
                <span class="text-xs font-mono bg-base-200 p-1 px-2 rounded-lg opacity-70 group-hover:opacity-100 transition-opacity">
                    {{ t(item.slug) }}
                </span>
            </template>

            <template #cell-status="{ item }">
                <div v-if="item.status === 'published'" class="flex items-center gap-2 text-success text-xs">
                    <PhCheckCircle weight="fill" class="w-3.5 h-3.5" />
                    {{ t('admin.pages.status_published', 'Published') }}
                </div>
                <div v-else-if="item.status === 'planned'" class="flex items-center gap-2 text-info text-xs">
                    <PhClock weight="fill" class="w-3.5 h-3.5" />
                    {{ t('admin.pages.status_planned', 'Planned') }}
                </div>
                <div v-else-if="item.status === 'archived'" class="flex items-center gap-2 text-error text-xs">
                    <PhArchive weight="fill" class="w-3.5 h-3.5" />
                    {{ t('admin.common.archived', 'Archived') }}
                </div>
                <div v-else class="flex items-center gap-2 opacity-40 text-xs text-base-content">
                    <PhFileText weight="fill" class="w-3.5 h-3.5" />
                    {{ t('admin.pages.status_draft', 'Draft') }}
                </div>
            </template>

            <template #cell-published_at="{ item }">
                <div class="flex items-center gap-2" :class="new Date(item.published_at) > new Date() ? 'text-info font-bold' : 'opacity-60'">
                    <PhClock v-if="new Date(item.published_at) > new Date()" weight="bold" class="w-3 h-3" />
                    <span class="text-xs">{{ item.published_at ? formatDate(item.published_at) : '-' }}</span>
                </div>
            </template>

            <template #cell-actions="{ item }">
                <div class="flex justify-end gap-2 text-center">
                    <a :href="`/${activeLocale}/${t(item.slug)}`" target="_blank" class="btn btn-sm btn-ghost btn-square hover:bg-info/10 hover:text-info transition-all" :title="t('admin.common.preview', 'Preview')">
                        <PhEye weight="regular" class="w-4 h-4" />
                    </a>
                    <Link :href="route('admin.pages.edit', item.id)" class="btn btn-sm btn-ghost btn-square hover:bg-primary/10 hover:text-primary transition-all" :title="t('admin.common.edit', 'Edit')">
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
