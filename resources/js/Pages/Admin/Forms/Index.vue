<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps(['forms']);

function deleteForm(id) {
    if (confirm('Are you sure you want to delete this form?')) {
        router.delete(`/admin/forms/${id}`);
    }
}
</script>

<template>
    <Head title="Forms Management" />
    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center text-base-content">
                <div>
                    <h1 class="text-3xl font-black tracking-tight flex items-center gap-3">
                        <i class="fas fa-wpforms text-primary"></i>
                        Forms
                    </h1>
                    <p class="text-sm opacity-50 mt-1">Configure and manage user-facing forms.</p>
                </div>
                <Link href="/admin/forms/create" class="btn btn-primary rounded-full px-6 shadow-lg shadow-primary/20">
                    <i class="fas fa-plus mr-2"></i> Create New Form
                </Link>
            </div>
        </template>

        <div class="bg-base-100 rounded-box shadow-sm border border-base-300 overflow-hidden">
                <table class="table w-full">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="form in forms" :key="form.id" class="hover:bg-base-200/50 transition-colors">
                                    <td>
                                        <div class="font-bold">{{ form.name }}</div>
                                        <div class="text-xs opacity-50">ID: {{ form.id }}</div>
                                    </td>
                                    <td>
                                        <span :class="['badge badge-sm', form.is_published ? 'badge-success' : 'badge-ghost']">
                                            {{ form.is_published ? 'Published' : 'Draft' }}
                                        </span>
                                    </td>
                                    <td>{{ new Date(form.created_at).toLocaleDateString() }}</td>
                                    <td class="text-right">
                                        <div class="flex justify-end gap-2">
                                            <Link :href="`/admin/forms/${form.id}/edit`" class="btn btn-ghost btn-xs text-primary">Edit</Link>
                                            <button @click="deleteForm(form.id)" class="btn btn-ghost btn-xs text-error">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="forms.length === 0">
                                    <td colspan="4" class="text-center py-8 opacity-50 italic text-sm">
                                        No forms found. Create your first one!
                                    </td>
                                </tr>
            </tbody>
        </table>
    </div>
</AdminLayout>
</template>
