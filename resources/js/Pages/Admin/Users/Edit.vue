<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { markRaw } from 'vue';
import { PhUser, PhHouse, PhUsers, PhFloppyDisk, PhX } from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ModuleHeader from '@/Components/Admin/ModuleHeader.vue';

const props = defineProps({
    user: Object
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: ''
});

const submit = () => {
    form.put(route('dashboard.users.update', props.user.id), {
        preserveScroll: true,
        onSuccess: () => {
            if (form.password) {
                form.reset('password', 'password_confirmation');
            }
        }
    });
};

const breadcrumbs = [
    { label: 'Dashboard', url: route('dashboard.index'), icon: markRaw(PhHouse) },
    { label: 'Users', url: route('dashboard.users.index'), icon: markRaw(PhUsers) },
    { label: 'Edit Profile' }
];
</script>

<template>
    <Head title="Edit User" />
    <AdminLayout>
        <div class="space-y-6">
            <div class="bg-base-100 p-6 rounded-box shadow-sm border border-base-300">
                <ModuleHeader 
                    title="Edit User" 
                    :description="`Update profile details for ${user.name}.`" 
                    :icon="markRaw(PhUser)"
                    :breadcrumbs="breadcrumbs"
                >
                    <template #actions>
                        <div class="flex items-center gap-2">
                            <Link :href="route('dashboard.users.index')" class="btn btn-ghost hover:bg-base-200">
                                <PhX weight="bold" class="w-4 h-4" /> Cancel
                            </Link>
                            <button 
                                @click="submit" 
                                class="btn btn-primary shadow-lg shadow-primary/20 hover:-translate-y-0.5"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing" class="loading loading-spinner loading-xs"></span>
                                <PhFloppyDisk v-else weight="bold" class="w-4 h-4" /> 
                                Save Changes
                            </button>
                        </div>
                    </template>
                </ModuleHeader>
            </div>

            <!-- Form Content -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <!-- Main Form Column -->
                <div class="xl:col-span-2 space-y-6">
                    <div class="bg-base-100 rounded-box shadow-sm border border-base-300 p-6">
                        <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                            <PhUser weight="bold" class="w-5 h-5 text-primary" /> Profile Details
                        </h2>
                        
                        <div class="space-y-4">
                            <!-- Name Source Group -->
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold">Full Name <span class="text-error">*</span></span>
                                </label>
                                <input 
                                    v-model="form.name" 
                                    type="text" 
                                    class="input input-bordered w-full bg-base-100 focus:bg-base-100 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all"
                                    :class="{'input-error bg-error/5 border-error': form.errors.name}"
                                    placeholder="Enter full name"
                                    required
                                />
                                <label class="label" v-if="form.errors.name">
                                    <span class="label-text-alt text-error font-medium">{{ form.errors.name }}</span>
                                </label>
                            </div>

                            <!-- Email Address -->
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold">Email Address <span class="text-error">*</span></span>
                                </label>
                                <input 
                                    v-model="form.email" 
                                    type="email" 
                                    class="input input-bordered w-full bg-base-100 focus:bg-base-100 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-mono text-sm"
                                    :class="{'input-error bg-error/5 border-error': form.errors.email}"
                                    placeholder="admin@example.com"
                                    required
                                />
                                <label class="label" v-if="form.errors.email">
                                    <span class="label-text-alt text-error font-medium">{{ form.errors.email }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="bg-base-100 rounded-box shadow-sm border border-base-300 p-6">
                        <h2 class="text-xl font-bold mb-2 flex items-center gap-2">
                            Change Password
                        </h2>
                        <p class="text-sm opacity-60 mb-6">Leave blank if you do not want to change the password.</p>
                        
                        <div class="space-y-4">
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold">New Password</span>
                                </label>
                                <input 
                                    v-model="form.password" 
                                    type="password" 
                                    class="input input-bordered w-full bg-base-100 focus:bg-base-100 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-mono"
                                    :class="{'input-error bg-error/5 border-error': form.errors.password}"
                                    placeholder="Enter new password"
                                    autocomplete="new-password"
                                />
                                <label class="label" v-if="form.errors.password">
                                    <span class="label-text-alt text-error font-medium">{{ form.errors.password }}</span>
                                </label>
                            </div>

                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold">Confirm New Password</span>
                                </label>
                                <input 
                                    v-model="form.password_confirmation" 
                                    type="password" 
                                    class="input input-bordered w-full bg-base-100 focus:bg-base-100 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-mono"
                                    placeholder="Re-type new password"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Metadata Column -->
                <div class="space-y-6">
                    <div class="bg-base-100 rounded-box shadow-sm border border-base-300 p-6">
                        <h2 class="text-xs font-black uppercase tracking-widest opacity-40 mb-4 pb-2 border-b border-base-200">Account Status</h2>
                        
                        <div class="space-y-4">
                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-semibold opacity-60">Created At</span>
                                <span class="text-sm font-mono opacity-80 bg-base-200 p-2 rounded-lg break-all">
                                    {{ new Date(user.created_at).toLocaleString() }}
                                </span>
                            </div>
                            
                            <div class="flex flex-col gap-1 mt-4">
                                <span class="text-xs font-semibold opacity-60">Last Updated</span>
                                <span class="text-sm font-mono opacity-80 bg-base-200 p-2 rounded-lg break-all">
                                    {{ new Date(user.updated_at).toLocaleString() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </AdminLayout>
</template>
