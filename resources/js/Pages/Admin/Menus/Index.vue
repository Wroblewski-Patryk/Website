<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps(['menus']);

function deleteMenu(id) {
    if (confirm('Are you sure you want to delete this menu?')) {
        router.delete(`/admin/menus/${id}`);
    }
}
</script>

<template>
    <Head title="Menus Management" />
    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center text-base-content">
                <div>
                    <h1 class="text-3xl font-black tracking-tight flex items-center gap-3">
                        <i class="fas fa-bars text-primary"></i>
                        Menus
                    </h1>
                    <p class="text-sm opacity-50 mt-1">Build and organize your site's navigation structures.</p>
                </div>
                <Link href="/admin/menus/create" class="btn btn-primary px-6 shadow-lg shadow-primary/20">
                    <i class="fas fa-plus mr-2"></i> Create New Menu
                </Link>
            </div>
        </template>

        <div class="bg-base-100 rounded-box shadow-sm border border-base-300 overflow-hidden">
            <table class="table w-full">
                <thead>
                    <tr class="border-b border-base-200">
                        <th class="bg-transparent text-[10px] uppercase tracking-widest opacity-40">Name</th>
                        <th class="bg-transparent text-[10px] uppercase tracking-widest opacity-40 text-center">Items</th>
                        <th class="bg-transparent text-[10px] uppercase tracking-widest opacity-40">Created At</th>
                        <th class="bg-transparent text-[10px] uppercase tracking-widest opacity-40 text-right">Actions</th>
                    </tr>
                </thead>
                            <tbody>
                                <tr v-for="menu in menus" :key="menu.id" class="hover:bg-primary/5 transition-colors border-b border-base-100/50">
                                    <td>
                                        <div class="font-bold text-base">{{ menu.name }}</div>
                                        <div class="text-[10px] opacity-30 font-mono tracking-tighter uppercase">UID: {{ menu.id }}</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="badge badge-primary badge-outline badge-sm rounded-full px-3 font-medium border-opacity-20 text-[10px]">
                                            {{ (menu.items || []).length }} LINKS
                                        </div>
                                    </td>
                                    <td class="text-xs opacity-60">
                                        {{ new Date(menu.created_at).toLocaleDateString() }}
                                    </td>
                                    <td class="text-right">
                                        <div class="flex justify-end gap-2">
                                            <Link :href="`/admin/menus/${menu.id}/edit`" class="btn btn-ghost btn-xs text-primary">Edit</Link>
                                            <button @click="deleteMenu(menu.id)" class="btn btn-ghost btn-xs text-error">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="menus.length === 0">
                                    <td colspan="4" class="text-center py-16 opacity-30 italic text-sm">
                                        <i class="fas fa-layer-group text-4xl mb-4 block"></i>
                                        No menus found. Create your first one!
                                    </td>
                                </tr>
            </tbody>
        </table>
    </div>
</AdminLayout>
</template>
