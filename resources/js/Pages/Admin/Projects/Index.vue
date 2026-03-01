<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    projects: Array
});

function deleteProject(id) {
    if (confirm('Are you sure you want to delete this project?')) {
        router.delete(`/admin/projects/${id}`);
    }
}
</script>

<template>
    <Head title="Project Management" />
    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center text-base-content">
                <div>
                    <h1 class="text-3xl font-black tracking-tight flex items-center gap-3">
                        <i class="fas fa-briefcase text-primary"></i>
                        Portfolio Projects
                    </h1>
                    <p class="text-sm opacity-50 mt-1">Manage and showcase your work collections.</p>
                </div>
                <Link href="/admin/projects/create" class="btn btn-primary px-6 shadow-lg shadow-primary/20">
                    <i class="fas fa-plus mr-2"></i> Create New Project
                </Link>
            </div>
        </template>

        <div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="project in projects" :key="project.id" class="card bg-base-100 shadow-sm border border-base-200 overflow-hidden group">
                    <figure class="h-48 bg-base-200 relative">
                        <img v-if="project.desktop_image" :src="project.desktop_image" class="w-full h-full object-cover transition-transform group-hover:scale-105" />
                        <div v-else class="flex items-center justify-center w-full h-full opacity-20">
                            <i class="fas fa-image text-4xl"></i>
                        </div>
                        <div class="absolute top-2 right-2 flex gap-1">
                            <div class="badge badge-neutral badge-xs opacity-70">{{ project.order }}</div>
                        </div>
                    </figure>
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-lg leading-tight">{{ project.title?.pl || 'Untitled' }}</h3>
                        </div>
                        <p class="text-sm opacity-60 line-clamp-2 mb-4">{{ project.category || 'No category' }}</p>
                        
                        <div class="flex justify-between items-center mt-auto pt-4 border-t border-base-200">
                             <div class="flex gap-2">
                                <Link :href="`/admin/projects/${project.id}/edit`" class="btn btn-ghost btn-xs text-primary">Edit</Link>
                                <button @click="deleteProject(project.id)" class="btn btn-ghost btn-xs text-error">Delete</button>
                             </div>
                             <span class="text-[10px] font-mono opacity-30">{{ project.slug }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="projects.length === 0" class="flex flex-col items-center justify-center py-24 opacity-30 italic">
                <i class="fas fa-briefcase text-5xl mb-4"></i>
                <p>No projects yet. Showcase your work!</p>
            </div>
        </div>
    </AdminLayout>
</template>
