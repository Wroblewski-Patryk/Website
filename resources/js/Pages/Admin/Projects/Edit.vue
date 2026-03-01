<script setup>
import { ref } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    project: Object
});

const form = useForm({
    title: props.project?.title || { pl: '', en: '' },
    slug: props.project?.slug || '',
    description: props.project?.description || { pl: '', en: '' },
    desktop_image: props.project?.desktop_image || '',
    mobile_image: props.project?.mobile_image || '',
    url: props.project?.url || '',
    category: props.project?.category || '',
    order: props.project?.order || 0
});

function submit() {
    if (props.project) {
        form.put(`/admin/projects/${props.project.id}`);
    } else {
        form.post('/admin/projects');
    }
}
</script>

<template>
    <Head title="Edit Project" />
    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <Link href="/admin/projects" class="btn btn-ghost btn-circle btn-sm"><i class="fas fa-arrow-left"></i></Link>
                    <h2 class="text-xl font-bold">{{ project ? 'Edit Project' : 'New Project' }}</h2>
                </div>
                <button @click="submit" class="btn btn-primary" :disabled="form.processing">
                    Save Project
                </button>
            </div>
        </template>

        <div class="p-8 max-w-5xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left: Content Info -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="card bg-base-100 shadow-sm border border-base-200">
                        <div class="card-body">
                            <h3 class="card-title text-sm uppercase tracking-widest opacity-50 mb-4">Project Information</h3>
                            
                            <div class="grid grid-cols-1 gap-4">
                                <!-- Title PL -->
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-semibold">Project Title (Polish)</span></label>
                                    <input type="text" v-model="form.title.pl" class="input input-bordered text-lg" placeholder="..." required />
                                </div>
                                
                                <!-- Title EN -->
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Project Title (English)</span></label>
                                    <input type="text" v-model="form.title.en" class="input input-bordered" placeholder="..." />
                                </div>

                                <!-- Slug -->
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-mono text-xs opacity-50">Slug (URL identifier)</span></label>
                                    <input type="text" v-model="form.slug" class="input input-bordered input-sm font-mono" placeholder="leave-empty-to-auto-generate" />
                                </div>

                                <!-- Description PL -->
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-semibold">Description (Polish)</span></label>
                                    <textarea v-model="form.description.pl" class="textarea textarea-bordered h-32" placeholder="Tell more about the project..."></textarea>
                                </div>

                                <!-- Description EN -->
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Description (English)</span></label>
                                    <textarea v-model="form.description.en" class="textarea textarea-bordered h-32" placeholder="English version..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Meta Features -->
                <div class="space-y-6">
                    <div class="card bg-base-100 shadow-sm border border-base-200">
                        <div class="card-body">
                            <h3 class="card-title text-sm uppercase tracking-widest opacity-50 mb-4">Media & URL</h3>
                            
                            <!-- Desktop Image -->
                            <div class="form-control mb-4">
                                <label class="label"><span class="label-text">Desktop Image URL</span></label>
                                <input type="text" v-model="form.desktop_image" class="input input-bordered input-sm" placeholder="URL to image" />
                            </div>

                            <!-- Mobile Image -->
                            <div class="form-control mb-4">
                                <label class="label"><span class="label-text">Mobile Image URL</span></label>
                                <input type="text" v-model="form.mobile_image" class="input input-bordered input-sm" placeholder="URL to image" />
                            </div>

                            <!-- External Link -->
                            <div class="form-control mb-4">
                                <label class="label"><span class="label-text">Project Link (External)</span></label>
                                <input type="text" v-model="form.url" class="input input-bordered input-sm" placeholder="https://..." />
                            </div>

                            <!-- Category -->
                            <div class="form-control mb-4">
                                <label class="label"><span class="label-text">Category</span></label>
                                <input type="text" v-model="form.category" class="input input-bordered input-sm" placeholder="e.g. UX/UI, Web Design" />
                            </div>

                            <!-- Order -->
                            <div class="form-control">
                                <label class="label"><span class="label-text">Display Order</span></label>
                                <input type="number" v-model="form.order" class="input input-bordered input-sm" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
