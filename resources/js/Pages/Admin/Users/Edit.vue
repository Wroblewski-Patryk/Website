<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { markRaw } from 'vue';
import { PhUser, PhHouse, PhUsers, PhFloppyDisk, PhX } from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ModuleHeader from '@/features/admin/shared/components/ModuleHeader.vue';
import { useTranslations } from '@/Composables/useTranslations';
import { useFormatter } from '@/Composables/useFormatter';

const props = defineProps({
    user_item: Object,
    roles: Array
});

const { t } = useTranslations();
const { formatDateTime } = useFormatter();

const form = useForm({
    name: props.user_item.name || '',
    email: props.user_item.email || '',
    password: '',
    password_confirmation: '',
    roles: props.user_item.roles?.map(r => r.name) || (props.user_item.id ? [] : ['editor'])
});

const submit = () => {
    if (props.user_item.id) {
        form.put(route('admin.users.update', props.user_item.id), {
            preserveScroll: true,
            onSuccess: () => {
                if (form.password) {
                    form.reset('password', 'password_confirmation');
                }
            }
        });
    } else {
        form.post(route('admin.users.store'), {
            preserveScroll: true
        });
    }
};

const breadcrumbs = [
    { label: t('admin.dashboard.title', 'Dashboard'), url: route('admin.dashboard.index'), icon: markRaw(PhHouse) },
    { label: t('admin.users.title', 'Users'), url: route('admin.users.index'), icon: markRaw(PhUsers) },
    { label: props.user_item.id ? t('admin.users.edit_user', 'Edit User') : t('admin.users.create_user', 'Create User') }
];
</script>

<template>
    <Head :title="user_item.id ? t('admin.users.edit_user', 'Edit User') : t('admin.users.create_user', 'Create User')" />
    <AdminLayout>
        <div class="space-y-6">
            <div class="bg-base-100 p-6 rounded-box shadow-sm border border-base-300">
                <ModuleHeader 
                    :title="user_item.id ? t('admin.users.edit_user', 'Edit User') : t('admin.users.create_user', 'Create User')" 
                    :description="user_item.id ? t('admin.users.edit_desc', `Update profile details for ${user_item.name}.`, { name: user_item.name }) : t('admin.users.create_desc', 'Create a new user account.')" 
                    :icon="markRaw(PhUser)"
                    :breadcrumbs="breadcrumbs"
                >
                    <template #actions>
                        <div class="flex items-center gap-2">
                            <Link :href="route('admin.users.index')" class="btn btn-ghost hover:bg-base-200">
                                <PhX weight="bold" class="w-4 h-4" /> {{ t('admin.common.cancel', 'Cancel') }}
                            </Link>
                            <button 
                                @click="submit" 
                                class="btn btn-primary shadow-lg shadow-primary/20 hover:-translate-y-0.5"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing" class="loading loading-spinner loading-xs"></span>
                                <PhFloppyDisk v-else weight="bold" class="w-4 h-4" /> 
                                {{ user_item.id ? t('admin.common.save', 'Save Changes') : t('admin.common.create', 'Create') }}
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
                            <PhUser weight="bold" class="w-5 h-5 text-primary" /> {{ t('admin.users.profile_details', 'Profile Details') }}
                        </h2>
                        
                        <div class="space-y-4">
                            <!-- Name Source Group -->
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold">{{ t('admin.users.full_name', 'Full Name') }} <span class="text-error">*</span></span>
                                </label>
                                <input 
                                    v-model="form.name" 
                                    type="text" 
                                    class="input input-bordered w-full bg-base-100 focus:bg-base-100 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all"
                                    :class="{'input-error bg-error/5 border-error': form.errors.name}"
                                    :placeholder="t('admin.users.name_placeholder', 'Enter full name')"
                                    required
                                />
                                <label class="label" v-if="form.errors.name">
                                    <span class="label-text-alt text-error font-medium">{{ form.errors.name }}</span>
                                </label>
                            </div>

                            <!-- Email Address -->
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold">{{ t('admin.users.email_address', 'Email Address') }} <span class="text-error">*</span></span>
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
                            {{ t('admin.users.change_password', 'Change Password') }}
                        </h2>
                        <p class="text-sm opacity-60 mb-6">{{ t('admin.users.password_hint', 'Leave blank if you do not want to change the password.') }}</p>
                        
                        <div class="space-y-4">
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold">{{ t('admin.users.new_password', 'New Password') }} <span v-if="!user_item.id" class="text-error">*</span></span>
                                </label>
                                <input 
                                    v-model="form.password" 
                                    type="password" 
                                    class="input input-bordered w-full bg-base-100 focus:bg-base-100 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-mono"
                                    :class="{'input-error bg-error/5 border-error': form.errors.password}"
                                    :placeholder="t('admin.users.password_placeholder', 'Enter new password')"
                                    autocomplete="new-password"
                                />
                                <label class="label" v-if="form.errors.password">
                                    <span class="label-text-alt text-error font-medium">{{ form.errors.password }}</span>
                                </label>
                            </div>

                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold">{{ t('admin.users.confirm_password', 'Confirm New Password') }} <span v-if="!user_item.id" class="text-error">*</span></span>
                                </label>
                                <input 
                                    v-model="form.password_confirmation" 
                                    type="password" 
                                    class="input input-bordered w-full bg-base-100 focus:bg-base-100 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-mono"
                                    :placeholder="t('admin.users.confirm_placeholder', 'Re-type new password')"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Metadata Column -->
                <div class="space-y-6">
                    <div class="bg-base-100 rounded-box shadow-sm border border-base-300 p-6">
                        <h2 class="text-xs font-black uppercase tracking-widest opacity-40 mb-4 pb-2 border-b border-base-200">{{ t('admin.users.role', 'Role') }}</h2>
                        <div class="form-control w-full">
                            <select 
                                :value="form.roles[0]" 
                                @change="form.roles = [$event.target.value]"
                                class="select select-bordered w-full bg-base-100 focus:bg-base-100 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all"
                            >
                                <option disabled value="">{{ t('admin.users.select_role', 'Select role') }}</option>
                                <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
                            </select>
                            <label class="label" v-if="form.errors.roles">
                                <span class="label-text-alt text-error font-medium">{{ form.errors.roles }}</span>
                            </label>
                        </div>
                    </div>

                    <div class="bg-base-100 rounded-box shadow-sm border border-base-300 p-6">
                        <h2 class="text-xs font-black uppercase tracking-widest opacity-40 mb-4 pb-2 border-b border-base-200">{{ t('admin.users.account_status', 'Account Status') }}</h2>
                        
                        <div class="space-y-4">
                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-semibold opacity-60">{{ t('admin.users.created_at', 'Created At') }}</span>
                                <span class="text-sm font-mono opacity-80 bg-base-200 p-2 rounded-lg break-all">
                                    {{ user_item.created_at ? formatDateTime(user_item.created_at) : '-' }}
                                </span>
                            </div>
                            
                            <div class="flex flex-col gap-1 mt-4">
                                <span class="text-xs font-semibold opacity-60">{{ t('admin.users.last_updated', 'Last Updated') }}</span>
                                <span class="text-sm font-mono opacity-80 bg-base-200 p-2 rounded-lg break-all">
                                    {{ user_item.updated_at ? formatDateTime(user_item.updated_at) : '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </AdminLayout>
</template>
