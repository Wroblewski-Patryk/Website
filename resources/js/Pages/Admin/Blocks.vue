<script setup>
import { markRaw } from 'vue';
import { PhHouse, PhCube } from '@phosphor-icons/vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ModuleHeader from '@/features/admin/shared/components/ModuleHeader.vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();

const breadcrumbs = [
    { label: t('admin.dashboard.title', 'Dashboard'), url: route('admin.dashboard.index'), icon: markRaw(PhHouse) },
    { label: t('admin.blocks.title', 'Blocks') }
];

</script>

<template>

    <AdminLayout>
        <div class="space-y-6">
            <div class="bg-base-100 p-6 rounded-box shadow-sm border border-base-300">
                <ModuleHeader 
                    :title="t('admin.blocks.title', 'Blocks')" 
                    :description="t('admin.blocks.desc', 'Manage global block defaults and structures.')" 
                    :breadcrumbs="breadcrumbs"
                    :icon="markRaw(PhCube)">
                    <template #actions>
                        <button class="btn btn-primary shadow-lg shadow-primary/20">
                            {{ t('admin.blocks.create', 'Create Block') }}
                        </button>
                    </template>
                </ModuleHeader>
            </div>

        <div class="space-y-6 mt-6">
            <div class="card bg-base-100 shadow-sm border border-base-200">
                <div class="card-body">
                    <p class="text-base-content/70 italic text-center py-10">
                        {{ t('admin.blocks.empty_state', 'No block defaults configured yet. Select a block to begin editing its default structural properties.') }}
                    </p>
                </div>
            </div>
        </div>
        </div>
    </AdminLayout>
</template>
