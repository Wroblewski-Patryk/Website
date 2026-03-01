<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps(['pages']);
const deleteForm = useForm({});

function deletePage(id) {
    if (confirm('Are you sure you want to delete this page?')) {
        deleteForm.delete(`/admin/pages/${id}`);
    }
}
</script>

<template>
    <Head title="Manage Pages" />
    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-black tracking-tight flex items-center gap-3">
                        <i class="fas fa-file-alt text-primary"></i>
                        Pages
                    </h1>
                    <p class="text-sm opacity-50 mt-1">Manage your website pages and visual content.</p>
                </div>
                <Link href="/admin/pages/create" class="btn btn-primary px-6 shadow-lg shadow-primary/20">
                    <i class="fas fa-plus mr-2"></i> Create Page
                </Link>
            </div>
        </template>

        <div class="bg-base-100 rounded-box shadow-sm border border-base-300 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="page in pages.data" :key="page.id">
                            <td>{{ page.id }}</td>
                            <!-- Spatie translates into arrays, so we show 'pl' or raw fallback -->
                            <td class="font-medium">{{ page.title?.pl || page.title?.en || page.title }}</td>
                            <td><span class="text-xs font-mono bg-base-200 p-1 rounded">{{ page.slug?.pl || page.slug?.en || page.slug }}</span></td>
                            <td>{{ new Date(page.created_at).toLocaleDateString() }}</td>
                            <td class="flex gap-2">
                                <Link :href="`/admin/pages/${page.id}/edit`" class="btn btn-sm btn-ghost">Edit</Link>
                                <button @click="deletePage(page.id)" class="btn btn-sm btn-error btn-outline">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="pages.data.length === 0">
                            <td colspan="5" class="text-center py-8 text-base-content/50">No pages found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="join mt-4 flex justify-center" v-if="pages.links.length > 3">
                <Link 
                    v-for="(link, k) in pages.links" 
                    :key="k" 
                    :href="link.url || '#'" 
                    class="join-item btn btn-sm"
                    :class="{'btn-active': link.active, 'btn-disabled': !link.url}"
                    v-html="link.label" />
            </div>
        </div>
    </AdminLayout>
</template>
