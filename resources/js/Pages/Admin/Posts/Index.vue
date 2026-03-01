<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps(['posts']);
const deleteForm = useForm({});

function deletePost(id) {
    if (confirm('Are you sure you want to delete this post?')) {
        deleteForm.delete(`/admin/posts/${id}`);
    }
}
</script>

<template>
    <Head title="Manage Posts" />
    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-black tracking-tight flex items-center gap-3">
                        <i class="fas fa-feather text-primary"></i>
                        Blog Posts
                    </h1>
                    <p class="text-sm opacity-50 mt-1">Share your thoughts and news with the world.</p>
                </div>
                <Link href="/admin/posts/create" class="btn btn-primary px-6 shadow-lg shadow-primary/20">
                    <i class="fas fa-plus mr-2"></i> Create Post
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
                            <th>Status</th>
                            <th>Published At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="post in posts.data" :key="post.id">
                            <td>{{ post.id }}</td>
                            <td class="font-medium">{{ post.title?.pl || post.title?.en || post.title }}</td>
                            <td>
                                <div class="badge" :class="post.is_published ? 'badge-success' : 'badge-ghost'">
                                    {{ post.is_published ? 'Published' : 'Draft' }}
                                </div>
                            </td>
                            <td>{{ post.published_at ? new Date(post.published_at).toLocaleDateString() : 'N/A' }}</td>
                            <td class="flex gap-2">
                                <Link :href="`/admin/posts/${post.id}/edit`" class="btn btn-sm btn-ghost">Edit</Link>
                                <button @click="deletePost(post.id)" class="btn btn-sm btn-error btn-outline">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="posts.data.length === 0">
                            <td colspan="5" class="text-center py-8 text-base-content/50">No posts found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="join mt-4 flex justify-center" v-if="posts.links.length > 3">
                <Link 
                    v-for="(link, k) in posts.links" 
                    :key="k" 
                    :href="link.url || '#'" 
                    class="join-item btn btn-sm"
                    :class="{'btn-active': link.active, 'btn-disabled': !link.url}"
                    v-html="link.label" />
            </div>
        </div>
    </AdminLayout>
</template>
