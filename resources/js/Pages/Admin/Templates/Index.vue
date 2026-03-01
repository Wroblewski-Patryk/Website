<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps(['templates']);
const deleteForm = useForm({});

function deleteTemplate(id) {
    if (confirm('Are you sure you want to delete this template?')) {
        deleteForm.delete(`/admin/templates/${id}`);
    }
}
</script>

<template>
    <Head title="Manage Templates" />
    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center text-base-content">
                <div>
                    <h1 class="text-3xl font-black tracking-tight flex items-center gap-3">
                        <i class="fas fa-layer-group text-primary"></i>
                        Templates
                    </h1>
                    <p class="text-sm opacity-50 mt-1">Manage global headers and footers.</p>
                </div>
                <Link href="/admin/templates/create" class="btn btn-primary px-6 shadow-lg shadow-primary/20">
                    <i class="fas fa-plus mr-2"></i> Create Template
                </Link>
            </div>
        </template>

        <div class="bg-base-100 rounded-box shadow-sm border border-base-300 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="template in templates.data" :key="template.id">
                            <td>{{ template.id }}</td>
                            <td class="font-medium">{{ template.name }}</td>
                            <td>
                                <div class="badge badge-neutral capitalize">{{ template.type }}</div>
                            </td>
                            <td>{{ new Date(template.created_at).toLocaleDateString() }}</td>
                            <td class="flex gap-2">
                                <Link :href="`/admin/templates/${template.id}/edit`" class="btn btn-sm btn-ghost">Edit</Link>
                                <button @click="deleteTemplate(template.id)" class="btn btn-sm btn-error btn-outline">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="templates.data.length === 0">
                            <td colspan="5" class="text-center py-8 text-base-content/50">No templates found. Create a global header or footer.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="join mt-4 flex justify-center" v-if="templates.links.length > 3">
                <Link 
                    v-for="(link, k) in templates.links" 
                    :key="k" 
                    :href="link.url || '#'" 
                    class="join-item btn btn-sm"
                    :class="{'btn-active': link.active, 'btn-disabled': !link.url}"
                    v-html="link.label" />
            </div>
        </div>
    </AdminLayout>
</template>
