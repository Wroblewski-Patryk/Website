<script setup>
import { markRaw, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { PhMagicWand, PhPencilSimple, PhTrash } from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ResourceTable from '@/features/admin/shared/components/ResourceTable.vue';
import { useTranslations } from '@/Composables/useTranslations';

const props = defineProps({
    animation_presets: Object,
});

const { t } = useTranslations();
const tableRef = ref(null);
const deleteForm = useForm({});

const columns = [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'name', label: t('admin.common.name', 'Name'), sortable: true },
    { key: 'slug', label: t('admin.common.url_slug', 'Slug'), sortable: true },
    { key: 'is_active', label: t('admin.common.status', 'Status'), sortable: true },
    { key: 'actions', label: t('admin.common.actions', 'Actions'), sortable: false, align: 'right' },
];

const deletePreset = (item) => {
    if (!confirm(t('admin.common.are_you_sure', 'Are you sure?'))) {
        return;
    }

    deleteForm.delete(route('admin.animation-presets.destroy', item.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="t('admin.menu.animation_presets', 'Animation Presets')" />
    <AdminLayout>
        <ResourceTable
            ref="tableRef"
            :title="t('admin.menu.animation_presets', 'Animation Presets')"
            :description="t('admin.builder.animation_presets_desc', 'Reusable GSAP animation definitions for block settings.')"
            :icon="markRaw(PhMagicWand)"
            :resources="animation_presets"
            :columns="columns"
            :create-route="route('admin.animation-presets.create')"
            :create-label="t('admin.common.add', 'Add New')"
            persistence-key="animation-presets"
            @delete-confirmed="deletePreset"
        >
            <template #cell-name="{ item }">
                <span class="font-bold">{{ item.name }}</span>
            </template>

            <template #cell-slug="{ item }">
                <span class="font-mono text-xs bg-base-200 px-2 py-1 rounded-lg">{{ item.slug }}</span>
            </template>

            <template #cell-is_active="{ item }">
                <span class="badge" :class="item.is_active ? 'badge-success' : 'badge-ghost'">
                    {{ item.is_active ? t('admin.common.active', 'Active') : t('admin.common.inactive', 'Inactive') }}
                </span>
            </template>

            <template #cell-actions="{ item }">
                <div class="flex justify-end gap-2">
                    <Link :href="route('admin.animation-presets.edit', item.id)" class="btn btn-sm btn-ghost btn-square">
                        <PhPencilSimple weight="regular" class="w-4 h-4" />
                    </Link>
                    <button class="btn btn-sm btn-ghost btn-square hover:text-error" @click="deletePreset(item)">
                        <PhTrash weight="regular" class="w-4 h-4" />
                    </button>
                </div>
            </template>
        </ResourceTable>
    </AdminLayout>
</template>
