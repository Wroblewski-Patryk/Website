<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    languages: Array
});

const isCreating = ref(false);
const editingLanguage = ref(null);

const form = useForm({
    code: '',
    name: '',
    is_default: false,
    is_active: true
});

function openCreate() {
    form.reset();
    editingLanguage.value = null;
    isCreating.value = true;
}

function openEdit(lang) {
    editingLanguage.value = lang;
    form.code = lang.code;
    form.name = lang.name;
    form.is_default = lang.is_default;
    form.is_active = lang.is_active;
    isCreating.value = true;
}

function submit() {
    if (editingLanguage.value) {
        form.put(`/admin/languages/${editingLanguage.value.id}`, {
            onSuccess: () => {
                isCreating.value = false;
                form.reset();
            }
        });
    } else {
        form.post('/admin/languages', {
            onSuccess: () => {
                isCreating.value = false;
                form.reset();
            }
        });
    }
}

function deleteLanguage(id) {
    if (confirm('Are you sure? This cannot be undone.')) {
        router.delete(`/admin/languages/${id}`);
    }
}
</script>

<template>
    <Head title="Language Management" />
    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center text-base-content">
                <div>
                    <h1 class="text-3xl font-black tracking-tight flex items-center gap-3">
                        <i class="fas fa-globe text-primary"></i>
                        Languages
                    </h1>
                    <p class="text-sm opacity-50 mt-1">Manage available locales and default site language.</p>
                </div>
                <button @click="openCreate" class="btn btn-primary px-6 shadow-lg shadow-primary/20">
                    <i class="fas fa-plus mr-2"></i> Add New Language
                </button>
            </div>
        </template>

        <div class="bg-base-100 rounded-box border border-base-300 shadow-sm overflow-hidden">
            <div v-if="isCreating" class="p-8 pb-0">
                <div class="card bg-base-100 shadow-xl border border-primary/20 mb-8 max-w-2xl">
                <div class="card-body">
                    <h3 class="card-title text-primary">{{ editingLanguage ? 'Edit Language' : 'New Language' }}</h3>
                    <form @submit.prevent="submit" class="grid grid-cols-2 gap-4">
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">Code (e.g. en, pl)</span></label>
                            <input type="text" v-model="form.code" class="input input-bordered w-full" :disabled="editingLanguage" required />
                        </div>
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">Name (e.g. English)</span></label>
                            <input type="text" v-model="form.name" class="input input-bordered w-full" required />
                        </div>
                        
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-4">
                                <input type="checkbox" v-model="form.is_default" class="checkbox checkbox-primary" />
                                <span class="label-text">Set as Default</span>
                            </label>
                        </div>
                        
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-4">
                                <input type="checkbox" v-model="form.is_active" class="checkbox checkbox-secondary" />
                                <span class="label-text">Active</span>
                            </label>
                        </div>

                        <div class="col-span-2 flex justify-end gap-2 mt-4">
                            <button type="button" @click="isCreating = false" class="btn btn-ghost">Cancel</button>
                            <button type="submit" class="btn btn-primary" :disabled="form.processing">Save Language</button>
                        </div>
                    </form>
                </div>
            </div>
            </div>

            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>Lang</th>
                            <th>Code</th>
                            <th>Status</th>
                            <th>Default</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="lang in languages" :key="lang.id" class="hover">
                            <td class="font-bold">{{ lang.name }}</td>
                            <td class="font-mono text-sm opacity-60">{{ lang.code }}</td>
                            <td>
                                <span v-if="lang.is_active" class="badge badge-success badge-sm">Active</span>
                                <span v-else class="badge badge-ghost badge-sm text-opacity-40">Inactive</span>
                            </td>
                            <td>
                                <span v-if="lang.is_default" class="badge badge-primary badge-sm">Default</span>
                            </td>
                            <td class="text-right">
                                <div class="flex justify-end gap-2">
                                    <button @click="openEdit(lang)" class="btn btn-ghost btn-xs text-primary">Edit</button>
                                    <button @click="deleteLanguage(lang.id)" class="btn btn-ghost btn-xs text-error" :disabled="lang.is_default">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="languages.length === 0">
                            <td colspan="5" class="text-center py-8 opacity-40 italic">No languages configured.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
