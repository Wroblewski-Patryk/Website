<script setup>
import { markRaw } from 'vue';
import { PhHouse, PhCube } from '@phosphor-icons/vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ModuleHeader from '@/Components/Admin/ModuleHeader.vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();

const breadcrumbs = [
    { label: t('admin.dashboard.title', 'Dashboard'), url: route('admin.dashboard.index'), icon: markRaw(PhHouse) },
    { label: t('admin.blocks.title', 'Blocks') }
];

</script>

<template>
    <Head :title="t('admin.blocks.title', 'Blocks')" />
    <AdminLayout>
        <template #header>
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
        </template>

        <div class="space-y-6 mt-6">
            <div class="card bg-base-100 shadow-sm border border-base-200">
                <div class="card-body">
                    <p class="text-base-content/70 italic text-center py-10">
                        {{ t('admin.blocks.empty_state', 'No block defaults configured yet. Select a block to begin editing its default structural properties.') }}
                    </p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
