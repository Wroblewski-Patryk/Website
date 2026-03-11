<script setup>
import { markRaw } from 'vue';
import { PhHouse, PhCube } from '@phosphor-icons/vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ModuleHeader from '@/Components/Admin/ModuleHeader.vue';

const breadcrumbs = [
    { label: 'Dashboard', url: route('dashboard.index'), icon: markRaw(PhHouse) },
    { label: 'Blocks' }
];

</script>

<template>
    <Head title="Blocks" />
    <AdminLayout>
        <template #header>
            <ModuleHeader 
                title="Blocks" 
                description="Manage global block defaults and structures." 
                :breadcrumbs="breadcrumbs"
                :icon="markRaw(PhCube)">
                <template #actions>
                    <button class="btn btn-primary shadow-lg shadow-primary/20">
                        Create Block
                    </button>
                </template>
            </ModuleHeader>
        </template>

        <div class="space-y-6 mt-6">
            <div class="card bg-base-100 shadow-sm border border-base-200">
                <div class="card-body">
                    <p class="text-base-content/70 italictext-center py-10">No block defaults configured yet. Select a block to begin editing its default structural properties.</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
